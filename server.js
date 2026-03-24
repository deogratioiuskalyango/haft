/**
 * Serves the Vite build from /dist and proxies YouTube Data API v3 at GET /api/videos.
 *
 * Auth (pick one):
 * - YOUTUBE_API_KEY — simplest for public channel/playlist reads.
 * - GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET + GOOGLE_OAUTH_REFRESH_TOKEN — OAuth2
 *   (same GCP project; obtain refresh token once via OAuth consent / OAuth Playground).
 *
 * Flow: resolve channel → uploads playlist ID → playlistItems (paged).
 */

import express from "express";
import dotenv from "dotenv";
import http from "node:http";
import path from "node:path";
import { fileURLToPath } from "node:url";
import {
  buildPesapalDirectOrderIframeUrl,
  buildPesapalQueryPaymentStatusUrl,
} from "./lib/pesapal-oauth.js";

dotenv.config();

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const app = express();

const PORT = parseInt(process.env.PORT, 10) || 3000;
const DIST = path.resolve(__dirname, "dist");

const API_KEY = process.env.YOUTUBE_API_KEY?.trim();
const OAUTH_CLIENT_ID = process.env.GOOGLE_CLIENT_ID?.trim();
const OAUTH_CLIENT_SECRET = process.env.GOOGLE_CLIENT_SECRET?.trim();
const OAUTH_REFRESH_TOKEN = process.env.GOOGLE_OAUTH_REFRESH_TOKEN?.trim();

const CHANNEL_ID = process.env.YOUTUBE_CHANNEL_ID?.trim();
const CHANNEL_HANDLE = process.env.YOUTUBE_CHANNEL_HANDLE?.trim();
const DEFAULT_MAX = Math.min(50, Math.max(1, parseInt(process.env.YOUTUBE_MAX_RESULTS, 10) || 12));
const CACHE_MS = Math.max(0, (parseInt(process.env.YOUTUBE_CACHE_SECONDS, 10) || 120) * 1000);

/** Pesapal (Mobile Money, cards, etc.) — same flow as Pesapal PHP iframe sample */
const PESAPAL_CONSUMER_KEY = process.env.PESAPAL_CONSUMER_KEY?.trim();
const PESAPAL_CONSUMER_SECRET = process.env.PESAPAL_CONSUMER_SECRET?.trim();
const PESAPAL_CALLBACK_URL = process.env.PESAPAL_CALLBACK_URL?.trim();
const PESAPAL_SANDBOX = process.env.PESAPAL_SANDBOX !== "false";
const PESAPAL_ORDER_API =
  process.env.PESAPAL_ORDER_API?.trim() ||
  (PESAPAL_SANDBOX
    ? "http://demo.pesapal.com/api/PostPesapalDirectOrderV4"
    : "https://www.pesapal.com/API/PostPesapalDirectOrderV4");
const PESAPAL_QUERY_API =
  process.env.PESAPAL_QUERY_API?.trim() ||
  (PESAPAL_SANDBOX
    ? "http://demo.pesapal.com/api/querypaymentstatus"
    : "https://www.pesapal.com/api/querypaymentstatus");

function pesapalConfigured() {
  return Boolean(PESAPAL_CONSUMER_KEY && PESAPAL_CONSUMER_SECRET && PESAPAL_CALLBACK_URL);
}

/** @type {{ uploadsPlaylistId: string, channelTitle: string, channelId: string, at: number } | null} */
let channelCache = null;
/** @type {Map<string, { at: number, body: object }>} */
const pageCache = new Map();

/** @type {{ token: string, expiresAt: number }} */
let accessTokenCache = { token: "", expiresAt: 0 };

function hasApiKeyAuth() {
  return Boolean(API_KEY);
}

function hasOAuthAuth() {
  return Boolean(OAUTH_CLIENT_ID && OAUTH_CLIENT_SECRET && OAUTH_REFRESH_TOKEN);
}

function assertYouTubeAuthConfigured() {
  if (hasApiKeyAuth() || hasOAuthAuth()) return;
  throw new Error(
    "Configure YOUTUBE_API_KEY or OAuth (GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_OAUTH_REFRESH_TOKEN) in .env"
  );
}

async function getOAuthAccessToken() {
  if (accessTokenCache.token && Date.now() < accessTokenCache.expiresAt - 60_000) {
    return accessTokenCache.token;
  }
  const body = new URLSearchParams({
    client_id: OAUTH_CLIENT_ID,
    client_secret: OAUTH_CLIENT_SECRET,
    refresh_token: OAUTH_REFRESH_TOKEN,
    grant_type: "refresh_token",
  });
  const r = await fetch("https://oauth2.googleapis.com/token", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: body.toString(),
  });
  const data = await r.json().catch(() => ({}));
  if (!r.ok) {
    const msg = data.error_description || data.error || "OAuth token refresh failed";
    const err = new Error(msg);
    err.status = r.status;
    throw err;
  }
  const expiresIn = typeof data.expires_in === "number" ? data.expires_in : 3600;
  accessTokenCache = {
    token: data.access_token,
    expiresAt: Date.now() + expiresIn * 1000,
  };
  return accessTokenCache.token;
}

/**
 * @param {string} apiPath e.g. "channels"
 * @param {Record<string, string | undefined>} params
 */
async function youtubeRequest(apiPath, params) {
  assertYouTubeAuthConfigured();

  const u = new URL(`https://www.googleapis.com/youtube/v3/${apiPath}`);
  for (const [k, v] of Object.entries(params)) {
    if (v != null && v !== "") u.searchParams.set(k, String(v));
  }

  /** @type {Record<string, string>} */
  const headers = { Accept: "application/json" };

  if (hasApiKeyAuth()) {
    u.searchParams.set("key", API_KEY);
    return fetchJson(u.toString(), { headers });
  }

  const token = await getOAuthAccessToken();
  headers.Authorization = `Bearer ${token}`;
  return fetchJson(u.toString(), { headers });
}

async function fetchJson(url, init = {}) {
  const r = await fetch(url, {
    ...init,
    headers: { Accept: "application/json", ...init.headers },
  });
  const data = await r.json().catch(() => ({}));
  if (!r.ok) {
    const msg = data.error?.message || data.error_description || data.error || r.statusText || "YouTube API error";
    const err = new Error(typeof msg === "string" ? msg : JSON.stringify(msg));
    err.status = r.status;
    throw err;
  }
  return data;
}

async function resolveUploadsPlaylistId() {
  if (channelCache && Date.now() - channelCache.at < CACHE_MS) {
    return {
      uploadsPlaylistId: channelCache.uploadsPlaylistId,
      channelTitle: channelCache.channelTitle,
      channelId: channelCache.channelId,
    };
  }

  let channelTitle = "";
  let uploadsPlaylistId = "";
  let resolvedChannelId = "";

  if (CHANNEL_ID) {
    const data = await youtubeRequest("channels", {
      part: "snippet,contentDetails",
      id: CHANNEL_ID,
    });
    const ch = data.items?.[0];
    if (!ch) throw new Error("Channel not found for YOUTUBE_CHANNEL_ID");
    resolvedChannelId = ch.id || CHANNEL_ID;
    channelTitle = ch.snippet?.title || "";
    uploadsPlaylistId = ch.contentDetails?.relatedPlaylists?.uploads || "";
  } else if (CHANNEL_HANDLE) {
    const handle = CHANNEL_HANDLE.replace(/^@/, "");
    const data = await youtubeRequest("channels", {
      part: "snippet,contentDetails",
      forHandle: handle,
    });
    const ch = data.items?.[0];
    if (!ch) throw new Error("Channel not found for YOUTUBE_CHANNEL_HANDLE");
    resolvedChannelId = ch.id || "";
    channelTitle = ch.snippet?.title || "";
    uploadsPlaylistId = ch.contentDetails?.relatedPlaylists?.uploads || "";
  } else {
    throw new Error("Set YOUTUBE_CHANNEL_ID or YOUTUBE_CHANNEL_HANDLE in .env");
  }

  if (!uploadsPlaylistId) throw new Error("Could not resolve uploads playlist for this channel");

  channelCache = { uploadsPlaylistId, channelTitle, channelId: resolvedChannelId, at: Date.now() };
  return { uploadsPlaylistId, channelTitle, channelId: resolvedChannelId };
}

function normalizeVideos(items) {
  return (items || [])
    .map((item) => {
      const sn = item.snippet || {};
      const vid = sn.resourceId?.videoId || item.contentDetails?.videoId;
      const thumbs = sn.thumbnails || {};
      const thumb =
        thumbs.maxres?.url || thumbs.high?.url || thumbs.medium?.url || thumbs.default?.url || "";
      return {
        id: vid,
        title: sn.title || "Untitled",
        description: sn.description || "",
        publishedAt: sn.publishedAt || "",
        thumbnail: thumb,
      };
    })
    .filter((v) => v.id);
}

app.get("/api/pesapal/status", (req, res) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.json({
    enabled: pesapalConfigured(),
    sandbox: PESAPAL_SANDBOX,
  });
});

app.options("/api/pesapal/init", (req, res) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Methods", "POST, OPTIONS");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");
  res.status(204).end();
});

app.post("/api/pesapal/init", express.json({ limit: "48kb" }), (req, res) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  if (!pesapalConfigured()) {
    return res.status(503).json({
      error:
        "Pesapal is not configured. Set PESAPAL_CONSUMER_KEY, PESAPAL_CONSUMER_SECRET, and PESAPAL_CALLBACK_URL in .env",
    });
  }

  const body = req.body || {};
  const amountRaw = body.amount;
  const firstName = String(body.firstName || "").trim();
  const lastName = String(body.lastName || "").trim();
  const email = String(body.email || "").trim();
  const phone = String(body.phone || "").trim();
  const donationType = String(body.donationType || "").trim();

  const amount = parseFloat(String(amountRaw).replace(/,/g, ""));
  if (!Number.isFinite(amount) || amount <= 0) {
    return res.status(400).json({ error: "Invalid donation amount." });
  }
  if (!firstName || !lastName || !email) {
    return res.status(400).json({ error: "First name, last name, and email are required." });
  }
  if (email.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    return res.status(400).json({ error: "Invalid email address." });
  }

  const reference = `HAFT-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
  const descParts = ["HAFT Uganda donation", donationType && `(${donationType})`].filter(Boolean);
  const description = descParts.join(" ").slice(0, 180);

  try {
    const iframeUrl = buildPesapalDirectOrderIframeUrl({
      consumerKey: PESAPAL_CONSUMER_KEY,
      consumerSecret: PESAPAL_CONSUMER_SECRET,
      callbackUrl: PESAPAL_CALLBACK_URL,
      orderApiUrl: PESAPAL_ORDER_API,
      amount,
      description,
      reference,
      firstName,
      lastName,
      email,
      phoneNumber: phone,
    });
    res.json({ iframeUrl, reference, sandbox: PESAPAL_SANDBOX });
  } catch (e) {
    console.error("[Pesapal init]", e);
    res.status(500).json({ error: "Could not start Pesapal checkout." });
  }
});

/**
 * Pesapal IPN — register this full URL in your Pesapal merchant dashboard.
 * Example: https://your-domain.com/api/pesapal/ipn
 */
app.get("/api/pesapal/ipn", async (req, res) => {
  const notification = req.query.pesapal_notification_type;
  const trackingId = req.query.pesapal_transaction_tracking_id;
  const merchantRef = req.query.pesapal_merchant_reference;

  if (!pesapalConfigured() || notification !== "CHANGE" || !trackingId || !merchantRef) {
    res.status(200).type("text/plain").send("OK");
    return;
  }

  try {
    const url = buildPesapalQueryPaymentStatusUrl({
      consumerKey: PESAPAL_CONSUMER_KEY,
      consumerSecret: PESAPAL_CONSUMER_SECRET,
      queryApiUrl: PESAPAL_QUERY_API,
      merchantReference: String(merchantRef),
      transactionTrackingId: String(trackingId),
    });
    const r = await fetch(url);
    const text = await r.text();
    console.log("[Pesapal IPN]", { merchantRef, trackingId, queryStatus: r.status, bodyPreview: text.slice(0, 400) });

    const ack = `pesapal_notification_type=${notification}&pesapal_transaction_tracking_id=${trackingId}&pesapal_merchant_reference=${merchantRef}`;
    res.status(200).type("text/plain").send(ack);
  } catch (e) {
    console.error("[Pesapal IPN]", e);
    res.status(500).type("text/plain").send("ERROR");
  }
});

app.get("/api/videos", async (req, res) => {
  res.setHeader("Access-Control-Allow-Origin", "*");

  if (!hasApiKeyAuth() && !hasOAuthAuth()) {
    return res.status(503).json({
      error:
        "Server not configured: set YOUTUBE_API_KEY in .env, or GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET + GOOGLE_OAUTH_REFRESH_TOKEN.",
    });
  }

  const pageToken = req.query.pageToken ? String(req.query.pageToken) : "";
  const maxResults = Math.min(
    50,
    Math.max(1, parseInt(String(req.query.maxResults), 10) || DEFAULT_MAX)
  );

  const cacheKey = `${pageToken}|${maxResults}`;
  const cached = pageCache.get(cacheKey);
  if (cached && Date.now() - cached.at < CACHE_MS) {
    return res.json(cached.body);
  }

  try {
    const { uploadsPlaylistId, channelTitle, channelId } = await resolveUploadsPlaylistId();
    const data = await youtubeRequest("playlistItems", {
      part: "snippet,contentDetails",
      playlistId: uploadsPlaylistId,
      maxResults: String(maxResults),
      pageToken: pageToken || undefined,
    });
    const videos = normalizeVideos(data.items);
    const body = {
      channelTitle,
      channelId: channelId || null,
      videos,
      nextPageToken: data.nextPageToken || null,
    };
    pageCache.set(cacheKey, { at: Date.now(), body });
    res.json(body);
  } catch (e) {
    const status = e.status && e.status >= 400 && e.status < 600 ? e.status : 500;
    res.status(status).json({ error: e.message || "Unknown error" });
  }
});

app.use(express.static(DIST, { extensions: ["html"] }));

app.use((req, res) => {
  res.status(404).send("Not found — run npm run build, then open /home.html or /pages/youtube.html");
});

/**
 * If PORT (default 3000) is taken, try the next ports so `npm run serve` still works.
 */
function startServer(port, attemptsLeft = 30) {
  const server = http.createServer(app);
  server.on("error", (err) => {
    if (err.code === "EADDRINUSE" && attemptsLeft > 1) {
      console.warn(`[serve] Port ${port} is already in use, trying ${port + 1}…`);
      startServer(port + 1, attemptsLeft - 1);
    } else {
      console.error(err);
      process.exit(1);
    }
  });
  server.listen(port, () => {
    console.log(`HAFT site + YouTube API at http://127.0.0.1:${port}`);
    console.log(`  Static: ${DIST}`);
    console.log(`  Videos: http://127.0.0.1:${port}/api/videos`);
    console.log(`  Auth: ${hasApiKeyAuth() ? "API key" : hasOAuthAuth() ? "OAuth refresh token" : "not configured"}`);
    console.log(
      `  Pesapal: ${pesapalConfigured() ? `enabled (${PESAPAL_SANDBOX ? "sandbox" : "live"})` : "not configured"}`,
    );
  });
}

startServer(PORT);

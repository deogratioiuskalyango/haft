/**
 * HAFT YouTube channel page — loads videos via GET /api/videos (Express proxy in server.js).
 * Feed-style UX (inspired by professional YouTube feed plugins): channel header, subscribe CTA,
 * grid / list / carousel layouts, sort, search, duration & view meta, live badges, lightbox + share.
 */

const grid = document.getElementById("yt-grid");
const statusEl = document.getElementById("yt-status");
const loadMoreBtn = document.getElementById("yt-load-more");
const searchInput = document.getElementById("yt-search");
const hintEl = document.getElementById("yt-config-hint");
const themeToggle = document.getElementById("yt-theme-toggle");
const channelHeaderEl = document.getElementById("yt-channel-header");
const sortSelect = document.getElementById("yt-sort");
const layoutGridBtn = document.getElementById("yt-layout-grid");
const layoutListBtn = document.getElementById("yt-layout-list");
const layoutCarouselBtn = document.getElementById("yt-layout-carousel");

/** @type {{ id: string, title: string, description: string, publishedAt: string, thumbnail: string, durationSeconds?: number | null, viewCount?: number | null, liveBroadcastContent?: string }[]} */
let allVideos = [];
let nextPageToken = null;
let loading = false;

/** @type {{ channelId: string | null, channelTitle: string | null, channelThumbUrl: string | null, subscriberCount: number | null }} */
let channelMeta = {
  channelId: null,
  channelTitle: null,
  channelThumbUrl: null,
  subscriberCount: null,
};

/** @type {'grid' | 'list' | 'carousel'} */
let layoutMode = "grid";
/** @type {'newest' | 'oldest' | 'title' | 'views'} */
let sortMode = "newest";

let resolveApiRootPromise = null;
let statusClearTimer = 0;

function isLocalHostname() {
  const h = window.location.hostname;
  return h === "127.0.0.1" || h === "localhost" || h === "[::1]";
}

function isHaftYoutubeApiResponse(res, text) {
  const ct = (res.headers.get("content-type") || "").toLowerCase();
  if (!ct.includes("json") && !/^\s*\{/.test(text)) return false;
  try {
    const j = JSON.parse(text);
    return j != null && typeof j === "object" && ("videos" in j || "error" in j);
  } catch {
    return false;
  }
}

async function fetchApiProbe(url, timeoutMs) {
  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), timeoutMs);
  try {
    const r = await fetch(url, { signal: ctrl.signal });
    const text = await r.text();
    return isHaftYoutubeApiResponse(r, text) ? r : null;
  } catch {
    return null;
  } finally {
    clearTimeout(t);
  }
}

async function resolveApiRoot() {
  const attr = document.body?.getAttribute("data-youtube-api-root");
  if (attr !== null && attr.trim() !== "") {
    return attr.trim().replace(/\/$/, "");
  }

  if (resolveApiRootPromise) {
    return resolveApiRootPromise;
  }

  resolveApiRootPromise = (async () => {
    const timeoutMs = 2500;
    const origin = window.location.origin;

    if (origin?.startsWith("http")) {
      const probeUrl = new URL("/api/videos?maxResults=1", origin).href;
      const hit = await fetchApiProbe(probeUrl, timeoutMs);
      if (hit) return "";
    }

    if (!isLocalHostname()) {
      return "";
    }

    for (let p = 3000; p <= 3025; p++) {
      const url = `http://127.0.0.1:${p}/api/videos?maxResults=1`;
      const hit = await fetchApiProbe(url, timeoutMs);
      if (hit) {
        return `http://127.0.0.1:${p}`;
      }
    }

    return "";
  })();

  return resolveApiRootPromise;
}

async function apiVideosUrl() {
  const root = await resolveApiRoot();
  const path = "/api/videos";
  return root ? `${root}${path}` : path;
}

function canUseClientYoutube() {
  const key = document.body?.getAttribute("data-youtube-api-key")?.trim();
  const id = document.body?.getAttribute("data-youtube-channel-id")?.trim();
  const handle = document.body?.getAttribute("data-youtube-channel-handle")?.trim();
  return Boolean(key && (id || handle));
}

function getClientMaxResults() {
  const n = parseInt(String(document.body?.getAttribute("data-youtube-max-results") || "12"), 10);
  return Math.min(50, Math.max(1, Number.isFinite(n) ? n : 12));
}

/** @type {Promise<ClientChannelContext> | null} */
let clientChannelLoadPromise = null;

/**
 * @typedef {{ apiKey: string, uploadsPlaylistId: string, channelId: string | null, channelTitle: string | null, channelThumbUrl: string | null, subscriberCount: number | null }} ClientChannelContext
 */

async function loadClientChannelContext() {
  const apiKey = document.body?.getAttribute("data-youtube-api-key")?.trim();
  const channelId = document.body?.getAttribute("data-youtube-channel-id")?.trim();
  const handleRaw = document.body?.getAttribute("data-youtube-channel-handle")?.trim();
  const handle = handleRaw ? handleRaw.replace(/^@/, "") : "";
  if (!apiKey || (!channelId && !handle)) {
    throw new Error("Missing data-youtube-api-key and channel id or handle on <body>");
  }
  const u = new URL("https://www.googleapis.com/youtube/v3/channels");
  u.searchParams.set("part", "snippet,contentDetails,statistics");
  u.searchParams.set("key", apiKey);
  if (channelId) u.searchParams.set("id", channelId);
  else u.searchParams.set("forHandle", handle);

  const r = await fetch(u.toString(), { headers: { Accept: "application/json" } });
  const data = await r.json().catch(() => ({}));
  if (!r.ok) {
    const msg = data.error?.message || data.error_description || r.statusText || "YouTube channels API error";
    throw new Error(typeof msg === "string" ? msg : JSON.stringify(msg));
  }
  const ch = data.items?.[0];
  if (!ch) {
    throw new Error("Channel not found. Check data-youtube-channel-id or data-youtube-channel-handle.");
  }
  const uploadsPlaylistId = ch.contentDetails?.relatedPlaylists?.uploads;
  if (!uploadsPlaylistId) {
    throw new Error("Could not resolve uploads playlist for this channel.");
  }
  const thumbs = ch.snippet?.thumbnails || {};
  const channelThumbUrl = thumbs.high?.url || thumbs.medium?.url || thumbs.default?.url || null;
  const sc = ch.statistics?.subscriberCount;
  let subscriberCount = sc != null && sc !== "" ? parseInt(String(sc), 10) : null;
  if (Number.isNaN(subscriberCount)) subscriberCount = null;
  return {
    apiKey,
    uploadsPlaylistId,
    channelId: ch.id || null,
    channelTitle: ch.snippet?.title || null,
    channelThumbUrl,
    subscriberCount,
  };
}

async function getClientChannelContext() {
  if (!clientChannelLoadPromise) {
    clientChannelLoadPromise = loadClientChannelContext();
  }
  try {
    return await clientChannelLoadPromise;
  } catch (e) {
    clientChannelLoadPromise = null;
    throw e;
  }
}

/** @param {unknown[]} items */
function normalizePlaylistItems(items) {
  return (items || [])
    .map((item) => {
      const it = /** @type {{ snippet?: Record<string, unknown>, contentDetails?: Record<string, unknown> }} */ (item);
      const sn = it.snippet || {};
      const resourceId = /** @type {{ videoId?: string }} */ (sn.resourceId);
      const vid = resourceId?.videoId || /** @type {{ videoId?: string }} */ (it.contentDetails)?.videoId;
      const thumbs = /** @type {Record<string, { url?: string }>} */ (sn.thumbnails) || {};
      const thumb =
        thumbs.maxres?.url || thumbs.high?.url || thumbs.medium?.url || thumbs.default?.url || "";
      return {
        id: typeof vid === "string" ? vid : "",
        title: typeof sn.title === "string" ? sn.title : "Untitled",
        description: typeof sn.description === "string" ? sn.description : "",
        publishedAt: typeof sn.publishedAt === "string" ? sn.publishedAt : "",
        thumbnail: thumb,
      };
    })
    .filter((v) => v.id);
}

/** @param {string | undefined} iso */
function parseIso8601DurationSeconds(iso) {
  if (!iso || typeof iso !== "string") return null;
  const m = iso.match(/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/);
  if (!m) return null;
  const h = parseInt(m[1] || "0", 10);
  const min = parseInt(m[2] || "0", 10);
  const s = parseInt(m[3] || "0", 10);
  return h * 3600 + min * 60 + s;
}

/**
 * @param {ReturnType<typeof normalizePlaylistItems>} videos
 * @param {string} apiKey
 */
async function enrichVideosClient(videos, apiKey) {
  const ids = videos.map((v) => v.id).filter(Boolean);
  if (!ids.length) return videos;
  const u = new URL("https://www.googleapis.com/youtube/v3/videos");
  u.searchParams.set("part", "snippet,contentDetails,statistics");
  u.searchParams.set("id", ids.join(","));
  u.searchParams.set("key", apiKey);
  const r = await fetch(u.toString(), { headers: { Accept: "application/json" } });
  const data = await r.json().catch(() => ({}));
  if (!r.ok) {
    return videos.map((v) => ({
      ...v,
      durationSeconds: null,
      viewCount: null,
      liveBroadcastContent: "none",
    }));
  }
  const byId = new Map();
  for (const item of data.items || []) {
    if (item && item.id) byId.set(item.id, item);
  }
  return videos.map((v) => {
    const item = byId.get(v.id);
    if (!item) {
      return { ...v, durationSeconds: null, viewCount: null, liveBroadcastContent: "none" };
    }
    const cd = item.contentDetails || {};
    const st = item.statistics || {};
    const sn = item.snippet || {};
    const vc = st.viewCount;
    return {
      ...v,
      durationSeconds: parseIso8601DurationSeconds(cd.duration),
      viewCount: vc != null && vc !== "" ? parseInt(String(vc), 10) : null,
      liveBroadcastContent: sn.liveBroadcastContent || "none",
    };
  });
}

/**
 * Direct YouTube Data API v3 (for static hosting where /api/videos is unavailable).
 * @param {string | null} pageToken
 */
async function fetchPageFromYouTubeApi(pageToken) {
  const ctx = await getClientChannelContext();
  const maxResults = getClientMaxResults();
  const u = new URL("https://www.googleapis.com/youtube/v3/playlistItems");
  u.searchParams.set("part", "snippet,contentDetails");
  u.searchParams.set("playlistId", ctx.uploadsPlaylistId);
  u.searchParams.set("maxResults", String(maxResults));
  u.searchParams.set("key", ctx.apiKey);
  if (pageToken) u.searchParams.set("pageToken", pageToken);

  const r = await fetch(u.toString(), { headers: { Accept: "application/json" } });
  const text = await r.text();
  let data = {};
  try {
    data = text ? JSON.parse(text) : {};
  } catch {
    throw new Error((text && text.slice(0, 160)) || r.statusText);
  }
  if (!r.ok) {
    const err = data.error?.message || data.error || r.statusText;
    throw new Error(typeof err === "string" ? err : JSON.stringify(err));
  }
  let videos = normalizePlaylistItems(data.items);
  videos = await enrichVideosClient(videos, ctx.apiKey);
  return {
    channelTitle: ctx.channelTitle,
    channelId: ctx.channelId,
    channelThumbUrl: ctx.channelThumbUrl,
    subscriberCount: ctx.subscriberCount,
    videos,
    nextPageToken: data.nextPageToken || null,
  };
}

function setStatus(msg, temporaryMs) {
  if (statusClearTimer) {
    clearTimeout(statusClearTimer);
    statusClearTimer = 0;
  }
  if (statusEl) statusEl.textContent = msg ?? "";
  if (temporaryMs && statusEl && msg) {
    statusClearTimer = window.setTimeout(() => {
      if (statusEl?.textContent === msg) statusEl.textContent = "";
      statusClearTimer = 0;
    }, temporaryMs);
  }
}

function stripHtml(html) {
  const d = document.createElement("div");
  d.innerHTML = html;
  return (d.textContent || d.innerText || "").replace(/\s+/g, " ").trim();
}

function shorten(text, max = 140) {
  const t = text.trim();
  if (t.length <= max) return t;
  return t.slice(0, max - 1).trim() + "…";
}

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  } catch {
    return iso;
  }
}

/** @param {number | null | undefined} sec */
function formatDuration(sec) {
  if (sec == null || Number.isNaN(sec)) return "";
  const h = Math.floor(sec / 3600);
  const m = Math.floor((sec % 3600) / 60);
  const s = Math.floor(sec % 60);
  if (h > 0) {
    return `${h}:${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;
  }
  return `${m}:${String(s).padStart(2, "0")}`;
}

/** @param {number | null | undefined} n */
function formatViewCount(n) {
  if (n == null || Number.isNaN(n)) return "";
  if (n >= 1_000_000) {
    const x = n / 1_000_000;
    return `${x >= 10 ? Math.round(x) : x.toFixed(1).replace(/\.0$/, "")}M views`;
  }
  if (n >= 1000) return `${Math.round(n / 1000)}K views`;
  return `${n} views`;
}

/** @param {number | null | undefined} n */
function formatSubscribers(n) {
  if (n == null || Number.isNaN(n)) return "";
  if (n >= 1_000_000) {
    const x = n / 1_000_000;
    return `${x >= 10 ? Math.round(x) : x.toFixed(1).replace(/\.0$/, "")}M subscribers`;
  }
  if (n >= 1000) return `${Math.round(n / 1000)}K subscribers`;
  return `${n} subscribers`;
}

function escapeHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function escapeAttr(s) {
  return String(s).replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}

/** @param {typeof allVideos} videos @param {typeof sortMode} key */
function sortVideos(videos, key) {
  const out = [...videos];
  switch (key) {
    case "oldest":
      return out.sort((a, b) => new Date(a.publishedAt).getTime() - new Date(b.publishedAt).getTime());
    case "title":
      return out.sort((a, b) => a.title.localeCompare(b.title, undefined, { sensitivity: "base" }));
    case "views":
      return out.sort((a, b) => {
        const va = a.viewCount ?? -1;
        const vb = b.viewCount ?? -1;
        return vb - va;
      });
    case "newest":
    default:
      return out.sort((a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime());
  }
}

function getFilteredVideos() {
  const q = (searchInput?.value || "").trim().toLowerCase();
  if (!q) return [...allVideos];
  return allVideos.filter(
    (v) =>
      v.title.toLowerCase().includes(q) || (v.description || "").toLowerCase().includes(q),
  );
}

function getDisplayVideos() {
  return sortVideos(getFilteredVideos(), sortMode);
}

function applyLayoutClass() {
  if (!grid) return;
  grid.classList.remove("youtube-grid--list", "youtube-grid--carousel");
  grid.dataset.layout = layoutMode;
  if (layoutMode === "list") grid.classList.add("youtube-grid--list");
  if (layoutMode === "carousel") grid.classList.add("youtube-grid--carousel");
}

function setLayout(mode) {
  layoutMode = mode;
  localStorage.setItem("haft-yt-layout", mode);
  applyLayoutClass();
  const map = [
    [layoutGridBtn, "grid"],
    [layoutListBtn, "list"],
    [layoutCarouselBtn, "carousel"],
  ];
  for (const [btn, m] of map) {
    if (!btn) continue;
    const on = m === mode;
    btn.classList.toggle("is-active", on);
    btn.setAttribute("aria-pressed", on ? "true" : "false");
  }
}

function updateChannelHeader() {
  if (!channelHeaderEl) return;
  if (!channelMeta.channelId) {
    channelHeaderEl.hidden = true;
    channelHeaderEl.innerHTML = "";
    return;
  }
  channelHeaderEl.hidden = false;
  const subs = formatSubscribers(channelMeta.subscriberCount);
  const subUrl = `https://www.youtube.com/channel/${encodeURIComponent(channelMeta.channelId)}?sub_confirmation=1`;
  const thumb = channelMeta.channelThumbUrl;
  const avatarBlock = thumb
    ? `<img class="youtube-channel-avatar" src="${escapeAttr(thumb)}" width="80" height="80" alt="" loading="lazy" decoding="async" />`
    : `<div class="youtube-channel-avatar youtube-channel-avatar--placeholder" aria-hidden="true"></div>`;
  channelHeaderEl.innerHTML = `
    <div class="youtube-channel-header-inner">
      ${avatarBlock}
      <div class="youtube-channel-header-text">
        <h3 class="youtube-channel-name">${escapeHtml(channelMeta.channelTitle || "Channel")}</h3>
        ${subs ? `<p class="youtube-channel-subs">${escapeHtml(subs)}</p>` : ""}
      </div>
      <a href="${escapeAttr(subUrl)}" class="primary-button pd-35 w-button youtube-subscribe-btn" target="_blank" rel="noopener noreferrer">Subscribe on YouTube</a>
    </div>
  `;
}

function buildModal() {
  const overlay = document.createElement("div");
  overlay.className = "youtube-modal";
  overlay.setAttribute("role", "dialog");
  overlay.setAttribute("aria-modal", "true");
  overlay.setAttribute("aria-label", "Video player");
  overlay.innerHTML = `
    <div class="youtube-modal-dialog">
      <button type="button" class="youtube-modal-close" aria-label="Close video">&times;</button>
      <div class="youtube-modal-frame-wrap">
        <iframe class="youtube-modal-frame" title="YouTube video player" allowfullscreen
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
      </div>
      <div class="youtube-modal-footer">
        <p class="youtube-modal-title"></p>
        <div class="youtube-modal-actions">
          <a class="secondary-button pd-35 w-button youtube-modal-watch" href="#" target="_blank" rel="noopener noreferrer">Watch on YouTube</a>
          <button type="button" class="secondary-button pd-35 w-button youtube-modal-copy">Copy link</button>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(overlay);

  const close = () => {
    overlay.classList.remove("is-open");
    const frame = overlay.querySelector(".youtube-modal-frame");
    frame.src = "";
    document.body.style.overflow = "";
  };

  overlay.querySelector(".youtube-modal-close").addEventListener("click", close);
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) close();
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && overlay.classList.contains("is-open")) {
      close();
      return;
    }
    if (overlay.classList.contains("is-open") || layoutMode !== "carousel" || !grid) return;
    if (!document.activeElement?.closest?.("#yt-grid")) return;
    const step = Math.min(320, grid.clientWidth * 0.85);
    if (e.key === "ArrowRight") {
      e.preventDefault();
      grid.scrollBy({ left: step, behavior: "smooth" });
    } else if (e.key === "ArrowLeft") {
      e.preventDefault();
      grid.scrollBy({ left: -step, behavior: "smooth" });
    }
  });

  const copyBtn = overlay.querySelector(".youtube-modal-copy");
  copyBtn.addEventListener("click", async () => {
    const id = copyBtn.getAttribute("data-video-id");
    if (!id) return;
    const url = `https://www.youtube.com/watch?v=${id}`;
    try {
      await navigator.clipboard.writeText(url);
      setStatus("Link copied to clipboard", 2500);
    } catch {
      window.prompt("Copy this link:", url);
    }
  });

  return {
    /** @param {string} videoId @param {string} title */
    open(videoId, title) {
      const frame = overlay.querySelector(".youtube-modal-frame");
      const titleEl = overlay.querySelector(".youtube-modal-title");
      const watch = overlay.querySelector(".youtube-modal-watch");
      titleEl.textContent = title;
      frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}?autoplay=1&rel=0`;
      watch.href = `https://www.youtube.com/watch?v=${encodeURIComponent(videoId)}`;
      copyBtn.setAttribute("data-video-id", videoId);
      overlay.classList.add("is-open");
      document.body.style.overflow = "hidden";
      overlay.querySelector(".youtube-modal-close").focus();
    },
    close,
  };
}

const modal = buildModal();

/** @param {typeof allVideos[0]} v */
function renderCard(v) {
  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "youtube-card";
  btn.setAttribute("data-video-id", v.id);
  btn.setAttribute("aria-label", `Play video: ${v.title}`);

  const plainDesc = shorten(stripHtml(v.description || ""));
  const live =
    v.liveBroadcastContent === "live" ||
    v.liveBroadcastContent === "upcoming";
  const liveLabel = v.liveBroadcastContent === "upcoming" ? "Upcoming" : "LIVE";
  const durationHtml = live
    ? `<span class="youtube-card-duration youtube-card-duration--live">${escapeHtml(liveLabel)}</span>`
    : v.durationSeconds != null
      ? `<span class="youtube-card-duration">${escapeHtml(formatDuration(v.durationSeconds))}</span>`
      : "";

  const viewsStr = formatViewCount(v.viewCount);
  const metaBits = [formatDate(v.publishedAt), viewsStr].filter(Boolean);
  const metaLine = metaBits.join(" · ");

  btn.innerHTML = `
    <span class="youtube-card-thumb-wrap">
      <img class="youtube-card-thumb" src="${escapeAttr(v.thumbnail)}" alt="" loading="lazy" decoding="async" width="480" height="270" />
      <span class="youtube-card-play" aria-hidden="true"><span class="youtube-card-play-icon">▶</span></span>
      ${durationHtml}
    </span>
    <span class="youtube-card-body">
      <span class="youtube-card-title">${escapeHtml(v.title)}</span>
      <span class="youtube-card-meta">${escapeHtml(metaLine)}</span>
      <span class="youtube-card-desc">${escapeHtml(plainDesc)}</span>
    </span>
  `;

  btn.addEventListener("click", () => modal.open(v.id, v.title));
  return btn;
}

function renderGrid() {
  if (!grid) return;
  const videos = getDisplayVideos();
  grid.innerHTML = "";
  grid.setAttribute("tabindex", layoutMode === "carousel" ? "0" : "-1");
  const frag = document.createDocumentFragment();
  for (const v of videos) frag.appendChild(renderCard(v));
  grid.appendChild(frag);
}

function refreshListStatus() {
  const q = (searchInput?.value || "").trim();
  const filtered = getFilteredVideos();
  const displayed = getDisplayVideos();
  if (q && !filtered.length) {
    setStatus("No matches");
    return;
  }
  if (q && filtered.length) {
    setStatus(`${filtered.length} match${filtered.length === 1 ? "" : "es"}`);
    return;
  }
  if (allVideos.length) {
    const ch = channelMeta.channelTitle ? ` from ${channelMeta.channelTitle}` : "";
    setStatus(`${allVideos.length} video${allVideos.length === 1 ? "" : "s"}${ch}`);
  } else {
    setStatus("");
  }
}

async function buildVideosFetchUrl(pageToken) {
  const base = await apiVideosUrl();
  const u = base.startsWith("http") ? new URL(base) : new URL(base, window.location.origin);
  if (pageToken) u.searchParams.set("pageToken", pageToken);
  return u.toString();
}

async function fetchPage(pageToken) {
  try {
    const res = await fetch(await buildVideosFetchUrl(pageToken), {
      headers: { Accept: "application/json" },
    });
    const text = await res.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch {
      throw new Error((text && text.slice(0, 160)) || res.statusText);
    }
    if (!res.ok) {
      const err = data.error || data.message || res.statusText;
      throw new Error(typeof err === "string" ? err : JSON.stringify(err));
    }
    return data;
  } catch (e) {
    if (canUseClientYoutube()) {
      return fetchPageFromYouTubeApi(pageToken);
    }
    throw e;
  }
}

async function loadInitial() {
  if (loading || !grid) return;
  loading = true;
  setStatus("Loading videos…");
  loadMoreBtn.hidden = true;
  try {
    const data = await fetchPage(null);
    allVideos = data.videos || [];
    nextPageToken = data.nextPageToken || null;
    channelMeta = {
      channelId: data.channelId || null,
      channelTitle: data.channelTitle || null,
      channelThumbUrl: data.channelThumbUrl || null,
      subscriberCount:
        typeof data.subscriberCount === "number" && !Number.isNaN(data.subscriberCount)
          ? data.subscriberCount
          : data.subscriberCount != null
            ? parseInt(String(data.subscriberCount), 10) || null
            : null,
    };
    updateChannelHeader();
    applyLayoutClass();
    renderGrid();
    refreshListStatus();
    loadMoreBtn.hidden = !nextPageToken;
  } catch (e) {
    console.error(e);
    setStatus("");
    renderGrid();
    loadMoreBtn.hidden = true;
    if (hintEl) {
      if (canUseClientYoutube()) {
        hintEl.innerHTML =
          "YouTube returned an error. Check that <code>data-youtube-api-key</code> and your channel id/handle are correct, YouTube Data API v3 is enabled for the key, and the key’s HTTP referrer restriction includes this site’s URL.";
      } else {
        hintEl.innerHTML =
          "<strong>Static hosting (e.g. haft-ug.com):</strong> there is no <code>/api/videos</code> on the server. Add <code>data-youtube-api-key</code> plus <code>data-youtube-channel-id</code> or <code>data-youtube-channel-handle</code> on <code>&lt;body&gt;</code> (same values as in your <code>.env</code> for local). In <a href=\"https://console.cloud.google.com/apis/credentials\" target=\"_blank\" rel=\"noopener noreferrer\">Google Cloud</a>, restrict the key to HTTP referrers such as <code>https://haft-ug.com/*</code>. <strong>Or</strong> host the Node app and set <code>data-youtube-api-root</code> to your API base URL. For local dev, run <code>npm run build</code> and <code>npm run serve</code>, or set <code>data-youtube-api-root=\"http://127.0.0.1:PORT\"</code>.";
      }
    }
  } finally {
    loading = false;
  }
}

async function loadMore() {
  if (loading || !nextPageToken) return;
  loading = true;
  setStatus("Loading more…");
  try {
    const data = await fetchPage(nextPageToken);
    const more = data.videos || [];
    allVideos = allVideos.concat(more);
    nextPageToken = data.nextPageToken || null;
    renderGrid();
    refreshListStatus();
    loadMoreBtn.hidden = !nextPageToken;
  } catch (e) {
    console.error(e);
    setStatus("Could not load more.");
  } finally {
    loading = false;
  }
}

function initTheme() {
  const saved = localStorage.getItem("haft-yt-theme");
  if (saved === "dark") {
    document.documentElement.classList.add("yt-dark");
    if (themeToggle) {
      themeToggle.setAttribute("aria-pressed", "true");
      themeToggle.textContent = "Light mode";
    }
  }
  themeToggle?.addEventListener("click", () => {
    const dark = document.documentElement.classList.toggle("yt-dark");
    localStorage.setItem("haft-yt-theme", dark ? "dark" : "light");
    themeToggle.setAttribute("aria-pressed", dark ? "true" : "false");
    themeToggle.textContent = dark ? "Light mode" : "Dark mode";
  });
}

function initLayoutAndSort() {
  const savedLayout = localStorage.getItem("haft-yt-layout");
  if (savedLayout === "list" || savedLayout === "carousel" || savedLayout === "grid") {
    layoutMode = savedLayout;
  }
  const savedSort = localStorage.getItem("haft-yt-sort");
  if (savedSort === "oldest" || savedSort === "title" || savedSort === "views" || savedSort === "newest") {
    sortMode = savedSort;
    if (sortSelect) sortSelect.value = savedSort;
  }
  setLayout(layoutMode);

  layoutGridBtn?.addEventListener("click", () => setLayout("grid"));
  layoutListBtn?.addEventListener("click", () => setLayout("list"));
  layoutCarouselBtn?.addEventListener("click", () => setLayout("carousel"));

  sortSelect?.addEventListener("change", () => {
    const v = sortSelect.value;
    if (v === "oldest" || v === "title" || v === "views" || v === "newest") {
      sortMode = v;
      localStorage.setItem("haft-yt-sort", v);
      renderGrid();
      refreshListStatus();
    }
  });
}

loadMoreBtn?.addEventListener("click", loadMore);
searchInput?.addEventListener("input", () => {
  renderGrid();
  refreshListStatus();
});

initTheme();
initLayoutAndSort();
loadInitial();

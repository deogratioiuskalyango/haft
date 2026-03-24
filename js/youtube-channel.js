/**
 * HAFT YouTube channel page — loads videos via GET /api/videos (Express proxy in server.js).
 *
 * - Tries same-origin /api/videos first (npm run serve on any port).
 * - If that fails on localhost, probes 127.0.0.1:3000–3025 for our JSON API
 *   (detects `videos` or `error` in body — includes YouTube quota/API errors).
 * - Override anytime: data-youtube-api-root="https://your-api-host" on <body>.
 */

const grid = document.getElementById("yt-grid");
const statusEl = document.getElementById("yt-status");
const loadMoreBtn = document.getElementById("yt-load-more");
const searchInput = document.getElementById("yt-search");
const hintEl = document.getElementById("yt-config-hint");
const themeToggle = document.getElementById("yt-theme-toggle");

/** @type {{ id: string, title: string, description: string, publishedAt: string, thumbnail: string }[]} */
let allVideos = [];
let nextPageToken = null;
let loading = false;

/** Cached result of the first full resolution (same session) */
let resolveApiRootPromise = null;

function isLocalHostname() {
  const h = window.location.hostname;
  return h === "127.0.0.1" || h === "localhost" || h === "[::1]";
}

/** True if this looks like GET /api/videos from our Express proxy (not a random 404 page). */
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

/**
 * Resolves API base: data-youtube-api-root, same-origin, or probe 127.0.0.1:3000–3025.
 */
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

function setStatus(msg) {
  if (statusEl) statusEl.textContent = msg ?? "";
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
      <p class="youtube-modal-title"></p>
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
    if (e.key === "Escape" && overlay.classList.contains("is-open")) close();
  });

  return {
    open(videoId, title) {
      const frame = overlay.querySelector(".youtube-modal-frame");
      const titleEl = overlay.querySelector(".youtube-modal-title");
      titleEl.textContent = title;
      frame.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(videoId)}?autoplay=1&rel=0`;
      overlay.classList.add("is-open");
      document.body.style.overflow = "hidden";
      overlay.querySelector(".youtube-modal-close").focus();
    },
    close,
  };
}

const modal = buildModal();

function renderCard(v) {
  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "youtube-card";
  btn.setAttribute("data-video-id", v.id);
  btn.setAttribute("aria-label", `Play video: ${v.title}`);

  const plainDesc = shorten(stripHtml(v.description || ""));

  btn.innerHTML = `
    <span class="youtube-card-thumb-wrap">
      <img class="youtube-card-thumb" src="${v.thumbnail}" alt="" loading="lazy" decoding="async" width="480" height="270" />
      <span class="youtube-card-play" aria-hidden="true"><span class="youtube-card-play-icon">▶</span></span>
    </span>
    <span class="youtube-card-body">
      <span class="youtube-card-title">${escapeHtml(v.title)}</span>
      <span class="youtube-card-meta">${formatDate(v.publishedAt)}</span>
      <span class="youtube-card-desc">${escapeHtml(plainDesc)}</span>
    </span>
  `;

  btn.addEventListener("click", () => modal.open(v.id, v.title));
  return btn;
}

function escapeHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function renderGrid(videos) {
  if (!grid) return;
  grid.innerHTML = "";
  const frag = document.createDocumentFragment();
  for (const v of videos) frag.appendChild(renderCard(v));
  grid.appendChild(frag);
}

function applySearchFilter() {
  const q = (searchInput?.value || "").trim().toLowerCase();
  if (!q) {
    renderGrid(allVideos);
    return;
  }
  const filtered = allVideos.filter(
    (v) =>
      v.title.toLowerCase().includes(q) ||
      (v.description || "").toLowerCase().includes(q)
  );
  renderGrid(filtered);
  setStatus(filtered.length ? `${filtered.length} match(es)` : "No matches");
}

async function buildVideosFetchUrl(pageToken) {
  const base = await apiVideosUrl();
  const u = base.startsWith("http")
    ? new URL(base)
    : new URL(base, window.location.origin);
  if (pageToken) u.searchParams.set("pageToken", pageToken);
  return u.toString();
}

async function fetchPage(pageToken) {
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
    renderGrid(allVideos);
    setStatus(
      data.channelTitle
        ? `${allVideos.length} video(s) from ${data.channelTitle}`
        : `${allVideos.length} video(s)`
    );
    loadMoreBtn.hidden = !nextPageToken;
  } catch (e) {
    console.error(e);
    setStatus("");
    renderGrid([]);
    loadMoreBtn.hidden = true;
    if (hintEl) {
      hintEl.innerHTML =
        "Could not reach <code>/api/videos</code>. In this folder run <code>npm run build</code> then keep <code>npm run serve</code> running (check the terminal for the port, e.g. 3000 or 3001). Reload this page. If it still fails, set <code>data-youtube-api-root=\"http://127.0.0.1:PORT\"</code> on <code>&lt;body&gt;</code> to match that port.";
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
    applySearchFilter();
    setStatus(`${allVideos.length} video(s) loaded`);
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

loadMoreBtn?.addEventListener("click", loadMore);
searchInput?.addEventListener("input", () => {
  if ((searchInput.value || "").trim()) applySearchFilter();
  else {
    renderGrid(allVideos);
    setStatus(allVideos.length ? `${allVideos.length} video(s)` : "");
  }
});

initTheme();
loadInitial();

/**
 * Replaces <section class="header-section"> and <section class="footer-section">
 * in all site HTML from partials/header.html and partials/footer.html.
 * Expects index.html at site root and one section per folder as {slug}/index.html.
 * Run from project root: node scripts/sync-layout.mjs
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");
const PARTIALS = path.join(ROOT, "partials");

const HEADER_RE = /<section class="header-section">[\s\S]*?<\/section>/;
const FOOTER_RE = /<section class="footer-section">[\s\S]*?<\/section>/;

const ASSET_ROOT = "images/";
const ASSET_NESTED = "../images/";

/** Skip when scanning for layout HTML (not content pages) */
const SKIP_LAYOUT_SCAN = new Set([
  "css",
  "dist",
  "home",
  "images",
  "js",
  "lib",
  "node_modules",
  "pages",
  "partials",
  "public",
  "scripts",
  "wpforms-pro-v1.8.7.2",
]);

/** Folder name (parent of index.html) -> nav highlight key */
const FOLDER_TO_ACTIVE = {
  about: "about",
  events: "events",
  causes: "causes",
  contact: "contact",
  volunteers: "volunteers",
  blog: "blog",
  "blog-detail": "blog",
  donation: "donation",
  "empower-women-sanitary-pad-project": "donation",
  fundraise: "fundraise",
  youtube: "youtube",
  "event-detail": "events",
  "cause-clean-water": "causes",
  "donation-policy": "donation-policy",
  "medical-disclaimer": "medical-disclaimer",
  accountability: "accountability",
  "terms-and-conditions": "terms-and-conditions",
  privacypolicy: "privacy-policy",
};

function fillTemplate(template, vars) {
  let out = template;
  for (const [k, v] of Object.entries(vars)) {
    out = out.split(`{{${k}}}`).join(v);
  }
  return out;
}

function reEsc(s) {
  return s.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function replaceFirst(html, pattern, replacement, errMsg) {
  if (!html.match(pattern)) {
    throw new Error(errMsg);
  }
  return html.replace(pattern, replacement);
}

function applyActive(html, active, { homePath, pagePrefix }) {
  if (active === "none") {
    return html;
  }

  const contactHref = `${pagePrefix}contact/`;

  const markOpen = (h, from, to) => {
    if (!h.includes(from)) {
      throw new Error(`sync-layout: could not find anchor fragment:\n${from}`);
    }
    return h.replace(from, to);
  };

  const navMain = (h, segment, label) => {
    const href = `${pagePrefix}${segment}`;
    const re = new RegExp(
      `<a\\s+href="${reEsc(href)}"\\s+class="nav-menu-link w-nav-link"\\s*>\\s*${reEsc(label)}\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${href}" aria-current="page" class="nav-menu-link w-nav-link w--current">${label}</a>`,
      `sync-layout: nav link not found: ${label} (${href})`,
    );
  };

  const navMainHome = (h) => {
    const re = new RegExp(
      `<a\\s+href="${reEsc(homePath)}"\\s+class="nav-menu-link w-nav-link"\\s*>\\s*Home\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${homePath}" aria-current="page" class="nav-menu-link w-nav-link w--current">Home</a>`,
      `sync-layout: nav Home link not found`,
    );
  };

  const navDrop = (h, segment, label) => {
    const href = `${pagePrefix}${segment}`;
    const re = new RegExp(
      `<a\\s+href="${reEsc(href)}"\\s+class="nav-dropdown-link w-dropdown-link"\\s*>\\s*${reEsc(label)}\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${href}" aria-current="page" class="nav-dropdown-link w-dropdown-link w--current">${label}</a>`,
      `sync-layout: dropdown link not found: ${label} (${href})`,
    );
  };

  const footLink = (h, segment, label) => {
    const href = `${pagePrefix}${segment}`;
    const re = new RegExp(
      `<a\\s+href="${reEsc(href)}"\\s+class="footer-menu-link"\\s*>\\s*${reEsc(label)}\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${href}" aria-current="page" class="footer-menu-link w--current">${label}</a>`,
      `sync-layout: footer link not found: ${label} (${href})`,
    );
  };

  const footHome = (h, label) => {
    const re = new RegExp(
      `<a\\s+href="${reEsc(homePath)}"\\s+class="footer-menu-link"\\s*>\\s*${reEsc(label)}\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${homePath}" aria-current="page" class="footer-menu-link w--current">${label}</a>`,
      `sync-layout: footer home link not found`,
    );
  };

  const copyrightLink = (h, segment, labelText) => {
    const href = `${pagePrefix}${segment}`;
    const re = new RegExp(
      `<a\\s+href="${reEsc(href)}"\\s+class="copyright-link"\\s*>\\s*${reEsc(labelText)}\\s*</a\\s*>`,
    );
    return replaceFirst(
      h,
      re,
      `<a href="${href}" aria-current="page" class="copyright-link w--current">${labelText}</a>`,
      `sync-layout: copyright link not found: ${labelText} (${href})`,
    );
  };

  if (active === "home") {
    html = markOpen(
      html,
      `<a href="${homePath}" class="brand-link w-nav-brand"`,
      `<a href="${homePath}" aria-current="page" class="brand-link w-nav-brand w--current"`,
    );
    html = navMainHome(html);
    html = markOpen(
      html,
      `<a href="${homePath}" class="footer-logo-link w-inline-block"`,
      `<a href="${homePath}" aria-current="page" class="footer-logo-link w-inline-block w--current"`,
    );
    html = footHome(html, "Home");
    return html;
  }

  if (active === "about") {
    html = navMain(html, "about/", "About");
    html = footLink(html, "about/", "About");
  } else if (active === "events") {
    html = navMain(html, "events/", "Events");
    html = footLink(html, "events/", "Events");
  } else if (active === "causes") {
    html = navMain(html, "causes/", "Causes");
    html = footLink(html, "causes/", "Causes");
  } else if (active === "contact") {
    const re = new RegExp(
      `<a\\s+href="${reEsc(contactHref)}"\\s+class="nav-menu-link w-nav-link"\\s*>\\s*Contact\\s*</a\\s*>`,
    );
    html = replaceFirst(
      html,
      re,
      `<a href="${contactHref}" aria-current="page" class="nav-menu-link w-nav-link w--current">Contact</a>`,
      `sync-layout: nav Contact link not found`,
    );
    html = footLink(html, "contact/", "Contact");
  } else if (active === "blog") {
    html = navDrop(html, "blog/", "Blog");
    html = footLink(html, "blog/", "Blog");
  } else if (active === "donation") {
    html = navDrop(html, "donation/", "Donation");
    html = footLink(html, "donation/", "Donation");
  } else if (active === "fundraise") {
    html = navDrop(html, "fundraise/", "Fundraise");
    html = footLink(html, "fundraise/", "Fundraise");
  } else if (active === "youtube") {
    html = navDrop(html, "youtube/", "YouTube");
  } else if (active === "volunteers") {
    html = navDrop(html, "volunteers/", "Volunteers");
    html = footLink(html, "volunteers/", "Volunteers");
  } else if (active === "donation-policy") {
    html = footLink(html, "donation-policy/", "Donation Policy");
  } else if (active === "medical-disclaimer") {
    html = footLink(html, "medical-disclaimer/", "Medical Disclaimer");
  } else if (active === "accountability") {
    html = footLink(html, "accountability/", "Accountability");
  } else if (active === "terms-and-conditions") {
    html = copyrightLink(
      html,
      "terms-and-conditions/",
      "Terms &amp; Condition",
    );
  } else if (active === "privacy-policy") {
    html = copyrightLink(html, "privacypolicy/", "Privacy Policy");
  }

  return html;
}

function layoutVarsForFile(filePath) {
  const rel = path.relative(ROOT, filePath).replace(/\\/g, "/");
  const isRootIndex = rel === "index.html";
  const homePath = isRootIndex ? "./" : "../";
  const pagePrefix = isRootIndex ? "" : "../";
  const assetPrefix = isRootIndex ? ASSET_ROOT : ASSET_NESTED;
  return { homePath, pagePrefix, assetPrefix, isRootIndex };
}

function buildLayout(active, { homePath, pagePrefix, assetPrefix }) {
  const vars = { HOME_PATH: homePath, PAGE_PREFIX: pagePrefix, ASSET_PREFIX: assetPrefix };

  let header = fillTemplate(
    fs.readFileSync(path.join(PARTIALS, "header.html"), "utf8"),
    vars,
  );
  let footer = fillTemplate(
    fs.readFileSync(path.join(PARTIALS, "footer.html"), "utf8"),
    vars,
  );

  const block = `${header.trimEnd()}\n${footer.trimStart()}`;
  const updated = applyActive(block, active, { homePath, pagePrefix });
  const footIdx = updated.indexOf('<section class="footer-section">');
  if (footIdx === -1) {
    throw new Error("sync-layout: could not split header/footer block");
  }
  header = updated.slice(0, footIdx).trimEnd();
  footer = updated.slice(footIdx);

  return { header, footer };
}

function activeForFile(filePath) {
  const rel = path.relative(ROOT, filePath).replace(/\\/g, "/");
  if (rel === "index.html") return "home";
  const parts = rel.split("/");
  if (parts.length === 2 && parts[1] === "index.html") {
    return FOLDER_TO_ACTIVE[parts[0]] || "none";
  }
  return "none";
}

function collectLayoutTargets() {
  const files = [path.join(ROOT, "index.html")];
  for (const ent of fs.readdirSync(ROOT, { withFileTypes: true })) {
    if (!ent.isDirectory() || SKIP_LAYOUT_SCAN.has(ent.name)) continue;
    const idx = path.join(ROOT, ent.name, "index.html");
    if (fs.existsSync(idx)) files.push(idx);
  }
  return files;
}

function processFile(filePath) {
  const { homePath, pagePrefix, assetPrefix } = layoutVarsForFile(filePath);
  const active = activeForFile(filePath);

  const { header, footer } = buildLayout(active, { homePath, pagePrefix, assetPrefix });
  let html = fs.readFileSync(filePath, "utf8");

  if (!HEADER_RE.test(html)) {
    throw new Error(`sync-layout: no header-section in ${filePath}`);
  }
  if (!FOOTER_RE.test(html)) {
    throw new Error(`sync-layout: no footer-section in ${filePath}`);
  }

  html = html.replace(HEADER_RE, header);
  html = html.replace(FOOTER_RE, footer);
  fs.writeFileSync(filePath, html, "utf8");
  console.log(
    "Updated:",
    path.relative(ROOT, filePath),
    active === "none" ? "(no nav highlight)" : `[nav: ${active}]`,
  );
}

function main() {
  for (const p of collectLayoutTargets()) {
    processFile(p);
  }
  console.log("sync-layout: done.");
}

main();

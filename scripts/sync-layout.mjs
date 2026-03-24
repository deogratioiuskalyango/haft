/**
 * Replaces <section class="header-section"> and <section class="footer-section">
 * in all site HTML from partials/header.html and partials/footer.html.
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

/** Nav highlight: flat pages/*.html basename -> logical section */
const FILE_TO_ACTIVE = {
  "about.html": "about",
  "events.html": "events",
  "causes.html": "causes",
  "contact.html": "contact",
  "volunteers.html": "volunteers",
  "blog.html": "blog",
  "blog-detail.html": "blog",
  "donation.html": "donation",
  "empower-women-sanitary-pad-project.html": "donation",
  "fundraise.html": "fundraise",
  "event-detail.html": "events",
  "cause-clean-water.html": "causes",
  "donation-policy.html": "donation-policy",
  "medical-disclaimer.html": "medical-disclaimer",
  "accountability.html": "accountability",
  "terms-and-conditions.html": "terms-and-conditions",
  "privacypolicy.html": "privacy-policy",
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

  const contactHref = `${pagePrefix}contact.html`;

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
    html = navMain(html, "about.html", "About");
    html = footLink(html, "about.html", "About");
  } else if (active === "events") {
    html = navMain(html, "events.html", "Events");
    html = footLink(html, "events.html", "Events");
  } else if (active === "causes") {
    html = navMain(html, "causes.html", "Causes");
    html = footLink(html, "causes.html", "Causes");
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
    html = footLink(html, "contact.html", "Contact");
  } else if (active === "blog") {
    html = navDrop(html, "blog.html", "Blog");
    html = footLink(html, "blog.html", "Blog");
  } else if (active === "donation") {
    html = navDrop(html, "donation.html", "Donation");
    html = footLink(html, "donation.html", "Donation");
  } else if (active === "fundraise") {
    html = navDrop(html, "fundraise.html", "Fundraise");
    html = footLink(html, "fundraise.html", "Fundraise");
  } else if (active === "volunteers") {
    html = navDrop(html, "volunteers.html", "Volunteers");
    html = footLink(html, "volunteers.html", "Volunteers");
  } else if (active === "donation-policy") {
    html = footLink(html, "donation-policy.html", "Donation Policy");
  } else if (active === "medical-disclaimer") {
    html = footLink(html, "medical-disclaimer.html", "Medical Disclaimer");
  } else if (active === "accountability") {
    html = footLink(html, "accountability.html", "Accountability");
  } else if (active === "terms-and-conditions") {
    html = copyrightLink(
      html,
      "terms-and-conditions.html",
      "Terms &amp; Condition",
    );
  } else if (active === "privacy-policy") {
    html = copyrightLink(html, "privacypolicy.html", "Privacy Policy");
  }

  return html;
}

function buildLayout(active, isRoot) {
  const homePath = isRoot ? "home.html" : "../home.html";
  const pagePrefix = isRoot ? "pages/" : "";
  const assetPrefix = isRoot ? ASSET_ROOT : ASSET_NESTED;

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

function processFile(filePath) {
  const isRoot = path.basename(filePath) === "home.html";
  let active = "home";
  if (!isRoot) {
    const base = path.basename(filePath);
    active = FILE_TO_ACTIVE[base] || "none";
  }

  const { header, footer } = buildLayout(active, isRoot);
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
  console.log("Updated:", path.relative(ROOT, filePath), active === "none" ? "(no nav highlight)" : `[nav: ${active}]`);
}

function main() {
  const pagesDir = path.join(ROOT, "pages");
  const pageFiles = fs
    .readdirSync(pagesDir)
    .filter((f) => f.endsWith(".html"))
    .map((f) => path.join(pagesDir, f));

  processFile(path.join(ROOT, "home.html"));
  for (const p of pageFiles) {
    processFile(p);
  }
  console.log("sync-layout: done.");
}

main();

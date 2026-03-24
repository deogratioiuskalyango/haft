/**
 * Removes Webflow/template promo UI: inline badge markup, removeWebflowBadges scripts.
 * Run from project root: node scripts/strip-webflow-promo.mjs
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");

const SCRIPT_RE =
  /<script type="text\/javascript">\s*\(function \(\) \{\s*function removeWebflowBadges\(\) \{[\s\S]*?window\.addEventListener\("load", removeWebflowBadges\);\s*\}\)\(\);\s*<\/script>\s*/g;

const INLINE_BADGE_RE =
  /<a href="https:\/\/webflow\.com\/templates\/designers\/victorflow"[\s\S]*?(?=<script src="[^"]*jquery-3\.5\.1\.min)/g;

const HAFT_LINK_BLOG =
  '<link href="../css/haft-locations.css" rel="stylesheet" type="text/css">';

function walkHtml(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const name of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, name.name);
    if (name.isDirectory()) walkHtml(full, out);
    else if (name.name.endsWith(".html")) out.push(full);
  }
  return out;
}

const files = [path.join(ROOT, "home.html"), ...walkHtml(path.join(ROOT, "pages"))];

for (const p of files) {
  let html = fs.readFileSync(p, "utf8");
  const before = html;

  html = html.replace(SCRIPT_RE, "");

  if (path.basename(p) === "blog-detail.html") {
    html = html.replace(INLINE_BADGE_RE, "\n\n");
    if (!html.includes("haft-locations.css")) {
      html = html.replace(
        /<link href="\.\.\/css\/kindflow-template\.webflow\.33ae4aea2\.css" rel="stylesheet" type="text\/css">/,
        `$&${HAFT_LINK_BLOG}`,
      );
    }
  }

  if (html !== before) {
    fs.writeFileSync(p, html, "utf8");
    console.log("updated:", path.relative(ROOT, p));
  }
}

console.log("strip-webflow-promo: done.");

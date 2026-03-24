/**
 * One-time: root css/, fix url() paths, update HTML, remove duplicate css dirs.
 * Run: node scripts/consolidate-css.mjs
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");

const SRC_MAIN = path.join(
  ROOT,
  "66a8bc9010054ff044b7ed6b/css/kindflow-template.webflow.33ae4aea2.css",
);
const SRC_HAFT = path.join(ROOT, "66a8bc9010054ff044b7ed6b/css/haft-locations.css");
const DEST_DIR = path.join(ROOT, "css");

function walkHtml(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const name of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, name.name);
    if (name.isDirectory()) walkHtml(full, out);
    else if (name.name.endsWith(".html")) out.push(full);
  }
  return out;
}

fs.mkdirSync(DEST_DIR, { recursive: true });

let mainCss = fs.readFileSync(SRC_MAIN, "utf8");
mainCss = mainCss.replaceAll('url("../../images/', 'url("../images/');
mainCss = mainCss.replaceAll("url('../../images/", "url('../images/");
fs.writeFileSync(
  path.join(DEST_DIR, "kindflow-template.webflow.33ae4aea2.css"),
  mainCss,
  "utf8",
);
fs.copyFileSync(SRC_HAFT, path.join(DEST_DIR, "haft-locations.css"));

const homePath = path.join(ROOT, "home.html");
let home = fs.readFileSync(homePath, "utf8");
home = home.replaceAll(
  "href=\"66a8bc9010054ff044b7ed6b/css/",
  'href="css/',
);
fs.writeFileSync(homePath, home, "utf8");

const pageFiles = walkHtml(path.join(ROOT, "pages"));
for (const p of pageFiles) {
  let html = fs.readFileSync(p, "utf8");
  const before = html;
  html = html.replaceAll(
    'href="../../66a8bc9010054ff044b7ed6b/css/',
    'href="../../css/',
  );
  html = html.replaceAll(
    'href="css/kindflow-template.webflow.33ae4aea2.css"',
    'href="../../css/kindflow-template.webflow.33ae4aea2.css"',
  );
  html = html.replaceAll(
    'href="css/haft-locations.css"',
    'href="../../css/haft-locations.css"',
  );
  if (html !== before) fs.writeFileSync(p, html, "utf8");
}

function rmDirIfEmpty(dir) {
  try {
    if (fs.existsSync(dir) && fs.readdirSync(dir).length === 0) {
      fs.rmdirSync(dir);
    }
  } catch {
    /* ignore */
  }
}

const oldCssDir = path.join(ROOT, "66a8bc9010054ff044b7ed6b/css");
for (const name of ["kindflow-template.webflow.33ae4aea2.css", "haft-locations.css"]) {
  const f = path.join(oldCssDir, name);
  if (fs.existsSync(f)) fs.unlinkSync(f);
}
rmDirIfEmpty(oldCssDir);

for (const name of fs.readdirSync(path.join(ROOT, "pages"))) {
  const cssDir = path.join(ROOT, "pages", name, "css");
  if (!fs.statSync(path.join(ROOT, "pages", name)).isDirectory()) continue;
  if (!fs.existsSync(cssDir)) continue;
  for (const f of fs.readdirSync(cssDir)) {
    fs.unlinkSync(path.join(cssDir, f));
  }
  fs.rmdirSync(cssDir);
}

console.log("consolidate-css: done. Root css/ is the single source.");

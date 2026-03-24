import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");

function walkHtml(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const name of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, name.name);
    if (name.isDirectory()) walkHtml(full, out);
    else if (name.name.endsWith(".html")) out.push(full);
  }
  return out;
}

const rootHtml = [path.join(ROOT, "home.html")].filter((p) => fs.existsSync(p));
const files = [...rootHtml, ...walkHtml(path.join(ROOT, "pages"))];

for (const p of files) {
  let html = fs.readFileSync(p, "utf8");
  const i = html.indexOf('class="more-template-badge');
  if (i === -1) {
    console.log("skip (no badge):", path.relative(ROOT, p));
    continue;
  }
  const aStart = html.lastIndexOf("<a ", i);
  const j = html.indexOf("<script ", i);
  if (aStart === -1 || j === -1) {
    throw new Error(`Could not parse badges in ${p}`);
  }
  html = `${html.slice(0, aStart)}\n\n${html.slice(j)}`;
  fs.writeFileSync(p, html, "utf8");
  console.log("updated:", path.relative(ROOT, p));
}

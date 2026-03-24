import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");

const INJECT_SCRIPT = `<script type="text/javascript">
(function () {
  function removeWebflowBadges() {
    [
      "a.w-webflow-badge",
      "a.more-template-badge",
      "a.buy-template-badge",
      ".hireus-badge",
    ].forEach(function (sel) {
      document.querySelectorAll(sel).forEach(function (el) {
        el.remove();
      });
    });
  }
  removeWebflowBadges();
  if (document.body) {
    var mo = new MutationObserver(removeWebflowBadges);
    mo.observe(document.body, { childList: true, subtree: true });
  }
  document.addEventListener("DOMContentLoaded", removeWebflowBadges);
  window.addEventListener("load", removeWebflowBadges);
})();
</script>`;

const DISCONNECT_RE =
  /window\.addEventListener\("load",\s*function\s*\(\)\s*\{\s*removeWebflowBadge\(\);\s*mo\.disconnect\(\);\s*\}\);/g;

const NEW_LOAD =
  'document.addEventListener("DOMContentLoaded", removeWebflowBadge);\n        window.addEventListener("load", removeWebflowBadge);';

const STYLE_RE = /\s*<style>\s*\.w-webflow-badge\s*\{[^}]*\}\s*<\/style>\s*/g;

function walkHtml(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const name of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, name.name);
    if (name.isDirectory()) walkHtml(full, out);
    else if (name.name.endsWith(".html")) out.push(full);
  }
  return out;
}

const files = [
  path.join(ROOT, "home.html"),
  ...walkHtml(path.join(ROOT, "pages")),
];

for (const p of files) {
  let html = fs.readFileSync(p, "utf8");
  let changed = false;

  if (html.includes("removeWebflowBadges") || html.includes("removeWebflowBadge")) {
    if (html.includes("mo.disconnect()")) {
      const next = html.replace(DISCONNECT_RE, NEW_LOAD);
      if (next === html) {
        throw new Error(`Could not patch mo.disconnect() in ${p}`);
      }
      html = next;
      changed = true;
    }
    const stripped = html.replace(STYLE_RE, "\n");
    if (stripped !== html) {
      html = stripped;
      changed = true;
    }
  } else if (
    html.includes("webflow.") &&
    html.includes("</body>") &&
    !html.includes("removeWebflowBadges") &&
    !html.includes("removeWebflowBadge")
  ) {
    html = html.replace(/<\/body>/i, `${INJECT_SCRIPT}\n</body>`);
    changed = true;
  }

  if (changed) {
    fs.writeFileSync(p, html, "utf8");
    console.log("patched:", path.relative(ROOT, p));
  }
}

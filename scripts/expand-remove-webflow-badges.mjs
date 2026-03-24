import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");

const INJECT_BEFORE_BODY = `<script type="text/javascript">
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
</script>
`;

const BLOCK_INDENTED = `        function removeWebflowBadge() {
          document.querySelectorAll("a.w-webflow-badge").forEach(function (el) {
            el.remove();
          });
        }
        removeWebflowBadge();
        var mo = new MutationObserver(removeWebflowBadge);
        if (document.body) {
          mo.observe(document.body, { childList: true, subtree: true });
        }
        document.addEventListener("DOMContentLoaded", removeWebflowBadge);
        window.addEventListener("load", removeWebflowBadge);`;

const BLOCK_INDENTED_NEW = `        function removeWebflowBadges() {
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
        var mo = new MutationObserver(removeWebflowBadges);
        if (document.body) {
          mo.observe(document.body, { childList: true, subtree: true });
        }
        document.addEventListener("DOMContentLoaded", removeWebflowBadges);
        window.addEventListener("load", removeWebflowBadges);`;

const BLOCK_COMPACT = `(function () {
  function removeWebflowBadge() {
    document.querySelectorAll("a.w-webflow-badge").forEach(function (el) {
      el.remove();
    });
  }
  removeWebflowBadge();
  if (document.body) {
    var mo = new MutationObserver(removeWebflowBadge);
    mo.observe(document.body, { childList: true, subtree: true });
  }
  document.addEventListener("DOMContentLoaded", removeWebflowBadge);
  window.addEventListener("load", removeWebflowBadge);
})();`;

const BLOCK_COMPACT_NEW = `(function () {
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
})();`;

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

  if (html.includes(BLOCK_INDENTED)) {
    html = html.replace(BLOCK_INDENTED, BLOCK_INDENTED_NEW);
    changed = true;
  } else if (html.includes(BLOCK_COMPACT)) {
    html = html.replace(BLOCK_COMPACT, BLOCK_COMPACT_NEW);
    changed = true;
  }

  const evPath = path.join(ROOT, "pages", "event-detail.html");
  if (p === evPath) {
    const startMarker =
      '<a href="https://webflow.com/templates/designers/victorflow"';
    const start = html.indexOf(startMarker);
    const endPat = '<script src="../js/jquery-3.5.1.min.dc5e7f18c8.js"';
    const end = html.indexOf(endPat, start);
    if (start !== -1 && end !== -1) {
      html = html.slice(0, start) + html.slice(end);
      changed = true;
    }
    if (
      html.includes("webflow.e31069727cf82cf68055a06abda6d8dc.js") &&
      !html.includes("removeWebflowBadges")
    ) {
      html = html.replace(
        /<script src="\.\.\/js\/webflow\.e31069727cf82cf68055a06abda6d8dc\.js"[^>]*><\/script>/,
        (m) => `${m}\n${INJECT_BEFORE_BODY}`
      );
      changed = true;
    }
  }

  if (changed) {
    fs.writeFileSync(p, html, "utf8");
    console.log("updated:", path.relative(ROOT, p));
  }
}

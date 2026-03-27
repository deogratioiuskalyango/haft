/**
 * One-time: home.html → index.html, pages/*.html → {slug}/index.html,
 * rewrites internal .html links to trailing-slash folder URLs, adds home/index.html redirect.
 * Run from project root: node scripts/migrate-clean-urls.mjs
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");
const PAGES = path.join(ROOT, "pages");

function migrateRootContent(html) {
  let c = html.replace(/href="pages\/([a-z0-9][a-z0-9-]*)\.html"/g, 'href="$1/"');
  c = c.replaceAll('href="home.html"', 'href="./"');
  c = c.replace(/href="([a-z0-9][a-z0-9-]*)\.html"/g, 'href="$1/"');
  return c;
}

function migrateNestedContent(html) {
  let c = html.replaceAll('href="../home.html"', 'href="../"');
  c = c.replace(/href="\.\.\/([a-z0-9][a-z0-9-]*)\.html"/g, 'href="../$1/"');
  c = c.replace(/href="([a-z0-9][a-z0-9-]*)\.html"/g, 'href="$1/"');
  c = c.replace(
    /location\.href='([a-z0-9][a-z0-9-]*)\.html'/g,
    "location.href='$1/'",
  );
  return c;
}

function main() {
  const homePath = path.join(ROOT, "home.html");
  if (!fs.existsSync(homePath)) {
    console.error("migrate-clean-urls: home.html not found (already migrated?)");
    process.exit(1);
  }

  const homeHtml = migrateRootContent(fs.readFileSync(homePath, "utf8"));
  fs.writeFileSync(path.join(ROOT, "index.html"), homeHtml, "utf8");
  console.log("Wrote index.html (from home.html)");

  const pageFiles = fs.readdirSync(PAGES).filter((f) => f.endsWith(".html"));
  for (const file of pageFiles) {
    const slug = file.replace(/\.html$/, "");
    const destDir = path.join(ROOT, slug);
    fs.mkdirSync(destDir, { recursive: true });
    const raw = fs.readFileSync(path.join(PAGES, file), "utf8");
    fs.writeFileSync(
      path.join(destDir, "index.html"),
      migrateNestedContent(raw),
      "utf8",
    );
    console.log("Wrote", path.join(slug, "index.html"));
  }

  const homeRedirectDir = path.join(ROOT, "home");
  fs.mkdirSync(homeRedirectDir, { recursive: true });
  fs.writeFileSync(
    path.join(homeRedirectDir, "index.html"),
    `<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="refresh" content="0; url=../" />
    <link rel="canonical" href="/" />
    <title>Redirecting…</title>
    <script>
      location.replace("../");
    </script>
  </head>
  <body>
    <p><a href="../">Continue to home</a></p>
  </body>
</html>
`,
    "utf8",
  );
  console.log("Wrote home/index.html (redirect to site root)");

  fs.unlinkSync(homePath);
  for (const file of pageFiles) {
    fs.unlinkSync(path.join(PAGES, file));
  }
  try {
    fs.rmdirSync(PAGES);
  } catch {
    /* keep pages/ if not empty */
  }

  console.log("migrate-clean-urls: done.");
}

main();

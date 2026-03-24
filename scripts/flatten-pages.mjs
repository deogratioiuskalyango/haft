/**
 * One-time migration: pages/<folder>/index.html -> pages/<name>.html
 * Run from project root: node scripts/flatten-pages.mjs
 * Requires legacy per-page folders to still exist.
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, "..");
const PAGES = path.join(ROOT, "pages");

const MIGRATIONS = [
  { dir: "Aboutwebflow-io", file: "about.html" },
  { dir: "Blog-webflow-io", file: "blog.html" },
  { dir: "Causes-webflow-io", file: "causes.html" },
  { dir: "Contact-webflow-io", file: "contact.html" },
  { dir: "Donation-webflow-io", file: "donation.html" },
  { dir: "Events-webflow-io", file: "events.html" },
  { dir: "Fundrise-webflow-io", file: "fundraise.html" },
  { dir: "Volunterreswebflow-io", file: "volunteers.html" },
  { dir: "blog details-template-webflow-io", file: "blog-detail.html" },
  { dir: "event details template-webflow-io", file: "event-detail.html" },
  { dir: "providing cleanwater-webflow-io", file: "cause-clean-water.html" },
];

const NESTED_LINK_REPLACEMENTS = [
  ["../Aboutwebflow-io/index.html", "about.html"],
  ["../Blog-webflow-io/index.html", "blog.html"],
  ["../Causes-webflow-io/index.html", "causes.html"],
  ["../Contact-webflow-io/index.html", "contact.html"],
  ["../Donation-webflow-io/index.html", "donation.html"],
  ["../Events-webflow-io/index.html", "events.html"],
  ["../Fundrise-webflow-io/index.html", "fundraise.html"],
  ["../Volunterreswebflow-io/index.html", "volunteers.html"],
  ["../blog details-template-webflow-io/index.html", "blog-detail.html"],
  ["../event details template-webflow-io/index.html", "event-detail.html"],
  ["../providing%20cleanwater-webflow-io/index.html", "cause-clean-water.html"],
  ["../providing cleanwater-webflow-io/index.html", "cause-clean-water.html"],
];

const HOME_LINK_REPLACEMENTS = [
  ["pages/Aboutwebflow-io/index.html", "pages/about.html"],
  ["pages/Blog-webflow-io/index.html", "pages/blog.html"],
  ["pages/Causes-webflow-io/index.html", "pages/causes.html"],
  ["pages/Contact-webflow-io/index.html", "pages/contact.html"],
  ["pages/Donation-webflow-io/index.html", "pages/donation.html"],
  ["pages/Events-webflow-io/index.html", "pages/events.html"],
  ["pages/Fundrise-webflow-io/index.html", "pages/fundraise.html"],
  ["pages/Volunterreswebflow-io/index.html", "pages/volunteers.html"],
  ["pages/blog details-template-webflow-io/index.html", "pages/blog-detail.html"],
  ["pages/event details template-webflow-io/index.html", "pages/event-detail.html"],
  ["pages/providing%20cleanwater-webflow-io/index.html", "pages/cause-clean-water.html"],
];

function transformNestedPage(html) {
  let h = html;
  h = h.split("../../images/").join("../images/");
  h = h.split("../../css/").join("../css/");
  h = h.split("../../js/").join("../js/");
  h = h.split("../../home.html").join("../home.html");
  for (const [from, to] of NESTED_LINK_REPLACEMENTS) {
    h = h.split(from).join(to);
  }
  h = h
    .split("location.href='../event details template-webflow-io/index.html'")
    .join("location.href='event-detail.html'");
  h = h
    .split('location.href="../event details template-webflow-io/index.html"')
    .join('location.href="event-detail.html"');
  return h;
}

function transformHome(html) {
  let h = html;
  for (const [from, to] of HOME_LINK_REPLACEMENTS) {
    h = h.split(from).join(to);
  }
  return h;
}

function main() {
  for (const { dir, file } of MIGRATIONS) {
    const src = path.join(PAGES, dir, "index.html");
    if (!fs.existsSync(src)) {
      throw new Error(`flatten-pages: missing ${src}`);
    }
    const out = path.join(PAGES, file);
    fs.writeFileSync(out, transformNestedPage(fs.readFileSync(src, "utf8")), "utf8");
    console.log("Wrote:", path.relative(ROOT, out));
  }

  const homePath = path.join(ROOT, "home.html");
  fs.writeFileSync(homePath, transformHome(fs.readFileSync(homePath, "utf8")), "utf8");
  console.log("Updated:", path.relative(ROOT, homePath));

  for (const { dir } of MIGRATIONS) {
    const d = path.join(PAGES, dir);
    fs.rmSync(d, { recursive: true, force: true });
    console.log("Removed:", path.relative(ROOT, d));
  }

  console.log("flatten-pages: done.");
}

main();

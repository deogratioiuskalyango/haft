import { copyFileSync, mkdirSync } from "node:fs";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const root = dirname(fileURLToPath(import.meta.url));
const projectRoot = join(root, "..");
const destDir = join(projectRoot, "dist", "js");
mkdirSync(destDir, { recursive: true });
copyFileSync(join(projectRoot, "js", "contact-form.js"), join(destDir, "contact-form.js"));

import { readdirSync } from "node:fs";
import { dirname, resolve } from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig, loadEnv } from "vite";

const __dirname = dirname(fileURLToPath(import.meta.url));

/** All root + pages/*.html entries for multi-page static build */
function htmlInputs() {
  const input = {
    index: resolve(__dirname, "index.html"),
    home: resolve(__dirname, "home.html"),
  };
  const pagesDir = resolve(__dirname, "pages");
  for (const file of readdirSync(pagesDir)) {
    if (file.endsWith(".html")) {
      const base = file.replace(/\.html$/, "");
      input[`pages/${base}`] = resolve(pagesDir, file);
    }
  }
  return input;
}

export default defineConfig(({ mode }) => {
  // Makes VITE_* from .env / .env.[mode] available to config if you need them here later
  loadEnv(mode, __dirname, "");

  const input = htmlInputs();

  return {
    root: __dirname,
    build: {
      rollupOptions: {
        input,
      },
    },
  };
});

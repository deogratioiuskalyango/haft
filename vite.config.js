import { existsSync, readdirSync, statSync } from "node:fs";
import { dirname, resolve } from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig, loadEnv } from "vite";

const __dirname = dirname(fileURLToPath(import.meta.url));

const SKIP_DIRS = new Set([
  "css",
  "dist",
  "images",
  "js",
  "lib",
  "node_modules",
  "partials",
  "public",
  "scripts",
  "wpforms-pro-v1.8.7.2",
]);

/** Root index + every {slug}/index.html (and home/index redirect) for multi-page build */
function htmlInputs() {
  const input = {
    index: resolve(__dirname, "index.html"),
  };
  const homeIdx = resolve(__dirname, "home", "index.html");
  if (existsSync(homeIdx)) {
    input["home/index"] = homeIdx;
  }
  for (const name of readdirSync(__dirname)) {
    if (SKIP_DIRS.has(name)) continue;
    const dir = resolve(__dirname, name);
    try {
      if (!statSync(dir).isDirectory()) continue;
    } catch {
      continue;
    }
    const idx = resolve(dir, "index.html");
    if (existsSync(idx)) {
      input[`${name}/index`] = idx;
    }
  }
  return input;
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, __dirname, "");
  const parsedPort = parseInt(String(env.PORT || "3000"), 10);
  const apiPort = Number.isFinite(parsedPort) && parsedPort > 0 ? parsedPort : 3000;
  const apiProxyTarget = `http://127.0.0.1:${apiPort}`;

  const input = htmlInputs();

  return {
    root: __dirname,
    /** Forward /api/* to Express (server.js) so contact + Pesapal + YouTube work during dev/preview */
    server: {
      proxy: {
        "/api": { target: apiProxyTarget, changeOrigin: true },
      },
    },
    preview: {
      proxy: {
        "/api": { target: apiProxyTarget, changeOrigin: true },
      },
    },
    build: {
      rollupOptions: {
        input,
      },
    },
  };
});

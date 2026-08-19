import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
//之後全專案都方便用 @/ 引用檔案
import { fileURLToPath, URL } from "node:url";

// https://vite.dev/config/
export default defineConfig({
  base: "/ckd101/g1/",
  plugins: [vue()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});

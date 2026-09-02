import { createApp } from "vue";
import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import router from "./router";
import App from "./App.vue";
import "./assets/scss/all.scss";
import "@/assets/scss/main.scss";
import "@/common/apiGuard.js";

if ("scrollRestoration" in history) {
  history.scrollRestoration = "auto";
}

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

createApp(App).use(router).use(pinia).mount("#app");

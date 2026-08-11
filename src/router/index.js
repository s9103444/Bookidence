import { createRouter, createWebHistory } from "vue-router";
import frontRoutes from "./front";
import adminRoutes from "./admin";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...frontRoutes, ...adminRoutes],
});

export default router;

import { createRouter, createWebHistory } from "vue-router";
import frontRoutes from "./front";
import adminRoutes from "./admin";
import { useGuildStore } from "../stores/guild";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...frontRoutes, ...adminRoutes],
});

router.beforeEach((to) => {
  if (to.meta.requiresLeader) {
    const guildStore = useGuildStore();
    if (guildStore.currentGuild.myRole !== "幹部") {
      return { name: "guild-detail", params: { id: to.params.id } };
    }
  }
});

export default router;

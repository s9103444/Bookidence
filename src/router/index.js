import { createRouter, createWebHistory } from "vue-router";
import entryRoutes from "./entry";
import frontRoutes from "./front";
import adminRoutes from "./admin";
import { useGuildStore } from "../stores/guild";
import {useAdminStore} from "../stores/adminAuth";
import { useUserStore } from "../stores/user";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...entryRoutes, ...frontRoutes, ...adminRoutes],
  scrollBehavior(to, from, savedPosition) {
    // 瀏覽器上一頁/下一頁：回到離開時的捲動位置
    if (savedPosition) {
      return savedPosition;
    }
    // 一般換頁（例如點卡片進公會）：捲動軸重置回頂部
    return { top: 0 };
  },
});

router.beforeEach((to) => {
  if(to.path.startsWith('/admin')&& to.name!=="admin-login"){
    const adminStore=useAdminStore();
    if(!adminStore.token){
      return{name:"admin-login"};
    }
  }
  if (to.meta.requiresAuth) {
    const userStore = useUserStore();
    if (!userStore.token) {
      return { name: "login" };
    }
  }
  if (to.meta.requiresLeader) {
    const guildStore = useGuildStore();
    if (guildStore.currentGuild.myRole !== "幹部") {
      return { name: "guild-detail", params: { id: to.params.id } };
    }
  }
});

export default router;

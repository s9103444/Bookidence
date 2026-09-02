import { createRouter, createWebHistory } from "vue-router";
import entryRoutes from "./entry";
import frontRoutes from "./front";
import adminRoutes from "./admin";
import { useGuildStore } from "../stores/guild";
import { useAdminStore } from "../stores/adminAuth";
import { useUserStore } from "../stores/user";
import { useKickedNoticeStore } from "../stores/kickedNotice";
import { API_BASE } from "../common/api";

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

router.beforeEach(async (to) => {
  if (to.path.startsWith('/admin') && to.name !== "admin-login") {
    const adminStore = useAdminStore();
    if (!adminStore.token) {
      return { name: "admin-login" };
    }
  }
  if (to.meta.requiresAuth) {
    const userStore = useUserStore();
    if (!userStore.token) {
      return { name: "login" };
    }
  }
  if (to.meta.requiresLeader) {
    // 直接連進來或重新整理時，guildStore.currentGuild.myRole 可能還沒被 GuildDetailView
    // 的 loadGuildDetail() 填過（那是元件 created() 才會跑，比這個守衛晚），
    // 所以這裡自己查一次目前登入者在這個公會的權限，不依賴 store 裡可能過期的舊值
    const guildStore = useGuildStore();
    const userStore = useUserStore();
    const guildId = to.params.id;
    const headers = {};
    if (userStore.token) {
      headers.Authorization = `Bearer ${userStore.token}`;
    }
    try {
      const res = await fetch(`${API_BASE}/guild_get_detail.php?guild_id=${guildId}`, { headers });
      const data = await res.json();
      const myRole = data.success ? data.guild.viewer_permission_level : null;
      guildStore.currentGuild.myRole = myRole;
      if (!myRole) {
        useKickedNoticeStore().show("你不是這個公會的會員，無法查看這個頁面。");
        return { name: "guilds" };
      }
      if (!["會長", "副會長"].includes(myRole)) {
        return { name: "guild-detail", params: { id: guildId } };
      }
    } catch (e) {
      return { name: "guild-detail", params: { id: guildId } };
    }
  }
  if (to.meta.requiresMembership) {
    // 跟 requiresLeader 同樣的邏輯，只是不限角色，只要是「在會中」成員就放行
    const guildStore = useGuildStore();
    const userStore = useUserStore();
    const guildId = to.params.id;
    if (!userStore.token) {
      return { name: "login" };
    }
    try {
      const res = await fetch(`${API_BASE}/guild_get_detail.php?guild_id=${guildId}`, {
        headers: { Authorization: `Bearer ${userStore.token}` },
      });
      const data = await res.json();
      const myRole = data.success ? data.guild.viewer_permission_level : null;
      guildStore.currentGuild.myRole = myRole;
      if (!myRole) {
        useKickedNoticeStore().show("你不是這個公會的會員，無法查看這個頁面。");
        return { name: "guilds" };
      }
    } catch (e) {
      return { name: "guild-detail", params: { id: guildId } };
    }
  }
});

export default router;

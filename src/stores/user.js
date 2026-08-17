import { defineStore } from "pinia";
import { API_BASE } from "@/common/api";

// Pinia 的寫法故意設計成跟 Options API 幾乎一樣的心智模型：
// state   對應 data()   → 放會變動的資料
// getters 對應 computed → 放由 state 算出來的值
// actions 對應 methods  → 放會修改 state 的邏輯
export const useUserStore = defineStore("user", {
  state: () => ({
    isLoggedIn: false,
    token: null,
    userId: null,
    userName: "",
    avatarUrl: "",
    xp: 0,
    level: 0,
  }),

  actions: {
    login(userData) {
      this.isLoggedIn = true;
      this.token = userData.token;
      this.userId = userData.userId;
      this.userName = userData.userName;
      this.avatarUrl = userData.avatarUrl ?? "";
      this.xp = userData.xp ?? 0;
      this.level = userData.level ?? 0;
    },
    logout() {
      const token = this.token;
      this.isLoggedIn = false;
      this.token = null;
      this.userId = null;
      this.userName = "";
      this.avatarUrl = "";
      this.xp = 0;
      this.level = 0;

      if (token) {
        fetch(`${API_BASE}/logout.php`, {
          method: "POST",
          headers: { Authorization: `Bearer ${token}` },
        });
      }
    },
    // 網頁重新整理後，pinia state 會被 persistedstate 還原出 token，
    // 但其他欄位不會持久化，所以要用 token 跟後端換回完整的使用者資料
    async restoreSession() {
      if (!this.token) return;

      const res = await fetch(`${API_BASE}/me.php`, {
        headers: { Authorization: `Bearer ${this.token}` },
      });
      const result = await res.json();

      if (!result.success) {
        this.token = null;
        this.isLoggedIn = false;
        return;
      }

      this.login({
        token: this.token,
        userId: result.user.user_id,
        userName: result.user.nickname,
        avatarUrl: "",
        xp: result.user.total_exp,
      });
    },
  },

  persist: {
    pick: ["token"],
  },
});

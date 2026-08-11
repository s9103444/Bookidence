import { defineStore } from "pinia";

// Pinia 的寫法故意設計成跟 Options API 幾乎一樣的心智模型：
// state   對應 data()   → 放會變動的資料
// getters 對應 computed → 放由 state 算出來的值
// actions 對應 methods  → 放會修改 state 的邏輯
export const useUserStore = defineStore("user", {
  state: () => ({
    isLoggedIn: false,
    userName: "",
    avatarUrl: "",
    xp: 0,
    level: 0,
  }),

  actions: {
    // TODO: 之後串真正的登入 API，把回傳的使用者資料傳進來
    login(userData) {
      this.isLoggedIn = true;
      this.userName = userData.userName;
      this.avatarUrl = userData.avatarUrl;
      this.xp = userData.xp;
      this.level = userData.level;
    },
    logout() {
      this.isLoggedIn = false;
      this.userName = "";
      this.avatarUrl = "";
      this.xp = 0;
      this.level = 0;
    },
  },
});

<script>
import { mapState, mapActions } from "pinia";
import { useUserStore } from "@/stores/user";
import AppIcon from "./AppIcon.vue";

export default {
  name: "AppHeader",
  components: { AppIcon },
  data() {
    return {
      isUserMenuOpen: false,
    };
  },
  computed: {
    ...mapState(useUserStore, ["isLoggedIn", "userName"]),
  },
  methods: {
    ...mapActions(useUserStore, ["logout"]),
    toggleUserMenu() {
      this.isUserMenuOpen = !this.isUserMenuOpen;
    },
    closeUserMenu() {
      this.isUserMenuOpen = false;
    },
    handleLogout() {
      this.logout();
      this.closeUserMenu();
      this.$router.push("/");
    },
  },
};
</script>

<template>
  <header class="app-header">
    <div class="app-header__side app-header__side--left">
      <router-link to="/" class="app-header__logo">
        <img src="@/assets/logo/Bookidence_logo.png" alt="Bookidence" />
      </router-link>
    </div>

    <nav class="app-header__nav">
      <router-link to="/guilds" class="nav-link">瀏覽讀書公會</router-link>
      <router-link to="/search" class="nav-link">搜索圖書</router-link>
      <router-link to="/news" class="nav-link">最新消息</router-link>
      <router-link to="/study" class="nav-link">我的專屬書房</router-link>
    </nav>

    <div class="app-header__side app-header__side--right">
      <div class="app-header__actions">
        <button class="icon-btn" aria-label="搜尋">
          <AppIcon name="search" :size="20" />
        </button>

        <button class="icon-btn" aria-label="通知">
          <AppIcon name="bell" :size="20" />
        </button>

        <!-- 已登入：帳號下拉選單 + 登出 -->
        <template v-if="isLoggedIn">
          <div class="nav-dropdown" @mouseleave="closeUserMenu">
            <button class="nav-link nav-dropdown__trigger" @click="toggleUserMenu">
              {{ userName }}
              <span class="nav-dropdown__arrow" :class="{ 'is-open': isUserMenuOpen }">▾</span>
            </button>

            <div v-if="isUserMenuOpen" class="nav-dropdown__menu">
              <router-link to="/profile" class="nav-dropdown__item" @click="closeUserMenu">會員專區</router-link>
              <router-link to="/create-guilds" class="nav-dropdown__item" @click="closeUserMenu">建立讀書公會</router-link>
            </div>
          </div>

          <button type="button" class="app-header__login" @click="handleLogout">登出</button>
        </template>

        <!-- 未登入：登入 / 註冊 -->
        <template v-else>
          <router-link :to="{ name: 'login' }" class="app-header__login">登入</router-link>
          <router-link :to="{ name: 'register' }" class="app-header__register">註冊</router-link>
        </template>
      </div>
    </div>
  </header>
</template>

<style scoped lang="scss">
@use "../../assets/scss/abstracts/variables" as *;

.app-header {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: $spacing-lg;
  height: $header-height;
  padding: 0 $spacing-lg;
  background: $primary;
  color: $neutral-100;
}

.app-header__side {
  display: flex;
  align-items: center;
}

.app-header__side--left {
  justify-content: flex-start;
}

.app-header__side--right {
  justify-content: flex-end;
}

.app-header__logo {
  width: 70px;
}

.app-header__nav {
  display: flex;
  align-items: center;
  gap: $spacing-lg;
  justify-content: center;
}

.nav-link {
  font-size: 14px;
  color: $neutral-100;
  padding: $spacing-sm 0;

  &:hover,
  &.router-link-active {
    opacity: 0.8;
  }
}

.nav-dropdown {
  position: relative;
}

.nav-dropdown__trigger {
  display: flex;
  align-items: center;
  gap: 4px;
  color: $neutral-100;
  font-size: 14px;
}

.nav-dropdown__arrow {
  font-size: 10px;
  transition: transform 0.15s ease;

  &.is-open {
    transform: rotate(180deg);
  }
}

.nav-dropdown__menu {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 160px;

  background: $neutral-100;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 10;
}

.nav-dropdown__item {
  display: block;
  padding: $spacing-sm $spacing-md;
  font-size: 14px;
  color: $neutral-800;

  &:hover {
    background: #f2f2f2;
  }
}

.app-header__actions {
  display: flex;
  align-items: center;
  gap: $spacing-md;
}

.icon-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  color: $neutral-100;

  &:hover {
    opacity: 0.8;
  }
}

.app-header__login {
  font-size: 14px;
  color: $neutral-100;
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  font-family: inherit;
}

.app-header__register {
  padding: $spacing-xs $spacing-md;
  background: $secondary;
  color: #2b2b2b;
  font-size: 14px;
  font-weight: 700;
  border-radius: 20px;

  &:hover {
    opacity: 0.9;
  }
}
</style>

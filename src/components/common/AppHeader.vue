<script>
import { mapState, mapActions } from "pinia";
import { useUserStore } from "@/stores/user";
import AppIcon from "./AppIcon.vue";
import MainSearch from "@/components/common/MainSearch.vue";
import NotificationPanel from "./NotificationPanel.vue";

function setNavActiveClass(el, isActive) {
  el.classList.toggle("is-current-page", !!isActive);
}

export default {
  name: "AppHeader",
  components: {
    AppIcon,
    MainSearch,
    NotificationPanel,
  },
  directives: {
    navActive: {
      mounted(el, binding) {
        setNavActiveClass(el, binding.value);
      },
      updated(el, binding) {
        setNavActiveClass(el, binding.value);
      },
    },
  },
  data() {
    return {
      isUserMenuOpen: false,
      isHamMenuOpen: false,
      searchActive: false,
      isNotificationOpen: false,
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
    toggleNotification() {
      this.isNotificationOpen = !this.isNotificationOpen;
      if (this.isNotificationOpen) {
        this.closeHamMenu(); // 一樣呼叫既有的 method
      }
    },
    closeNotification() {
      this.isNotificationOpen = false;
    },
    toggleHamMenuOpen() {
      this.isHamMenuOpen = !this.isHamMenuOpen;
      if (this.isHamMenuOpen) {
        this.searchActive = false;
        this.closeNotification();
      }
    },
    toggleMainSearch() {
      this.searchActive = !this.searchActive;
      if (this.searchActive) {
        this.isHamMenuOpen = false;
      }
    },
    closeHamMenu() {
      this.isHamMenuOpen = false;
    },
    handleClickOutside(e) {
      if (
        this.$refs.dropdownRef &&
        !this.$refs.dropdownRef.contains(e.target)
      ) {
        this.closeUserMenu();
      }
      if (
        this.$refs.notificationRef &&
        !this.$refs.notificationRef.contains(e.target)
      ) {
        this.closeNotification();
      }
    },
    handleLogout() {
      this.logout();
      this.closeUserMenu();
      this.closeHamMenu();
      this.$router.push({ name: "home" });
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
  },
  unmounted() {
    document.removeEventListener("click", this.handleClickOutside);
  },
};
</script>

<template>
  <div class="app-header-wrapper">
    <MainSearch
      :search-active="searchActive"
      @close="searchActive = false"
    ></MainSearch>
    <header class="app-header">
      <div class="app-header__side app-header__side--left">
        <router-link :to="{ name: 'home' }" class="app-header__logo">
          <img src="@/assets/logo/Bookidence_logo.png" alt="Bookidence" />
        </router-link>
      </div>

<<<<<<< HEAD
      <nav class="app-header__nav" :class="{ 'app-header__nav--active': isHamMenuOpen }">
        <router-link to="/guilds" class="nav-link"
          v-nav-active="$route.path === '/guilds' || $route.path.startsWith('/guilds/')"
          @click="closeHamMenu">瀏覽讀書公會</router-link>
        <router-link to="/search" class="nav-link" v-nav-active="$route.path === '/search'"
          @click="closeHamMenu">搜索圖書</router-link>
        <router-link to="/news" class="nav-link" v-nav-active="$route.path === '/news'"
          @click="closeHamMenu">最新消息</router-link>
        <router-link to="/study" class="nav-link" v-nav-active="$route.path === '/study'"
          @click="closeHamMenu">我的專屬書房</router-link>

        <!-- 小螢幕時 app-header__actions 會被隱藏，登入狀態改在這裡顯示 -->
        <template v-if="isLoggedIn">
          <router-link to="/member/user-settings" class="nav-link ham-open"
            @click="closeHamMenu">會員專區</router-link>
          <router-link to="/create-guilds" class="nav-link ham-open" @click="closeHamMenu">建立讀書公會</router-link>
          <a href="#" class="nav-link ham-open" @click.prevent="handleLogout">登出</a>
=======
      <nav
        class="app-header__nav"
        :class="{ 'app-header__nav--active': isHamMenuOpen }"
      >
        <div class="name">{{ userName }}，歡迎回來！</div>
        <router-link
          to="/front/guilds"
          class="nav-link"
          v-nav-active="
            $route.path === '/front/guilds' ||
            $route.path.startsWith('/front/guilds/')
          "
          @click="closeHamMenu"
          >瀏覽讀書公會</router-link
        >
        <router-link
          to="/front/search"
          class="nav-link"
          v-nav-active="$route.path === '/front/search'"
          @click="closeHamMenu"
          >搜索圖書</router-link
        >
        <router-link
          to="/front/news"
          class="nav-link"
          v-nav-active="$route.path === '/front/news'"
          @click="closeHamMenu"
          >最新消息</router-link
        >
        <router-link
          to="/front/study"
          class="nav-link"
          v-nav-active="$route.path === '/front/study'"
          @click="closeHamMenu"
          >我的專屬書房</router-link
        >

        <!-- 小螢幕時 app-header__actions 會被隱藏，登入狀態改在這裡顯示 -->
        <template v-if="isLoggedIn">
          <router-link
            to="/front/member/user-settings"
            class="nav-link ham-open"
            @click="closeHamMenu"
            >會員專區</router-link
          >
          <router-link
            to="/front/create-guilds"
            class="nav-link ham-open"
            @click="closeHamMenu"
            >建立讀書公會</router-link
          >
          <a href="#" class="nav-link ham-open" @click.prevent="handleLogout"
            >登出</a
          >
>>>>>>> timmi_workspace
        </template>
        <template v-else>
          <router-link
            :to="{ name: 'login' }"
            class="nav-link ham-open"
            @click="closeHamMenu"
            >登入</router-link
          >
          <router-link
            :to="{ name: 'register' }"
            class="nav-link ham-open"
            @click="closeHamMenu"
            >註冊</router-link
          >
        </template>
      </nav>

      <div class="app-header__side app-header__side--right">
        <div class="app-header__actions">
          <button class="icon-btn" aria-label="搜尋" @click="toggleMainSearch">
            <AppIcon name="search" :size="20" />
          </button>

          <div class="notification-dropdown" ref="notificationRef">
            <button
              class="icon-btn"
              aria-label="通知"
              @click="toggleNotification"
            >
              <AppIcon name="bell" :size="20" />
            </button>

            <NotificationPanel
              v-if="isNotificationOpen"
              class="notification-dropdown__panel"
            ></NotificationPanel>
          </div>

          <!-- 已登入：帳號下拉選單 + 登出 -->
          <template v-if="isLoggedIn">
            <div
              class="nav-dropdown"
              ref="dropdownRef"
              @mouseleave="closeUserMenu"
            >
              <button
                class="nav-link nav-dropdown__trigger"
                @click="toggleUserMenu"
              >
                {{ userName }}
                <span
                  class="nav-dropdown__arrow"
                  :class="{ 'is-open': isUserMenuOpen }"
                  >▾</span
                >
              </button>

              <div v-if="isUserMenuOpen" class="nav-dropdown__menu">
<<<<<<< HEAD
                <router-link to="/member/user-settings" class="nav-dropdown__item"
                  @click="closeUserMenu">會員專區</router-link>
                <router-link to="/create-guilds" class="nav-dropdown__item"
                  @click="closeUserMenu">建立讀書公會</router-link>
=======
                <router-link
                  to="/front/member/user-settings"
                  class="nav-dropdown__item"
                  @click="closeUserMenu"
                  >會員專區</router-link
                >
                <router-link
                  to="/front/create-guilds"
                  class="nav-dropdown__item"
                  @click="closeUserMenu"
                  >建立讀書公會</router-link
                >
>>>>>>> timmi_workspace
              </div>
            </div>

            <button
              type="button"
              class="app-header__login"
              @click="handleLogout"
            >
              登出
            </button>
          </template>

          <!-- 未登入：登入 / 註冊 -->
          <template v-else>
            <router-link :to="{ name: 'login' }" class="app-header__login"
              >登入</router-link
            >
            <router-link :to="{ name: 'register' }" class="app-header__register"
              >註冊</router-link
            >
          </template>
        </div>

        <!-- 漢堡按鈕（小螢幕才會顯示，CSS 控制），跟搜尋/通知放在同一個右側群組裡 -->
        <button
          type="button"
          class="hamburger"
          :class="{ 'hamburger--active': isHamMenuOpen }"
          @click="toggleHamMenuOpen"
        >
          <span class="hamburger__line"></span>
          <span class="hamburger__line"></span>
          <span class="hamburger__line"></span>
        </button>
      </div>
    </header>
  </div>
</template>

<style scoped lang="scss">
@use "../../assets/scss/abstracts/variables" as *;

.app-header-wrapper {
  position: relative;
}

//新增主搜尋欄位
.search {
  position: absolute;
  width: 100%;
  left: 0;
  top: $header-height;
  z-index: 50;
  transform: translateY(-100%);
  transition: transform 0.2s ease;

  &.search-active {
    transform: translateY(0);
  }
}

.app-header {
  position: relative;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: $spacing-lg;
  height: $header-height;
  padding: 0 $spacing-lg;
  background: $primary;

  color: $neutral-100;
  z-index: 100;
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
  gap: $spacing-md;
}

.app-header__logo {
  width: 70px;
  flex-shrink: 0;
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

  &.is-current-page {
    color: $secondary;
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
  z-index: 50;
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

// 右側動作區 (搜尋/通知/會員)
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

.notification-dropdown {
  position: relative;
}

.notification-dropdown__panel {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: $spacing-sm;
  z-index: 50;
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

.nav-dropdown__logout {
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  cursor: pointer;
  font-family: inherit;

  &:hover {
    background: #f2f2f2;
  }
}

// 漢堡按鈕預設（大螢幕隱藏）
.hamburger {
  display: none;
  background-color: transparent;
  border: none;
  padding: 8px;
  cursor: pointer;
  z-index: 999;
}

.hamburger__line {
  display: block;
  width: 24px;
  height: 3px;
  margin: 5px auto;
  background-color: #ffffff;
  border-radius: 2px;
  transition: all 0.3s ease-in-out;
}

.hamburger--active {
  .hamburger__line:nth-child(2) {
    opacity: 0;
  }

  .hamburger__line:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
  }

  .hamburger__line:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
  }
}

.ham-open {
  display: none;
}

// 響應式斷點（小螢幕）
@media (max-width: $breakpoint-desktop) {
  .app-header {
    display: flex;
    justify-content: space-between;
  }

  .hamburger {
    display: block;
  }

  .ham-open {
    display: block;
  }

  .app-header__nav {
    display: flex;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    background-color: $neutral-300;
    color: $primary !important;
    align-items: stretch !important;
    transition: all 0.3s ease;
    z-index: 100;
    overflow: hidden;
    font-weight: 500;
    max-height: 0;
    padding: 0;
    gap: 0px;
  }

  .nav-link {
    display: block;
    margin: 0;
    text-align: center;
    padding: $spacing-md 0;
    color: $neutral-600 !important;
    background-color: $neutral-200;
    font-weight: 600;
    transition:
      background-color 0.2s ease,
      color 0.2s ease;

    &:hover {
      display: block;
      background-color: $neutral-300 !important;
      color: $neutral-800 !important;
    }

    &.router-link-active {
      display: block;
      background-color: #f5f5f5 !important;
      color: $primary !important;
      opacity: 1;
    }
  }

  .name {
    display: block;
    width: 100%;
    padding-right: auto;
    font-size: 14px;
    color: $neutral-100;
    background-color: $primary;
    display: block;
    font-weight: 600;
    text-align: center;
    padding: 14px;
  }

  .app-header__nav--active {
    max-height: 500px;
    padding: $spacing-md auto;
  }

  .app-header__login,
  .app-header__register,
  .nav-dropdown {
    display: none;
  }
}
</style>

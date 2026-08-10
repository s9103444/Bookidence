<script setup>
import { ref } from "vue";

// 「我的專屬書房」是下拉選單，點擊後才展開子選單
// isBookroomOpen 這個變數就是用來記錄「現在是展開還是收起」
// const isBookroomOpen = ref(false);

// function toggleBookroom() {
//   isBookroomOpen.value = !isBookroomOpen.value;
// }

// function closeBookroom() {
//   isBookroomOpen.value = false;
// }

// 「會員登入後」的下拉選單，點擊後才展開子選單
// isUserMenuOpen 這個變數就是用來記錄「現在是展開還是收起」

const isUserMenuOpen = ref(false);

function toggleUserMenu() {
  isUserMenuOpen.value = !isUserMenuOpen.value;
}

function closeBookroom() {
  isUserMenuOpen.value = false;
}


</script>

<template>
  <header class="app-header">
    <router-link to="/" class="app-header__logo">
      <img src="@/assets/logo/Bookidence_logo.png" alt="Bookidence" />
    </router-link>

    <nav class="app-header__nav">
          <router-link to="/guilds" class="nav-link">瀏覽讀書公會</router-link>
          <router-link to="/search" class="nav-link">搜索圖書</router-link>
          <router-link to="/news" class="nav-link">最新消息</router-link>
          <router-link to="/study" class="nav-link">我的專屬書房</router-link>
       
      
    </nav>

    <div class="app-header__actions">
      <button class="icon-btn" aria-label="搜尋">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="currentColor"
          viewBox="0 0 24 24"
          width="20"
          height="20"
        >
          <path
            d="M22 22h-2v-2h2v2Zm-2-2h-2v-2h2v2Zm-6-2H6v-2h8v2Zm4 0h-2v-2h2v2ZM6 16H4v-2h2v2Zm10 0h-2v-2h2v2ZM4 14H2V6h2v8Zm14 0h-2V6h2v8ZM6 6H4V4h2v2Zm10 0h-2V4h2v2Zm-2-2H6V2h8v2Z"
          />
        </svg>
      </button>

      <button class="icon-btn" aria-label="通知">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="currentColor"
          viewBox="0 0 24 24"
          width="20"
          height="20"
        >
          <path
            d="M9 2h6v2H9zM7 4h2v2H7zm8 0h2v2h-2zM5 6h2v7H5zm12 0h2v7h-2zM3 13h2v4H3zm16 0h2v4h-2z"
          />
          <path d="M3 15h18v2H3zm5 3h2v2H8zm6 0h2v2h-2zm-4 2h4v2h-4z" />
        </svg>
      </button>

          <!-- 下拉選單：外層包一個 div，用 @mouseleave 滑走時自動收起 -->
        <div class="nav-dropdown" @mouseleave="closeUserMenu">
              <button class="nav-link nav-dropdown__trigger" @click="toggleUserMenu">
                小森愛讀書
                <span class="nav-down__arrow" :class="{'is-open':isUserMenuOpen}">▾</span>
              </button>
          
            <div v-if="isUserMenuOpen" class="nav-dropdown__menu">
                <router-link to="/profile" class="nav-dropdown__item" @click="closeUserMenu">會員專區</router-link>
                <router-link to="/create-guilds" class="nav-dropdown__item " @click="closeUserMenu">建立讀書公會</router-link>
                <!-- <button class="nav-dropdown__item nav-dropdown__logout" @click="closeUserMenu">
                登出</button> -->
            </div>
         </div>

            <router-link to="/login" class="app-header__login">登出</router-link>
            <router-link to="/login?mode=register" class="app-header__register"
              >註冊</router-link
            >
       
    </div>
  </header>
</template>

<style scoped lang="scss">
@use "../../assets/scss/abstracts/variables" as *;

.app-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-lg;
  height: $header-height;
  padding: 0 $spacing-lg;
  background: $primary;
  color: $neutral-100;
}

.app-header__logo {
  width: 70px;
}

.app-header__nav {
  display: flex;
  align-items: center;
  gap: $spacing-lg;
  flex: 1;
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

// 下拉選單相關樣式
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
  left: 0;
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
  flex-shrink: 0;
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

.nav-dropdown__logout{
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
</style>

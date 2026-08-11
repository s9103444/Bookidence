<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const isUserMenuOpen = ref(false);
const dropdownRef = ref(null);
const isHamMenuOpen = ref(false);

function toggleUserMenu() {
  isUserMenuOpen.value = !isUserMenuOpen.value;
}

function closeUserMenu() {
  isUserMenuOpen.value = false;
}

function toggleHamMenuOpen() {
  isHamMenuOpen.value = !isHamMenuOpen.value;
}

function closeHamMenu() {
  isHamMenuOpen.value = false;
}

function handleClickOutside(e){

  if(dropdownRef.value &&! dropdownRef.value.contains(e.target)){
    closeUserMenu();
  }

}

onMounted(()=>{
  document.addEventListener("click",handleClickOutside);

});

onUnmounted(()=>{
  document.removeEventListener("click",handleClickOutside);

});

</script>

<template>
  <header class="app-header">
    <router-link to="/" class="app-header__logo">
      <img src="@/assets/logo/Bookidence_logo.png" alt="Bookidence" />
    </router-link>

    <!-- ← 在這裡加上漢堡按鈕 -->
      <button type="button" class="hamburger" :class="{ 'hamburger--active': isHamMenuOpen }" @click="toggleHamMenuOpen">
        <span class="hamburger__line"></span>
        <span class="hamburger__line"></span>
        <span class="hamburger__line"></span>
      </button>


    <nav class="app-header__nav" :class="{ 'app-header__nav--active': isHamMenuOpen }" >
          <router-link to="/guilds" class="nav-link" @click="closeHamMenu" >瀏覽讀書公會</router-link>
          <router-link to="/search" class="nav-link" @click="closeHamMenu">搜索圖書</router-link>
          <router-link to="/news" class="nav-link" @click="closeHamMenu">最新消息</router-link>
          <router-link to="/study" class="nav-link" @click="closeHamMenu">我的專屬書房</router-link>

          <router-link to="/login" class="nav-link ham-open" @click="closeHamMenu">登入</router-link>
          <router-link to="/register" class="nav-link ham-open" @click="closeHamMenu">註冊</router-link>
       
      
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
        <div class="nav-dropdown" ref="dropdownRef" @mouseleave="closeUserMenu">
              <button class="nav-link nav-dropdown__trigger" @click="toggleUserMenu">
                小森愛讀書
                <span class="nav-dropdown__arrow" :class="{'is-open':isUserMenuOpen}">▾</span>
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
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-lg;
  height: $header-height;
  padding: 0 $spacing-lg;
  background: $primary;
  color: $neutral-100;
  z-index: 100;
}

.app-header__logo {
  width: 70px;
  flex-shrink: 0;
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

// 漢堡按鈕預設（大螢幕隱藏）
.hamburger {
  display: none;
  background-color: transparent;
  border: none;
  padding: 8px;
  cursor: pointer;
  z-index: 999;
}

// 三條線
.hamburger__line {
  display: block;
  width: 24px;
  height: 3px;
  margin: 5px auto;
  background-color: #ffffff;
  border-radius: 2px;
  transition: all 0.3s ease-in-out;

  
}

// 點擊後的動畫
.hamburger--active {
  .hamburger__line:nth-child(2) {
    opacity: 0;
  }

  .hamburger__line:nth-child(1) {
    /* 垂直位移 8px (正好抵銷 margin:5px + height:3px)，確保在中間與第3條交會 */
    transform: translateY(8px) rotate(45deg);
  }

  .hamburger__line:nth-child(3) {
    /* 向上位移 8px */
    transform: translateY(-8px) rotate(-45deg);
  }
}

.ham-open{
  display: none;
}
// 響應式斷點（小螢幕）
@media (max-width: 810px) {
  .hamburger {
    display: block; // 顯示漢堡按鈕
    
  }
  .ham-open{
  display: block;
}
  .app-header__nav {
    display: flex;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    flex-direction: column;
    background-color:$neutral-300;
    color:$primary !important;
    align-items: stretch !important;
    transition: all 0.3s ease;
    z-index: 100;
    overflow: hidden; 
    font-weight: 500;
     max-height: 0; 
     padding: 0;
    gap:0px;

    // &:hover,
    //   &.router-link-active {
    //     background-color: #f5f5f5;
    //     color: $primary !important;
    //     opacity: 1;
    //   } 

  }
  .nav-link {
      display: block;                  // 轉為塊狀元素，獨立區塊
      margin: 0;               // 每個選項獨立開來（關鍵！）
      text-align: center;
      padding: $spacing-md 0;
      
      /* 強制設定文字顏色為深綠色 (用 !important 避免被預設樣式蓋掉) */
      color: #005a5b !important;      // 若 $primary 不對，直接填你的深綠色碼
      font-weight: 600;
      transition: background-color 0.2s ease, color 0.2s ease;

      /* 滑過去 (Hover) 或 當前頁面 (Active) 的個別卡片變色 */
      &:hover{
        display: block; 
        background-color: $primary !important; 
        color:#f5f5f5  !important;           

      }
      &.router-link-active {
        display: block;   
        background-color: #f5f5f5 !important; // 背景變灰白
        color: $primary !important;           // 文字保持深綠色
        opacity: 1;}
      }
  
  .app-header__nav--active {
    // display: none;
    max-height: 500px; 
    padding: $spacing-md auto;

  }
  
  .app-header__actions {
    display: none;
  }
}


</style>

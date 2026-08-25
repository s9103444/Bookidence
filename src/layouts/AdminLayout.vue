<!--
AdminLayout 後台外框

後台每一頁共用的左側選單、上方標題列。頁面本身只要寫內容，
外框會自動包上去 —— 就像前台的 FrontLayout 那樣。

要新增一頁後台：
1. 在 src/views/admin/ 建一支 .vue
2. 到 src/router/admin.js 加一條路由，記得填 meta.title
3. 如果那頁要出現在左側選單，回到這支的 navItems 加一筆

meta.title 會同時決定兩件事：左側選單哪一項反白、上方標題列顯示什麼字。

有些區底下不只一頁（書籍管理、系統共同管理）。那幾條路由要填一樣的
meta.group，左側才知道這幾頁都算同一項；選單項目自己再多寫一個 children，
點進那一區時底下才會展開子選單。
-->

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppIcon from '../components/common/AppIcon.vue'
import { useAdminBooksStore } from '../stores/adminBooks.js'
import { useAdminReportsStore } from '../stores/adminReports.js'
import { useAdminStore } from '../stores/adminAuth.js'

const route = useRoute()
const router = useRouter()
const adminBooksStore = useAdminBooksStore()
const adminReportsStore = useAdminReportsStore()
const adminStore=useAdminStore()
adminStore.restoreSession()

// 寫成 computed 是因為 badge 的數字要跟著審核變 ——
// 寫成一般的變數只會抓到剛進頁面時的值，審核完側邊欄還是舊數字。
const navItems = computed(() => [
  { label: '總覽', to: '/admin', icon: 'chart' },
  {
    label: '書籍管理',
    to: '/admin/books',
    icon: 'book',
    group: '書籍管理',
    children: [
      {
        label: '申請審核',
        to: '/admin/books/applications',
        badge: adminBooksStore.pendingApplicationCount,
      },
      { label: '正式書籍', to: '/admin/books/list' },
      { label: '書籍分類', to: '/admin/books/categories' },
    ],
  },
  { label: '會員管理', to: '/admin/members', icon: 'users', group: '會員管理' },
  { label: '公會管理', to: '/admin/guilds', icon: 'building-community', group: '公會管理' },
  {
    label: '檢舉管理',
    to: '/admin/reports',
    icon: 'flag',
    group: '檢舉管理',
    badge: adminReportsStore.pendingCount,
  },
  {
    label: '系統共同管理',
    to: '/admin/settings',
    icon: 'settings-cog',
    group: '系統共同管理',
    children: [
      { label: '經驗值規則', to: '/admin/settings' },
      { label: '輪播圖與最新消息', to: '/admin/announcements' },
      { label: '常見問題 FAQ', to: '/admin/support' },
    ],
  },
])

// 不能用 RouterLink 自帶的 router-link-active：「總覽」的網址是 /admin，
// 而其他每一頁的網址都是 /admin/ 開頭，會害「總覽」在每一頁都亮著。
function isActive(item) {
  if (item.group) return route.meta.group === item.group
  return route.path === item.to
}

// 子項目要在自己底下更深的頁面也保持亮著：看《原子習慣》的審核詳情時，
// 網址是 /admin/books/applications/A-0207，「申請審核」還是該亮的那一項。
// 後面那個斜線不能省 —— 少了它 /admin/books/list 也會被算進 /admin/books。
function isChildActive(child) {
  return route.path === child.to || route.path.startsWith(`${child.to}/`)
}

const pageTitle = computed(() => route.meta.title ?? '')

// 麵包屑：前面幾層可以點回去，最後一層是現在這頁。
// 例如看審核詳情時是「書籍管理 › 申請審核 › 審核申請」。
const crumbs = computed(() => {
  const list = []
  const group = navItems.value.find((item) => item.group && item.group === route.meta.group)

  // 沒有子選單的區（例如會員管理），列表頁本身的標題就等於區名，
  // 這時候不用再加一層，不然會變成「會員管理 › 會員管理」
  if (group && group.label !== pageTitle.value) {
    list.push({ label: group.label, to: group.to })

    const child = group.children?.find(isChildActive)
    // 現在就在子項目本身的話不用加，不然會跟最後一層重複
    if (child && route.path !== child.to) {
      list.push({ label: child.label, to: child.to })
    }
  }

  list.push({ label: pageTitle.value, to: null })
  return list
})

function handleLogout() {
  adminStore.logout()
  router.push({name:"admin-login"})
}
</script>

<template>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <RouterLink to="/admin" class="admin-sidebar__brand">
        <img
          src="@/assets/logo/Bookidence_wide_logo.png"
          alt="Bookidence"
          class="admin-sidebar__brand-logo"
        />
        <span class="admin-sidebar__brand-sub">ADMIN CONSOLE</span>
      </RouterLink>

      <nav class="admin-sidebar__nav" aria-label="後台主選單">
        <ul class="admin-sidebar__list">
          <li v-for="item in navItems" :key="item.to">
            <RouterLink
              :to="item.to"
              class="admin-sidebar__link"
              :class="{
                'admin-sidebar__link--active': isActive(item) && !item.children,
                'admin-sidebar__link--open': isActive(item) && item.children,
              }"
              :aria-current="isActive(item) && !item.children ? 'page' : undefined"
            >
              <AppIcon :name="item.icon" :size="16" />
              {{ item.label }}
              <span v-if="item.badge" class="admin-sidebar__badge">{{ item.badge }}</span>
            </RouterLink>

            <ul v-if="item.children && isActive(item)" class="admin-sidebar__sublist">
              <li v-for="child in item.children" :key="child.to">
                <RouterLink
                  :to="child.to"
                  class="admin-sidebar__sublink"
                  :class="{ 'admin-sidebar__sublink--active': isChildActive(child) }"
                  :aria-current="isChildActive(child) ? 'page' : undefined"
                >
                  {{ child.label }}
                  <span v-if="child.badge" class="admin-sidebar__badge">{{ child.badge }}</span>
                </RouterLink>
              </li>
            </ul>
          </li>
        </ul>
      </nav>

      <div class="admin-sidebar__foot">
        <button type="button" class="admin-sidebar__link" @click="handleLogout">
          <AppIcon name="logout" :size="16" />
          登出
        </button>
      </div>
    </aside>

    <div class="admin-body">
      <header class="admin-topbar">
        <nav aria-label="目前位置">
          <ol class="admin-topbar__crumb">
            <li v-for="crumb in crumbs" :key="crumb.label">
              <RouterLink v-if="crumb.to" :to="crumb.to" class="admin-topbar__crumb-link">
                {{ crumb.label }}
              </RouterLink>
              <span v-else aria-current="page">{{ crumb.label }}</span>
            </li>
          </ol>
        </nav>

        <div class="admin-topbar__user">
          <span class="admin-topbar__avatar" aria-hidden="true"></span>
          {{ adminStore.staffName }}
        </div>
      </header>

      <main class="admin-content">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style scoped lang="scss">
@use '../assets/scss/abstracts/variables' as *;

// 後台桌機優先，不做手機版。窄於這個寬度會出現橫向捲軸而不是擠爛。
.admin-layout {
  display: flex;
  min-width: 1100px;
  min-height: 100vh;
  background: $neutral-200;
}

.admin-sidebar {
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  width: $sidebar-width;
  height: 100vh;
  padding-top: $spacing-lg;
  background: $primary;
  position: sticky;
  top: 0;

  &__brand {
    display: block;
    padding: 0 $spacing-lg $spacing-md;
    margin-bottom: $spacing-md;
    border-bottom: 1px solid rgba(#fff, 0.16);
    text-decoration: none;
  }

  &__brand-logo {
    display: block;
    width: 150px;
    max-width: 100%;
    height: auto;
  }

  &__brand-sub {
    display: block;
    margin-top: $spacing-sm;
    color: rgba(#fff, 0.5);
    font-size: $label-xxs-size;
    letter-spacing: 0.3em;
  }

  &__list {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  // RouterLink 會渲染成 <a>、登出是 <button>，兩個要長一樣，
  // 所以按鈕要把瀏覽器預設的外觀清掉
  &__link {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    height: 42px;
    padding: 0 $spacing-lg;
    border: 0;
    border-left: 4px solid transparent;
    background: none;
    color: rgba(#fff, 0.75);
    font-family: inherit;
    font-size: $p-sm-size;
    text-align: left;
    text-decoration: none;
    cursor: pointer;

    &:hover {
      background: $primary-500;
      color: $neutral-100;
    }

    &--active {
      background: $neutral-100;
      border-left-color: $primary;
      color: $primary;
      font-weight: $heading-weight;

      &:hover {
        background: $neutral-100;
        color: $primary;
      }
    }

    &--open {
      color: $neutral-100;
      font-weight: $heading-weight;
    }
  }

  &__sublist {
    list-style: none;
    margin: 0 0 $spacing-sm;
    padding: 0;
    background: $primary-500;
  }

  &__sublink {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    height: 36px;
    // 左邊縮排對齊主項目的文字（$spacing-lg 的內距 + 16px 圖示 + 10px 間距）
    padding: 0 $spacing-md 0 ($spacing-lg + 26px);
    border-left: 4px solid transparent;
    color: rgba(#fff, 0.75);
    font-size: $p-xs-size;
    text-decoration: none;

    &:hover {
      color: $neutral-100;
    }

    &--active {
      background: $neutral-100;
      border-left-color: $secondary;
      color: $primary;
      font-weight: $heading-weight;

      &:hover {
        color: $primary;
      }
    }
  }

  &__badge {
    margin-left: auto;
    min-width: 20px;
    padding: 0 $spacing-xs;
    border-radius: $btn-radius-rnd;
    background: $color-danger;
    color: $neutral-100;
    font-size: $label-xxs-size;
    font-weight: $heading-weight;
    line-height: 18px;
    text-align: center;
  }

  &__foot {
    margin-top: auto;
    padding: $spacing-sm 0 $spacing-lg;
    border-top: 1px solid rgba(#fff, 0.16);
  }
}

.admin-body {
  flex: 1;
  min-width: 0;
}

.admin-topbar {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  height: 44px;
  padding: 0 $spacing-lg;
  background: $neutral-100;
  border-bottom: 1px solid $neutral-300;

  &__crumb {
    display: flex;
    gap: $spacing-sm;
    list-style: none;
    margin: 0;
    padding: 0;
    color: $neutral-600;
    font-size: $p-sm-size;

    // 兩層以上才需要分隔線，第一層前面不加
    li + li::before {
      content: '/';
      margin-right: $spacing-sm;
      color: $neutral-400;
    }
  }

  &__crumb-link {
    color: $neutral-600;
    text-decoration: none;

    &:hover {
      color: $primary;
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    li:last-child {
      color: $neutral-800;
      font-weight: $heading-weight;
    }
  }

  &__user {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    margin: 0 0 0 auto;
    color: $neutral-800;
    font-size: $p-sm-size;
  }

  &__avatar {
    width: 26px;
    height: 26px;
    border-radius: $btn-radius-rnd;
    background: $neutral-300;
  }
}

.admin-content {
  padding: $spacing-lg;
}
</style>

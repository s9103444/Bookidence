<!--
AdminLayout 後台外框

後台每一頁共用的左側選單、上方標題列。頁面本身只要寫內容，
外框會自動包上去 —— 就像前台的 FrontLayout 那樣。

要新增一頁後台：
1. 在 src/views/admin/ 建一支 .vue
2. 到 src/router/admin.js 加一條路由，記得填 meta.title
3. 如果那頁要出現在左側選單，回到這支的 navItems 加一筆

meta.title 會同時決定兩件事：左側選單哪一項反白、上方標題列顯示什麼字。

「系統共同管理」底下有三頁共用同一個選單項目，所以那三條路由要多填
meta.group: '系統共同管理'，左側才知道這三頁都算它。
-->

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppIcon from '../components/common/AppIcon.vue'

const route = useRoute()
const router = useRouter()

const navItems = [
  { label: '總覽', to: '/admin', icon: 'chart' },
  { label: '書籍管理', to: '/admin/books', icon: 'book' },
  { label: '會員管理', to: '/admin/members', icon: 'users' },
  { label: '公會管理', to: '/admin/guilds', icon: 'building-community' },
  { label: '檢舉管理', to: '/admin/reports', icon: 'flag' },
  {
    label: '系統共同管理',
    to: '/admin/settings',
    icon: 'settings-cog',
    group: '系統共同管理',
  },
]

// 不能用 RouterLink 自帶的 router-link-active：「總覽」的網址是 /admin，
// 而其他每一頁的網址都是 /admin/ 開頭，會害「總覽」在每一頁都亮著。
function isActive(item) {
  if (item.group) return route.meta.group === item.group
  return route.path === item.to
}

const pageTitle = computed(() => route.meta.title ?? '')
const pageGroup = computed(() => route.meta.group ?? '')

function handleLogout() {
  router.push('/admin/login')
}
</script>

<template>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <RouterLink to="/admin" class="admin-sidebar__brand">
        <span class="admin-sidebar__brand-name">Bookidence</span>
        <span class="admin-sidebar__brand-sub">ADMIN CONSOLE</span>
      </RouterLink>

      <nav class="admin-sidebar__nav" aria-label="後台主選單">
        <ul class="admin-sidebar__list">
          <li v-for="item in navItems" :key="item.to">
            <RouterLink
              :to="item.to"
              class="admin-sidebar__link"
              :class="{ 'admin-sidebar__link--active': isActive(item) }"
              :aria-current="isActive(item) ? 'page' : undefined"
            >
              <AppIcon :name="item.icon" :size="16" />
              {{ item.label }}
            </RouterLink>
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
            <li v-if="pageGroup">{{ pageGroup }}</li>
            <li aria-current="page">{{ pageTitle }}</li>
          </ol>
        </nav>

        <p class="admin-topbar__user">
          <span class="admin-topbar__avatar" aria-hidden="true"></span>
          書芸
        </p>
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

  &__brand-name {
    display: block;
    color: $neutral-100;
    font-size: $label-lg-size;
    font-weight: $heading-weight;
    letter-spacing: 0.1em;
  }

  &__brand-sub {
    display: block;
    margin-top: $spacing-xs;
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

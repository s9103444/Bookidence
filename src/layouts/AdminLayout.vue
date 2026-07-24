<script setup>
// 後台的外框：左側固定側邊欄放管理選單，右邊是內容。
// 跟前台是完全不同的外觀，所以獨立成另一個 layout。
</script>

<template>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <div class="admin-sidebar__title">後台管理</div>
      <nav class="admin-sidebar__nav">
        <router-link to="/admin">總覽</router-link>
        <router-link to="/admin/members">會員管理</router-link>
        <router-link to="/admin/books">書籍管理</router-link>
      </nav>
    </aside>

    <main class="admin-main">
      <router-view />
    </main>
  </div>
</template>

<style scoped lang="scss">
@use '../assets/scss/abstracts/variables' as *;

.admin-layout {
  display: flex;
  min-height: 100vh;
}

.admin-sidebar {
  width: $sidebar-width;
  flex-shrink: 0;
  background: #2b2b2b;
  color: #fff;
  padding: $spacing-md;

  &__title {
    font-weight: 700;
    margin-bottom: $spacing-lg;
  }

  &__nav {
    display: flex;
    flex-direction: column;
    gap: $spacing-sm;

    a {
      color: #ccc;
      padding: $spacing-sm;
      border-radius: 4px;

      &:hover,
      &.router-link-active {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
      }
    }
  }
}

.admin-main {
  flex: 1;
  padding: $spacing-lg;
}
</style>

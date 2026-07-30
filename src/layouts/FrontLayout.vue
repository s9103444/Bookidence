<script setup>
// 這是前台的外框：Header / Footer 都改成共用元件，
// 這裡不再放任何導覽列的內容，之後要改 Nav，直接去改
// components/common/AppHeader.vue 就好，不用來這裡找
import AppHeader from '../components/common/AppHeader.vue'
import AppFooter from '../components/common/AppFooter.vue'

import { useRoute } from 'vue-router'
const route = useRoute()
</script>

<template>
  <div class="front-layout">
    <AppHeader v-if="!route.meta.hideHeader" />

    <main class="front-main">
      <!-- 這裡會依照網址，換成對應的頁面元件 -->
      <router-view />
    </main>

    <AppFooter />
  </div>
</template>

<style scoped lang="scss">
@use '../assets/scss/abstracts/variables' as *;

.front-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.front-main {
  flex: 1;
  padding: $spacing-lg;
}
</style>

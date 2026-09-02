<script setup>
// App.vue 本身不放任何畫面內容
// 它只負責一件事：根據目前網址，決定要顯示 FrontLayout 還是 AdminLayout
// 判斷邏輯都寫在 router 裡，這裡完全不用管
import { useUserStore } from '@/stores/user';
import KickedNoticeModal from '@/components/common/KickedNoticeModal.vue';

useUserStore().restoreSession();
</script>

<template>
  <router-view />
  <KickedNoticeModal />
</template>
<style lang="scss"></style>

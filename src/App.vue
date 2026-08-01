<script setup>
// App.vue 本身不放任何畫面內容
// 它只負責一件事：根據目前網址，決定要顯示 FrontLayout 還是 AdminLayout
// 判斷邏輯都寫在 router 裡，這裡完全不用管
</script>

<template>
  <router-view />
</template>
<style lang="scss"></style>

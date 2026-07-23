<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

// 用 route.query.mode 判斷要顯示登入還是註冊表單，預設（沒有 query 或 mode=login）顯示登入表單
const route = useRoute()
const isRegister = computed(() => route.query.mode === 'register')
</script>

<template>
  <div v-if="isRegister">
    <h1>註冊</h1>
    <p>這是前台「註冊」頁面，等功能開發時再把內容補上。</p>
  </div>
  <div v-else>
    <h1>登入</h1>
    <p>這是前台「登入」頁面，等功能開發時再把內容補上。</p>
  </div>
</template>

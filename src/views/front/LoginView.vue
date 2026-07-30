<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

// 用 route.query.mode 判斷要顯示登入還是註冊表單，預設（沒有 query 或 mode=login）顯示登入表單
const route = useRoute()
const isRegister = computed(() => route.query.mode === 'register')
</script>

<template>
<div class="auth-wrap">
  <!-- 左側插畫欄：文案隨分頁切換 -->
  <div class="art-panel"></div>
  <!-- 右側表單欄 -->
  <div class="form-panel">
    <div v-if="isRegister">...註冊欄位...</div>
    <div v-else>...登入欄位...</div>
  </div>
</div>
</template>

<!-- src/views/front/GuildSettingsView.vue -->
<template>
  <div class="guild-settings">
    <h1>公會設定</h1>
    <p>這是公會設定頁，等功能開發時再把內容補上。</p>
  </div>
</template>

<script>
export default {
  created() {
    console.log('公會 ID：', this.$route.params.id)
  },
}
</script>

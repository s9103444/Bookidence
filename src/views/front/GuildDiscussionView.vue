<!-- src/views/front/GuildDiscussionView.vue -->
<template>
  <div class="guild-discussion">
    <h1>討論區</h1>
    <p>這是討論區頁面，等功能開發時再把內容補上。</p>
  </div>
</template>

<script>
export default {
  created() {
    console.log('公會 ID：', this.$route.params.id)
    console.log('里程碑 ID：', this.$route.params.milestoneId)
  },
}
</script>

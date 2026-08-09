<!-- src/views/front/GuildMembersView.vue -->
<template>
  <div class="guild-members">
    <h1>成員列表</h1>
    <p>這是成員列表頁，等功能開發時再把內容補上。</p>
  </div>
</template>

<script>
export default {
  created() {
    console.log('公會 ID：', this.$route.params.id)
  },
}
</script>

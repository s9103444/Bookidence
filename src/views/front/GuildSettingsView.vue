<!-- src/views/front/GuildSettingsView.vue -->
<script setup>
import GuildBreadcrumb from "@/layouts/GuildBreadcrumb.vue";
import { useRoute } from "vue-router";

const route = useRoute();

</script>


<template>

  <GuildBreadcrumb :items="[
    { label: '❮  公會主頁', to: `/guilds/${route.params.id}` },// guilds/:id 填入目前公會的 id
    { label: '公會設定' }
]" />

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

<style lang="scss">
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;
</style>

<template>
  <div class="news-maintenance">
    <img
      class="hero-panda"
      src="@/assets/images/crypanda.png"
      alt="哭哭熊貓"
    />

    <h1 class="title">最新消息維修中</h1>
    <p class="subtitle">晚點再回來看看吧</p>

    <AppButton :to="{ name: 'home' }" color="secondary">回首頁逛逛</AppButton>
  </div>
</template>

<script setup>
import AppButton from "@/components/common/AppButton.vue";
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.news-maintenance {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: $spacing-md;
  padding: $spacing-xl $spacing-md;
  text-align: center;
}

.hero-panda {
  width: min(320px, 70vw);
  height: auto;
  // box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.title {
  margin-top: $spacing-lg;
  font-size: $h4-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $primary;
}

.subtitle {
  margin-bottom: $spacing-sm;
  font-size: $p-md-size;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-600;
}
</style>

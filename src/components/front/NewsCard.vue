<script>
import AppIcon from '@/components/common/AppIcon.vue'

export default {
  components: {
    AppIcon,
  },
  props: {
    tag: {
      type: String,
      default: '系統更新',
    },
    icon: {
      type: String,
      default: 'bell',
    },
    title: {
      type: String,
      default: '',
    },
    summary: {
      type: String,
      default: '',
    },
    date: {
      type: String,
      default: '',
    },
    views: {
      type: Number,
      default: 0,
    },
  },
}
</script>

<template>
  <div class="news-card">
    <div class="news-card__header">
      <span class="news-card__icon">
        <AppIcon :name="icon" :size="20" />
      </span>
      <span class="news-card__tag">{{ tag }}</span>
    </div>

    <p class="news-card__title">{{ title }}</p>
    <p class="news-card__summary">{{ summary }}</p>

    <div class="news-card__meta">
      <span>
        <AppIcon name="clock" :size="14" />
        {{ date }}
      </span>
      <span>
        <AppIcon name="eye" :size="14" />
        {{ views.toLocaleString() }}
      </span>
    </div>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.news-card {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
  width: 100%;
  height: 100%;
  padding: $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: 12px;
  flex-shrink: 0;
}

.news-card__header {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.news-card__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  border-radius: 50%;
  background: $primary-300;
  color: $primary;
}

.news-card__tag {
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-600;
}

.news-card__title {
  font-weight: 700;
  line-height: $heading-line-height;
}

.news-card__summary {
  font-size: $p-sm-size;
  line-height: 1.5;
  min-height: 3em; // 固定保留 2 行的高度，簡介不滿 2 行時下面的資訊列也能對齊
  color: $neutral-500;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.news-card__meta {
  display: flex;
  align-items: center;
  gap: $spacing-md;
  margin-top: auto;
  font-size: $p-xs-size;
  color: $neutral-500;

  span {
    display: flex;
    align-items: center;
    gap: 4px;
  }
}
</style>

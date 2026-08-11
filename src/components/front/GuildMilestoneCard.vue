<script>
import AppIcon from '@/components/common/AppIcon.vue'

export default {
  components: {
    AppIcon,
  },
  props: {
    milestoneId: {
      type: [Number, String],
      required: true,
    },
    index: {
      type: String,
      default: '01',
    },
    title: {
      type: String,
      default: '閱讀里程碑',
    },
    readingRange: {
      type: String,
      default: '',
    },
    completeDate: {
      type: String,
      default: '',
    },
  },
  emits: ['view-discussion'],
  methods: {
    handleClick() {
      this.$emit('view-discussion', this.milestoneId)
    },
  },
}
</script>

<template>
  <div class="guild-milestone-card">
    <div class="guild-milestone-card__header">
      <span>{{ title }}</span>
      <strong class="guild-milestone-card__index">{{ index }}</strong>
    </div>
    <div class="guild-milestone-card__icon">
      <AppIcon name="book" :size="40" />
    </div>
    <p class="guild-milestone-card__meta">
      <span class="guild-milestone-card__label">閱讀區</span> {{ readingRange }}
    </p>
    <p class="guild-milestone-card__meta">
      <span class="guild-milestone-card__label">完讀日</span> {{ completeDate }}
    </p>
    <button class="guild-milestone-card__btn" @click="handleClick">
      進入討論區 <AppIcon name="arrow-right" :size="16" />
    </button>
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.guild-milestone-card {
  padding: $spacing-lg;
  border-radius: 12px;
  background: $secondary-100;
  text-align: center;
}

.guild-milestone-card__header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: $spacing-xs;
  font-weight: 700;
  color: $neutral-800;
  margin-bottom: $spacing-md;
}

.guild-milestone-card__index {
  font-size: $h6-size;
  color: $primary;
}

.guild-milestone-card__icon {
  color: $primary;
  margin-bottom: $spacing-md;
}

.guild-milestone-card__meta {
  font-size: $p-sm-size;
  color: $neutral-600;
  margin-bottom: 4px;
}

.guild-milestone-card__label {
  display: inline-block;
  padding: 2px $spacing-sm;
  margin-right: $spacing-xs;
  border-radius: $btn-radius-std;
  background: $neutral-200;
  font-size: $p-xs-size;
  color: $neutral-600;
}

.guild-milestone-card__btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-top: $spacing-md;
  padding: $spacing-xs $spacing-lg;
  border-radius: $btn-radius-rnd;
  background: $primary;
  color: $neutral-100;
  font-weight: 700;
  font-size: $p-sm-size;

  &:hover {
    background: $primary-500;
  }
}
</style>

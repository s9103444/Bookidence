<script>
  import AppIcon from '../../../components/common/AppIcon.vue';

  const bookCategories = [
    { id: 1, name: '心理成長', subtitle: '自我成長・療癒', icon: 'heart' },
    { id: 2, name: '商業理財', subtitle: '商場・理財', icon: 'chart' },
    { id: 3, name: '歷史人文', subtitle: '人物・事件', icon: 'article' },
    { id: 4, name: '科普知識', subtitle: '科學・探索', icon: 'test-tube' },
    { id: 5, name: '醫療生活', subtitle: '健康・日常', icon: 'leaf' },
    { id: 6, name: '藝術設計', subtitle: '美學・創意', icon: 'brush' },
    { id: 7, name: '社會議題', subtitle: '公共・議題', icon: 'users' },
    { id: 8, name: '推理懸疑', subtitle: '文學・故事', icon: 'search' },
    { id: 9, name: '奇幻科幻', subtitle: '想像・冒險', icon: 'sparkles' },
    { id: 10, name: '文學小說', subtitle: '文學・故事', icon: 'book' },
    { id: 11, name: '漫畫', subtitle: '圖像・故事', icon: 'zap' },
    { id: 12, name: '生活風格', subtitle: '生活・日常', icon: 'home' }
  ];
  export default {
  components: { AppIcon },
  props: {
    selectedCategoryIds: {
      type: Array
    }
  },
  emits: ['toggle-category'],
  computed: {
    bookCategories() {
      return bookCategories;
    }
  }
  }
</script>

<template>
  <p class="register-step3__title">你的閱讀偏好</p>
  <p class="register-step3__subtitle">讓我們更了解你，推薦更適合你的書籍！</p>
  <div class="register-step3">
    <div
      v-for="category in bookCategories"
      :key="category.id"
      class="register-step3__card"
      :class="{ 'register-step3__card--selected': selectedCategoryIds.includes(category.id) }"
      @click="$emit('toggle-category', category.id)"
    >
    <div>
      <AppIcon :name="category.icon" :size="24" class="register-step3__card-icon" />
      <p class="register-step3__card-title">{{ category.name }}</p>
      <p class="register-step3__card-subtitle">{{ category.subtitle }}</p>
    </div>
    </div>
  </div>
</template>

<style lang="scss">
  @use '@/assets/scss/abstracts/variables' as *;
  .register-step3{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: $spacing-md;

    &__title{
      font-weight: $heading-weight;
    }

    &__subtitle{
      font-size: $p-sm-size;
      margin-bottom: $spacing-md;
    }

    &__card{
      border: 1px solid $neutral-400;
      text-align: center;
      color: $primary;
      padding-block: $spacing-xs;
      border-radius: $btn-radius-std;
      transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;

      &-icon{
        margin-top: $spacing-xs;
      }

      &-title{
        font-weight: $heading-weight;
      }

      &-subtitle{
        font-size: $p-xs-size;
      }

      &--selected{
        background-color: $primary-300;
        color: $neutral-100;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        border: none;
      }
    }

    @media (max-width: $breakpoint-tablet) {
      grid-template-columns: repeat(3, 1fr);
    }

    @media (max-width: $breakpoint-mobile) {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>
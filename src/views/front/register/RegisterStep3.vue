<script>
  import {
    IconBook, IconWand, IconSearch, IconHeart, IconChartLine,
    IconBuildingBank, IconPalette, IconFlask, IconUsers,
    IconMoodSmile, IconHome
  } from '@tabler/icons-vue';

  const bookCategories = [
    { id: 1, name: '心理成長', subtitle: '自我成長・療癒', icon: IconHeart },
    { id: 2, name: '商業理財', subtitle: '商場・理財', icon: IconChartLine },
    { id: 3, name: '歷史人文', subtitle: '人物・事件', icon: IconBuildingBank },
    { id: 4, name: '科普知識', subtitle: '科學・探索', icon: IconFlask },
    { id: 5, name: '醫療生活', subtitle: '健康・日常', icon: IconHeart },
    { id: 6, name: '藝術設計', subtitle: '美學・創意', icon: IconPalette },
    { id: 7, name: '社會議題', subtitle: '公共・議題', icon: IconUsers },
    { id: 8, name: '推理懸疑', subtitle: '文學・故事', icon: IconSearch },
    { id: 9, name: '奇幻科幻', subtitle: '想像・冒險', icon: IconWand },
    { id: 10, name: '文學小說', subtitle: '文學・故事', icon: IconBook },
    { id: 11, name: '漫畫', subtitle: '圖像・故事', icon: IconMoodSmile },
    { id: 12, name: '生活風格', subtitle: '生活・日常', icon: IconHome }
  ];
  export default {
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
  <div class="register-step3">
    <div
      v-for="category in bookCategories"
      :key="category.id"
      class="register-step3__card"
      :class="{ 'register-step3__card--selected': selectedCategoryIds.includes(category.id) }"
      @click="$emit('toggle-category', category.id)"
    >
      {{ category.name }}
    </div>
  </div>
</template>
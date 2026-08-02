<script>
  const bookCategories = [
    { id: 1, name: '心理成長' },
    { id: 2, name: '商業理財' },
    { id: 3, name: '歷史人文' },
    { id: 4, name: '科普知識' },
    { id: 5, name: '醫療生活' },
    { id: 6, name: '藝術設計' },
    { id: 7, name: '社會議題' },
    { id: 8, name: '推理懸疑' },
    { id: 9, name: '奇幻科幻' },
    { id: 10, name: '文學小說' },
    { id: 11, name: '漫畫' },
    { id: 12, name: '生活風格' }
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
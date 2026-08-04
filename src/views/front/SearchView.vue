<script setup>
  import BookCard from '@/components/front/BookCard.vue';
  import littlePrinceCover from '@/assets/images/little-prince-cover.png';

  const books = [
  {
    id: 1,
    title: '北歐時間：世界第一幸福國度教會我的事',
    author: '詹姆斯・克利爾',
    categories: ['心理成長', '習慣養成'],
    description: '細微改變帶來巨大成就的實踐法則，幫助你建立好習慣，打破壞習慣。',
    cover: littlePrinceCover
  },
  {
    id: 2,
    title: '深度工作力：淺薄時代，個人成功的關鍵能力',
    author: '卡爾・紐波特',
    categories: ['職場工作', '自我管理'],
    description: '在分心時代重新奪回專注力，用深度工作創造無可取代的價值。',
    cover: littlePrinceCover
  },
  {
    id: 3,
    title: '被討厭的勇氣：自我啟發之父阿德勒的教導',
    author: '岸見一郎',
    categories: ['心理成長', '人際關係'],
    description: '所有煩惱都來自人際關係，阿德勒心理學帶你找回選擇人生的自由。',
    cover: littlePrinceCover
  }
];
</script>
<template>
  <div class="search-view">
    <ul class="search-view__list">
      <li v-for="book in books" :key="book.id">
        <BookCard
          :book-id="book.id"
          :cover-image="book.cover"
          :title="book.title"
          :author="book.author"
          :categories="book.categories"
          :description="book.description"
    ></BookCard>
      </li>
    </ul>
    
  </div>
</template>

<style lang="scss" scoped>
.search-view {
    padding: 40px;
}

.search-view__list{
  display: flex;
  gap: 24px;
}
</style>

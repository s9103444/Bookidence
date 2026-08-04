<script setup>
  import BookCard from '@/components/front/BookCard.vue';
  import littlePrinceCover from '@/assets/images/little-prince-cover.png';
  import { Carousel, Slide } from 'vue3-carousel';
  import 'vue3-carousel/carousel.css';
  import{ref} from 'vue';
  import AppIcon from '@/components/common/AppIcon.vue';
  import SectionTitle from '@/components/front/SectionTitle.vue';

  const carouselRef= ref(null);
  function goPrev(){
    carouselRef.value.prev();
  }

  function goNext(){
    carouselRef.value.next();
  }


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
  },
  {
    id: 4,
    title: '北歐時間：世界第一幸福國度教會我的事2',
    author: '詹姆斯・克利爾',
    categories: ['心理成長', '習慣養成'],
    description: '細微改變帶來巨大成就的實踐法則，幫助你建立好習慣，打破壞習慣。',
    cover: littlePrinceCover
  },
  {
    id: 5,
    title: '深度工作力：淺薄時代，個人成功的關鍵能力2',
    author: '卡爾・紐波特',
    categories: ['職場工作', '自我管理'],
    description: '在分心時代重新奪回專注力，用深度工作創造無可取代的價值。',
    cover: littlePrinceCover
  },
  {
    id: 6,
    title: '被討厭的勇氣：自我啟發之父阿德勒的教導2',
    author: '岸見一郎',
    categories: ['心理成長', '人際關係'],
    description: '所有煩惱都來自人際關係，阿德勒心理學帶你找回選擇人生的自由。',
    cover: littlePrinceCover
  },
  {
    id: 7,
    title: '北歐時間：世界第一幸福國度教會我的事3',
    author: '詹姆斯・克利爾',
    categories: ['心理成長', '習慣養成'],
    description: '細微改變帶來巨大成就的實踐法則，幫助你建立好習慣，打破壞習慣。',
    cover: littlePrinceCover
  },
  {
    id: 8,
    title: '深度工作力：淺薄時代，個人成功的關鍵能力3',
    author: '卡爾・紐波特',
    categories: ['職場工作', '自我管理'],
    description: '在分心時代重新奪回專注力，用深度工作創造無可取代的價值。',
    cover: littlePrinceCover
  },
];
</script>
<template>
  <div class="search-view">
    <div class="search-view__header">
      <SectionTitle>推薦好書</SectionTitle>
      <div class="carousel-nav">
        <button type="button" class="carousel-nav__btn" aria-label="上一頁" @click="goPrev">
          <AppIcon name="arrow-left"></AppIcon>
        </button>
        <button type="button" class="carousel-nav__btn" aria-label="下一頁" @click="goNext">
          <AppIcon name="arrow-right"></AppIcon>
        </button>
      </div>
    </div>
    <Carousel
    ref="carouselRef"
    :items-to-show="4"
    :items-to-scroll="4"
    :gap="24"
    :wrap-around="true"
    snap-align="start">
      <Slide v-for="book in books" :key="book.id">
        <BookCard
          :book-id="book.id"
          :cover-image="book.cover"
          :title="book.title"
          :author="book.author"
          :categories="book.categories"
          :description="book.description">
        </BookCard>
      </Slide>
    </Carousel>
    
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;

.search-view {
    padding: 40px;
}

.search-view__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.carousel-nav {
    display: flex;
    gap: 8px;
}

.carousel-nav__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 1px solid $primary;
    border-radius: $btn-radius-rnd;
    background-color: transparent;
    color: $primary;
    cursor: pointer;
}


</style>

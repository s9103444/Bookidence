<script setup>
  import BookCard from '@/components/front/BookCard.vue';
  import littlePrinceCover from '@/assets/images/little-prince-cover.png';
  import { Carousel, Slide } from 'vue3-carousel';
  import 'vue3-carousel/carousel.css';
  import{ref} from 'vue';
  import AppIcon from '@/components/common/AppIcon.vue';
  import SectionTitle from '@/components/front/SectionTitle.vue';
  import SearchBar from '@/components/common/SearchBar.vue';
  import AppButton from '@/components/common/AppButton.vue';
  import recommendBookImage from '@/assets/images/recommend-book.png';

  const keyword = ref('');

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

// 申請推薦好書的三個步驟。
// 設計稿上第二、三筆的說明文字跟圖示對不上（打勾配「供人閱讀」、書本配「我們會評估」），
// 且第二筆標題與第一筆重複，這裡先依圖示順序修正，第三筆標題「加入書庫」待設計師確認。
const applySteps = [
  { icon: 'form-edit', iconSize: 28, title: '申請填寫表單', desc: '告訴我們，你想推薦的書籍' },
  { icon: 'check-circle', iconSize: 33, title: '審核評估', desc: '我們會評估書籍的內容與價值' },
  { icon: 'book', iconSize: 28, title: '加入書庫', desc: '通過後將供所有會員閱讀' },
];

const breakpoints = {
        768:  { itemsToShow: 2, itemsToScroll: 2 },
        1024:  { itemsToShow: 3, itemsToScroll: 3 },
        1440: { itemsToShow: 4, itemsToScroll: 4 },
        };
</script>
<template>
  <div class="search-view">
    <header class="search-view__intro">
      <h1 class="search-view__title">搜索圖書</h1>
      <p class="search-view__subtitle">探索更多好書</p>
    </header>

    <div class="search-view__search">
      <SearchBar v-model="keyword" size="md" color="neutral" placeholder="搜尋書名、作者、ISBN或關鍵字"></SearchBar>
    </div>

    <div class="search-view__header">
      <SectionTitle>推薦好書</SectionTitle>
      <div class="carousel-nav">
        <button type="button" class="carousel-nav__btn" aria-label="上一頁" @click="goPrev">
          <AppIcon name="chevron-left" :size="16"></AppIcon>
        </button>
        <button type="button" class="carousel-nav__btn" aria-label="下一頁" @click="goNext">
          <AppIcon name="chevron-right" :size="16"></AppIcon>
        </button>
      </div>
    </div>
    <Carousel
    ref="carouselRef"
    :items-to-show="1"
    :items-to-scroll="1"
    :breakpoints="breakpoints"
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

    <section class="apply">
      <div class="apply__intro">
        <SectionTitle>申請推薦好書</SectionTitle>
        <p class="apply__subtitle">找不到想推薦的書嗎？填寫申請表單，我們會將它加入書庫！</p>
      </div>

      <div class="apply__content">
        <img class="apply__image" :src="recommendBookImage" alt="">

        <ul class="apply__steps">
          <li class="apply__step" v-for="step in applySteps" :key="step.desc">
            <span class="apply__step-icon">
              <AppIcon :name="step.icon" :size="step.iconSize"></AppIcon>
            </span>
            <div>
              <h3 class="apply__step-title">{{ step.title }}</h3>
              <p class="apply__step-desc">{{ step.desc }}</p>
            </div>
          </li>
        </ul>

        <AppButton class="apply__btn" color="primary" variant="outlined" size="lg">
          申請推薦書籍
        </AppButton>
      </div>
    </section>
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.search-view {
    max-width: 1440px; // 設計稿基準寬度，超寬螢幕內容鎖在這、兩側留白
    margin-inline: auto;
    padding: $spacing-xl;

    @include tablet {
        padding: $spacing-lg;
    }

    @include mobile {
        padding: $spacing-md;
    }
}

.search-view :deep(.carousel__slide){
    align-items: stretch;
}

.search-view__intro {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
}

.search-view__title {
    font-size: $h4-size;
    font-weight: $heading-weight;
    line-height: $heading-line-height;
    letter-spacing: $letter-spacing-base;
    color: $primary;
}

.search-view__subtitle {
    font-size: $p-lg-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-800;
}

.search-view__search {
    display: flex;
    margin-top: $spacing-lg;
}

.search-view__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: $spacing-xl;
    margin-bottom: $spacing-lg;
}

.carousel-nav {
    display: flex;
    gap: 14px;
}

// ---------- 申請推薦好書 ----------
.apply {
    display: flex;
    flex-direction: column;
    gap: 120px; // 設計稿標題區與內容區的間距
    margin-top: 120px;

    @include tablet {
        gap: $spacing-xl;
        margin-top: $spacing-xl;
    }
}

.apply__intro {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
}

.apply__subtitle {
    font-size: $p-lg-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-700;
}

.apply__content {
    display: flex;
    flex-direction: column;
    gap: $spacing-lg;
    width: 100%;
    max-width: 597px; // 設計稿內容區寬度
    margin-inline: auto;
}

.apply__image {
    width: 100%;
}

.apply__steps {
    display: flex;
    flex-direction: column;
    gap: $spacing-lg;
}

.apply__step {
    display: flex;
    align-items: flex-start;
    gap: $spacing-xxs;
}

.apply__step-icon {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    padding: $spacing-sm;
    color: $primary;
}

.apply__step-title {
    font-size: $h6-size;
    font-weight: $heading-weight;
    line-height: $heading-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-700;
}

.apply__step-desc {
    margin-top: $spacing-sm;
    font-size: $label-md-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-700;
}

.apply__btn {
    width: 100%;
    margin-top: $spacing-md;
}

.carousel-nav__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border: 1px solid $primary;
    border-radius: $btn-radius-rnd;
    background-color: transparent;
    color: $primary;
    cursor: pointer;
}


</style>

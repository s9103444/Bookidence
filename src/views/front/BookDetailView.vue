<script setup>
import SectionTitle from '@/components/front/SectionTitle.vue';
import GuildReadingCard from '@/components/front/GuildReadingCard.vue';
import BookReviewCard from '@/components/front/BookReviewCard.vue';
import AppIcon from '@/components/common/AppIcon.vue';
import AppModal from '@/components/common/AppModal.vue';
import ReportReviewForm from '@/components/front/ReportReviewForm.vue';

import guildBackground from '@/assets/images/guild/guildBackground.png';
import {ref,computed} from 'vue';
import { useRoute } from 'vue-router';
import { books, getBookById } from '@/data/books';

const route = useRoute();

// 這頁顯示哪一本，看網址上的 id（/books/3 就是第 3 本）。
// 書單是三頁共用的假資料，等後端 API 好了再換成打 API。
// 網址上的 id 亂打時先退回第一本，免得整頁空白噴錯。
const book = computed(() => getBookById(route.params.id) || books[0]);

const guilds = computed(() => [
  { id: 1, name: '文青小時光', image: guildBackground, currentBook: book.value.title, memberCount: 80, location: '線上' },
  { id: 2, name: '晨讀俱樂部', image: guildBackground, currentBook: book.value.title, memberCount: 124, location: '線上' },
  { id: 3, name: '慢生活讀書會', image: guildBackground, currentBook: book.value.title, memberCount: 56, location: '台北市' },
  { id: 4, name: '週末書桌', image: guildBackground, currentBook: book.value.title, memberCount: 92, location: '線上' },
]);

const reviews = computed(() => book.value.reviews);

// 心得篩選的三個選項。
const reviewFilters = [
  { value: 'latest', label: '最新心得' },
  { value: 'all', label: '所有心得' },
  { value: 'top', label: '最高心得' },
];

const activeFilter = ref('latest');
const isCollected= ref(false);

const likeIds=ref(JSON.parse(localStorage.getItem('likedReviews')||'[]'));
function togglelike(reviewId){
  if(likeIds.value.includes(reviewId)){
    likeIds.value=likeIds.value.filter(id=>id !==reviewId);
  }else{
    likeIds.value=[...likeIds.value,reviewId];
  }
  localStorage.setItem('likedReviews',JSON.stringify(likeIds.value));
}

const displayReviews=computed(()=>{
  if(activeFilter.value==='latest'){
    return [...reviews.value].sort((a,b)=>new Date(b.createdAt)-new Date(a.createdAt));
  }
  if(activeFilter.value==='top'){
    return [...reviews.value].sort((a,b)=>b.likeCount-a.likeCount);
  }
  return reviews.value;
})

// 檢舉彈窗。開關和「正在檢舉哪一則」分成兩個變數：一個管顯示，一個管內容。
// 只用一個開關的話，彈窗打開後不知道要顯示哪個人的名字（頁面上有五顆旗子）。
const isReportOpen = ref(false);
const reportTarget = ref(null);

function openReport(review){
  reportTarget.value=review;
  isReportOpen.value=true;
}

// 還沒有後端，先印出來確認資料對不對。
// 之後這裡會改成打 API，把資料存進 report 表。
const reportedIds=ref(JSON.parse(localStorage.getItem('reportedReviews')||'[]'));

function handleReportSubmit(payload){
  console.log('送出檢舉',{
    reviewId:reportTarget.value.id,
    reportedUserCode:reportTarget.value.userCode,
    ...payload,
  });
  //檢舉過不再顯示
  if(!reportedIds.value.includes(reportTarget.value.id)){
    reportedIds.value=[...reportedIds.value,reportTarget.value.id];
    localStorage.setItem('reportedReviews',JSON.stringify(reportedIds.value));
  }
  isReportOpen.value=false;
}
</script>

<template>
  <div class="book-detail">
    <!-- ---------- 書籍主資訊 ---------- -->
    <section class="book-hero">
      <img class="book-hero__cover" :src="book.cover" :alt="book.title">

      <div class="book-hero__info">
        <h1 class="book-hero__title">{{ book.title }}</h1>

        <ul class="book-hero__meta">
          <li>作者：{{ book.author }}</li>
          <li>譯者：{{ book.translator }}</li>
          <li>出版日期：{{ book.publishDate }}</li>
          <li>出版社：{{ book.publisher }}</li>
          <li>ISBN：{{ book.isbn }}</li>
        </ul>

        <ul class="book-hero__stats">
          <li>
            <AppIcon name="user" :size="20"></AppIcon>
            <span>{{ book.reviewCount }}人寫過心得</span>
          </li>
          <li>
            <!-- 愛心跟下面「加入我的藏書」按鈕同一顆，把數字與按鈕串起來 -->
            <AppIcon name="heart" :size="20"></AppIcon>
            <span>{{ book.collectCount }}人加入藏書</span>
          </li>
        </ul>

        <button
        type="button"
        class="book-hero__collect"
        :class="{'book-hero__collect--active':isCollected}"
        @click="isCollected=!isCollected">
          <AppIcon :name="isCollected?'heart-filled':'heart'" :size="24"></AppIcon>
          {{isCollected?'已加入藏書':'加入我的藏書' }}
        </button>
      </div>
    </section>

    <!-- ---------- 書籍介紹 ---------- -->
    <section class="book-section">
      <SectionTitle>書籍介紹</SectionTitle>
      <div class="book-intro">
        <p v-for="(paragraph, index) in book.description" :key="index">
          {{ paragraph }}
        </p>
      </div>
    </section>

    <!-- ---------- 這個公會正在讀 ---------- -->
    <section class="book-section">
      <SectionTitle>這些公會正在讀...</SectionTitle>
      <div class="guild-reading">
        <GuildReadingCard
          v-for="guild in guilds"
          :key="guild.id"
          :image="guild.image"
          :name="guild.name"
          :current-book="guild.currentBook"
          :member-count="guild.memberCount"
          :location="guild.location">
        </GuildReadingCard>
      </div>
    </section>

    <!-- ---------- 書籍心得公開區 ---------- -->
    <section class="book-section">
      <SectionTitle>書籍心得公開區</SectionTitle>

      <div class="review-filter">
        <button
          v-for="filter in reviewFilters"
          :key="filter.value"
          type="button"
          class="review-filter__btn"
          :class="{ 'review-filter__btn--active': filter.value === activeFilter }"
          @click="activeFilter=filter.value">
          {{ filter.label }}
        </button>
      </div>

      <div class="review-list">
        <template v-for="review in displayReviews" :key="review.id">
          <p v-if="reportedIds.includes(review.id)" class="review-list__hidden">
            這則心得已檢舉 不再顯示
          </p>

          <BookReviewCard
            v-else
            :avatar="review.avatar"
            :username="review.username"
            :date="review.date"
            :content="review.content"
            :like-count="review.likeCount"
            :is-liked="likeIds.includes(review.id)"
            @like="togglelike(review.id)"
            @report="openReport(review)">
          </BookReviewCard>
        </template>
      </div>
    </section>

    <!-- ---------- 檢舉彈窗 ---------- -->
    <!-- reportTarget 一開始是 null，用 ?. 避免跟不存在的東西要名字而報錯 -->
    <AppModal v-model="isReportOpen" title="檢舉申請">
      <ReportReviewForm
        :reported-name="reportTarget?.username"
        :reported-id="reportTarget?.userCode"
        @submit="handleReportSubmit" />
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.book-detail {
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

// 四個大區塊之間的距離
.book-detail > * + * {
  margin-top: 80px;

  @include tablet {
    margin-top: $spacing-xl;
  }
}

// 「標題 + 內容」的通用排法，書籍介紹／公會／心得三區共用
.book-section {
  display: flex;
  flex-direction: column;
  gap: $spacing-xl;
}

// ---------- 書籍主資訊 ----------
.book-hero {
  display: flex;
  gap: $spacing-xl;

  @include desktop {
    gap: 76px; // 設計稿封面與資訊的間距，寬螢幕才放得下
  }

  @include tablet {
    flex-direction: column;
    align-items: center;
    gap: $spacing-lg;
  }
}

.book-hero__cover {
  flex-shrink: 0;
  width: 280px;
  height: 400px;
  border-radius: 4px;
  object-fit: cover;

  @include mobile {
    width: 200px;
    height: 286px; // 跟 280×400 同比例
  }
}

.book-hero__info {
  display: flex;
  flex-direction: column;
  gap: 28px; // 設計稿資訊區各段落的間距
  min-width: 0; // 長書名才能正常換行，不會把版面撐寬
}

.book-hero__title {
  font-size: $h5-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
}

.book-hero__meta {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
  font-size: $p-sm-size;
  color: $neutral-700;
}

.book-hero__stats {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
  font-size: $p-sm-size;
  color: $neutral-700;

  li {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  // 圖示用 currentColor，會自動跟著上面的文字顏色走
  svg {
    flex-shrink: 0;
  }
}

.book-hero__collect {
  display: flex;
  align-items: center;
  align-self: flex-start; // 按鈕只要跟文字一樣寬，不要撐滿整欄
  gap: 12px;
  padding: $spacing-sm 20px;
  border: 1px solid $neutral-500;
  border-radius: $btn-radius-std;
  background-color: transparent;
  font-size: $p-sm-size;
  color: $neutral-800;
  cursor: pointer;

  // 愛心用主色，跟灰色外框做出對比
  svg {
    flex-shrink: 0;
    color: $primary-300;
  }

  &:hover {
    border-color: $primary;
    color: $primary;
  }
}
//加入藏書的狀態
.book-hero__collect--active {
  border-color: $primary;
  color: $primary;

  svg {
    color: $primary;
  }
}

// ---------- 書籍介紹 ----------
.book-intro {
  font-size: $p-sm-size;
  font-weight: $text-weight;
  line-height: 2; 
  letter-spacing: $letter-spacing-base;
  color: $neutral-600;
}

// ---------- 這個公會正在讀 ----------
.guild-reading {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: $spacing-lg;

  @include tablet {
    grid-template-columns: 1fr;
  }
}

// ---------- 書籍心得公開區 ----------
.review-filter {
  display: flex;
  flex-wrap: wrap;
  gap: $spacing-md;

  @include mobile {
    gap: $spacing-sm;
  }
}

.review-filter__btn {
  padding: $spacing-xs $spacing-lg;
  border: 1px solid $primary-300;
  border-radius: $btn-radius-rnd;
  background-color: transparent;
  font-size: $p-xs-size;
  line-height: 24px;
  color: $primary-300;
  cursor: pointer;

  // 手機版：橫向 padding 從 24 縮到 10，三顆才排得下一行；
  // 縱向從 4 加到 10，把點擊區從 32px 撐到 44px（手指的建議最小值）。
  // flex: 1 讓三顆等分整條寬度，比全部擠在左邊整齊。
  @include mobile {
    flex: 1;
    padding: 10px;
  }
}

.review-filter__btn--active {
  background-color: $primary-300;
  color: $neutral-100;
}

.review-list {
  display: flex;
  flex-direction: column;
  gap: $spacing-lg;
}

// 被自己檢舉過的心得，原地換成這一行。
// 不直接讓卡片消失是刻意的：位置不動，使用者不會突然找不到剛剛看到哪，
// 而且這一行本身就是回饋，不需要再另外做提示訊息。
.review-list__hidden {
  padding: $spacing-md;
  border-radius: $btn-radius-std;
  background-color: $neutral-200;
  font-size: $p-xs-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-500;
}
</style>

<script setup>
import SectionTitle from '@/components/front/SectionTitle.vue';
import GuildReadingCard from '@/components/front/GuildReadingCard.vue';
import BookReviewCard from '@/components/front/BookReviewCard.vue';
import AppIcon from '@/components/common/AppIcon.vue';
import AppModal from '@/components/common/AppModal.vue';
import ReportReviewForm from '@/components/front/ReportReviewForm.vue';

import bookCover from '@/assets/images/little-prince-cover.png';
import guildBackground from '@/assets/images/guild/guildBackground.png';
import guildAvatar from '@/assets/images/guild/guildAvatar.png';
import girlAvatar from '@/assets/images/guild/girl.png';
import boyAvatar from '@/assets/images/guild/boy.png';
import {ref,computed} from 'vue';


// 以下都是假資料，等後端 API 好了再換掉。
// 之後這頁要用網址上的 id（/books/:id）去跟後端要這本書的資料。
const book = {
  title: '北歐時間：世界第一幸福國度教會我的事',
  author: '日暮 Inko',
  translator: '林美琪',
  publishDate: '2025/10/29',
  publisher: '幸福文化',
  isbn: '000-0000000000',
  cover: bookCover,
  reviewCount: 200,
  collectCount: 328,
  description: [
    '在充斥著高效與忙碌的現代生活中，我們是否遺失了生活的本質？《北歐時間：世界第一幸福國度教會我的事》帶領讀者走進全球幸福指數最高的國度，探索北歐人不慌不忙的「時間美學」。',
    '本書分享了北歐人如何在高效率與慢節奏之間，取得工作與生活的完美平衡。從擁抱自然的放鬆、珍惜與家人相處的Hygge（舒適溫馨），到凡事追求剛剛好的Lagom（中庸哲學）。這不僅是一本文化觀察，更是一份生活提案，教我們在喧囂的日常中放慢腳步，重新找回屬於自己的幸福步調。',
  ],
};

const guilds = [
  { id: 1, name: '文青小時光', image: guildBackground, currentBook: '北歐時間：世界第一幸福國度教會我的事', memberCount: 80, location: '線上' },
  { id: 2, name: '晨讀俱樂部', image: guildBackground, currentBook: '北歐時間：世界第一幸福國度教會我的事', memberCount: 124, location: '線上' },
  { id: 3, name: '慢生活讀書會', image: guildBackground, currentBook: '北歐時間：世界第一幸福國度教會我的事', memberCount: 56, location: '台北市' },
  { id: 4, name: '週末書桌', image: guildBackground, currentBook: '北歐時間：世界第一幸福國度教會我的事', memberCount: 92, location: '線上' },
];

// createdAt 是給之後排序用的（「最新評論」要比日期、「最高評論」要比 likeCount），
// date 則是畫面上直接顯示的字串。
// userCode 是顯示給人看的會員編號（檢舉彈窗要顯示），不是資料庫的 user_id。
// 之後接 API 時兩個都要有：編號給人看，user_id 存進 report 表的 reported_user_id。
const reviews = [
  { id: 3, username: 'reading_cat', userCode: 'BKD00312', avatar: guildAvatar, date: 'Jun 20, 2026 8:03 PM', createdAt: '2026-06-20T20:03:00', likeCount: 12, content: '文筆很舒服，配圖也很療癒。比較可惜的是後半段有些論點重複，如果能再多一點實際案例會更好。' },
  { id: 1, username: 'Lora2412545', userCode: 'BKD00246', avatar: girlAvatar, date: 'Jul 01, 2026 3:41 PM', createdAt: '2026-07-01T15:41:00', likeCount: 20, content: '這本書溫柔地敲醒了被時間追趕的我們。作者透過北歐的「放慢」哲學，讓人重新思考工作與生活的本質。它不只是文化觀察，更是一劑實用的心靈解藥，提醒我們：幸福不在於填滿日程，而是在剛剛好的日常裡，留出心靈的空白。' },
  { id: 2, username: 'Kevin_0912', userCode: 'BKD00187', avatar: boyAvatar, date: 'Jun 28, 2026 9:12 AM', createdAt: '2026-06-28T09:12:00', likeCount: 45, content: '讀完最大的感想是「原來慢下來不是懶惰」。書中提到的 Lagom 觀念徹底改變我安排一天的方式，現在會刻意留白，反而更有效率。' },
  { id: 4, username: 'Amy.chen', userCode: 'BKD00421', avatar: girlAvatar, date: 'Jun 15, 2026 11:27 AM', createdAt: '2026-06-15T11:27:00', likeCount: 63, content: '推薦給每個覺得「時間永遠不夠用」的人。它不會告訴你怎麼把行程塞得更滿，而是讓你重新問自己：這些行程真的都必要嗎？' },
  { id: 5, username: 'slowmorning', userCode: 'BKD00098', avatar: boyAvatar, date: 'Jun 02, 2026 7:15 AM', createdAt: '2026-06-02T07:15:00', likeCount: 8, content: '把 Hygge 那一章讀了三遍。作者描述北歐冬天窩在家裡點蠟燭的段落，光是看文字就覺得暖起來了。' },
];

// 心得篩選的三個選項。
const reviewFilters = [
  { value: 'latest', label: '最新評論' },
  { value: 'all', label: '所有評論' },
  { value: 'top', label: '最高評論' },
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
    return [...reviews].sort((a,b)=>new Date(b.createdAt)-new Date(a.createdAt));
  }
  if(activeFilter.value==='top'){
    return [...reviews].sort((a,b)=>b.likeCount-a.likeCount);
  }
  return reviews;
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
function handleReportSubmit(payload){
  console.log('送出檢舉',{
    reviewId:reportTarget.value.id,
    reportedUserCode:reportTarget.value.userCode,
    ...payload,
  });
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
            <span>{{ book.reviewCount }}人評論</span>
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
        <BookReviewCard
          v-for="review in displayReviews"
          :key="review.id"
          :avatar="review.avatar"
          :username="review.username"
          :date="review.date"
          :content="review.content"
          :like-count="review.likeCount"
          :is-liked="likeIds.includes(review.id)"
          @like="togglelike(review.id)"
          @report="openReport(review)">
        </BookReviewCard>
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
</style>

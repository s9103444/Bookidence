<script setup>
import SectionTitle from '@/components/front/SectionTitle.vue';
import GuildReadingCard from '@/components/front/GuildReadingCard.vue';
import BookReviewCard from '@/components/front/BookReviewCard.vue';
import AppIcon from '@/components/common/AppIcon.vue';
import AppModal from '@/components/common/AppModal.vue';
import AppButton from '@/components/common/AppButton.vue';
import ReportReviewForm from '@/components/front/ReportReviewForm.vue';

import {ref,computed,onMounted,watch} from 'vue';
import { useRoute } from 'vue-router';
import { API_BASE, API_STATIC } from '@/common/api.js';
import { resolveImageUrl } from '@/common/image.js';
import defaultAvatar from '@/assets/images/guild/guildAvatar.png';
import { useUserStore } from '@/stores/user.js';
import { useBookStore } from '@/stores/book.js';

const route = useRoute();
const userStore = useUserStore();
const bookStore = useBookStore();

const book = ref(null);
const reviews = ref([]);
const guilds = ref([]);
const loading = ref(true);
const loadError = ref('');

function coverUrlOf(path) {
  return path ? `${API_STATIC}/uploads/${path}` : null;
}

function toBook(row, categories) {
  return {
    id: row.book_id,
    title: row.title,
    author: row.author,
    publisher: row.publisher,
    publishDate: row.p_date,
    isbn: row.isbn,
    cover: coverUrlOf(row.bc_image),
    categories,
    // 資料庫存的是一整段文字，用換行切成陣列，模板才畫得出一段一段的 <p>
    description: (row.description ?? '').split('\n').filter((p) => p.trim()),
    reviewCount: row.reviewCount ?? null,
    collectCount: row.collectCount ?? null,
  };
}

function toGuild(row) {
  return {
    id: row.guild_id,
    name: row.guild_name,
    image: resolveImageUrl(row.guild_avatar, defaultAvatar),
    currentBook: row.title,
    memberCount: Number(row.member_count),
  };
}

function toReview(row) {
  return {
    id: row.b_thought_id,
    username: row.nickname,
    userCode: row.member_code,
    avatar: resolveImageUrl(row.avatar_url, defaultAvatar),
    date: row.updated_at,
    content: row.bth_content,
    likeCount: 0,
  };
}

async function fetchBook() {
  loading.value = true;
  loadError.value = '';

  const headers = userStore.token ? { Authorization: `Bearer ${userStore.token}` } : {};

  try {
    const [bookRes, reviewRes, guildRes] = await Promise.all([
      fetch(`${API_BASE}/get_book_detail.php?book_id=${route.params.id}`),
      fetch(`${API_BASE}/book_thoughts_list.php?book_id=${route.params.id}`, { headers }),
      fetch(`${API_BASE}/guilds_reading_book.php?book_id=${route.params.id}`),
    ]);

    const bookResult = await bookRes.json();

    if (!bookResult.success) {
      loadError.value = bookResult.message || '找不到這本書';
      book.value = null;
      reviews.value = [];
      guilds.value = [];
      return;
    }

    book.value = toBook(bookResult.book, bookResult.categories ?? []);

    const reviewResult = await reviewRes.json();
    reviews.value = reviewResult.success ? reviewResult.data.map(toReview) : [];

    const guildResult = await guildRes.json();
    guilds.value = guildResult.success ? guildResult.data.map(toGuild) : [];

    fetchCollected();
  } catch (e) {
    console.error('[書籍詳情]', e);
    loadError.value = '載入失敗，請稍後再試';
    book.value = null;
    reviews.value = [];
    guilds.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(fetchBook);

// 從一本書的詳情頁點到另一本時，元件不會重建，只有網址變，所以要自己重撈
watch(() => route.params.id, fetchBook);

// 心得的排序方式。
const reviewFilters = [
  { value: 'latest', label: '最新' },
  { value: 'oldest', label: '最舊' },
  { value: 'top', label: '最多讚' },
];

const activeFilter = ref('latest');
const isCollected = ref(false);
const collectBusy = ref(false);
const collectError = ref('');
const isLoginPromptOpen = ref(false);
const loginPromptText = ref('');

function openLoginPrompt(text) {
  loginPromptText.value = text;
  isLoginPromptOpen.value = true;
}

// 沒有「這本書我收藏了嗎」的單筆 API，所以撈整份藏書清單再比對。
// 資料量小（一個人幾十本），值得省下一支新 API
async function fetchCollected() {
  collectError.value = '';

  if (!userStore.token) {
    isCollected.value = false;
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/my_book.php`, {
      headers: { Authorization: `Bearer ${userStore.token}` },
    });
    const result = await res.json();
    const myBooks = result.data ?? [];
    isCollected.value = myBooks.some((row) => Number(row.book_id) === Number(route.params.id));
  } catch (e) {
    console.error('[藏書狀態]', e);
  }
}

async function toggleCollect() {
  if (collectBusy.value) return;

  if (!userStore.token) {
    openLoginPrompt('登入後就能收藏這本書，隨時在「我的藏書」找到它。');
    return;
  }

  collectBusy.value = true;
  collectError.value = '';

  const wasCollected = isCollected.value;

  try {
    const result = wasCollected
      ? await bookStore.removeCollection(book.value.id)
      : await bookStore.addCollection(book.value.id);

    if (!result.success) {
      collectError.value = result.message || '操作失敗，請稍後再試';
      return;
    }

    isCollected.value = !wasCollected;

    // 上面那個「N 人加入藏書」要跟著動，不然按了半天數字不變很怪
    if (typeof book.value.collectCount === 'number') {
      book.value.collectCount += wasCollected ? -1 : 1;
    }
  } catch (e) {
    console.error('[加入藏書]', e);
    collectError.value = '連線失敗，請稍後再試';
  } finally {
    collectBusy.value = false;
  }
}

const likeIds=ref(JSON.parse(localStorage.getItem('likedReviews')||'[]'));
function togglelike(reviewId){
  if(likeIds.value.includes(reviewId)){
    likeIds.value=likeIds.value.filter(id=>id !==reviewId);
  }else{
    likeIds.value=[...likeIds.value,reviewId];
  }
  localStorage.setItem('likedReviews',JSON.stringify(likeIds.value));
}

// 讚數要跟卡片上顯示的那個數字用同一條公式算，
// 不然排序讀到的值跟畫面看到的對不起來
function likeScore(review){
  return review.likeCount+(likeIds.value.includes(review.id)?1:0);
}

const displayReviews=computed(()=>{
  const sorted=[...reviews.value].sort((a,b)=>new Date(b.date)-new Date(a.date));

  if(activeFilter.value==='oldest') return sorted.reverse();
  if(activeFilter.value==='top') return sorted.sort((a,b)=>likeScore(b)-likeScore(a));
  return sorted;
})

// 檢舉彈窗。開關和「正在檢舉哪一則」分成兩個變數：一個管顯示，一個管內容。
// 只用一個開關的話，彈窗打開後不知道要顯示哪個人的名字（頁面上有五顆旗子）。
const isReportOpen = ref(false);
const reportTarget = ref(null);

function openReport(review){
  if(!userStore.token){
    openLoginPrompt('登入後才能檢舉心得，這樣管理員才知道是誰送出的。');
    return;
  }
  reportTarget.value=review;
  reportDone.value=false;
  reportError.value='';
  isReportOpen.value=true;
}

const reportedIds=ref(JSON.parse(localStorage.getItem('reportedReviews')||'[]'));

// 送出成功就翻成 true，彈窗改顯示成功畫面
const reportDone=ref(false);
const reportError=ref('');
const reportBusy=ref(false);

async function handleReportSubmit(payload){
  if(reportBusy.value) return;
  reportBusy.value=true;
  reportError.value='';

  try{
    const res=await fetch(`${API_BASE}/book_thought_report.php`,{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        Authorization:`Bearer ${userStore.token}`,
      },
      body:JSON.stringify({
        b_thought_id:reportTarget.value.id,

        reason:payload.reason,

        reason_detail:payload.detail,
      }),
    });

    const result=await res.json();

    if(!result.success){
      reportError.value=result.message||'檢舉失敗，請稍後再試。';
      return;
    }

    reportDone.value=true;

    //檢舉過不再顯示
    if(!reportedIds.value.includes(reportTarget.value.id)){
      reportedIds.value=[...reportedIds.value,reportTarget.value.id];
      localStorage.setItem('reportedReviews',JSON.stringify(reportedIds.value));
    }
  }catch(e){
    console.error('[檢舉心得]',e);
    reportError.value='連線失敗，請檢查網路後再試一次。';
  }finally{
    reportBusy.value=false;
  }
}
</script>

<template>
  <div class="book-detail">
    <p v-if="loading" class="book-detail__state">載入中…</p>

    <p v-else-if="loadError" class="book-detail__state">{{ loadError }}</p>

    <template v-else-if="book">
    <!-- ---------- 書籍主資訊 ---------- -->
    <section class="book-hero">
      <img class="book-hero__cover" :src="book.cover" :alt="book.title">

      <div class="book-hero__info">
        <h1 class="book-hero__title">{{ book.title }}</h1>

        <ul class="book-hero__meta">
          <li>作者：{{ book.author }}</li>
          <li>出版日期：{{ book.publishDate }}</li>
          <li>出版社：{{ book.publisher }}</li>
          <li>ISBN：{{ book.isbn }}</li>
        </ul>

        <ul class="book-hero__stats">
          <li v-if="book.reviewCount !== null">
            <AppIcon name="user" :size="20"></AppIcon>
            <span>{{ book.reviewCount }}人寫過心得</span>
          </li>
          <li v-if="book.collectCount !== null">
            <!-- 愛心跟下面「加入我的藏書」按鈕同一顆，把數字與按鈕串起來 -->
            <AppIcon name="heart" :size="20"></AppIcon>
            <span>{{ book.collectCount }}人加入藏書</span>
          </li>
        </ul>

        <button
        type="button"
        class="book-hero__collect"
        :class="{'book-hero__collect--active':isCollected}"
        :disabled="collectBusy"
        @click="toggleCollect">
          <AppIcon :name="isCollected?'heart-filled':'heart'" :size="24"></AppIcon>
          {{isCollected?'已加入藏書':'加入我的藏書' }}
        </button>

        <p v-if="collectError" class="book-hero__collect-error" role="alert">{{ collectError }}</p>
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
      <div class="guild-reading" v-if="guilds.length">
        <GuildReadingCard
          v-for="guild in guilds"
          :key="guild.id"
          :image="guild.image"
          :name="guild.name"
          :current-book="guild.currentBook"
          :member-count="guild.memberCount">
        </GuildReadingCard>
      </div>

      <div class="section-empty" v-else>
        <p class="section-empty__text">還沒有公會在讀這本書，創一個來揪人共讀吧。</p>
        <AppButton
          :to="userStore.token ? { name: 'create-guilds' } : null"
          @click="!userStore.token && openLoginPrompt('登入後就能創建自己的讀書公會，揪大家一起讀這本書。')"
        >
          創建讀書公會
        </AppButton>
      </div>
    </section>

    <!-- ---------- 書籍心得公開區 ---------- -->
    <section class="book-section">
      <SectionTitle>書籍心得公開區</SectionTitle>

      <div class="review-filter" v-if="reviews.length">
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

      <div class="section-empty" v-if="!reviews.length">
        <p class="section-empty__text">還沒有人寫下這本書的心得，來當第一個吧。</p>
        <AppButton
          :to="userStore.token ? { name: 'study' } : null"
          @click="!userStore.token && openLoginPrompt('登入後就能在自己的書房寫下心得，公開給其他人看。')"
        >
          去我的書房寫
        </AppButton>
      </div>

      <div class="review-list" v-else>
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

    </template>

    <AppModal v-model="isLoginPromptOpen" title="請先登入">
      <p class="login-prompt__text">{{ loginPromptText }}</p>

      <div class="login-prompt__actions">
        <AppButton :to="{ name: 'login' }">前往登入</AppButton>
      </div>
    </AppModal>

    <!-- ---------- 檢舉彈窗 ---------- -->
    <!-- reportTarget 一開始是 null，用 ?. 避免跟不存在的東西要名字而報錯 -->
    <AppModal v-model="isReportOpen" :title="reportDone ? '檢舉已送出' : '檢舉申請'">
      <template v-if="reportDone">
        <p class="report-done__text">管理員會盡快查看你的檢舉。</p>

        <div class="report-done__actions">
          <AppButton @click="isReportOpen = false">關閉</AppButton>
        </div>
      </template>

      <template v-else>
        <p v-if="reportError" class="report-error">{{ reportError }}</p>

        <ReportReviewForm
          :reported-name="reportTarget?.username"
          :reported-id="reportTarget?.userCode"
          @submit="handleReportSubmit" />
      </template>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.book-detail__state {
  padding: $spacing-xl 0;
  text-align: center;
  color: $neutral-500;
}

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

.section-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: $spacing-md;
  padding: $spacing-xl 0;
}

.section-empty__text {
  font-size: $p-md-size;
  line-height: $text-line-height;
  color: $neutral-600;
}

.login-prompt__text {
  text-align: center;
  font-size: $p-md-size;
  line-height: $text-line-height;
  color: $neutral-700;
}

.login-prompt__actions {
  display: flex;
  justify-content: center;
  margin-top: $spacing-lg;
}

.report-done__text {
  text-align: center;
  font-size: $p-md-size;
  line-height: $text-line-height;
  color: $neutral-700;
}


.report-done__actions {
  display: flex;
  justify-content: center;
  margin-top: $spacing-lg;
}

.report-error {
  margin-bottom: $spacing-md;
  font-size: $p-sm-size;
  color: $color-danger;
}

.book-hero__collect-error {
  margin-top: $spacing-sm;
  font-size: $p-sm-size;
  color: $color-danger;
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
  transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;

  &:hover,
  &:focus-visible {
    background-color: $primary-100;
    border-color: $primary-500;
    color: $primary-500;
  }

  &:focus-visible {
    outline: 2px solid $primary-300;
    outline-offset: 2px;
  }

  @media (prefers-reduced-motion: reduce) {
    transition: none;
  }

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

  &:hover,
  &:focus-visible {
    background-color: $primary-500;
    border-color: $primary-500;
    color: $neutral-100;
  }
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

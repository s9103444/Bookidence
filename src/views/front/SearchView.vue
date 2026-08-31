<script setup>
  import BookCard from '@/components/front/BookCard.vue';
  import { Carousel, Slide } from 'vue3-carousel';
  import 'vue3-carousel/carousel.css';
  import{ref,computed,onMounted} from 'vue';
  import { useRouter } from 'vue-router';
  import { API_BASE } from '@/common/api.js';
  import { resolveImageUrl } from '@/common/image.js';
  import AppIcon from '@/components/common/AppIcon.vue';
  import SectionTitle from '@/components/front/SectionTitle.vue';
  import SearchBar from '@/components/common/SearchBar.vue';
  import AppButton from '@/components/common/AppButton.vue';
  import recommendBookImage from '@/assets/images/recommend-book.png';

  const keyword = ref('');
  const router = useRouter();

  const books = ref([]);

  // 卡片上那句簡介：資料庫沒有這個欄位，改切書籍介紹的第一句。
  // 切句號不切字數，才不會斷在句子中間
  function summaryOf(description) {
    const text = (description ?? '').trim();
    if (!text) return '';

    const [first] = text.split('。');
    if (first.length > 45) return `${first.slice(0, 45)}…`;
    return text.includes('。') ? `${first}。` : first;
  }

  // 不帶 page 就是整個書庫，輪播本身就等於「瀏覽全部」
  async function fetchBooks() {
    try {
      const res = await fetch(`${API_BASE}/book_search.php?keyword=`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const result = await res.json();
      books.value = result.data.map((row) => ({
        id: row.book_id,
        title: row.title,
        author: row.author,
        cover: resolveImageUrl(row.bc_image, ''),
        // 沒有分類的書這欄是 null，直接 split 會爆
        categories: row.categories ? row.categories.split(',') : [],
        summary: summaryOf(row.description),
      }));
    } catch (e) {
      console.error('[推薦好書]', e);
      books.value = [];
    }
  }

  onMounted(fetchBooks);

  // 分類清單從書單推出來，所以不會出現「點進去一本書都沒有」的分類。
  // flatMap 把每本書的分類攤平成一長串，Set 去掉重複的
  const categories = computed(() =>
    [...new Set(books.value.flatMap((book) => book.categories))]
  );

  // 關鍵字放在網址上（/search/result?q=...），
  // 這樣結果頁可以被收藏、被分享，重整也還在。
  function submitSearch(){
    if(!keyword.value.trim()) return;
    router.push({ name: 'search-result', query: { q: keyword.value.trim() } });
  }

  const carouselRef= ref(null);
  function goPrev(){
    carouselRef.value.prev();
  }

  function goNext(){
    carouselRef.value.next();
  }



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

    <div class="search-view__search" @keyup.enter="submitSearch">
      <SearchBar v-model="keyword" size="md" color="neutral" placeholder="搜尋書名、作者或 ISBN"></SearchBar>
    </div>

    <nav class="category-grid" v-if="categories.length" aria-label="依分類瀏覽書籍">
      <RouterLink
        v-for="name in categories"
        :key="name"
        class="category-grid__card"
        :to="{ name: 'search-result', query: { q: name } }"
      >
        {{ name }}
      </RouterLink>
    </nav>

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
          :description="book.summary">
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

        <AppButton class="apply__btn" color="primary" variant="outlined" size="lg" to="/books/apply">
          申請推薦書籍
        </AppButton>
      </div>
    </section>
  </div>
</template>

<style lang="scss" scoped>

@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

// ---------- 依分類瀏覽（外框沿用註冊頁第三步的卡片）----------
.category-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: $spacing-sm;
  margin-top: $spacing-lg;
  margin-bottom: $spacing-xl;

  &__card {
    padding-block: $spacing-sm;
    border: 1px solid $neutral-400;
    border-radius: $btn-radius-std;
    color: $primary;
    font-weight: $heading-weight;
    text-align: center;
    transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;

    &:hover,
    &:focus-visible {
      background-color: $primary-300;
      border-color: $primary-300;
      color: $neutral-100;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
  }

  @include tablet {
    grid-template-columns: repeat(4, 1fr);
  }

  // 手機放不下 6 排，改成橫向滑一排。最右邊要露一截，使用者才知道可以滑
  @include mobile {
    display: flex;
    overflow-x: auto;
    padding-bottom: $spacing-sm;

    &__card {
      flex: 0 0 auto;
      padding-inline: $spacing-md;
    }
  }
}

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

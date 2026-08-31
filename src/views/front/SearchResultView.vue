<script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import SearchBar from '@/components/common/SearchBar.vue';
  import SectionTitle from '@/components/front/SectionTitle.vue';
  import AppButton from '@/components/common/AppButton.vue';
  import BookSearchResultCard from '@/components/front/BookSearchResultCard.vue';
  import AppPagination from '@/components/common/AppPagination.vue';
  import recommendBookImage from '@/assets/images/recommend-book.png';
  import { API_BASE } from '@/common/api.js';
  import { resolveImageUrl } from '@/common/image.js';

  const route = useRoute();
  const router = useRouter();

  const keyword = ref(route.query.q || '');

  // 網址列的關鍵字才是唯一依據。使用者按上一頁、或直接把網址貼給別人時，
  // 輸入框要跟著網址走，不然畫面上打的字跟列表結果會對不起來。
  watch(() => route.query.q, (q) => {
    keyword.value = q || '';
  });

  function submitSearch() {
    router.push({ name: 'search-result', query: { q: keyword.value.trim() } });
  }

  const results = ref([]);
  const total = ref(0);
  const perPage = ref(10);
  const loading = ref(false);
  const error = ref('');

  // 頁碼放網址上，重新整理和把連結貼給別人都還會停在同一頁
  const page = computed(() => Math.max(1, Number(route.query.page) || 1));

  const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)));

  // 卡片上那句簡介：資料庫沒有這個欄位，改切書籍介紹的第一句。
  // 切句號不切字數，才不會斷在句子中間
  function summaryOf(description) {
    const text = (description ?? '').trim();
    if (!text) return '';

    const [first] = text.split('。');
    if (first.length > 45) return `${first.slice(0, 45)}…`;
    return text.includes('。') ? `${first}。` : first;
  }

  function toBook(row) {
    return {
      id: row.book_id,
      title: row.title,
      author: row.author,
      publisher: row.publisher,
      publishDate: row.p_date,
      cover: resolveImageUrl(row.bc_image, ''),
      // 沒有分類的書這欄是 null，直接 split 會爆
      categories: row.categories ? row.categories.split(',') : [],
      summary: summaryOf(row.description),
    };
  }

  async function fetchResults() {
    loading.value = true;
    error.value = '';

    try {
      const params = new URLSearchParams({
        keyword: (route.query.q || '').trim(),
        page: page.value,
      });

      const res = await fetch(`${API_BASE}/book_search.php?${params}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const result = await res.json();
      results.value = result.data.map(toBook);
      total.value = result.total;
      perPage.value = result.perPage;
    } catch (e) {
      console.error('[搜尋結果]', e);
      error.value = '載入失敗，請稍後再試';
      results.value = [];
      total.value = 0;
    } finally {
      loading.value = false;
    }
  }

  onMounted(fetchResults);

  watch(() => [route.query.q, route.query.page], fetchResults);

  // 網址是使用者能亂打的（?page=99），或搜了新關鍵字剩兩頁卻還停在第 5 頁。
  // 撈回來才知道總共幾頁，所以是撈完之後才把頁碼收回來
  watch(totalPages, (value) => {
    if (page.value > value) {
      router.replace({ name: 'search-result', query: { ...route.query, page: value } });
    }
  });

  function goToPage(target) {
    router.push({ name: 'search-result', query: { ...route.query, page: target } });
    // 分頁器在整頁最下面，不捲回去的話換頁後會停在下一頁的最後一張卡片旁邊
    window.scrollTo({ top: 0 });
  }

  // 收藏狀態統一放在頁面上，卡片只負責顯示。
  // 存進 localStorage，重整之後才不會把已收藏的書變回沒收藏。
  const collectedIds = ref(JSON.parse(localStorage.getItem('collectedBooks') || '[]'));

  watch(collectedIds, (ids) => {
    localStorage.setItem('collectedBooks', JSON.stringify(ids));
  }, { deep: true });

  function toggleCollect(bookId) {
    const index = collectedIds.value.indexOf(bookId);
    if (index === -1) {
      collectedIds.value.push(bookId);
    } else {
      collectedIds.value.splice(index, 1);
    }
  }
</script>

<template>
  <div class="search-result-view">
    <h1 class="search-result-view__title">搜索內容</h1>

    <div class="search-result-view__search" @keyup.enter="submitSearch">
      <SearchBar v-model="keyword" size="md" color="neutral" placeholder="搜尋書名、作者或 ISBN"></SearchBar>
    </div>

    <section class="results">
      <h2 class="results__label">搜尋結果</h2>

      <p class="results__empty" v-if="loading" role="status">載入中…</p>

      <p class="results__empty" v-else-if="error" role="status">{{ error }}</p>

      <ul class="results__list" v-else-if="results.length">
        <li v-for="book in results" :key="book.id">
          <BookSearchResultCard
            :book-id="book.id"
            :cover-image="book.cover"
            :title="book.title"
            :author="book.author"
            :categories="book.categories"
            :publisher="book.publisher"
            :publish-date="book.publishDate"
            :description="book.summary"
            :is-collected="collectedIds.includes(book.id)"
            @toggle-collect="toggleCollect">
          </BookSearchResultCard>
        </li>
      </ul>

      <p class="results__empty" v-else role="status">
        找不到符合「{{ route.query.q }}」的書籍。
      </p>

      <AppPagination
        class="results__pagination"
        :current-page="page"
        :total-pages="totalPages"
        @change="goToPage">
      </AppPagination>
    </section>

    <section class="apply">
      <div class="apply__intro">
        <SectionTitle>申請推薦好書</SectionTitle>
        <p class="apply__subtitle">找不到想推薦的書嗎？填寫申請表單，我們會將它加入書庫！</p>
      </div>

      <img class="apply__image" :src="recommendBookImage" alt="">

      <AppButton class="apply__btn" color="primary" variant="outlined" size="lg" to="/books/apply">
        申請推薦書籍
      </AppButton>
    </section>
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.search-result-view {
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

.search-result-view__title {
    font-size: $h4-size;
    font-weight: $heading-weight;
    line-height: $heading-line-height;
    letter-spacing: $letter-spacing-base;
    color: $primary;
}

.search-result-view__search {
    display: flex;
    margin-top: $spacing-xl;

    @include mobile {
        margin-top: $spacing-lg;
    }
}

// ---------- 搜尋結果 ----------
.results {
    margin-top: $spacing-xl;

    @include mobile {
        margin-top: $spacing-lg;
    }
}

.results__label {
    font-size: $label-md-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-600;
}

.results__list {
    display: flex;
    flex-direction: column;
    gap: $spacing-xl;
    margin-top: $spacing-lg;

    @include tablet {
        gap: $spacing-lg;
    }
}

.results__pagination {
    margin-top: $spacing-xl;
}

.results__empty {
    margin-top: $spacing-lg;
    font-size: $p-md-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-600;
}

// ---------- 申請推薦好書 ----------
// 桌機是左文右圖、按鈕跟在文字下面；平板以下改成 文字 → 圖 → 按鈕。
// 用格線區域排，是因為按鈕在兩種版型裡的位置不一樣，
// 靠 HTML 順序排不出來（桌機它要在左欄，手機要在最下面）。
.apply {
    display: grid;
    // 設計稿是文字 365、插圖 489。用 fr 讓兩欄按比例一起縮，
    // 寫死 px 的話壓縮會全部落在插圖上，切版前會被擠掉三分之一。
    grid-template-columns: 365fr 489fr;
    grid-template-areas:
        "intro image"
        "btn   image";
    column-gap: 19px; // 設計稿文字區與插圖的間距
    row-gap: $spacing-xl;
    align-items: center;
    width: 100%;
    max-width: 886px; // 設計稿這一區的寬度
    margin-top: 120px;
    margin-inline: auto;

    @include tablet {
        grid-template-columns: 1fr;
        grid-template-areas:
            "intro"
            "image"
            "btn";
        row-gap: $spacing-lg;
        margin-top: $spacing-xl;
    }

    // margin-top 不跟著縮：跟卡片間距一樣的話，這一區會被看成清單的下一筆
    @include mobile {
        row-gap: $spacing-md;
    }
}

// SectionTitle 字級寫死 28px，這裡只在這一頁縮一階，沒有動到元件本身
@include mobile {
    .apply__intro :deep(.section-title) {
        font-size: $h6-size;
    }
}

.apply__intro {
    grid-area: intro;
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

    @include mobile {
        font-size: $p-md-size;
    }
}

.apply__btn {
    grid-area: btn;
    justify-self: start;

    // 平板以下拉滿版：靠左會跟置中的插圖變成兩種對齊
    @include tablet {
        justify-self: stretch;
    }
}

.apply__image {
    grid-area: image;
    width: 100%;
    max-width: 489px; // 設計稿插圖寬度

    @include tablet {
        justify-self: center;
    }
}
</style>

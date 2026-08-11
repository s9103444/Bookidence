<!--
AppPagination 分頁器

一排頁碼按鈕，左右各一顆箭頭。頁數多的時候中間會收成「…」，
所以不管一共幾頁，畫面上最多就七顆頁碼。

⚠️ 先看這裡：它只負責「畫出那排按鈕」和「告訴你使用者點了第幾頁」。
   算總頁數、把資料切成一頁一頁，都要你自己做。
   只放這個元件的話，按鈕會亮但畫面不會變。

--------------------------------------------------
最短的完整範例（後台表格用這個就好）
--------------------------------------------------

    <script setup>
    import { ref, computed } from 'vue';
    import AppPagination from '@/components/common/AppPagination.vue';

    const members = ref([ ...一大包資料... ]);

    const perPage = 10;              // 一頁放幾筆，自己決定
    const page = ref(1);             // 現在第幾頁

    // 一共幾頁：總筆數除以每頁筆數，除不盡要進位
    const totalPages = computed(() =>
      Math.max(1, Math.ceil(members.value.length / perPage))
    );

    // 這一頁要顯示的那幾筆
    const pagedMembers = computed(() =>
      members.value.slice((page.value - 1) * perPage, page.value * perPage)
    );

    function goToPage(p) {
      page.value = p;
    }
    <\/script>

    <template>
      <tr v-for="m in pagedMembers" :key="m.id"> ... </tr>

      <AppPagination
        :current-page="page"
        :total-pages="totalPages"
        @change="goToPage"
      />
    </template>

⚠️ 畫面上要跑的是切過的那份（pagedMembers），不是原始那包（members）。
   這裡寫錯的話會變成「每一頁都顯示全部資料」。

--------------------------------------------------
前台頁面：把頁碼放到網址上
--------------------------------------------------

上面的 page 換成從網址讀，其他都一樣。
好處是重新整理還停在第 3 頁、把連結貼給別人他看到的也是第 3 頁：

    const page = computed(() => Number(route.query.page) || 1);

    function goToPage(p) {
      router.push({ query: { ...route.query, page: p } });
      window.scrollTo({ top: 0 });
    }

{ ...route.query, page: p } 的意思是「網址上原本的東西全部留著，只換頁碼」。
少了前面那三個點，換頁會把搜尋關鍵字弄丟。

後台表格建議用一般的 ref 就好，因為一頁可能有兩三個表格，
全部都想寫進網址會互相打架（換這個表格的頁碼，另一個也跟著跳）。

--------------------------------------------------
可以傳什麼
--------------------------------------------------

current-page  現在在第幾頁，從 1 開始（必填）
total-pages   一共幾頁（必填）

@change       使用者點了某一頁，會把頁碼傳出來

--------------------------------------------------
會踩到的地方
--------------------------------------------------

- 只有一頁的時候整排會自己隱藏，不用另外寫判斷。

- 資料筆數變少的時候要把頁碼推回第 1 頁。
  例如在第 5 頁重新搜尋、結果只剩兩頁，畫面會變空白。
  頁碼放網址的話，重新搜尋時不要帶 page 就好；
  放 ref 的話，換條件的地方順手寫 page.value = 1。

- 換頁後記得捲回畫面頂端。分頁器在整頁最下面，
  不捲的話使用者換完頁會停在原地，看到的是新一頁的最後幾筆。

- 它不會記得自己在第幾頁。頁碼是你給的，它只是通知你「有人想去第 3 頁」，
  要不要真的換、換完存去哪，都是你決定。
-->
<script setup>
import { computed } from 'vue';
import AppIcon from '@/components/common/AppIcon.vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(['change']);

// 現在這頁的左右各留幾顆。1 的意思是「5」旁邊會有 4 和 6。
const SIBLINGS = 1;

// 產生一段連續的頁碼：range(3, 7) 會得到 [3, 4, 5, 6, 7]，頭尾都包含。
function range(start, end) {
  const result = [];
  for (let n = start; n <= end; n++) {
    result.push(n);
  }
  return result;
}

// 產生畫面上要排的東西，數字代表頁碼，'...' 代表要顯示刪節號。
// 頭尾兩頁永遠看得到，這樣使用者隨時能一鍵回到第一頁或跳到最後一頁。
const pages = computed(() => {
  const total = props.totalPages;
  const current = props.currentPage;

  // 頭 + 尾 + 現在這頁 + 左右鄰居 + 兩個刪節號 = 塞得下就全部列出來，
  // 不然「1 … 2 3 4 … 5」會比直接列「1 2 3 4 5」還長。
  const maxSlots = SIBLINGS * 2 + 5;
  if (total <= maxSlots) return range(1, total);

  const left = Math.max(current - SIBLINGS, 1);
  const right = Math.min(current + SIBLINGS, total);

  // 左邊還剩不只一頁沒顯示才需要刪節號，
  // 只差一頁的話直接把那頁印出來，不然會出現「1 … 3」這種蠢事
  const hasLeftGap = left > 2;
  const hasRightGap = right < total - 1;

  if (!hasLeftGap && hasRightGap) {
    return [...range(1, maxSlots - 2), '...', total];
  }

  if (hasLeftGap && !hasRightGap) {
    return [1, '...', ...range(total - (maxSlots - 3), total)];
  }

  return [1, '...', ...range(left, right), '...', total];
});

function go(page) {
  // 擋掉超出範圍和「點了現在這頁」，不然外面每次都要自己防一遍
  if (page < 1 || page > props.totalPages || page === props.currentPage) return;
  emit('change', page);
}
</script>

<template>
  <nav v-if="totalPages > 1" class="pagination" aria-label="分頁">
    <button
      type="button"
      class="pagination__btn"
      aria-label="上一頁"
      :disabled="currentPage === 1"
      @click="go(currentPage - 1)">
      <AppIcon name="chevron-left" :size="14"></AppIcon>
    </button>

    <template v-for="(item, index) in pages" :key="index">
      <span v-if="item === '...'" class="pagination__gap" aria-hidden="true">…</span>

      <button
        v-else
        type="button"
        class="pagination__btn"
        :class="{ 'pagination__btn--current': item === currentPage }"
        :aria-current="item === currentPage ? 'page' : undefined"
        :aria-label="`第 ${item} 頁`"
        @click="go(item)">
        {{ item }}
      </button>
    </template>

    <button
      type="button"
      class="pagination__btn"
      aria-label="下一頁"
      :disabled="currentPage === totalPages"
      @click="go(currentPage + 1)">
      <AppIcon name="chevron-right" :size="14"></AppIcon>
    </button>
  </nav>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap; // 頁數多又遇到窄螢幕時換行，不要擠出畫面
    gap: $spacing-sm;

    @include mobile {
        gap: $spacing-xs;
    }
}

// 圓框 + 透明底，跟搜索圖書頁輪播的左右箭頭同一套視覺。
.pagination__btn {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border: 1px solid $primary;
    border-radius: $btn-radius-rnd;
    background-color: transparent;
    color: $primary;
    font-size: $label-sm-size;
    font-weight: $text-weight;
    letter-spacing: $letter-spacing-base;
    cursor: pointer;

    @include mobile {
        width: 32px;
        height: 32px;
        font-size: $label-xs-size;
    }
}

// 只有滑鼠裝置才套 hover，觸控裝置點完顏色會卡住
@media (hover: hover) {
    .pagination__btn:hover:not(:disabled):not(.pagination__btn--current) {
        background-color: $primary-100;
    }
}

.pagination__btn--current {
    background-color: $primary;
    color: $neutral-100;
    cursor: default;
}

.pagination__btn:disabled {
    border-color: $neutral-400;
    color: $neutral-400;
    cursor: not-allowed;
}

.pagination__gap {
    width: 24px;
    text-align: center;
    font-size: $label-md-size;
    font-weight: $text-weight;
    color: $neutral-500;
    user-select: none;
}
</style>

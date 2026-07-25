<script setup>
import GuildCard from '../../components/front/GuildCard.vue'
//需要用到公會卡片 所以import進來

// 先用假資料撐版面，確認排版對不對。
// 之後接 API 資料時，把這幾個陣列換成從後端拿到的真實資料即可，
// 下面 template 的寫法完全不用改。
const categories = ['全部', '奇幻文學', '文學小說', '藝術人文', '心理勵志', '科技商業', '藝術設計', '職場工作']
const hotGuilds = Array.from({ length: 4 })
const readingNow = Array.from({ length: 6 })
const allGuilds = Array.from({ length: 12 })
</script>

<template>
  <div class="guild-list">
    <!-- 1. Hero 區：標題 + 插圖 + 建立公會按鈕 -->
    <section class="frame frame--hero">
      <span class="frame__label">Hero 區</span>
      <div class="hero__text">
        <h1>瀏覽讀書公會</h1>
        <button class="hero__cta">+ 建立讀書公會</button>
      </div>
      <div class="hero__illustration">插圖</div>
    </section>

    <!-- 2. 篩選列：分類標籤 + 搜尋框 -->
    <section class="frame frame--filter">
      <span class="frame__label">篩選列</span>
      <div class="filter-bar">
        <button
          v-for="cat in categories"
          :key="cat"
          class="filter-bar__tag"
        >
          {{ cat }}
        </button>
        <input class="filter-bar__search" type="text" placeholder="搜尋公會…" />
      </div>
    </section>

    <!-- 3. 熱門讀書公會：橫向排列，之後可以加左右箭頭做輪播 -->
    <section class="frame frame--section">
      <span class="frame__label">熱門讀書公會</span>
      <h2>熱門讀書公會</h2>
      <div class="card-row">
        <GuildCard v-for="(guild, index) in hotGuilds" :key="index" />
      </div>
    </section>

    <!-- 4. 這個公會正在讀……：橫向書封，先用色塊佔位 -->
    <section class="frame frame--section">
      <span class="frame__label">這個公會正在讀……</span>
      <h2>這個公會正在讀……</h2>
      <div class="book-row">
        <div v-for="(book, index) in readingNow" :key="index" class="book-row__item">
          <div class="book-row__cover">書封</div>
          <p class="book-row__title">書名 / 公會名</p>
        </div>
      </div>
    </section>

    <!-- 5. 所有讀書公會：格狀排列，之後這裡會加分頁或無限捲動 -->
    <section class="frame frame--section">
      <span class="frame__label">所有讀書公會</span>
      <h2>所有讀書公會</h2>
      <div class="card-grid">
        <GuildCard v-for="(guild, index) in allGuilds" :key="index" />
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

// 這些虛線框跟標籤只是暫時的佔位視覺，
// 等版面順序確認沒問題、Mockup 細節也定案了，
// 就可以把 .frame 相關的樣式整個拿掉，換成正式設計
.frame {
  position: relative;
  margin-bottom: $spacing-xl;
  padding: $spacing-lg;
  border: 1px dashed $neutral-400;
  border-radius: 8px;
}

.frame__label {
  position: absolute;
  top: -10px;
  left: $spacing-md;
  padding: 0 $spacing-xs;
  background: $neutral-100;
  font-size: $p-xs-size;
  color: $neutral-500;
}

.frame--hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-lg;
}

.hero__cta {
  margin-top: $spacing-md;
  padding: $spacing-sm $spacing-lg;
  background: $secondary;
  color: $neutral-800;
  font-weight: 700;
  border-radius: 20px;
}

.hero__illustration {
  flex-shrink: 0;
  width: 240px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $neutral-200;
  color: $neutral-500;
  border-radius: 8px;
}

.filter-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: $spacing-sm;
}

.filter-bar__tag {
  padding: $spacing-xs $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: 20px;
  font-size: $p-sm-size;

  &:hover {
    background: $neutral-200;
  }
}

.filter-bar__search {
  margin-left: auto;
  padding: $spacing-xs $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: 20px;
  font-size: $p-sm-size;
  min-width: 200px;
}

.card-row {
  display: flex;
  gap: $spacing-md;
  overflow-x: auto; // 卡片一多，橫向捲動，不會把頁面撐爆
  padding-bottom: $spacing-xs;
    // 在橫向捲動的情境下，卡片才需要固定寬度，跟 grid 情境的行為互相獨立
  :deep(.guild-card) {
    width: 260px;
    flex-shrink: 0;
  }
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  //這行改卡片最小寬度
  gap: $spacing-md;
}

.book-row {
  display: flex;
  gap: $spacing-md;
  overflow-x: auto;
}

.book-row__item {
  flex-shrink: 0;
  width: 120px;
  text-align: center;
}

.book-row__cover {
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $neutral-200;
  color: $neutral-500;
  border-radius: 6px;
  margin-bottom: $spacing-xs;
}

.book-row__title {
  font-size: $p-xs-size;
  color: $neutral-500;
}
</style>

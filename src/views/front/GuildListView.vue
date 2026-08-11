<script>
import GuildCard from '../../components/front/GuildCard.vue'
import { Carousel, Slide } from 'vue3-carousel'
import 'vue3-carousel/carousel.css'
import AppIcon from '@/components/common/AppIcon.vue'
import SectionTitle from '@/components/front/SectionTitle.vue'

export default {
  components: {
    GuildCard,
    Carousel,
    Slide,
    AppIcon,
    SectionTitle,
  },
  data() {
    return {
      // 12 個分類，對齊書籍分類系統，「全部」是篩選用的額外選項不算在 12 個裡面
      categories: [
        '全部', '心理成長', '商業理財', '歷史人文', '科普知識',
        '醫療生活', '藝術設計', '社會議題', '推理懸疑',
        '奇幻科幻', '文學小說', '漫畫', '生活風格',
      ],
      selectedCategory: '全部',
      keyword: '',

      // 輪播的斷點設定，跟 SearchView 的 breakpoints 邏輯一致，數字可以之後再調
      carouselBreakpoints: {
        768: { itemsToShow: 2, itemsToScroll: 2 },
        1024: { itemsToShow: 3, itemsToScroll: 3 },
        1440: { itemsToShow: 4, itemsToScroll: 4 },
      },

      hotGuilds: [
        { guildId: 1, avatar: '', name: '月光書巷', description: '每週共讀一本小說，分享角色、劇情與那些令人難忘的文字。', currentBook: '秘密中的秘密', memberCount: 200, city: '台北市', tags: ['奇幻科幻', '文學小說', '心理成長'] },
        { guildId: 2, avatar: '', name: '溫暖筆語公會', description: '每週共讀一本小說，分享角色、劇情與那些令人難忘的文字。', currentBook: '城與不確定的牆', memberCount: 200, city: '台中市', tags: ['奇幻科幻', '文學小說', '心理成長'] },
        { guildId: 3, avatar: '', name: '壁爐與貓', description: '奇幻與架空冒險：喜歡跟著主角踏入宏大的世界觀與神秘古老的歷史……', currentBook: '小王子', memberCount: 56, city: '台北市', tags: ['奇幻科幻', '心理成長'] },
        { guildId: 4, avatar: '', name: '月光書巷', description: '每週共讀一本小說，分享角色、劇情與那些令人難忘的文字。', currentBook: '秘密中的秘密', memberCount: 200, city: '台北市', tags: ['奇幻科幻', '文學小說', '心理成長'] },
      ],
      readingNow: [
        { bookId: 1, cover: '', title: '秘密中的秘密', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
        { bookId: 2, cover: '', title: '致富心態', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
        { bookId: 3, cover: '', title: '蛤蟆先生去看心理師', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
        { bookId: 4, cover: '', title: '厭式教養', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
        { bookId: 5, cover: '', title: '小王子', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
        { bookId: 6, cover: '', title: '數字的心思考', guildName: '深夜讀小說', memberCount: 200, region: '北部進行' },
      ],
      allGuilds: Array.from({ length: 12 }).map((_, index) => ({
        guildId: index + 1,
        avatar: '',
        name: '月光書巷',
        description: '每週共讀一本小說，分享角色、劇情與那些令人難忘的文字。',
        currentBook: '秘密中的秘密',
        memberCount: 200,
        city: '台北市',
        tags: ['奇幻科幻', '文學小說', '心理成長'],
      })),
    }
  },
  computed: {
    filteredGuilds() {
      return this.allGuilds
        .filter((guild) => this.selectedCategory === '全部' || guild.tags.includes(this.selectedCategory))
        .filter((guild) => guild.name.includes(this.keyword))
    },
  },
  methods: {
    selectCategory(category) {
      this.selectedCategory = category
    },
    goToGuildDetail(guildId) {
      this.$router.push({ name: 'guild-detail', params: { id: guildId } })
    },
    goPrev() {
      this.$refs.hotGuildCarousel.prev()
    },
    goNext() {
      this.$refs.hotGuildCarousel.next()
    },
  },
}
</script>

<template>
  <div class="guild-list">
    <section class="hero">
      <div class="hero__text">
        <h1 class="hero__title">瀏覽讀書公會</h1>
        <button class="hero__cta">+ 建立讀書公會</button>
      </div>
    </section>

    <section class="section">
      <div class="section__header">
        <SectionTitle>熱門讀書會</SectionTitle>
        <div class="carousel-nav">
          <button type="button" class="carousel-nav__btn" aria-label="上一頁" @click="goPrev">
            <AppIcon name="chevron-left" :size="14" />
          </button>
          <button type="button" class="carousel-nav__btn" aria-label="下一頁" @click="goNext">
            <AppIcon name="chevron-right" :size="14" />
          </button>
        </div>
      </div>

      <Carousel
        ref="hotGuildCarousel"
        :items-to-show="1"
        :items-to-scroll="1"
        :breakpoints="carouselBreakpoints"
        :gap="24"
        :wrap-around="true"
        snap-align="start"
      >
        <Slide v-for="guild in hotGuilds" :key="guild.guildId">
          <GuildCard v-bind="guild" @view-guild="goToGuildDetail" />
        </Slide>
      </Carousel>
    </section>

    <section class="section">
      <SectionTitle>這個公會正在讀……</SectionTitle>
      <div class="book-row">
        <div v-for="book in readingNow" :key="book.bookId" class="book-row__item">
          <div class="book-row__cover">
            <img v-if="book.cover" :src="book.cover" :alt="book.title" />
          </div>
          <p class="book-row__title">{{ book.title }}</p>
          <p class="book-row__guild">{{ book.guildName }}</p>
        </div>
      </div>
    </section>

    <section class="section">
      <SectionTitle>所有讀書公會</SectionTitle>

      <p class="filter-bar__label">書的類別</p>
      <div class="filter-bar">
        <button
          v-for="category in categories"
          :key="category"
          class="filter-bar__tag"
          :class="{ 'filter-bar__tag--active': selectedCategory === category }"
          @click="selectCategory(category)"
        >
          {{ category }}
        </button>
      </div>

      <div class="filter-bar__search">
        <AppIcon name="search" :size="16" />
        <input v-model="keyword" type="text" placeholder="搜尋關鍵字" />
      </div>


      <p v-if="filteredGuilds.length === 0" class="empty-text">找不到符合條件的讀書公會</p>

      <div v-else class="card-grid">
        <GuildCard
          v-for="guild in filteredGuilds"
          :key="guild.guildId"
          v-bind="guild"
          @view-guild="goToGuildDetail"
        />
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-list {
  max-width: 1200px;
  margin: 0 auto;
  padding: $spacing-xl;

  @include tablet {
    padding: $spacing-lg;
  }

  @include mobile {
    padding: $spacing-md;
  }
}

.hero {
  position: relative;
  display: flex;
  align-items: center;
  min-height: 320px;
  padding: $spacing-xl;
  margin-bottom: $spacing-xl;
  border-radius: 16px;
  background: $neutral-800 url('../../assets/images/guild/bookguilds-banner.png') center / cover no-repeat;
}

.hero__title {
  color: $neutral-100;
  margin-bottom: $spacing-md;
}

.hero__cta {
  padding: $spacing-sm $spacing-lg;
  background: $secondary;
  color: $neutral-800;
  font-weight: 700;
  border-radius: $btn-radius-rnd;
}

.section {
  margin-bottom: $spacing-xl;
}

.section__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: $spacing-lg;
}

.carousel-nav {
  display: flex;
  gap: 14px;
}

.carousel-nav__btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border: 1px solid $primary;
  border-radius: $btn-radius-rnd;
  background-color: transparent;
  color: $primary;
  cursor: pointer;
}

// 讓輪播裡的卡片高度對齊，跟 SearchView 同一個處理方式
.guild-list :deep(.carousel__slide) {
  align-items: stretch;
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
  width: 100%;
  aspect-ratio: unquote($book-cover-ratio);
  border-radius: 6px;
  overflow: hidden;
  background: $primary-300;
  margin-bottom: $spacing-xs;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.book-row__title {
  font-size: $p-sm-size;
  font-weight: 700;
  margin-bottom: 2px;
}

.book-row__guild {
  font-size: $p-xs-size;
  color: $neutral-500;
}

.filter-bar__label {
  font-weight: 700;
  margin-bottom: $spacing-sm;
}

.filter-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: $spacing-sm;
  margin-bottom: $spacing-sm;
}

.filter-bar__tag {
  padding: $spacing-xs $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-sm-size;
  color: $neutral-700;

  &:hover {
    background: $neutral-200;
  }

  &.filter-bar__tag--active {
    background: $primary;
    border-color: $primary;
    color: $neutral-100;
  }
}

.filter-bar__search {
  display: flex;
  align-items: center;
  gap: 6px;
  width: 100%;
  max-width: 320px;
  padding: $spacing-xs $spacing-md;
  margin-bottom: $spacing-sm;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  color: $neutral-500;

  input {
    border: none;
    outline: none;
    flex: 1;
    font-size: $p-sm-size;
  }

  @include mobile {
    max-width: 100%;
  }
}

.empty-text {
  color: $neutral-500;
  text-align: center;
  padding: $spacing-xl 0;
}

.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: $spacing-md;
}
</style>
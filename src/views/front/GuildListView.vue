<script>
import GuildCard from '../../components/front/GuildCard.vue'
import { Carousel, Slide } from 'vue3-carousel'
import 'vue3-carousel/carousel.css'
import AppIcon from '@/components/common/AppIcon.vue'
import AppButton from '@/components/common/AppButton.vue'
import SectionTitle from '@/components/front/SectionTitle.vue'
import GuildPreviewModal from '@/components/front/GuildPreviewModal.vue'
import { API_BASE, API_STATIC } from '@/common/api'
import { useUserStore } from '@/stores/user'

// 公會沒有真的地區欄位，用 guild_id 固定對應到一個地區當裝飾用（不影響篩選）
const REGIONS = ['北部', '中部', '南部', '東部', '線上']
function getGuildRegion(guildId) {
  return REGIONS[guildId % REGIONS.length]
}

export default {
  components: {
    GuildCard,
    Carousel,
    Slide,
    AppIcon,
    AppButton,
    SectionTitle,
    GuildPreviewModal,
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

      allGuilds: [],

      isPreviewOpen: false,
      previewGuild: null,
      joinError: '',
      isJoining: false,
    }
  },
  computed: {
    hotGuilds() {
      // 沒有真的「熱門」判斷邏輯，先用人數當代理值，取前 4 筆
      return [...this.allGuilds].sort((a, b) => b.memberCount - a.memberCount).slice(0, 4)
    },
    readingNow() {
      // 同一本書可能好幾個公會都在讀，只取第一個(member_count 較高的那個)代表，避免同一本書洗版
      const seenTitles = new Set()
      const uniqueByBook = this.allGuilds.filter((guild) => {
        if (seenTitles.has(guild.currentBook)) return false
        seenTitles.add(guild.currentBook)
        return true
      })
      return uniqueByBook.slice(0, 6).map((guild) => ({
        guildId: guild.guildId,
        cover: guild.currentBookCover,
        title: guild.currentBook,
        guildName: guild.name,
      }))
    },
    filteredGuilds() {
      return this.allGuilds
        .filter((guild) => this.selectedCategory === '全部' || guild.tags.includes(this.selectedCategory))
        .filter((guild) => guild.name.includes(this.keyword))
    },
  },
  async mounted() {
    const res = await fetch(`${API_BASE}/guild_list.php`)
    const result = await res.json()
    if (result.success) {
      this.allGuilds = result.data.map((row) => ({
        guildId: row.guild_id,
        avatar: row.guild_avatar ? `${API_STATIC}/src/common/uploads/${row.guild_avatar}` : '',
        name: row.guild_name,
        description: row.intro,
        currentBook: row.current_book_title,
        currentBookCover: row.current_book_cover ? `${API_STATIC}/src/common/uploads/${row.current_book_cover}` : '',
        memberCount: Number(row.member_count),
        tags: row.tags ? row.tags.split(',') : [],
        region: getGuildRegion(row.guild_id),
      }))
    }
  },
  methods: {
    selectCategory(category) {
      this.selectedCategory = category
    },
    goToGuildDetail(guildId) {
      this.$router.push({ name: 'guild-detail', params: { id: guildId } })
    },
    async openGuildPreview(guildId) {
      const base = this.allGuilds.find((g) => g.guildId === guildId)
      if (!base) return

      this.joinError = ''
      this.previewGuild = { ...base, isMember: false }
      this.isPreviewOpen = true

      // 用 token 直接判斷(不用 isLoggedIn/userId)，因為重新整理頁面後 restoreSession() 是非同步的，
      // isLoggedIn/userId 這兩個欄位不會馬上就緒，但 token 是從 localStorage 同步還原的，不會有這個問題
      const userStore = useUserStore()
      const requests = [fetch(`${API_BASE}/guild_get_schedule.php?guild_id=${guildId}`).then((r) => r.json())]
      if (userStore.token) {
        requests.push(
          fetch(`${API_BASE}/guild_get_members.php?guild_id=${guildId}`, {
            headers: { Authorization: `Bearer ${userStore.token}` },
          }).then((r) => r.json())
        )
      }
      const [scheduleResult, membersResult] = await Promise.all(requests)

      // 等資料回來的時候使用者可能已經切去看別的公會了，不要把舊的結果蓋上去
      if (!this.previewGuild || this.previewGuild.guildId !== guildId) return

      if (scheduleResult.success && scheduleResult.record) {
        const record = scheduleResult.record
        this.previewGuild.currentBook = {
          cover: record.bc_image ? `${API_STATIC}/src/common/uploads/${record.bc_image}` : '',
          title: record.title,
          author: record.author,
          publisher: record.publisher,
          publishDate: record.p_date,
          isbn: record.isbn,
        }
        this.previewGuild.discussionBoards = (scheduleResult.segments || []).map((segment) => ({
          id: segment.segment_id,
          title: '章節分段討論板',
          dueDate: segment.expected_end_date,
          chapterRange: `第${segment.start_chapter}章～第${segment.end_chapter}章`,
        }))
      }

      if (membersResult?.success) {
        this.previewGuild.isMember = membersResult.viewer_is_member
      }
    },
    async handleGuildJoined(guildId) {
      this.isJoining = true
      this.joinError = ''

      const userStore = useUserStore()
      const formData = new FormData()
      formData.append('guild_id', guildId)

      try {
        const res = await fetch(`${API_BASE}/guild_join.php`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${userStore.token}` },
          body: formData,
        })
        const result = await res.json()

        if (!result.success) {
          this.joinError = result.message || '加入公會失敗，請稍後再試'
          return
        }

        this.isPreviewOpen = false
        this.goToGuildDetail(guildId)
      } finally {
        this.isJoining = false
      }
    },
    handleEnterGuild(guildId) {
      this.isPreviewOpen = false
      this.goToGuildDetail(guildId)
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
        <AppButton color="secondary" :to="{ name: 'create-guilds' }">+ 建立讀書公會</AppButton>
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
          <GuildCard v-bind="guild" @view-guild="openGuildPreview" />
        </Slide>
      </Carousel>
    </section>

    <section class="section">
      <div class="section__header">
        <SectionTitle>這個公會正在讀……</SectionTitle>
      </div>
      <div class="book-row">
        <div
          v-for="book in readingNow"
          :key="book.guildId"
          class="book-row__item"
          @click="openGuildPreview(book.guildId)"
        >
          <div class="book-row__cover">
            <img v-if="book.cover" :src="book.cover" :alt="book.title" />
          </div>
          <p class="book-row__title">{{ book.title }}</p>
          <p class="book-row__guild">{{ book.guildName }}</p>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="section__header">
        <SectionTitle>所有讀書公會</SectionTitle>
      </div>

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
          @view-guild="openGuildPreview"
        />
      </div>
    </section>

    <GuildPreviewModal
      v-model="isPreviewOpen"
      :guild="previewGuild"
      :join-error="joinError"
      :is-joining="isJoining"
      @joined="handleGuildJoined"
      @enter-guild="handleEnterGuild"
    />
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
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  overflow-x: auto;
}

.book-row__item {
  flex-shrink: 0;
  width: 120px;
  text-align: center;
  cursor: pointer;
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
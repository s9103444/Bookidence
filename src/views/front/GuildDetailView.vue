<script>
import GuildEventCard from '../../components/front/GuildEventCard.vue'
import GuildMilestoneCard from '../../components/front/GuildMilestoneCard.vue'
import SectionTitle from '../../components/front/SectionTitle.vue'
import { IconSettings, IconEdit, IconArrowRight } from '@tabler/icons-vue'

export default {
  components: {
    GuildEventCard,
    GuildMilestoneCard,
    SectionTitle,
    IconSettings,
    IconEdit,
    IconArrowRight,
  },
  data() {
    return {
      isGuildLeader: false, // 控制是否為公會幹部

      guild: {
        guildId: 3,
        name: '壁爐與貓',
        thumbnailImage: '',
        memberCount: 56,
        tags: ['奇幻小說', '心靈成長'],
        description: '深夜的鐘聲響起，這裡是愛書人的避風港。有劈啪作響的溫暖壁爐，有腳邊打盹的貓，還有手中那本尚未讀完的書。',
        preferenceIntro: '我們偏好的書籍類型不設限，但更傾向於具有療癒、探索感或引人深思的作品：',
        preferences: [
          { id: 1, label: '奇幻與架空冒險', desc: '喜歡跟著主角踏入宏大的世界觀與神祕古老的歷史。' },
          { id: 2, label: '雋永散文與心靈療癒', desc: '在文字中尋找共鳴，撫平日常的焦慮與疲憊。' },
          { id: 3, label: '經典文學與各類小說', desc: '品味文字的細膩編織，探討故事背後的人性與智慧。' },
        ],
      },

      announcementTitle: '公會守則與規定',
      announcementContent:
        '保持溫柔與包容：每個人對書籍的理解與喜好不同，這裡嚴禁流於高深的學術爭辯或批判他人的閱讀品味。\n\n' +
        '安靜的陪伴：在共讀時間請保持安靜，尊重彼此翻頁的空間，讓想獨處的人也能安心待著。\n\n' +
        '嚴禁過度商業或社交目的：這裡不歡迎推銷、直銷或過度的利益搭訕，請讓公會回歸最純粹的書香與溫度。',
      isEditingAnnouncement: false,
      announcementDraft: '',

      // 左側「相關功能」導覽清單，routeName 是 null 代表對應的路由還沒建立，先不能點
      relatedLinks: [
        { id: 1, label: '建立讀書活動', routeName: 'event-apply', requiresLeader: false },
        { id: 2, label: '設定讀書排程', routeName: 'guild-reading-schedule', requiresLeader: true },
        { id: 3, label: '成員列表', routeName: 'guild-members', requiresLeader: false },
        { id: 4, label: '檢舉事件', routeName: 'report', requiresLeader: false },
        { id: 5, label: '公會設定', routeName: 'guild-settings', requiresLeader: true },
      ],

      currentBook: {
        id: 1,
        cover: '',
        title: '小王子',
        author: '安東尼．聖修伯里',
        tag: '小說',
        description: '故事描述一位年輕的王子拜訪多個星球（包括地球）的歷程，探討孤獨、友誼、愛與失落等主題。雖然表面上是一本兒童讀物，卻對人生、成年人和人性提供了深刻的洞察。',
      },

      events: [
        { eventId: 1, eventType: '線下活動', eventTime: '2026.10.10 (五) 19:00 - 21:30 (GMT+8)', location: '台灣台北市松山區復興北路1號6樓之3-603教室', locationNote: '亞細亞大樓六樓-小樹屋共享空間', participantCount: 5 },
        { eventId: 2, eventType: '線上活動', eventTime: '2026.10.17 (五) 20:00 - 22:00 (GMT+8)', location: 'Google Meet 線上會議室', locationNote: '報名後寄送連結', participantCount: 12 },
        { eventId: 3, eventType: '線下活動', eventTime: '2026.10.24 (五) 19:00 - 21:00 (GMT+8)', location: '台北市信義區松高路11號', locationNote: '', participantCount: 8 },
        { eventId: 4, eventType: '線下活動', eventTime: '2026.10.31 (五) 19:30 - 21:30 (GMT+8)', location: '台北市大安區羅斯福路四段1號', locationNote: '台大校友會館 3F', participantCount: 20 },
      ],

      milestones: [
        { id: 1, index: '01', title: '閱讀里程碑', readingRange: '第一章節 - 第五章節', completeDate: '8/1' },
        { id: 2, index: '02', title: '閱讀里程碑', readingRange: '第一章節 - 第五章節', completeDate: '8/1' },
        { id: 3, index: '03', title: '閱讀里程碑', readingRange: '第一章節 - 第五章節', completeDate: '8/1' },
      ],
    }
  },
  created() {
    console.log('公會 ID：', this.$route.params.id)
  },
  computed: {
    linksWithAccess() {
      return this.relatedLinks.map((link) => ({
        ...link,
        isDisabled: !link.routeName || (link.requiresLeader && !this.isGuildLeader),
      }))
    },
  },
  methods: {
    goToGuildSettings() {
      this.$router.push({ name: 'guild-settings', params: { id: this.guild.guildId } })
    },
    goToBookDetail() {
      this.$router.push({ name: 'book-detail', params: { id: this.currentBook.id } })
    },
    goToDiscussion(milestoneId) {
      this.$router.push({ name: 'guild-discussion', params: { id: this.guild.guildId, milestoneId } })
    },
    goToGuildFeature(routeName, requiresLeader = false) {
      if (!routeName) {
        console.log('這個功能的路由還沒建立，先不動作')
        return
      }
      if (requiresLeader && !this.isGuildLeader) {
        console.log('這個功能需要公會長或副會長權限才能使用')
        return
      }
      this.$router.push({ name: routeName, params: { id: this.guild.guildId } })
    },
    goToRelatedLink(link) {
      this.goToGuildFeature(link.routeName, link.requiresLeader)
    },
    goToEventDetail(eventId) {
      this.$router.push({ name: 'event-detail', params: { id: this.guild.guildId, eventId } })
    },
    startEditAnnouncement() {
      this.announcementDraft = this.announcementContent // 先把目前內容複製一份到草稿
      this.isEditingAnnouncement = true
    },
    saveAnnouncement() {
      this.announcementContent = this.announcementDraft // 草稿存回正式內容
      this.isEditingAnnouncement = false
      // 之後這裡要打 API 把新內容存回後端，取代目前純前端記憶體的假動作
    },
    cancelEditAnnouncement() {
      this.isEditingAnnouncement = false // 直接丟掉草稿，announcementContent 完全沒被動過
    },
  },
}
</script>

<template>
  <div class="guild-detail">
    <!-- Hero -->
    <section class="guild-detail__hero">
      <button v-if="isGuildLeader" class="guild-detail__settings-btn" aria-label="公會設定" @click="goToGuildSettings">
        <IconSettings :size="20" stroke-width="2" />
      </button>
    </section>

    <!-- 3:9 版面 -->
    <div class="guild-detail__layout">
      <aside class="guild-detail__aside">
        <div class="guild-detail__thumbnail">
          <img v-if="guild.thumbnailImage" :src="guild.thumbnailImage" :alt="guild.name" />
        </div>

        <section class="section guild-detail__announcement-section">
          <div class="section__header">
            <SectionTitle>公告欄</SectionTitle>
            <button
              v-if="isGuildLeader && !isEditingAnnouncement"
              class="section__edit-btn"
              aria-label="編輯公告"
              @click="startEditAnnouncement"
            >
              <IconEdit :size="18" stroke-width="2" />
            </button>
          </div>

          <div class="guild-detail__announcement">
            <p class="guild-detail__announcement-title">📍 {{ announcementTitle }}</p>

            <div class="guild-detail__announcement-body">
              <p v-if="!isEditingAnnouncement" class="guild-detail__rules">{{ announcementContent }}</p>
              <textarea
                v-else
                v-model="announcementDraft"
                class="guild-detail__announcement-textarea"
              ></textarea>
            </div>

            <div v-if="isEditingAnnouncement" class="guild-detail__announcement-actions">
              <button class="guild-detail__announcement-btn guild-detail__announcement-btn--save" @click="saveAnnouncement">
                儲存
              </button>
              <button class="guild-detail__announcement-btn guild-detail__announcement-btn--cancel" @click="cancelEditAnnouncement">
                取消
              </button>
            </div>
          </div>
        </section>

        <section class="section guild-detail__related-section">
          <p class="related-nav__title">相關功能</p>
          <ul class="related-nav">
            <li v-for="link in linksWithAccess" :key="link.id">
              <button
                class="related-nav__link"
                :class="{ 'related-nav__link--disabled': link.isDisabled }"
                @click="goToRelatedLink(link)"
              >
                {{ link.label }}
              </button>
            </li>
          </ul>
        </section>
      </aside>

      <main class="guild-detail__main">
        <section class="guild-detail__intro">
          <p class="guild-detail__type">讀書公會</p>
          <h1 class="guild-detail__name">{{ guild.name }}</h1>
          <p class="guild-detail__member-count">{{ guild.memberCount }}位成員</p>

          <div class="guild-detail__meta">
            <span v-for="tag in guild.tags" :key="tag" class="guild-detail__tag">{{ tag }}</span>
          </div>

          <p class="guild-detail__description">{{ guild.description }}</p>
          <p class="guild-detail__preference-intro">{{ guild.preferenceIntro }}</p>
          <ul class="guild-detail__preference-list">
            <li v-for="pref in guild.preferences" :key="pref.id">
              <strong>{{ pref.label }}：</strong>{{ pref.desc }}
            </li>
          </ul>
        </section>

        <section class="section guild-detail__book-section">
          <SectionTitle>本期讀物</SectionTitle>

          <div class="current-book">
            <div class="current-book__cover">
              <img v-if="currentBook.cover" :src="currentBook.cover" :alt="currentBook.title" />
            </div>
            <div class="current-book__info">
              <h3 class="current-book__title">{{ currentBook.title }}</h3>
              <p class="current-book__author">{{ currentBook.author }}</p>
              <span class="current-book__tag">{{ currentBook.tag }}</span>
              <p class="current-book__desc">{{ currentBook.description }}</p>
              <div class="current-book__actions">
                <button class="current-book__btn current-book__btn--filled" @click="goToBookDetail">
                  瞭解此書 <IconArrowRight :size="16" stroke-width="2" />
                </button>
                <button
                  class="current-book__btn current-book__btn--outline"
                  :disabled="!isGuildLeader"
                  @click="goToGuildFeature('guild-reading-schedule', true)"
                >
                  設定讀書排程 <IconArrowRight :size="16" stroke-width="2" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <section class="section guild-detail__events-section">
          <SectionTitle>即將到來的活動</SectionTitle>
          <div class="guild-detail__events">
            <GuildEventCard
              v-for="event in events"
              :key="event.eventId"
              v-bind="event"
              @view-event="goToEventDetail"
            ></GuildEventCard>
          </div>
        </section>

        <section class="section guild-detail__discussion-section">
          <SectionTitle>討論區</SectionTitle>
          <div class="discussion-grid">
            <GuildMilestoneCard
              v-for="milestone in milestones"
              :key="milestone.id"
              :milestone-id="milestone.id"
              :index="milestone.index"
              :title="milestone.title"
              :reading-range="milestone.readingRange"
              :complete-date="milestone.completeDate"
              @view-discussion="goToDiscussion"
            />
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-detail {
  max-width: 1100px;
  margin: 0 auto;
  padding: $spacing-lg;

  @include tablet {
    padding: $spacing-md;
  }
}

// ---------- Hero ----------
.guild-detail__hero {
  position: relative;
  min-height: 360px;
  border-radius: 16px;
  background: $neutral-800 url('../../assets/images/guild/guildBackground.png') center / cover no-repeat;
  margin-bottom: $spacing-xl;
}

.guild-detail__settings-btn {
  position: absolute;
  top: $spacing-lg;
  right: $spacing-lg;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: $neutral-100;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $neutral-700;

  &:hover {
    background: $neutral-200;
  }
}

// ---------- 3:9 版面 ----------
.guild-detail__layout {
  display: grid;
  grid-template-columns: 3fr 9fr;
  gap: $spacing-xl;
  align-items: start;

  @include tablet {
    display: flex;
    flex-direction: column;
  }
}

.guild-detail__aside {
  display: flex;
  flex-direction: column;
  gap: $spacing-xl;

  @include tablet {
    display: contents;
  }
}

.guild-detail__main {
  display: flex;
  flex-direction: column;
  gap: $spacing-xl;
  min-width: 0;

  @include tablet {
    display: contents;
  }
}

// ---------- 頭像 ----------
.guild-detail__thumbnail {
  position: relative;
  z-index: 2;
  margin-top: -60px;
  width: 150px;
  height: 150px;
  border-radius: 8px;
  border: 6px solid $neutral-100;
  overflow: hidden;
  background: $neutral-200;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  @include tablet {
    order: 1;
    margin-top: -50px;
  }
}

.guild-detail__intro {
  @include tablet {
    order: 2;
  }
}

.guild-detail__announcement-section {
  @include tablet {
    order: 3;
  }
}

.guild-detail__related-section {
  @include tablet {
    order: 4;
  }
}

.guild-detail__book-section {
  @include tablet {
    order: 5;
  }
}

.guild-detail__events-section {
  @include tablet {
    order: 6;
  }
}

.guild-detail__discussion-section {
  @include tablet {
    order: 7;
  }
}

// ---------- 公會介紹（右欄上方） ----------
.guild-detail__type {
  font-size: $p-sm-size;
  color: $neutral-500;
  margin-bottom: 2px;
}

.guild-detail__name {
  font-size: $h4-size;
  font-weight: $heading-weight;
  color: $neutral-800;
  margin-bottom: $spacing-xs;
}

.guild-detail__member-count {
  font-size: $p-sm-size;
  color: $neutral-500;
  margin-bottom: $spacing-sm;
}

.guild-detail__meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: $spacing-sm;
  margin-bottom: $spacing-md;
}

.guild-detail__tag {
  padding: $spacing-xs $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-sm-size;
  color: $neutral-700;
}

.guild-detail__description {
  color: $neutral-700;
  line-height: 1.7;
  margin-bottom: $spacing-sm;
}

.guild-detail__preference-intro {
  color: $neutral-700;
  margin-bottom: $spacing-xs;
}

.guild-detail__preference-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
  color: $neutral-600;
  font-size: $p-sm-size;
  line-height: 1.6;
}

// ---------- Section 共用 ----------
.section {
  margin-bottom: $spacing-xl;
}

.section__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.section__edit-btn {
  color: $neutral-500;

  &:hover {
    color: $primary;
  }
}

// ---------- 公告欄 ----------
.guild-detail__announcement {
  padding: $spacing-md;
  border: 1px solid $neutral-300;
  border-radius: 12px;
}

.guild-detail__announcement-title {
  font-weight: 700;
  color: $neutral-800;
  margin-bottom: $spacing-sm;
}

.guild-detail__announcement-body {
  max-height: 220px;
  overflow-y: auto;
}

.guild-detail__rules {
  white-space: pre-line; // 保留 \n\n 換行，不加這行文字會全部擠成一行
  color: $neutral-700;
  font-size: $p-xs-size;
  line-height: 1.7;
}

.guild-detail__announcement-textarea {
  width: 100%;
  min-height: 200px;
  border: none;
  outline: none;
  resize: vertical; // 使用者可以自己拖曳調整高度，不想要可以拿掉這行
  font-family: inherit;
  font-size: $p-xs-size;
  line-height: 1.7;
  color: $neutral-700;
  background: transparent;
}

.guild-detail__announcement-actions {
  display: flex;
  gap: $spacing-sm;
  margin-top: $spacing-sm;
}

.guild-detail__announcement-btn {
  padding: $spacing-xs $spacing-md;
  border-radius: $btn-radius-std;
  font-size: $p-sm-size;
  font-weight: 700;

  &--save {
    background: $primary;
    color: $neutral-100;

    &:hover {
      background: $primary-500;
    }
  }

  &--cancel {
    border: 1px solid $neutral-300;
    color: $neutral-600;

    &:hover {
      background: $neutral-200;
    }
  }
}

// ---------- 相關功能 ----------
.related-nav__title {
  font-weight: 700;
  color: $neutral-800;
  margin-bottom: $spacing-sm;
}

.related-nav {
  display: flex;
  flex-direction: column;

  @include tablet {
    flex-direction: row;
    flex-wrap: wrap;
    gap: $spacing-sm;
  }
}

.related-nav__link {
  width: 100%;
  text-align: left;
  padding: $spacing-sm 0;
  border-bottom: 1px solid $neutral-200;
  color: $neutral-700;
  font-size: $p-sm-size;

  &:hover {
    color: $primary;
  }

  &--disabled {
    color: $neutral-400;
    cursor: not-allowed;
  }

  @include tablet {
    width: auto;
    padding: $spacing-xs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-rnd;
  }
}

// ---------- 本期讀物 ----------
.current-book {
  display: flex;
  align-items: flex-start;
  gap: $spacing-lg;
  padding: $spacing-lg;
  border-radius: 12px;
  background: $secondary-100;

  @include mobile {
    flex-direction: column;
  }
}

.current-book__cover {
  flex-shrink: 0;
  width: 160px;
  aspect-ratio: unquote($book-cover-ratio);
  border-radius: 6px;
  overflow: hidden;
  background: $primary-300;

  @include mobile {
    width: 100%;
    max-width: 220px;
  }

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.current-book__info {
  flex: 1;
  min-width: 0;
}

.current-book__title {
  font-size: $h5-size;
  font-weight: $heading-weight;
  color: $neutral-800;
  margin-bottom: 2px;
}

.current-book__author {
  color: $neutral-600;
  margin-bottom: $spacing-sm;
}

.current-book__tag {
  display: inline-block;
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-400;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-600;
  margin-bottom: $spacing-sm;
}

.current-book__desc {
  color: $neutral-700;
  font-size: $p-sm-size;
  line-height: 1.7;
  margin-bottom: $spacing-md;
}

.current-book__actions {
  display: flex;
  gap: $spacing-sm;
  flex-wrap: wrap;
}

.current-book__btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: $spacing-xs $spacing-lg;
  border-radius: $btn-radius-rnd;
  font-size: $p-sm-size;
  font-weight: 700;

  &--filled {
    background: $primary;
    color: $neutral-100;

    &:hover {
      background: $primary-500;
    }
  }

  &--outline {
    border: 1px solid $primary;
    color: $primary;
    background: transparent;

    &:hover {
      background: $primary-100;
    }
  }

  &:disabled {
    opacity: 0.4;
    cursor: not-allowed;

    &:hover {
      background: inherit; // 蓋掉 --filled / --outline 的 hover 效果，disabled 狀態不該有互動回饋
    }
  }
}

// ---------- 即將到來的活動 ----------
.guild-detail__events {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: $spacing-lg;

  @include mobile {
    grid-template-columns: 1fr;
  }
}

// ---------- 討論區 ----------
.discussion-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: $spacing-lg;

  @include tablet {
    grid-template-columns: repeat(2, 1fr);
  }

  @include mobile {
    grid-template-columns: 1fr;
  }
}
</style>
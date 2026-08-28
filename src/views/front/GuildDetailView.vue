<script>
import GuildEventCard from '@/components/front/GuildEventCard.vue'
import GuildMilestoneCard from '@/components/front/GuildMilestoneCard.vue'
import SectionTitle from '@/components/front/SectionTitle.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppButton from '@/components/common/AppButton.vue'
import guildFrame from '@/assets/images/guild/guild-frame.png'
import { useGuildStore } from '@/stores/guild'
import { API_BASE, API_STATIC } from '@/common/api'
import defaultGuildBackground from '@/assets/images/guild/guildBackground.png'

export default {
  components: {
    GuildEventCard,
    GuildMilestoneCard,
    SectionTitle,
    AppIcon,
    AppButton,
  },
  data() {
    return {
      guildFrame,
      guildStore: useGuildStore(),

      guild: {
        memberCount: 56,
        tags: ['奇幻小說', '心靈成長'],
        },
      announcementTitle: '公會守則與規定',
      

      // 左側「相關功能」導覽清單，routeName 是 null 代表對應的路由還沒建立，先不能點
      relatedLinks: [
        { id: 1, label: '建立讀書活動', routeName: 'event-apply', requiresLeader: false },
        { id: 2, label: '設定讀書排程', routeName: 'guild-reading-schedule', requiresLeader: true },
        { id: 3, label: '成員列表', routeName: 'guild-members', requiresLeader: false },
        { id: 4, label: '檢舉事件', routeName: 'report', requiresLeader: false },
        { id: 5, label: '公會設定', routeName: 'guild-settings', requiresLeader: true },
      ],

      currentBook: {
        id: null,
        cover: '',
        title: '',
        author: '',
        tag: '',
        description: '',
      },

      events: [],

      isEditingAnnouncement: false,
      announcementDraft: '',
      isEditingIntro: false,
      introDraft: '',
    }
  },
  created() {
    console.log('公會 ID：', this.$route.params.id)
    this.loadCurrentBook(this.$route.params.id)
    this.loadGuildDetail(this.$route.params.id)
    this.loadEvents()
},
  computed: {
    isGuildLeader: {
      get() {
        return this.guildStore.currentGuild.myRole === '幹部'
      },
      set(value) {
        this.guildStore.currentGuild.myRole = value ? '幹部' : '一般會員'
      },
    },
    linksWithAccess() {
      return this.relatedLinks.map((link) => ({
        ...link,
        isDisabled: !link.routeName || (link.requiresLeader && !this.isGuildLeader),
      }))
    },
    
    displayMilestones() {
      return this.guildStore.currentGuild.milestones.map((m, i) => ({
        milestoneId: m.id,
        index: String(i + 1).padStart(2, '0'),
        title: '閱讀里程碑',
        readingRange: `第${m.startChapter}章節 - 第${m.endChapter}章節`,
        completeDate: m.dueDate.slice(5).replace('-', '/').replace(/^0/, ''),
      }))
    },
  },
  methods: {

    resolveImageUrl(path, fallback){
      if(!path)return fallback;
      return path.startsWith('http') ? path : `${API_STATIC}/src/common/uploads/${path}`;
    },

    loadCurrentBook(guildId) {
    fetch(`${API_BASE}/guild_get_schedule.php?guild_id=${guildId}`)
      .then(res => res.json()).then(data => {
    //console.log(data);
    if(data.success && data.record){
      this.currentBook = {
        id: data.record.book_id,
        cover: data.record.bc_image.startsWith('http')
        ? data.record.bc_image
        : `${API_STATIC}/src/common/uploads/${data.record.bc_image}`,
        title: data.record.title,
        author: data.record.author,
        tag: this.currentBook.tag,
        description: data.record.description,
          }
        }
      })
    },

    loadGuildDetail(guildId) {
      fetch(`${API_BASE}/guild_get_detail.php?guild_id=${guildId}`)
        .then(res => res.json()).then(data => {
          if (data.success && data.guild) {
            this.guildStore.currentGuild.name = data.guild.guild_name
            this.guildStore.currentGuild.introContent = data.guild.intro
            this.guildStore.currentGuild.announcementContent = data.guild.announcement
            this.guildStore.currentGuild.thumbnailImage = data.guild.guild_avatar.startsWith('http')
                    ? data.guild.guild_avatar
                    : `${API_STATIC}/src/common/uploads/${data.guild.guild_avatar}`
            this.guildStore.currentGuild.backgroundUrl = this.resolveImageUrl(data.guild.guild_skin, defaultGuildBackground)
            this.guild.memberCount = data.guild.member_count
            }
        })
    },

    loadEvents(){
      fetch(`${API_BASE}/guild_get_events.php?guild_id=${this.$route.params.id}`).then(res => res.json()).then(data => {
        if(data.success){
          const weekdays = ['日', '一', '二', '三', '四', '五', '六' ];
          this.events = data.events.map(event => {
            const [y, m, d] = event.event_date.split('-');
            const weekday = weekdays[new Date(event.event_date).getDay()];
              return {
                eventId: event.event_id,
                bookName: event.book_title,
                author: event.book_author,
                coverImage: `${API_STATIC}/src/common/uploads/${event.bc_image}`,
                eventType: event.event_type.includes('線上') ? '線上活動' : '線下活動',
                eventTime: `${y}.${m}.${d} (${weekday}) ${event.event_time.slice(0, 5)} - ${event.event_end_time.slice(0, 5)} (GMT+8)`,
                location: event.event_location || event.meeting_url,
                locationNote: '',
                participantCount: event.participant_count,
              };
          });
        }
      });
    },


    goToDiscussion(milestoneId) {
      this.$router.push({ name: 'guild-discussion', params: { id: this.$route.params.id, milestoneId } })
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
      this.$router.push({ name: routeName, params: { id: this.$route.params.id } })
    },
    goToRelatedLink(link) {
      this.goToGuildFeature(link.routeName, link.requiresLeader)
    },
    goToEventDetail(eventId) {
      this.$router.push({ name: 'event-detail', params: { id: this.$route.params.id, eventId } })
    },
    startEditAnnouncement() {
      this.announcementDraft = this.guildStore.currentGuild.announcementContent // 先把目前內容複製一份到草稿
      this.isEditingAnnouncement = true
    },
    saveAnnouncement() {
      this.guildStore.currentGuild.announcementContent = this.announcementDraft // 草稿存回正式內容
      this.isEditingAnnouncement = false
      // 之後這裡要打 API 把新內容存回後端，取代目前純前端記憶體的假動作
    },
    cancelEditAnnouncement() {
      this.isEditingAnnouncement = false // 直接丟掉草稿，announcementContent 完全沒被動過
    },
    startEditIntro() {
      this.introDraft = this.guildStore.currentGuild.introContent
      this.isEditingIntro = true
    },
    saveIntro() {
      this.guildStore.currentGuild.introContent = this.introDraft
      this.isEditingIntro = false
    },
    cancelEditIntro() {
      this.isEditingIntro = false
    },
  },
}
</script>

<template>
  <div class="guild-detail">
    <button class="demo-toggle" @click="isGuildLeader = !isGuildLeader">
      [DEMO] 目前身分：{{ isGuildLeader ? '幹部' : '一般會員' }}（點擊切換）
    </button>

    <!-- Hero -->
    <section class="guild-detail__hero" :style="{backgroundImage: `url(${guildStore.currentGuild.backgroundUrl})`}"></section>

    <!-- 3:9 版面 -->
    <div class="guild-detail__layout">
      <aside class="guild-detail__aside">
        <div class="guild-detail__thumbnail">
          <img v-if="guildStore.currentGuild.thumbnailImage" :src="guildStore.currentGuild.thumbnailImage" :alt="guildStore.currentGuild.name" class="guild-detail__thumbnail-photo" />
          <img :src="guildFrame" alt="" class="guild-detail__thumbnail-frame" />
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
              <AppIcon name="pencil" :size="18" />
            </button>
          </div>

          <div class="guild-detail__announcement">
            <p class="guild-detail__announcement-title">📍 {{ announcementTitle }}</p>

            <div class="guild-detail__announcement-body">
              <p v-if="!isEditingAnnouncement" class="guild-detail__rules">{{ guildStore.currentGuild.announcementContent }}</p>
              <textarea
                v-else
                v-model="announcementDraft"
                class="guild-detail__announcement-textarea"
              ></textarea>
            </div>

            <div v-if="isEditingAnnouncement" class="guild-detail__announcement-actions">
              <AppButton size="xs" @click="saveAnnouncement">儲存</AppButton>
              <AppButton size="xs" variant="outlined" @click="cancelEditAnnouncement">取消</AppButton>
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
          <h1 class="guild-detail__name">{{ guildStore.currentGuild.name }}</h1>
          <p class="guild-detail__member-count">{{ guild.memberCount }}位成員</p>

          <div class="guild-detail__meta">
            <span v-for="tag in guild.tags" :key="tag" class="guild-detail__tag">{{ tag }}</span>
          </div>

          <section class="guild-detail__intro-block">
            <button
              v-if="isGuildLeader && !isEditingIntro"
              class="guild-detail__intro-edit-btn"
              aria-label="編輯公會介紹"
              @click="startEditIntro"
            >
              <AppIcon name="pencil" :size="20" />
            </button>

            <p v-if="!isEditingIntro" class="guild-detail__description">{{ guildStore.currentGuild.introContent }}</p>
            <textarea v-else v-model="introDraft" class="guild-detail__intro-textarea"></textarea>

            <div v-if="isEditingIntro" class="guild-detail__announcement-actions">
              <AppButton size="xs" @click="saveIntro">儲存</AppButton>
              <AppButton size="xs" variant="outlined" @click="cancelEditIntro">取消</AppButton>
            </div>
          </section>
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
                <AppButton :to="{ name: 'book-detail', params: { id: currentBook.id } }">
                  瞭解此書 <AppIcon name="arrow-right" :size="16" />
                </AppButton>
                <AppButton
                  variant="outlined"
                  :to="isGuildLeader ? { name: 'guild-reading-schedule', params: { id: $route.params.id } } : null"
                  :disabled="!isGuildLeader"
                >
                  設定讀書排程 <AppIcon name="arrow-right" :size="16" />
                </AppButton>
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
              v-for="milestone in displayMilestones"
              :key="milestone.milestoneId"
              :milestone-id="milestone.milestoneId"
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
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-top: calc(-1 * #{$spacing-lg});
  min-height: 400px;
  background-color: $neutral-800;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  margin-bottom: $spacing-xl;

  @include tablet {
    margin-top: calc(-1 * #{$spacing-md});
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
  margin-top: calc(-1 * (#{$spacing-xl} + 200px / 3));
  width: 200px;
  aspect-ratio: 1 / 1;
  overflow: hidden;

  @include tablet {
    order: 1;
    margin-top: calc(-1 * (#{$spacing-xl} + 200px / 3));
  }
}

.guild-detail__thumbnail-photo {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.guild-detail__thumbnail-frame {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  pointer-events: none;
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
  color: $primary;
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
  white-space: pre-line;
  padding-right: 32px;
}

.guild-detail__intro-block {
  position: relative;
}

.guild-detail__intro-edit-btn {
  position: absolute;
  top: 0;
  right: 0;
  color: $neutral-500;

  &:hover {
    color: $primary;
  }
}

.guild-detail__intro-textarea {
  @include form-field-base;
  font-size: $p-sm-size;
  line-height: 1.7;
  color: $neutral-700;
  border-radius: 8px;
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
  --btn-surface: #{$secondary-100}; // outlined 按鈕要吃這個區塊的底色,不然會露白底

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

// ---------- DEMO 用切換鈕 ----------
.demo-toggle {
  position: fixed;
  bottom: $spacing-md;
  right: $spacing-md;
  z-index: 999;
  padding: $spacing-xs $spacing-md;
  background: $color-danger;
  color: $neutral-100;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  font-weight: 700;
}
</style>
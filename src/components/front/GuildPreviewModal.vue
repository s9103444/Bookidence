<script>
import AppModal from '@/components/common/AppModal.vue'
import AppButton from '@/components/common/AppButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import SectionTitle from '@/components/front/SectionTitle.vue'

export default {
  name: 'GuildPreviewModal',
  components: {
    AppModal,
    AppButton,
    AppIcon,
    SectionTitle,
  },
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    guild: {
      type: Object,
      default: null,
    },
    joinError: {
      type: String,
      default: '',
    },
    isJoining: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue', 'joined', 'enter-guild'],
  data() {
    return {
      // 申請審核流程先註解掉，目前一律直接加入，只會停在 'preview'
      step: 'preview', // 'preview'（公會簡介）→ 'apply'（審核問卷）→ 'submitted'（送出成功）
      // answers: ['', '', ''],
    }
  },
  computed: {
    isOpen: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      },
    },
    joinButtonLabel() {
      if (this.guild?.isMember) return '前往公會'
      return this.isJoining ? '加入中...' : '加入公會'
    },
  },
  watch: {
    // 彈窗一旦關閉，下次打開要回到最一開始的簡介畫面，不能停在上次填問卷填到一半的狀態
    modelValue(isOpenNow) {
      if (!isOpenNow) {
        this.step = 'preview'
        // this.answers = ['', '', '']
      }
    },
  },
  methods: {
    handleJoinClick() {
      // 目前全部公會都是直接加入，不分審核制，申請審核流程先註解掉（見下方 template 與 submitApplication）
      if (this.guild?.isMember) {
        this.$emit('enter-guild', this.guild.guildId)
        return
      }
      this.$emit('joined', this.guild.guildId)
    },
    // submitApplication() {
    //   // 之後這裡要打 API 把 answers 送到後端，目前先假裝送出成功
    //   this.step = 'submitted'
    // },
    closeModal() {
      this.isOpen = false
    },
    formatDueDate(dateStr) {
      if (!dateStr) return ''
      const [, month, day] = dateStr.split('-')
      return `${Number(month)}/${Number(day)}`
    },
  },
}
</script>

<template>
  <AppModal v-model="isOpen">
    <div v-if="guild" class="guild-preview">
      <!-- 步驟一：公會簡介 -->
      <template v-if="step === 'preview'">
        <div class="guild-preview__header">
          <div class="guild-preview__header-main">
            <div class="guild-preview__avatar">
              <img v-if="guild.avatar" :src="guild.avatar" :alt="guild.name" />
              <span v-else>頭像</span>
            </div>
            <div class="guild-preview__header-info">
              <h1 class="guild-preview__name">{{ guild.name }}</h1>
              <p class="guild-preview__meta">
                <AppIcon name="users" :size="16" />{{ guild.memberCount }}位成員
              </p>
              <p class="guild-preview__meta">
                <AppIcon name="map-pin" :size="16" />{{ guild.region }}
              </p>
            </div>
          </div>

          <AppButton
            class="guild-preview__join-btn"
            :disabled="isJoining"
            @click="handleJoinClick"
          >
            {{ joinButtonLabel }} <AppIcon name="arrow-right" :size="16" />
          </AppButton>
        </div>

        <p v-if="joinError" class="guild-preview__error" role="alert">{{ joinError }}</p>

        <div class="guild-preview__tags">
          <span v-for="tag in guild.tags" :key="tag" class="guild-preview__tag">{{ tag }}</span>
        </div>

        <section class="guild-preview__section">
          <SectionTitle>關於公會</SectionTitle>
          <p class="guild-preview__description">{{ guild.description }}</p>
        </section>

        <section v-if="guild.currentBook" class="guild-preview__section">
          <SectionTitle>正在閱讀中</SectionTitle>
          <div class="guild-preview__book">
            <div class="guild-preview__book-cover">
              <img v-if="guild.currentBook.cover" :src="guild.currentBook.cover" :alt="guild.currentBook.title" />
            </div>
            <div class="guild-preview__book-info">
              <p>書名：{{ guild.currentBook.title }}</p>
              <p>作者：{{ guild.currentBook.author }}</p>
              <p v-if="guild.currentBook.translator">譯者：{{ guild.currentBook.translator }}</p>
              <p>出版社：{{ guild.currentBook.publisher }}</p>
              <p>出版日期：{{ guild.currentBook.publishDate }}</p>
              <p>ISBN：{{ guild.currentBook.isbn }}</p>
            </div>
          </div>
        </section>

        <section v-if="guild.discussionBoards?.length" class="guild-preview__section">
          <SectionTitle>閱讀章節分段</SectionTitle>
          <div class="guild-preview__boards">
            <div v-for="board in guild.discussionBoards" :key="board.id" class="guild-preview__board">
              <p class="guild-preview__board-title">{{ board.title }}</p>
              <span class="guild-preview__board-count">
                <AppIcon name="calendar" :size="14" />{{ formatDueDate(board.dueDate) }}
              </span>
              <p class="guild-preview__board-range"># {{ board.chapterRange }}</p>
            </div>
          </div>
        </section>
      </template>

      <!--
        目前全部公會都是按「加入公會」即可直接加入，不需要走申請審核流程，
        以下步驟二（申請審核問卷）與步驟三（送出成功）先整段註解掉，之後如果要恢復審核制再打開。

      <template v-else-if="step === 'apply'">
        <div class="guild-preview__header">
          <div class="guild-preview__header-main">
            <div class="guild-preview__avatar">
              <img v-if="guild.avatar" :src="guild.avatar" :alt="guild.name" />
              <span v-else>頭像</span>
            </div>
            <div class="guild-preview__header-info">
              <h1 class="guild-preview__name">{{ guild.name }}</h1>
              <p class="guild-preview__meta">
                <AppIcon name="users" :size="16" />{{ guild.memberCount }}位成員
              </p>
              <p class="guild-preview__meta">
                <AppIcon name="map-pin" :size="16" />{{ guild.region }}
              </p>
            </div>
          </div>
        </div>

        <div class="guild-preview__tags">
          <span v-for="tag in guild.tags" :key="tag" class="guild-preview__tag">{{ tag }}</span>
        </div>

        <section class="guild-preview__section">
          <SectionTitle>申請審核</SectionTitle>
          <div
            v-for="(question, index) in guild.applyQuestions"
            :key="index"
            class="guild-preview__question"
          >
            <p class="guild-preview__question-label">Q{{ index + 1 }}.{{ question }}</p>
            <textarea
              v-model="answers[index]"
              class="guild-preview__question-input"
              placeholder="請在此輸入答案"
            ></textarea>
          </div>
        </section>

        <AppButton class="guild-preview__submit-btn" @click="submitApplication">
          送出申請 <AppIcon name="arrow-right" :size="16" />
        </AppButton>
      </template>

      <template v-else-if="step === 'submitted'">
        <div class="guild-preview__submitted">
          <div class="guild-preview__submitted-icon">
            <AppIcon name="check" :size="28" />
          </div>
          <p class="guild-preview__submitted-title">你的申請已送出</p>
          <p class="guild-preview__submitted-desc">待公會審核通過後，就能正式加入「{{ guild.name }}」囉！</p>
          <AppButton @click="closeModal">確認</AppButton>
        </div>
      </template>
      -->
    </div>
  </AppModal>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-preview__header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: $spacing-md;

  @include mobile {
    flex-direction: column;
    align-items: stretch;
  }
}

.guild-preview__header-main {
  display: flex;
  gap: $spacing-md;
  align-items: center;
  min-width: 0;
}

.guild-preview__avatar {
  flex-shrink: 0;
  width: 72px;
  height: 72px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $primary-300;
  color: $neutral-100;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.guild-preview__name {
  margin: 0 0 4px;
  font-size: $h5-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  color: $neutral-800;
}

.guild-preview__meta {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: $p-sm-size;
  color: $neutral-600;
}

.guild-preview__tags {
  display: flex;
  flex-wrap: wrap;
  gap: $spacing-sm;
  margin: $spacing-md 0;
}

.guild-preview__tag {
  padding: $spacing-xs $spacing-md;
  border: 1px solid $primary;
  border-radius: $btn-radius-rnd;
  font-size: $p-sm-size;
  color: $primary;
}

.guild-preview__error {
  margin: $spacing-sm 0 0;
  padding: $spacing-sm $spacing-md;
  border-left: 4px solid $color-danger;
  border-radius: 0 $btn-radius-std $btn-radius-std 0;
  background: $neutral-200;
  font-size: $p-sm-size;
  color: $neutral-800;
}

.guild-preview__join-btn {
  width: auto;
  flex-shrink: 0;

  @include mobile {
    width: 100%;
  }
}

.guild-preview__section {
  margin-bottom: $spacing-lg;

  :deep(.section-title) {
    font-size: $p-lg-size;
    margin-bottom: $spacing-sm;
  }
}

.guild-preview__description {
  color: $neutral-700;
  line-height: 1.7;
  font-size: $p-sm-size;
}

.guild-preview__book {
  display: flex;
  gap: $spacing-md;

  @include mobile {
    flex-direction: column;
  }
}

.guild-preview__book-cover {
  flex-shrink: 0;
  width: 96px;
  aspect-ratio: unquote($book-cover-ratio);
  border-radius: 6px;
  overflow: hidden;
  background: $primary-300;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.guild-preview__book-info {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
  font-size: $p-sm-size;
  color: $neutral-700;
}

.guild-preview__boards {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;
}

.guild-preview__board {
  position: relative;
  padding: $spacing-md;
  border-radius: 8px;
  background: $neutral-200;
}

.guild-preview__board-title {
  font-weight: 700;
  margin-bottom: 4px;
}

.guild-preview__board-count {
  position: absolute;
  top: $spacing-md;
  right: $spacing-md;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 2px $spacing-sm;
  border-radius: $btn-radius-rnd;
  background: $neutral-100;
  font-size: $p-xs-size;
  color: $neutral-600;
}

.guild-preview__board-range {
  font-size: $p-sm-size;
  color: $neutral-600;
}

.guild-preview__question {
  padding: $spacing-md;
  margin-bottom: $spacing-sm;
  border-radius: 8px;
  background: $neutral-200;
}

.guild-preview__question-label {
  font-weight: 700;
  color: $primary;
  margin-bottom: $spacing-xs;
}

.guild-preview__question-input {
  @include form-field-base;
  width: 100%;
  min-height: 90px;
  resize: vertical;
  font-family: inherit;
}

.guild-preview__submit-btn {
  width: 100%;
}

.guild-preview__submitted {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: $spacing-sm;
  padding: $spacing-xl 0;
  text-align: center;
}

.guild-preview__submitted-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: $primary;
  color: $neutral-100;
}

.guild-preview__submitted-title {
  font-size: $h6-size;
  font-weight: 700;
  color: $neutral-800;
}

.guild-preview__submitted-desc {
  color: $neutral-600;
  font-size: $p-sm-size;
}
</style>
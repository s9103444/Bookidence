<script>
import AppIcon from '@/components/common/AppIcon.vue';
import littlePrinceCover from '@/assets/images/little-prince-cover.png';
import CreateGuildStep1 from './guild-create/CreateGuildStep1.vue';
import CreateGuildStep2 from './guild-create/CreateGuildStep2.vue';
import CreateGuildStep3 from './guild-create/CreateGuildStep3.vue';
import CreateGuildStep4 from './guild-create/CreateGuildStep4.vue';

export default {
  name: 'CreateBookGuilds',
  components: {
    AppIcon,
    CreateGuildStep1,
    CreateGuildStep2,
    CreateGuildStep3,
    CreateGuildStep4
  },
  data() {
    return {
      currentStep: 1,

      // Step1
      guildName: '',
      guildAnnouncement: '',
      guildIntro: '',
      guildAvatarFile: null,
      guildAvatarPreview: '',

      // Step2
      bookSearchKeyword: '',
      selectedBook: null,
      // mock 書籍資料,之後接 Google Books API 直接替換這份陣列
      allBooks: [
        {
          id: 1,
          title: '小王子',
          author: '安東尼・聖修伯里',
          category: '文學小說',
          publisher: '大塊文化',
          publishDate: '2018/09/27',
          coverUrl: littlePrinceCover,
          description:
            '小王子從自己的星球出發,一路造訪了六個奇特的星球,遇見形形色色的大人,最後降落在地球上,與一隻狐狸的相遇,讓他學會了愛與責任的意義,也讓每個讀過這本書的大人,重新想起自己曾經也是個孩子。'
        }
      ],
      // 討論板固定至少 1 筆,boardId 用遞增計數器產生,不是真的資料庫 PK
      discussionBoards: [
        { boardId: 1, chapterFrom: '', chapterTo: '', dueDate: '' }
      ],
      nextBoardId: 2,

      // Step3
      reviewQuestions: ['', '', ''],
      // mock 好友清單,之後接好友系統 API 再替換
      inviteFriendList: [
        { id: 1, name: '我是你朋友1', memberCode: 'BKD00014', lastOnlineText: '3天前', avatarUrl: '', invited: false },
        { id: 2, name: '我是你朋友2', memberCode: 'BKD00015', lastOnlineText: '3天前', avatarUrl: '', invited: false },
        { id: 3, name: '我是你朋友3', memberCode: 'BKD00016', lastOnlineText: '3天前', avatarUrl: '', invited: false }
      ]
    };
  },
  computed: {
    stepLabels() {
      return {
        1: '設定公會基本資訊',
        2: '設定書籍',
        3: '其他設定',
        4: '完成!'
      };
    },
    isStep1Valid() {
      return this.guildName.trim() !== '' && this.guildAvatarFile !== null;
    },
    isStep2Valid() {
      const hasBook = this.selectedBook !== null;
      const boardsFilled = this.discussionBoards.every(
        board => board.chapterFrom !== '' && board.chapterTo !== '' && board.dueDate !== ''
      );
      return hasBook && boardsFilled;
    },
    isCurrentStepValid() {
      switch (this.currentStep) {
        case 1:
          return this.isStep1Valid;
        case 2:
          return this.isStep2Valid;
        case 3:
          return true; // 審核題目跟邀請好友都不強制
        default:
          return false;
      }
    },
    progressFillWidth() {
      const fraction = (this.currentStep - 1) / 3;
      return `calc(75% * ${fraction})`;   // 改動:原本是 calc((100% - 80px) * fraction)
    },
    nextButtonLabel() {
      return this.currentStep === 3 ? '完成設定' : '下一步';
    }
  },
  methods: {
    goToPrevStep() {
      this.currentStep -= 1;
    },
    goToNextStep() {
      if (!this.isCurrentStepValid) return;
      if (this.currentStep < 4) {
        this.currentStep += 1;
      }
    },
    handleAvatarSelected({ file, previewUrl }) {
      this.guildAvatarFile = file;
      this.guildAvatarPreview = previewUrl;
    },
    addDiscussionBoard() {
      this.discussionBoards.push({
        boardId: this.nextBoardId,
        chapterFrom: '',
        chapterTo: '',
        dueDate: ''
      });
      this.nextBoardId += 1;
    },
    removeDiscussionBoard(boardId) {
      this.discussionBoards = this.discussionBoards.filter(
        board => board.boardId !== boardId
      );
    },
    updateDiscussionBoard({ boardId, field, value }) {
      const board = this.discussionBoards.find(b => b.boardId === boardId);
      if (board) board[field] = value;
    },
    toggleInviteFriend(friendId) {
      const friend = this.inviteFriendList.find(f => f.id === friendId);
      if (friend) friend.invited = !friend.invited;
    },
    goToMyGuild() {
      // mock 階段還沒有真的新公會 id,先導回公會1
      this.$router.push({ name: 'guild-detail', params: { id: 1 } });
    }
  }
};
</script>

<template>
  <div class="guild-create-page">
    <div class="guild-create-page__heading">
      <button class="guild-create-page__back" @click="$router.back()">
        <AppIcon name="arrow-left" :size="20" />
      </button>
      <h1>創建讀書公會</h1>
    </div>

    <div class="guild-create-progress">
      <div class="guild-create-progress__line-fill" :style="{ width: progressFillWidth }"></div>
      <div class="guild-create-progress__step" v-for="step in 4" :key="step">
        <div
          class="guild-create-progress__circle"
          :class="{
            'guild-create-progress__circle--active': step === currentStep,
            'guild-create-progress__circle--completed': step < currentStep
          }"
        >
          {{ step }}
        </div>
        <span
          class="guild-create-progress__label"
          :class="{ 'guild-create-progress__label--active': step === currentStep }"
        >
          {{ stepLabels[step] }}
        </span>
      </div>
    </div>

    <div class="guild-create-body">
      <CreateGuildStep1
        v-if="currentStep === 1"
        :guild-name="guildName"
        :guild-announcement="guildAnnouncement"
        :guild-intro="guildIntro"
        :guild-avatar-preview="guildAvatarPreview"
        @update:guild-name="guildName = $event"
        @update:guild-announcement="guildAnnouncement = $event"
        @update:guild-intro="guildIntro = $event"
        @avatar-selected="handleAvatarSelected"
      />

      <CreateGuildStep2
        v-else-if="currentStep === 2"
        :book-search-keyword="bookSearchKeyword"
        :all-books="allBooks"
        :selected-book="selectedBook"
        :discussion-boards="discussionBoards"
        @update:book-search-keyword="bookSearchKeyword = $event"
        @select-book="selectedBook = $event"
        @add-board="addDiscussionBoard"
        @remove-board="removeDiscussionBoard"
        @update-board="updateDiscussionBoard"
      />

      <CreateGuildStep3
        v-else-if="currentStep === 3"
        :review-questions="reviewQuestions"
        :invite-friend-list="inviteFriendList"
        @update:review-questions="reviewQuestions = $event"
        @toggle-invite="toggleInviteFriend"
      />

      <CreateGuildStep4
        v-else-if="currentStep === 4"
        @view-my-guild="goToMyGuild"
      />

      <div v-if="currentStep !== 4" class="guild-create-body__buttons">
        <button
          v-show="currentStep !== 1"
          class="guild-create-body__prev"
          @click="goToPrevStep"
        >
          上一步
        </button>
        <button
          class="guild-create-body__next"
          :disabled="!isCurrentStepValid"
          @click="goToNextStep"
        >
          {{ nextButtonLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;

.guild-create-page {
  max-width: 900px;
  margin-inline: auto;
  padding: $spacing-xl;

  &__heading {
    display: flex;
    align-items: center;
    gap: $spacing-xs;
    margin-bottom: $spacing-lg;
    color: $primary;

    h1 {
      color: $primary;
    }
  }

  &__back {
    background: none;
    border: none;
    cursor: pointer;
    color: $primary;
    display: flex;
    align-items: center;
  }
}

.guild-create-progress {
  display: flex;
  justify-content: space-between;
  position: relative;
  padding-bottom: $spacing-xl;

  &__line-fill {
    position: absolute;
    top: 16px;
    left: 12.5%;
    height: 2px;
    background-color: $primary-300;
    z-index: 0;
    transition: width 0.3s ease;
  }

  &::before {
    content: '';
    position: absolute;
    top: 16px;
    left: 12.5%;
    right: 12.5%;
    height: 2px;
    background-color: $neutral-300;
    z-index: 0;
  }

  &__step {
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: center;
    gap: $spacing-xs;
    position: relative;
  }

  &__label {
    font-size: $p-sm-size;
    color: $neutral-500;

    &--active {
      font-weight: $heading-weight;
      color: $neutral-800;
    }
  }

  &__circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid $neutral-300;
    color: $neutral-400;
    background-color: $neutral-100;
    font-weight: $heading-weight;
    z-index: 1;

    &--active {
      background-color: $primary;
      border-color: $primary;
      color: $neutral-100;
    }

    &--completed {
      background-color: $primary-300;
      border-color: $primary-300;
      color: $neutral-100;
    }
  }
}

.guild-create-body {
  &__buttons {
    display: flex;
    justify-content: center;
    gap: $spacing-lg;
    margin-top: $spacing-xl;
  }

  &__prev {
    background-color: transparent;
    color: $primary;
    width: 200px;
    padding: $spacing-xxs;
    border-radius: $btn-radius-std;
    border: 1px solid $primary;
    cursor: pointer;
  }

  &__next {
    background-color: $primary;
    color: $neutral-100;
    width: 200px;
    padding: $spacing-xxs;
    border-radius: $btn-radius-std;
    border: none;
    cursor: pointer;

    &:disabled {
      background-color: $neutral-400;
      cursor: not-allowed;
    }
  }
}
</style>
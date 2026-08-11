<script>
import guildAvatarSquare from '@/assets/images/guild/guildAvatar-square.png'
import littlePrinceCover from '@/assets/images/little-prince-cover.png'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'
import ReportReviewForm from '@/components/front/ReportReviewForm.vue'

export default {
  components: {
    AppIcon,
    AppModal,
    ReportReviewForm,
  },
  data() {
    return {
      guild: {
        guildId: this.$route.params.id,
        name: '壁爐與貓',
        avatar: guildAvatarSquare,
      },
      book: {
        id: 1,
        cover: littlePrinceCover,
        title: '小王子',
        author: '安托萬·德·聖修伯里',
        category: '經典文學',
        translator: '繆詠華',
        publishDate: '2015/10/16',
        publisher: '二魚文化事業有限公司',
        isbn: '000-0000000000',
      },
      milestone: {
        index: '01',
        chapterRange: '第一章節 - 第五章節',
      },
      newCommentText: '',
      openMenuCommentId: null, // 目前哪一則留言的「...」選單被打開，null 代表都沒打開
      likedCommentIds: JSON.parse(localStorage.getItem('bookidence-liked-comments') || '[]'), // 目前使用者按過讚的留言/回覆 ID
      reportedCommentIds: JSON.parse(localStorage.getItem('bookidence-reported-comments') || '[]'), // 目前使用者檢舉過的留言/回覆 ID
      isReportModalOpen: false, // 檢舉表單彈窗開關
      reportingItemId: null, // 目前正在檢舉哪一則留言/回覆的 ID
      reportingItemAuthor: '', // 目前正在檢舉的留言/回覆，作者名稱（傳給表單顯示「被檢舉人」）
      replyingToId: null, // 目前正在回覆哪一則主留言（回覆框只會出現在這一則下面），null 代表沒有任何回覆框打開
      replyText: '', // 回覆框裡使用者正在打的內容
      editingId: null, // 目前正在編輯哪一則留言/回覆，null 代表沒有任何項目在編輯狀態
      editText: '', // 編輯狀態下，使用者正在修改的內容
      pendingImage: '', // 使用者在主留言輸入框選好、還沒送出的圖片（存成 data URL 預覽）
      comments: [
        {
          id: 1,
          author: '讓子彈飛一會',
          role: '副會長',
          time: '3小時前',
          content: '笑死，我把這張「大蟒蛇消化大象」的圖給別人看，她第一句話直接說「這是帽子吧」，跟書裡的大人一模一樣，我笑到不行。後來想想我自己出社會後好像也越來越常這樣，看到什麼都先用最簡單、最方便的方式解讀，懶得多想一層。可能我們每個人心裡都住著一個把大象看成帽子的大人，只是平常沒被拆穿而已。',
          image: '',
          isMine: false,
          reactions: [{ emoji: '❤️', count: 1 }],
          replies: [
            { id: 11, author: '讀字慢半拍', time: '2小時前', content: '真的QQ隨著年紀越大越成了自己討厭的樣子', isMine: false, reactions: [{ emoji: '❤️', count: 1 }] },
            { id: 12, author: '夜貓子讀書會', time: '2小時前', content: '真的QQ隨著年紀越大越成了自己討厭的樣子', isMine: false, reactions: [{ emoji: '❤️', count: 1 }] },
          ],
        },
        {
          id: 2,
          author: '小森愛讀書',
          role: null,
          time: '3小時前',
          content: '我倒是對「你要永遠對你馴養的東西負責」這句印象最深。第一次讀的時候只覺得是狐狸在講道理，但這次搭配小王子跟玫瑰的關係一起看，才意識到這句話其實有點沉重──馴養不是一時興起，而是一種承諾，一旦開始了就不能說放就放。放到現代人際關係裡也很適用，我們常常很輕易地建立連結，卻很少想到「負責」這個部分，責任感常常被忽略，這也是我覺得這本書寫給大人看的原因之一。',
          image: '',
          isMine: true,
          reactions: [],
          replies: [],
        },
        {
          id: 3,
          author: '貓與可頌',
          role: '一般成員',
          time: '3小時前',
          content: '小王子問狐狸「什麼是馴養」的時候，我覺得他其實是在問「什麼是愛」，只是他還不知道那個詞，只能用最直接的方式去問。這種天真反而讓答案更有力量──狐狸沒有給一個很抽象的定義，而是講「建立連結」，還有「你的玫瑰對你來說會變得比全世界任何一朵玫瑰都重要」，用很具體的畫面去解釋一個很抽象的概念。我覺得這也是這本書厲害的地方，它不是用大人的邏輯講道理，而是讓一個孩子問出最單純也最核心的問題，逼我們重新想一遍自己已經習慣不去想的事情。',
          image: '',
          isMine: false,
          reactions: [],
          replies: [],
        },
      ],
    }
  },
  created() {
    console.log('公會 ID：', this.guild.guildId)
    console.log('里程碑 ID：', this.$route.params.milestoneId)
    // 之後這裡打 API，依照 guildId + milestoneId 抓真正的討論串資料
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    goBack() {
      this.$router.push({ name: 'guild-detail', params: { id: this.guild.guildId } })
    },
    goToBookDetail() {
      this.$router.push({ name: 'book-detail', params: { id: this.book.id } })
    },
    submitComment() {
      if (!this.newCommentText.trim() && !this.pendingImage) return
      this.comments.push({
        id: Date.now(),
        author: '小森愛讀書',
        role: null,
        time: '剛剛',
        content: this.newCommentText,
        image: this.pendingImage,
        isMine: true,
        reactions: [],
        replies: [],
      })
      this.newCommentText = ''
      this.pendingImage = ''
      // 之後這裡要打 API 把留言（含圖片）存到後端，取代目前純前端記憶體的假動作
    },
    toggleMenu(commentId) {
      this.openMenuCommentId = this.openMenuCommentId === commentId ? null : commentId
    },
    handleClickOutside(event) {
      // 如果目前有選單開著，而且這次點擊的位置不在「選單本身」或「觸發選單的按鈕」裡面，就關掉選單
      if (this.openMenuCommentId !== null && !event.target.closest('.comment__menu, .comment__menu-btn')) {
        this.openMenuCommentId = null
      }
    },
    isSpecialRole(role) {
      // 只有會長、副會長才顯示身分標籤，一般成員不顯示
      return role === '會長' || role === '副會長'
    },
    isLiked(itemId) {
      return this.likedCommentIds.includes(itemId)
    },
    likeCountFor(item) {
      const heartReaction = item.reactions.find(reaction => reaction.emoji === '❤️')
      return heartReaction ? heartReaction.count : 0
    },
    toggleLike(item) {
      const isCurrentlyLiked = this.likedCommentIds.includes(item.id)
      const heartReaction = item.reactions.find(reaction => reaction.emoji === '❤️')

      if (isCurrentlyLiked) {
        this.likedCommentIds = this.likedCommentIds.filter(id => id !== item.id)
        if (heartReaction) heartReaction.count -= 1
      } else {
        this.likedCommentIds = [...this.likedCommentIds, item.id]
        if (heartReaction) {
          heartReaction.count += 1
        } else {
          item.reactions.push({ emoji: '❤️', count: 1 })
        }
      }
      localStorage.setItem('bookidence-liked-comments', JSON.stringify(this.likedCommentIds))
      // 之後這裡要打 API 把讚的狀態存到後端，取代目前純前端 localStorage 的假動作
    },
    isReported(itemId) {
      return this.reportedCommentIds.includes(itemId)
    },
    openReportModal(item) {
      this.reportingItemId = item.id
      this.reportingItemAuthor = item.author
      this.isReportModalOpen = true
    },
    handleReportSubmit(payload) {
      // payload 長相：{ reason: '人身攻擊', detail: '補充說明' }
      const itemId = this.reportingItemId
      if (itemId !== null && !this.reportedCommentIds.includes(itemId)) {
        this.reportedCommentIds = [...this.reportedCommentIds, itemId]
        localStorage.setItem('bookidence-reported-comments', JSON.stringify(this.reportedCommentIds))
      }
      console.log('檢舉留言 ID：', itemId, '原因：', payload.reason, '補充說明：', payload.detail)
      // 之後這裡要打 API，把 itemId、payload.reason、payload.detail 一起送到後端，取代目前純前端的假動作
      this.isReportModalOpen = false
    },
    toggleReplyBox(commentId) {
      // 回覆框只會有一個同時打開，不管點的是主留言還是子回覆的「回覆」，
      // 都是對同一則主留言（commentId）展開回覆框，因為目前只做兩層留言結構
      this.replyingToId = this.replyingToId === commentId ? null : commentId
      this.replyText = ''
    },
    submitReply(comment) {
      if (!this.replyText.trim()) return
      comment.replies.push({
        id: Date.now(),
        author: '小森愛讀書',
        time: '剛剛',
        content: this.replyText,
        isMine: true,
        reactions: [],
      })
      this.replyText = ''
      this.replyingToId = null
      // 之後這裡要打 API 把回覆存到後端，取代目前純前端記憶體的假動作
    },
    startEdit(item) {
      this.editingId = item.id
      this.editText = item.content
      this.openMenuCommentId = null // 開始編輯後，收起「...」選單
    },
    cancelEdit() {
      this.editingId = null
      this.editText = ''
    },
    saveEdit(item) {
      if (!this.editText.trim()) return
      item.content = this.editText
      this.editingId = null
      this.editText = ''
      // 之後這裡要打 API 把編輯後的內容存到後端，取代目前純前端記憶體的假動作
    },
    deleteComment(commentId) {
      this.comments = this.comments.filter(comment => comment.id !== commentId)
      this.openMenuCommentId = null
      // 之後這裡要打 API 把留言從後端刪除，取代目前純前端記憶體的假動作
    },
    deleteReply(comment, replyId) {
      comment.replies = comment.replies.filter(reply => reply.id !== replyId)
      this.openMenuCommentId = null
      // 之後這裡要打 API 把回覆從後端刪除，取代目前純前端記憶體的假動作
    },
    triggerImageUpload() {
      this.$refs.imageInput.click()
    },
    handleImageSelect(event) {
      const file = event.target.files[0]
      if (!file) return
      const reader = new FileReader()
      reader.onload = (loadEvent) => {
        this.pendingImage = loadEvent.target.result
      }
      reader.readAsDataURL(file)
      event.target.value = '' // 清空，讓使用者可以重複選同一張圖片
    },
    removePendingImage() {
      this.pendingImage = ''
    },
  },
}
</script>

<template>
  <div class="guild-discussion">
    <!-- 左側：公會小標識 + 書籍資訊 -->
    <aside class="discussion-sidebar">
      <div class="discussion-sidebar__guild">
        <img :src="guild.avatar" :alt="guild.name" class="discussion-sidebar__guild-avatar" />
        <p class="discussion-sidebar__guild-name">{{ guild.name }}</p>
      </div>

      <div class="discussion-sidebar__book-cover">
        <img v-if="book.cover" :src="book.cover" :alt="book.title" />
      </div>

      <h2 class="discussion-sidebar__book-title">{{ book.title }}</h2>

      <ul class="discussion-sidebar__book-meta">
        <li>作者：{{ book.author }}</li>
        <li>類別：{{ book.category }}</li>
        <li>譯者：{{ book.translator }}</li>
        <li>出版日期：{{ book.publishDate }}</li>
        <li>出版社：{{ book.publisher }}</li>
        <li>ISBN：{{ book.isbn }}</li>
      </ul>

      <button class="discussion-sidebar__book-btn" @click="goToBookDetail">查看詳細書籍</button>
    </aside>

    <!-- 右側：討論串 -->
    <main class="discussion-main">
      <button class="discussion-main__back" @click="goBack">
        <AppIcon name="chevron-left" :size="18" />
        分段章節討論區：{{ milestone.index }}
      </button>
      <span class="discussion-main__chapter-tag">章節 {{ milestone.chapterRange }}</span>

      <div class="comment-box">
        <textarea
          v-model="newCommentText"
          class="comment-box__textarea"
          placeholder="在這邊留下你的想法"
          @keydown.enter.exact.prevent="submitComment"
        ></textarea>
        <div v-if="pendingImage" class="comment-box__image-preview">
          <img :src="pendingImage" alt="預覽圖片" />
          <button class="comment-box__image-remove" @click="removePendingImage">✕</button>
        </div>
        <div class="comment-box__actions">
          <input ref="imageInput" type="file" accept="image/*" class="comment-box__file-input" @change="handleImageSelect" />
          <button class="comment-box__icon-btn" @click="triggerImageUpload">
            <AppIcon name="image" :size="18" />
          </button>
          <button class="comment-box__submit" @click="submitComment">
            <AppIcon name="send" :size="16" />
          </button>
        </div>
      </div>

      <ul class="comment-list">
        <li v-for="comment in comments" :key="comment.id" class="comment">
          <div class="comment__body">
            <div class="comment__header">
              <span class="comment__author">{{ comment.author }}</span>
              <span v-if="isSpecialRole(comment.role)" class="comment__role">{{ comment.role }}</span>
              <span class="comment__time">{{ comment.time }}</span>
              <button v-if="comment.isMine" class="comment__menu-btn" @click="toggleMenu(comment.id)">
                <AppIcon name="more-horizontal" :size="16" />
              </button>
              <div v-if="comment.isMine && openMenuCommentId === comment.id" class="comment__menu">
                <button @click="startEdit(comment)">編輯留言</button>
                <button @click="deleteComment(comment.id)">刪除留言</button>
              </div>
            </div>

            <template v-if="editingId === comment.id">
              <textarea v-model="editText" class="comment__edit-textarea"></textarea>
              <div class="comment__edit-actions">
                <button class="comment__edit-save" @click="saveEdit(comment)">儲存</button>
                <button class="comment__edit-cancel" @click="cancelEdit">取消</button>
              </div>
            </template>
            <p v-else class="comment__content">{{ comment.content }}</p>

            <img v-if="comment.image" :src="comment.image" class="comment__image" alt="" />
            <div class="comment__reactions">
              <button
                class="comment__like-btn"
                :class="{ 'comment__like-btn--active': isLiked(comment.id) }"
                @click="toggleLike(comment)"
              >
                <AppIcon name="thumbs-up" :size="16" />
                <span v-if="likeCountFor(comment) > 0">{{ likeCountFor(comment) }}</span>
              </button>
              <button
                class="comment__report-btn"
                :class="{ 'comment__report-btn--active': isReported(comment.id) }"
                aria-label="檢舉這則留言"
                @click="openReportModal(comment)"
              >
                <AppIcon name="flag" :size="16" />
              </button>
              <button class="comment__reply-btn" @click="toggleReplyBox(comment.id)">回覆</button>
            </div>

            <div v-if="replyingToId === comment.id" class="reply-box">
              <textarea
                v-model="replyText"
                class="reply-box__textarea"
                placeholder="回覆這則留言..."
                @keydown.enter.exact.prevent="submitReply(comment)"
              ></textarea>
              <button class="reply-box__submit" @click="submitReply(comment)">
                <AppIcon name="send" :size="16" />
              </button>
            </div>
          </div>

          <ul v-if="comment.replies.length" class="comment-list comment-list--nested">
            <li v-for="reply in comment.replies" :key="reply.id" class="comment">
              <div class="comment__body">
                <div class="comment__header">
                  <span class="comment__author">{{ reply.author }}</span>
                  <span class="comment__time">{{ reply.time }}</span>
                  <button v-if="reply.isMine" class="comment__menu-btn" @click="toggleMenu(reply.id)">
                    <AppIcon name="more-horizontal" :size="16" />
                  </button>
                  <div v-if="reply.isMine && openMenuCommentId === reply.id" class="comment__menu">
                    <button @click="startEdit(reply)">編輯留言</button>
                    <button @click="deleteReply(comment, reply.id)">刪除留言</button>
                  </div>
                </div>

                <template v-if="editingId === reply.id">
                  <textarea v-model="editText" class="comment__edit-textarea"></textarea>
                  <div class="comment__edit-actions">
                    <button class="comment__edit-save" @click="saveEdit(reply)">儲存</button>
                    <button class="comment__edit-cancel" @click="cancelEdit">取消</button>
                  </div>
                </template>
                <p v-else class="comment__content">{{ reply.content }}</p>

                <div class="comment__reactions">
                  <button
                    class="comment__like-btn"
                    :class="{ 'comment__like-btn--active': isLiked(reply.id) }"
                    @click="toggleLike(reply)"
                  >
                    <AppIcon name="thumbs-up" :size="16" />
                    <span v-if="likeCountFor(reply) > 0">{{ likeCountFor(reply) }}</span>
                  </button>
                  <button
                    class="comment__report-btn"
                    :class="{ 'comment__report-btn--active': isReported(reply.id) }"
                    aria-label="檢舉這則留言"
                    @click="openReportModal(reply)"
                  >
                    <AppIcon name="flag" :size="16" />
                  </button>
                  <button class="comment__reply-btn" @click="toggleReplyBox(comment.id)">回覆</button>
                </div>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </main>

    <AppModal v-model="isReportModalOpen" title="檢舉留言">
      <ReportReviewForm
        :reported-name="reportingItemAuthor"
        @submit="handleReportSubmit"
      />
    </AppModal>
  </div>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-discussion {
  display: grid;
  grid-template-columns: 3fr 9fr;
  gap: $spacing-xl;
  max-width: 1100px;
  margin: 0;
  padding: $spacing-lg;

  @include tablet {
    grid-template-columns: 1fr;
  }
}

// ---------- 左側 ----------
.discussion-sidebar {
  position: sticky;
  top: $spacing-lg;
  align-self: start;

  @include tablet {
    position: static;
  }
}

.discussion-sidebar__book-cover {
  width: 100%;
  aspect-ratio: unquote($book-cover-ratio);
  border-radius: 8px;
  overflow: hidden;
  background: $primary-300;
  margin-bottom: $spacing-md;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  @include tablet {
    max-width: 200px;
    margin-left: auto;
    margin-right: auto;
  }
}

.discussion-sidebar__book-title {
  font-size: $h6-size;
  font-weight: $heading-weight;
  color: $neutral-800;
  margin-bottom: $spacing-sm;
}

.discussion-sidebar__book-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: $p-sm-size;
  color: $neutral-600;
  margin-bottom: $spacing-md;
}

.discussion-sidebar__book-btn {
  width: 100%;
  padding: $spacing-xs;
  border: 1px solid $primary;
  border-radius: $btn-radius-std;
  color: $primary;
  font-weight: 700;
  font-size: $p-sm-size;
  margin-bottom: $spacing-lg;

  &:hover {
    background: $primary-100;
  }
}

.discussion-sidebar__guild {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  margin-bottom: $spacing-md;
  padding-bottom: $spacing-md;
  border-bottom: 1px solid $neutral-200;
}

.discussion-sidebar__guild-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.discussion-sidebar__guild-name {
  font-size: $p-sm-size;
  font-weight: 700;
  color: $neutral-700;
}

// ---------- 右側 ----------
.discussion-main__back {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: $h6-size;
  font-weight: $heading-weight;
  color: $primary;
  margin-bottom: $spacing-xs;
}

.discussion-main__chapter-tag {
  display: inline-block;
  padding: 2px $spacing-sm;
  background: $secondary-100;
  border-radius: $btn-radius-std;
  font-size: $p-xs-size;
  color: $neutral-700;
  margin-bottom: $spacing-lg;
  margin-left: $spacing-sm;
}

.comment-box {
  border: 2px solid $primary;
  border-radius: 12px;
  padding: $spacing-md;
  margin-bottom: $spacing-xl;
}

.comment-box__textarea {
  width: 100%;
  min-height: 80px;
  border: none;
  outline: none;
  resize: vertical;
  font-family: inherit;
  font-size: $p-sm-size;
  color: $neutral-700;
}

.comment-box__actions {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  justify-content: flex-end;
  color: $neutral-500;
}

.comment-box__submit {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: $primary;
  color: $neutral-100;

  &:hover {
    background: $primary-500;
  }
}

.comment-list {
  display: flex;
  flex-direction: column;
  gap: $spacing-lg;

  &--nested {
    margin-top: $spacing-md;
    margin-left: $spacing-xl;
    padding-left: $spacing-md;
    border-left: 2px solid $neutral-200;
  }
}

.comment__header {
  position: relative;
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  margin-bottom: $spacing-xs;
}

.comment__author {
  font-weight: 700;
  color: $neutral-800;
}

.comment__role {
  padding: 1px $spacing-xs;
  background: transparent;
  border: 1px solid $primary;
  color: $primary;
  font-size: $p-xs-size;
  border-radius: $btn-radius-std;
}

.comment__time {
  font-size: $p-xs-size;
  color: $neutral-400;
}

.comment__menu-btn {
  margin-left: auto;
  color: $neutral-400;

  &:hover {
    color: $neutral-700;
  }
}

.comment__menu {
  position: absolute;
  top: 100%;
  right: 0;
  z-index: 5;
  display: flex;
  flex-direction: column;
  background: $neutral-100;
  border: 1px solid $neutral-300;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  overflow: hidden;

  button {
    padding: $spacing-sm $spacing-md;
    text-align: left;
    font-size: $p-sm-size;
    color: $neutral-700;
    white-space: nowrap;

    &:hover {
      background: $neutral-200;
    }
  }
}

.comment__content {
  font-size: $p-sm-size;
  color: $neutral-700;
  line-height: 1.7;
  margin-bottom: $spacing-sm;
}

.comment__image {
  max-width: 240px;
  border-radius: 8px;
  margin-bottom: $spacing-sm;
}

.comment__reactions {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
}

.comment__reply-btn {
  font-size: $p-xs-size;
  color: $neutral-500;
  font-weight: 700;

  &:hover {
    color: $primary;
  }
}

.comment__like-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-300;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-500;
  font-weight: 700;

  svg {
    flex-shrink: 0;
  }

  &:not(.comment__like-btn--active):hover {
    border-color: $primary;
    color: $primary;
  }
}

.comment__like-btn--active {
  border-color: $primary;
  background: $primary;
  color: $neutral-100;
}

.comment__report-btn {
  display: flex;
  align-items: center;
  color: $neutral-500;

  svg {
    flex-shrink: 0;
  }

  &:hover {
    color: $primary;
  }
}

.comment__report-btn--active {
  color: $primary;
}

.comment-box__file-input {
  display: none;
}

.comment-box__icon-btn {
  display: flex;
  align-items: center;
  color: $neutral-500;

  &:hover {
    color: $primary;
  }
}

.comment-box__image-preview {
  position: relative;
  display: inline-block;
  margin-top: $spacing-sm;

  img {
    max-width: 160px;
    border-radius: 8px;
    display: block;
  }
}

.comment-box__image-remove {
  position: absolute;
  top: -8px;
  right: -8px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: $neutral-800;
  color: $neutral-100;
  font-size: 10px;
}

.reply-box {
  display: flex;
  align-items: flex-end;
  gap: $spacing-sm;
  margin-top: $spacing-sm;
}

.reply-box__textarea {
  flex: 1;
  min-height: 40px;
  border: 1px solid $primary-300;
  border-radius: 8px;
  padding: $spacing-xs $spacing-sm;
  resize: vertical;
  font-family: inherit;
  font-size: $p-sm-size;
  color: $neutral-700;

  &:focus-visible {
    outline: 2px solid $primary-300;
    outline-offset: 2px;
  }
}

.reply-box__submit {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: $primary;
  color: $neutral-100;
  flex-shrink: 0;

  &:hover {
    background: $primary-500;
  }
}

.comment__edit-textarea {
  width: 100%;
  min-height: 60px;
  border: 1px solid $neutral-300;
  border-radius: 8px;
  padding: $spacing-xs $spacing-sm;
  resize: vertical;
  font-family: inherit;
  font-size: $p-sm-size;
  color: $neutral-700;
  margin-bottom: $spacing-xs;
}

.comment__edit-actions {
  display: flex;
  gap: $spacing-sm;
  margin-bottom: $spacing-sm;
}

.comment__edit-save {
  padding: 2px $spacing-sm;
  background: $primary;
  color: $neutral-100;
  border-radius: $btn-radius-std;
  font-size: $p-xs-size;
  font-weight: 700;

  &:hover {
    background: $primary-500;
  }
}

.comment__edit-cancel {
  padding: 2px $spacing-sm;
  border: 1px solid $neutral-300;
  color: $neutral-600;
  border-radius: $btn-radius-std;
  font-size: $p-xs-size;
  font-weight: 700;

  &:hover {
    background: $neutral-200;
  }
}
</style>
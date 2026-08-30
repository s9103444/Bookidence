<script>
import guildAvatarSquare from '@/assets/images/guild/guildAvatar-square.png'
import littlePrinceCover from '@/assets/images/little-prince-cover.png'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'
import AppButton from '@/components/common/AppButton.vue'
import ReportReviewForm from '@/components/front/ReportReviewForm.vue'
import { useGuildStore } from '@/stores/guild'
import { useUserStore } from '@/stores/user'
import { API_BASE, API_STATIC } from '@/common/api'

export default {
  components: {
    AppIcon,
    AppModal,
    AppButton,
    ReportReviewForm,
  },
  data() {
    return {
      context: null, // segment/guild/book 資訊，來自 guild_get_discussion.php 的 context 區塊
      messages: [], // 扁平留言陣列，前端用 parent_message_id 組成兩層巢狀結構
      isLoading: true,
      loadError: '',
      postError: '',
      fallbackGuildAvatar: guildAvatarSquare,
      fallbackBookCover: littlePrinceCover,
      newCommentText: '',
      openMenuCommentId: null, // 目前哪一則留言的「...」選單被打開，null 代表都沒打開
      reportedCommentIds: JSON.parse(localStorage.getItem('bookidence-reported-comments') || '[]'), // 目前使用者檢舉過的留言/回覆 ID
      isReportModalOpen: false, // 檢舉表單彈窗開關
      reportingItemId: null, // 目前正在檢舉哪一則留言/回覆的 ID
      reportingItemAuthor: '', // 目前正在檢舉的留言/回覆，作者名稱（傳給表單顯示「被檢舉人」）
      reportingItemContent: '', // 目前正在檢舉的留言/回覆，內容（存進檢舉詳情當作引用）
      guildStore: useGuildStore(),
      userStore: useUserStore(),
      replyingToId: null, // 目前正在回覆哪一則主留言（回覆框只會出現在這一則下面），null 代表沒有任何回覆框打開
      replyText: '', // 回覆框裡使用者正在打的內容
      editingId: null, // 目前正在編輯哪一則留言/回覆，null 代表沒有任何項目在編輯狀態
      editText: '', // 編輯狀態下，使用者正在修改的內容
    }
  },
  computed: {
    guildAvatarUrl() {
      return this.resolveImageUrl(this.context?.guild_avatar, this.fallbackGuildAvatar)
    },
    bookCoverUrl() {
      return this.resolveImageUrl(this.context?.bc_image, this.fallbackBookCover)
    },
    formattedPublishDate() {
      return this.context?.p_date ? this.context.p_date.replaceAll('-', '/') : ''
    },
    milestoneIndex() {
      return this.context ? String(this.context.sort_order).padStart(2, '0') : ''
    },
    chapterRangeText() {
      if (!this.context) return ''
      return `第${this.context.start_chapter}章節 - 第${this.context.end_chapter}章節`
    },
    topLevelComments() {
      return this.messages
        .filter(m => m.parent_message_id === null)
        .map(m => ({ ...m, replies: this.messages.filter(r => r.parent_message_id === m.message_id) }))
    },
  },
  created() {
    this.loadDiscussion()
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    resolveImageUrl(path, fallback) {
      if (!path) return fallback
      return path.startsWith('http') ? path : `${API_STATIC}/src/common/uploads/${path}`
    },
    async loadDiscussion() {
      this.isLoading = true
      this.loadError = ''
      try {
        const headers = {}
        if (this.userStore.token) {
          headers.Authorization = `Bearer ${this.userStore.token}`
        }
        const res = await fetch(`${API_BASE}/guild_get_discussion.php?segment_id=${this.$route.params.milestoneId}`, { headers })
        const data = await res.json()
        if (!data.success) {
          this.loadError = data.message || '討論串載入失敗，請稍後再試'
          return
        }
        this.context = data.context
        this.messages = data.messages
      } catch (e) {
        this.loadError = '討論串載入失敗，請稍後再試'
      } finally {
        this.isLoading = false
      }
    },
    goBack() {
      this.$router.push({ name: 'guild-detail', params: { id: this.$route.params.id } })
    },
    isMine(message) {
      return message.user_id === this.userStore.userId
    },
    canModify(message) {
      return this.isMine(message) && !message.is_under_review
    },
    isSpecialRole(permissionLevel) {
      // 只有會長、副會長才顯示身分標籤，一般成員不顯示
      return permissionLevel === '會長' || permissionLevel === '副會長'
    },
    formatTime(postedAt) {
      if (!postedAt) return ''
      const posted = new Date(postedAt.replace(' ', 'T'))
      const diffMin = Math.floor((Date.now() - posted.getTime()) / 60000)
      if (diffMin < 1) return '剛剛'
      if (diffMin < 60) return `${diffMin} 分鐘前`
      const diffHour = Math.floor(diffMin / 60)
      if (diffHour < 24) return `${diffHour} 小時前`
      const diffDay = Math.floor(diffHour / 24)
      if (diffDay === 1) return '昨天'
      if (diffDay < 7) return `${diffDay} 天前`
      return `${posted.getFullYear()}/${posted.getMonth() + 1}/${posted.getDate()}`
    },
    async submitComment() {
      if (!this.newCommentText.trim()) return
      this.postError = ''
      try {
        const res = await fetch(`${API_BASE}/guild_post_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({
            segment_id: this.$route.params.milestoneId,
            content: this.newCommentText,
            parent_message_id: null,
          }),
        })
        const result = await res.json()
        if (!result.success) {
          this.postError = result.message || '發表留言失敗'
          return
        }
        this.messages.push({
          message_id: result.message_id,
          parent_message_id: null,
          user_id: this.userStore.userId,
          posted_at: result.posted_at,
          content: this.newCommentText,
          nickname: this.userStore.userName,
          member_code: '',
          permission_level: null,
          like_count: 0,
          is_liked_by_me: false,
          is_under_review: false,
        })
        this.newCommentText = ''
      } catch (e) {
        this.postError = '發表留言失敗，請稍後再試'
      }
    },
    isLiked(item) {
      return item.is_liked_by_me
    },
    likeCountFor(item) {
      return item.like_count || 0
    },
    async toggleLike(item) {
      try {
        const res = await fetch(`${API_BASE}/guild_like_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({ message_id: item.message_id }),
        })
        const result = await res.json()
        if (!result.success) {
          this.postError = result.message || '按讚失敗'
          return
        }
        const target = this.messages.find(m => m.message_id === item.message_id)
        if (target) {
          target.is_liked_by_me = result.liked
          target.like_count = result.like_count
        }
      } catch (e) {
        this.postError = '按讚失敗，請稍後再試'
      }
    },
    isReported(itemId) {
      return this.reportedCommentIds.includes(itemId)
    },
    openReportModal(item) {
      this.reportingItemId = item.message_id
      this.reportingItemAuthor = item.nickname
      this.reportingItemContent = item.content
      this.isReportModalOpen = true
    },
    async handleReportSubmit(payload) {
      try {
        const res = await fetch(`${API_BASE}/guild_report_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({
            message_id: this.reportingItemId,
            target_type: '留言',
            reason: payload.reason,
            reason_detail: payload.detail,
          }),
        })
        const result = await res.json()
        if (result.success) {
          if (!this.reportedCommentIds.includes(this.reportingItemId)) {
            this.reportedCommentIds = [...this.reportedCommentIds, this.reportingItemId]
            localStorage.setItem('bookidence-reported-comments', JSON.stringify(this.reportedCommentIds))
          }
          const target = this.messages.find(m => m.message_id === this.reportingItemId)
          if (target) target.is_under_review = true
        }
      } finally {
        this.isReportModalOpen = false
      }
    },
    toggleReplyBox(commentId) {
      // 回覆框只會有一個同時打開，不管點的是主留言還是子回覆的「回覆」，
      // 都是對同一則主留言（commentId）展開回覆框，因為目前只做兩層留言結構
      this.replyingToId = this.replyingToId === commentId ? null : commentId
      this.replyText = ''
    },
    async submitReply(comment) {
      if (!this.replyText.trim()) return
      this.postError = ''
      try {
        const res = await fetch(`${API_BASE}/guild_post_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({
            segment_id: this.$route.params.milestoneId,
            content: this.replyText,
            parent_message_id: comment.message_id,
          }),
        })
        const result = await res.json()
        if (!result.success) {
          this.postError = result.message || '回覆失敗'
          return
        }
        this.messages.push({
          message_id: result.message_id,
          parent_message_id: comment.message_id,
          user_id: this.userStore.userId,
          posted_at: result.posted_at,
          content: this.replyText,
          nickname: this.userStore.userName,
          member_code: '',
          permission_level: null,
          like_count: 0,
          is_liked_by_me: false,
          is_under_review: false,
        })
        this.replyText = ''
        this.replyingToId = null
      } catch (e) {
        this.postError = '回覆失敗，請稍後再試'
      }
    },
    startEdit(item) {
      this.editingId = item.message_id
      this.editText = item.content
      this.openMenuCommentId = null // 開始編輯後，收起「...」選單
    },
    cancelEdit() {
      this.editingId = null
      this.editText = ''
    },
    async saveEdit(item) {
      if (!this.editText.trim()) return
      try {
        const res = await fetch(`${API_BASE}/guild_edit_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({ message_id: item.message_id, content: this.editText }),
        })
        const result = await res.json()
        if (!result.success) {
          this.postError = result.message || '編輯失敗'
          return
        }
        const target = this.messages.find(m => m.message_id === item.message_id)
        if (target) target.content = this.editText
        this.editingId = null
        this.editText = ''
      } catch (e) {
        this.postError = '編輯失敗，請稍後再試'
      }
    },
    async deleteMessage(messageId) {
      try {
        const res = await fetch(`${API_BASE}/guild_delete_discussion.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.userStore.token}` },
          body: JSON.stringify({ message_id: messageId }),
        })
        const result = await res.json()
        if (!result.success) {
          this.postError = result.message || '刪除失敗'
          return
        }
        this.messages = this.messages.filter(m => m.message_id !== messageId && m.parent_message_id !== messageId)
        this.openMenuCommentId = null
      } catch (e) {
        this.postError = '刪除失敗，請稍後再試'
      }
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
  },
}
</script>

<template>
  <div class="guild-discussion">
    <div v-if="isLoading" class="discussion-status">載入中...</div>
    <div v-else-if="loadError" class="discussion-status discussion-status--error">{{ loadError }}</div>

    <template v-else>
      <!-- 左側：公會小標識 + 書籍資訊 -->
      <aside class="discussion-sidebar">
        <div class="discussion-sidebar__guild">
          <img :src="guildAvatarUrl" :alt="context.guild_name" class="discussion-sidebar__guild-avatar" />
          <p class="discussion-sidebar__guild-name">{{ context.guild_name }}</p>
        </div>

        <div class="discussion-sidebar__book-cover">
          <img :src="bookCoverUrl" :alt="context.title" />
        </div>

        <h2 class="discussion-sidebar__book-title">{{ context.title }}</h2>

        <ul class="discussion-sidebar__book-meta">
          <li>作者：{{ context.author }}</li>
          <li>出版日期：{{ formattedPublishDate }}</li>
          <li>出版社：{{ context.publisher }}</li>
          <li>ISBN：{{ context.isbn }}</li>
        </ul>

        <AppButton
          variant="outlined"
          class="discussion-sidebar__book-btn"
          :to="{ name: 'book-detail', params: { id: context.book_id } }"
        >查看詳細書籍</AppButton>
      </aside>

      <!-- 右側：討論串 -->
      <main class="discussion-main">
        <button class="discussion-main__back" @click="goBack">
          <AppIcon name="chevron-left" :size="18" />
          分段章節討論區：{{ milestoneIndex }}
        </button>
        <span class="discussion-main__chapter-tag">章節 {{ chapterRangeText }}</span>

        <div class="comment-box">
          <textarea
            v-model="newCommentText"
            class="comment-box__textarea"
            placeholder="在這邊留下你的想法"
            @keydown.enter.exact.prevent="submitComment"
          ></textarea>
          <p v-if="postError" class="comment-box__error">{{ postError }}</p>
          <div class="comment-box__actions">
            <button class="comment-box__submit" @click="submitComment">
              <AppIcon name="send" :size="16" />
            </button>
          </div>
        </div>

        <ul class="comment-list">
          <li v-for="comment in topLevelComments" :key="comment.message_id" class="comment">
            <div class="comment__body">
              <div class="comment__header">
                <span class="comment__author">{{ comment.nickname }}</span>
                <span v-if="isSpecialRole(comment.permission_level)" class="comment__role">{{ comment.permission_level }}</span>
                <span v-if="comment.is_under_review" class="comment__review-badge">審核中</span>
                <span class="comment__time">{{ formatTime(comment.posted_at) }}</span>
                <button v-if="canModify(comment)" class="comment__menu-btn" @click="toggleMenu(comment.message_id)">
                  <AppIcon name="more-horizontal" :size="16" />
                </button>
                <div v-if="canModify(comment) && openMenuCommentId === comment.message_id" class="comment__menu">
                  <button @click="startEdit(comment)">編輯留言</button>
                  <button @click="deleteMessage(comment.message_id)">刪除留言</button>
                </div>
              </div>

              <template v-if="editingId === comment.message_id">
                <textarea v-model="editText" class="comment__edit-textarea"></textarea>
                <div class="comment__edit-actions">
                  <AppButton size="xs" @click="saveEdit(comment)">儲存</AppButton>
                  <AppButton size="xs" variant="outlined" @click="cancelEdit">取消</AppButton>
                </div>
              </template>
              <p v-else class="comment__content">{{ comment.content }}</p>

              <div class="comment__reactions">
                <button
                  class="comment__like-btn"
                  :class="{ 'comment__like-btn--active': isLiked(comment) }"
                  @click="toggleLike(comment)"
                >
                  <AppIcon name="thumbs-up" :size="16" />
                  <span v-if="likeCountFor(comment) > 0">{{ likeCountFor(comment) }}</span>
                </button>
                <button
                  class="comment__report-btn"
                  :class="{ 'comment__report-btn--active': isReported(comment.message_id) }"
                  aria-label="檢舉這則留言"
                  @click="openReportModal(comment)"
                >
                  <AppIcon name="flag" :size="16" />
                </button>
                <button class="comment__reply-btn" @click="toggleReplyBox(comment.message_id)">回覆</button>
              </div>

              <div v-if="replyingToId === comment.message_id" class="reply-box">
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
              <li v-for="reply in comment.replies" :key="reply.message_id" class="comment">
                <div class="comment__body">
                  <div class="comment__header">
                    <span class="comment__author">{{ reply.nickname }}</span>
                    <span v-if="reply.is_under_review" class="comment__review-badge">審核中</span>
                    <span class="comment__time">{{ formatTime(reply.posted_at) }}</span>
                    <button v-if="canModify(reply)" class="comment__menu-btn" @click="toggleMenu(reply.message_id)">
                      <AppIcon name="more-horizontal" :size="16" />
                    </button>
                    <div v-if="canModify(reply) && openMenuCommentId === reply.message_id" class="comment__menu">
                      <button @click="startEdit(reply)">編輯留言</button>
                      <button @click="deleteMessage(reply.message_id)">刪除留言</button>
                    </div>
                  </div>

                  <template v-if="editingId === reply.message_id">
                    <textarea v-model="editText" class="comment__edit-textarea"></textarea>
                    <div class="comment__edit-actions">
                      <AppButton size="xs" @click="saveEdit(reply)">儲存</AppButton>
                      <AppButton size="xs" variant="outlined" @click="cancelEdit">取消</AppButton>
                    </div>
                  </template>
                  <p v-else class="comment__content">{{ reply.content }}</p>

                  <div class="comment__reactions">
                    <button
                      class="comment__like-btn"
                      :class="{ 'comment__like-btn--active': isLiked(reply) }"
                      @click="toggleLike(reply)"
                    >
                      <AppIcon name="thumbs-up" :size="16" />
                      <span v-if="likeCountFor(reply) > 0">{{ likeCountFor(reply) }}</span>
                    </button>
                    <button
                      class="comment__report-btn"
                      :class="{ 'comment__report-btn--active': isReported(reply.message_id) }"
                      aria-label="檢舉這則留言"
                      @click="openReportModal(reply)"
                    >
                      <AppIcon name="flag" :size="16" />
                    </button>
                    <button class="comment__reply-btn" @click="toggleReplyBox(comment.message_id)">回覆</button>
                  </div>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </main>
    </template>

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

.discussion-status {
  grid-column: 1 / -1;
  padding: $spacing-xl;
  text-align: center;
  color: $neutral-600;

  &--error {
    color: $color-danger;
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
  margin-bottom: $spacing-lg;
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

.comment-box__error {
  font-size: $p-xs-size;
  color: $color-danger;
  margin-top: $spacing-xs;
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

.comment__review-badge {
  padding: 1px $spacing-xs;
  background: $neutral-200;
  color: $neutral-600;
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
    outline: none;
    border-color: $primary;
    box-shadow: 0 0 0 3px rgba($primary, 0.2);
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
  @include form-field-base;
  font-size: $p-sm-size;          // 覆蓋,保留留言原本字級
  color: $neutral-700;             // 覆蓋,保留原本文字顏色
  background-color: transparent;   // 覆蓋,避免蓋掉留言卡片本身背景
  border-radius: 8px;              // 覆蓋,保留原本圓角(跟 $btn-radius-std 不一定同值)
  margin-bottom: $spacing-xs;      // 保留原本留白,mixin 沒有這個屬性
}

.comment__edit-actions {
  display: flex;
  gap: $spacing-sm;
  margin-bottom: $spacing-sm;
}

</style>

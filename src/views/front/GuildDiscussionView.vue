<script>
import guildAvatarSquare from '../../assets/images/guild/guildAvatar-square.png'
import AppIcon from '@/components/common/AppIcon.vue'

export default {
  components: {
    AppIcon,
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
        cover: '',
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
      comments: [
        {
          id: 1,
          author: '小森讀取中',
          role: '副會長',
          time: '3小時前',
          content: '笑死，我把這張「大蟒蛇消化大象」的圖給別人看，她第一句話直接說「這是帽子吧」，跟書裡的大人一模一樣，我笑到不行。後來想想我自己出社會後好像也越來越常這樣，看到什麼都先用最簡單、最方便的方式解讀，懶得多想一層。可能我們每個人心裡都住著一個把大象看成帽子的大人，只是平常沒被拆穿而已。',
          image: '',
          isMine: false,
          reactions: [{ emoji: '❤️', count: 1 }],
          replies: [
            { id: 11, author: '小森讀取中', time: '2小時前', content: '真的QQ隨著年紀越大越成了自己討厭的樣子', isMine: false, reactions: [{ emoji: '❤️', count: 1 }] },
            { id: 12, author: '小森讀取中', time: '2小時前', content: '真的QQ隨著年紀越大越成了自己討厭的樣子', isMine: false, reactions: [{ emoji: '❤️', count: 1 }] },
          ],
        },
        {
          id: 2,
          author: '小森讀取中',
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
          author: '小森讀取中',
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
  methods: {
    goBack() {
      this.$router.push({ name: 'guild-detail', params: { id: this.guild.guildId } })
    },
    goToBookDetail() {
      this.$router.push({ name: 'book-detail', params: { id: this.book.id } })
    },
    submitComment() {
      if (!this.newCommentText.trim()) return
      this.comments.push({
        id: Date.now(),
        author: '我',
        role: null,
        time: '剛剛',
        content: this.newCommentText,
        image: '',
        isMine: true,
        reactions: [],
        replies: [],
      })
      this.newCommentText = ''
      // 之後這裡要打 API 把留言存到後端，取代目前純前端記憶體的假動作
    },
    toggleMenu(commentId) {
      this.openMenuCommentId = this.openMenuCommentId === commentId ? null : commentId
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
    reportComment(itemId) {
      if (!this.reportedCommentIds.includes(itemId)) {
        this.reportedCommentIds = [...this.reportedCommentIds, itemId]
        localStorage.setItem('bookidence-reported-comments', JSON.stringify(this.reportedCommentIds))
      }
      this.openMenuCommentId = null
      // 之後這裡要打 API 把檢舉紀錄送到後端，取代目前純前端 localStorage 的假動作
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
        <textarea v-model="newCommentText" class="comment-box__textarea" placeholder="在這邊留下你的想法"></textarea>
        <div class="comment-box__actions">
          <AppIcon name="image" :size="18" />
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
                <button>編輯留言</button>
                <button>刪除留言</button>
              </div>
            </div>
            <p class="comment__content">{{ comment.content }}</p>
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
                @click="reportComment(comment.id)"
              >
                <AppIcon name="flag" :size="16" />
              </button>
              <button class="comment__reply-btn">回覆</button>
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
                    <button>編輯留言</button>
                    <button>刪除留言</button>
                  </div>
                </div>
                <p class="comment__content">{{ reply.content }}</p>
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
                    @click="reportComment(reply.id)"
                  >
                    <AppIcon name="flag" :size="16" />
                  </button>
                  <button class="comment__reply-btn">回覆</button>
                </div>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </main>
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
  border: 1px solid $neutral-300;
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
  background: $primary-100;
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

.comment__reaction {
  padding: 2px $spacing-sm;
  background: $neutral-200;
  border-radius: $btn-radius-rnd;
  font-size: $p-xs-size;
  color: $neutral-700;
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
  font-size: $p-xs-size;
  color: $neutral-500;
  font-weight: 700;

  svg {
    flex-shrink: 0;
  }

  &:hover {
    color: $primary;
  }
}

.comment__like-btn--active {
  color: $primary;
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
</style>
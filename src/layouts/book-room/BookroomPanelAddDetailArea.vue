<template>
  <div class="layout">
    <BookRoomNavBar class="nav" color="brown" size="md" @click="$emit('back')"
      >選擇結果</BookRoomNavBar
    >
    <div class="detail-wrapper">
      <section class="book-hero">
        <div class="img-cover">
          <img class="book-hero__cover" :src="book.cover" :alt="book.title" />
        </div>

        <div class="book-hero__info">
          <div class="infos">
            <h1 class="book-hero__title">{{ book.title }}</h1>
            <ul class="book-hero__meta">
              <li>作者：{{ book.author }}</li>
              <li>譯者：{{ book.translator }}</li>
              <li>出版日期：{{ book.publishDate }}</li>
              <li>出版社：{{ book.publisher }}</li>
              <li>ISBN：{{ book.isbn }}</li>
            </ul>
            <ul class="book-hero__stats">
              <li>
                <AppIcon name="user" :size="20"></AppIcon>
                <span>{{ book.reviewCount }}人評論</span>
              </li>
              <li>
                <AppIcon name="heart" :size="20"></AppIcon>
                <span>{{ book.collectCount }}人加入藏書</span>
              </li>
            </ul>
          </div>

          <div class="btns">
            <!-- <button
              type="button"
              class="action-btn book-hero__collect"
              :class="{ 'book-hero__collect--active': isCollected }"
              @click="isCollected = !isCollected"
            >
              <AppIcon
                class="icon"
                :name="isCollected ? 'heart-filled' : 'heart'"
                :size="24"
              ></AppIcon>
              {{ isCollected ? "已加入藏書" : "加入我的藏書" }}
            </button> -->
            <button
              type="button"
              class="action-btn book-hero__book_herf"
              @click="goToBookDetail"
            >
              詳細書籍資訊
            </button>
          </div>
        </div>
      </section>
      <div>
        <div class="review-title">
          <div class="title-content">
            <div class="ink">
              <img
                src="../../assets/images/book-room-element/ink.png"
                alt="ink"
              />
            </div>
            <span>我的心得</span>
          </div>

          <div class="review-action">
            <div class="likes">
              <div class="like-icon">
                <img
                  src="../../assets/images/book-room-element/like-icon.png"
                  alt="like"
                />
              </div>
              <span class="like-num">20</span>
            </div>
            <div class="review-edit" @click="changeToEdit">
              <div>
                <img
                  src="../../assets/images/book-room-element/edit-icon.png"
                  alt="edit"
                />
              </div>
              <span>編輯心得</span>
            </div>
            <div
              class="revirew-delete"
              :class="{ 'is-disabled': isEditingReview }"
              @click="deleteReview()"
            >
              <div>
                <img
                  src="../../assets/images/book-room-element/delete-icon.png"
                  alt="delete"
                />
              </div>
              <span>刪除心得</span>
            </div>
          </div>
        </div>
        <hr />
        <p
          class="my-review-context"
          v-if="!isEditingReview && currentReviewContent"
        >
          {{ currentReviewContent }}
        </p>
        <p
          class="my-review-context"
          v-else-if="!isEditingReview"
          @click="changeToEdit"
          style="cursor: pointer"
        >
          尚未留下心得，點擊新增 &rarr;
        </p>
        <div class="review-area" v-else>
          <textarea
            name="bookReview"
            id="book-review"
            v-model="draftReviewContent"
            class="my-review-context"
          ></textarea>
          <div class="review-action">
            <AppButton
              size="xs"
              color="brown"
              variant="outlined"
              @click="cancelEdit()"
              >取消編輯</AppButton
            >
            <AppButton size="xs" color="brown" @click="confirmEdit()"
              >確認編輯</AppButton
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import AppIcon from "../../components/common/AppIcon.vue";
import AppButton from "../../components/common/AppButton.vue";

export default {
  components: {
    BookRoomNavBar,
    AppIcon,
    AppButton,
  },
  props: {
    book: { type: Object, required: true },
  },
  data() {
    return {
      isCollected: false,
      isEditingReview: false,
      draftReviewContent: "",
      currentReviewContent:
        "第一次看《暮光之城》是高中的時候，跟著同學一起窩在誰家的沙發上，抱著一大包洋芋片，結果整部片子看到一半就沒人在吃東西了。那時候只覺得，天啊，這個森林也太美了吧，永遠濕濕的、霧濛濛的，好像隨時都會有什麼事情發生。貝拉一開始給我的感覺其實有點笨拙，走路會摔跤、講話也不太乾脆，但也正是這種不完美，讓她好像就是我們身邊隨便一個轉學生。而愛德華出現的那一刻，說真的，教室裡那陣風吹過來、他猛然轉頭看她的那個畫面，我到現在都還記得，那種一間鍾情的感覺被拍得太到位了。長大後，反而更喜歡他們之間那種小心翼翼的靠近——愛德華明明那麼危險，卻拼命克制自己，這種「我很想靠近你，但我怕傷害你」的矛盾，比起單純的浪漫台詞更打動我。棒球那場戲也很經典，雷雨天、全家一起玩超能力棒球，畫面感十足，是全片節奏最輕快也最好看的一段。而現在，會覺得有些台詞和節奏帶點青澀的稚氣，貝拉的一些選擇放到現在也會讓人忍不住翻白眼，但那份「愛得很用力、很不顧一切」的青春感，其實正是它迷人的地方。它記錄了我那個年紀對愛情最初、最單純的想像——轟轟烈烈、命中注定、願意為對方放棄一切。曾經是那種會為了一個眼神心跳漏拍一拍的年紀，也許會笑自己當年怎麼那麼容易被感動，但那份感動，其實一直都在。",
    };
  },
  methods: {
    goToBookDetail() {
      this.$router.push({ name: "book-detail", params: { id: this.book.id } });
    },
    changeToEdit() {
      this.draftReviewContent = this.currentReviewContent;
      this.isEditingReview = true;
    },
    confirmEdit() {
      let r = window.confirm("確認更新文章內容嗎？");

      if (this.draftReviewContent == "") {
        alert("編輯欄位不得為空！");
        return;
      } else {
        if (r) {
          this.currentReviewContent = this.draftReviewContent;
          this.isEditingReview = false;
        }
      }
    },
    cancelEdit() {
      let r = window.confirm("要放棄此次編輯內容嗎？");
      if (r) {
        this.draftReviewContent = "";
        this.isEditingReview = false;
      }
    },
    deleteReview() {
      if (this.isEditingReview) return;
      let r = window.confirm("確定要刪除全部心得內容嗎？");
      if (r) {
        this.currentReviewContent = "";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;
.layout {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.nav {
  margin-bottom: 24px;
  flex-shrink: 0;
}

.detail-wrapper {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge

  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}

.btns {
  display: flex;
  gap: 20px;
}

.book-hero {
  display: flex;
  margin-left: 24px;
  margin-bottom: 40px;
}
.book-hero__info {
  color: $brown;
}

.book-hero__title {
  font-size: $h6-size;
}

.book-hero__meta {
  font-size: $p-sm-size;
}
.book-hero__stats li {
  display: flex;
  align-items: center;
  gap: 8px;
  & span {
    font-size: $p-sm-size;
  }
}

.book-hero__collect {
  font-size: $p-sm-size;
  font-weight: $heading-weight;
  padding-inline: $spacing-md;
  padding-block: $spacing-sm;
  border-radius: $btn-radius-std;
  display: flex;
  align-items: center;
  gap: 10px;
  color: $neutral-100;
  background-color: $brown;
  transition:
    background-color 0.15s ease,
    transform 0.15s ease;

  &:hover {
    background-color: darken($brown, 8%);
    transform: translateY(1px);
  }
}

.book-hero__book_herf {
  font-size: $p-sm-size;
  font-weight: $heading-weight;
  color: $brown;
  border: 1px solid $brown;
  padding-inline: $spacing-md;
  padding-block: $spacing-sm;
  border-radius: $btn-radius-std;
  transition:
    background-color 0.15s ease,
    transform 0.15s ease;

  &:hover {
    background-color: rgba($brown, 0.08);
    transform: translateY(1px);
  }
}

.book-hero__info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.review-title {
  display: flex;
  margin-inline: 10px;
  align-items: flex-end;
  justify-content: space-between;
}

.title-content {
  display: flex;
  align-items: flex-end;
  & span {
    color: $brown;
    font-size: $p-lg-size;
    font-weight: $heading-weight;
    display: inline-block;
    margin-left: -8px;
  }
}

.img-cover {
  margin-right: 36px;
  aspect-ratio: unquote($book-cover-ratio);
  width: 180px;

  & img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
}

.review-action {
  display: flex;
  gap: 12px;
  margin-bottom: 2px;
  font-size: $p-sm-size;
  & span {
    color: $brown;
  }
}

.revirew-delete,
.review-edit,
.likes {
  color: $brown;
  display: flex;
  align-items: center;
  gap: 4px;
}

.revirew-delete {
  cursor: pointer;

  &.is-disabled {
    cursor: not-allowed;
    opacity: 0.4;
    pointer-events: none;
  }
}

.likes {
  font-weight: $heading-weight;
}

hr {
  margin-inline: 5px;
  display: block;
  margin-block: 16px;
  border: none;
  border-top: 1px solid $brown-light;
}
.my-review-context {
  padding: 6px;
  height: 200px;
  resize: vertical;
  color: $brown;
  margin-inline: 24px;
  font-size: $p-sm-size;
  line-height: $text-line-height;
  white-space: pre-wrap;
  outline: none;
}
.review-area {
  display: flex;
  flex-direction: column;
}
.review-action {
  cursor: pointer;
  display: flex;
  gap: 24px;
  justify-content: center;
  margin-top: 24px;
}
.action-btn {
  font-size: $label-xs-size;
}

@media (max-width: 960px) {
  .book-hero {
    margin-bottom: 20px;
    margin-left: 0px;
    margin-inline: 10px;
    display: grid;
    grid-template-columns: 1fr;
    justify-items: center;
    grid-template-areas:
      "cover"
      "meta"
      "btn";
  }
  .img-cover {
    grid-area: cover;
    width: 60%;
    margin-right: 0;
    margin-bottom: 16px;
  }
  .infos {
    grid-area: meta;
    width: 100%;
    justify-self: stretch;
    margin-left: 4px;
    text-align: left;
  }

  .btns {
    margin-block: 16px;
    margin-right: auto;
    grid-area: btn;
  }

  .book-hero__info {
    display: contents;
  }

  .review-title {
    flex-direction: column;
    align-items: flex-start;
  }

  .review-action {
    margin-top: 10px;
  }

  hr {
    margin-inline: 24px;
    display: block;
    color: $brown;
    margin-top: 10px;
    margin-bottom: 16px;
  }

  .icon {
    width: 20px;
    height: 20px;
  }
}
</style>

<template>
  <div class="layout">
    <BookRoomNavBar class="nav" color="brown" size="md" @click="$emit('back')"
      >選擇結果</BookRoomNavBar
    >

    <div class="detail-wrapper">
      <div class="select-wrapper">
        <select
          class="status-select"
          v-model="selectedStatus"
          @change="handleStatusChange"
        >
          <option value="未閱讀">未閱讀</option>
          <option value="閱讀中">閱讀中</option>
          <option value="已完讀">已完讀</option>
        </select>
      </div>
      <section class="book-hero">
        <div class="img-cover">
          <img
            :src="`${apiStatic}/uploads/${book.bc_image}`"
            alt="book-cover"
          />
        </div>

        <div class="book-hero__info">
          <div class="infos">
            <div>
              <h1 class="book-hero__title">{{ book.title }}</h1>
            </div>
            <ul class="book-hero__meta">
              <li>作者：{{ book.author }}</li>
              <li>譯者：{{ book.translator }}</li>
              <li>出版日期：{{ book.publishDate }}</li>
              <li>出版社：{{ book.publisher }}</li>
              <li>ISBN：{{ book.isbn }}</li>
            </ul>
            <!-- <ul class="book-hero__stats">
              <li>
                <AppIcon name="user" :size="20"></AppIcon>
                <span>{{ book.reviewCount }}人評論</span>
              </li>
              <li>
                <AppIcon name="heart" :size="20"></AppIcon>
                <span>{{ book.collectCount }}人加入藏書</span>
              </li>
            </ul> -->
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
            <!-- <div class="likes">
              <div class="like-icon">
                <img
                  src="../../assets/images/book-room-element/like-icon.png"
                  alt="like"
                />
              </div>
              <span class="like-num">20</span>
            </div> -->
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
          <div class="article-status-kit">
            <label class="article-status-label" for="review-status"
              >心得公開狀態</label
            >
            <select id="review-status" v-model="reviewStatus">
              <option value="公開">公開</option>
              <option value="非公開">非公開</option>
              <option value="儲存草稿">儲存草稿</option>
            </select>
          </div>
          <div class="review-action">
            <AppButton
              size="xs"
              color="brown"
              variant="outlined"
              @click="cancelEdit()"
              class="cancel-btn"
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
import { API_STATIC } from "../../common/api.js";
import { useBookStore } from "../../stores/book.js";
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
      isEditingReview: false,
      draftReviewContent: "",
      reviewStatus: "非公開",
      selectedStatus: "未閱讀",
    };
  },
  methods: {
    goToBookDetail() {
      this.$router.push({
        name: "book-detail",
        params: { id: this.book.book_id },
      });
    },
    changeToEdit() {
      this.reviewStatus = this.bookStore.myBookThought?.bth_status ?? "非公開";
      this.draftReviewContent = this.currentReviewContent;
      this.isEditingReview = true;
    },
    async confirmEdit() {
      let r = window.confirm("確認更新文章內容嗎？");

      if (this.draftReviewContent == "") {
        alert("編輯欄位不得為空！");
        return;
      } else {
        if (r) {
          const result = await this.bookStore.saveBookThought(
            this.book.book_id,
            this.draftReviewContent,
            this.reviewStatus,
          );
          if (result.success) {
            this.isEditingReview = false;
            this.bookStore.fetchBookThought(this.book.book_id);
          }
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
    async deleteReview() {
      if (this.isEditingReview) return;
      let r = window.confirm("確定要刪除全部心得內容嗎？");
      if (r) {
        const result = await this.bookStore.deleteBookThought(
          this.book.book_id,
        );
        if (result.success) {
          this.isEditingReview = false;
          this.bookStore.fetchBookThought(this.book.book_id);
        }
      }
    },
    handleStatusChange() {
      this.bookStore.updateReadingStatus(
        this.selectedStatus,
        this.book.book_id,
      );
    },
  },
  computed: {
    apiStatic() {
      return API_STATIC;
    },
    bookStore() {
      return useBookStore();
    },
    currentReviewContent() {
      return this.bookStore.myBookThought?.bth_content ?? "";
    },
  },
  mounted() {
    this.bookStore.fetchBookThought(this.book.book_id);
    this.selectedStatus = this.book.r_status ?? "未閱讀";
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
  margin-inline: 12px;
  display: flex;
  margin-bottom: 0px;
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
  gap: 2px;
  align-items: flex-end;
  & span {
    color: $brown;
    font-size: $label-md-size;
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
  gap: 24px;
  justify-content: center;
  margin-top: 24px;
  margin-bottom: 2px;
  font-size: $p-sm-size;
  & span {
    color: $brown;
  }
}

.cancel-btn {
  --btn-surface: #f5ede1;
}

.ink {
  width: 30px;

  & img {
    display: block;
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

.revirew-delete,
.review-edit {
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

.my-review-context {
  height: 200px;
  resize: vertical;
  color: $brown;
  margin-inline: 12px;
  margin-top: 10px;
  font-size: $p-sm-size;
  line-height: $text-line-height;
  white-space: pre-wrap;
  outline: none;
  background-color: rgb(245, 237, 221);
  border: 1px solid rgb(195, 179, 158);
  padding: 8px;
}
.review-area {
  display: flex;
  flex-direction: column;
}
.article-status-kit {
  margin-left: auto;
  margin-block: 10px;
  margin-right: 12px;
  // margin-bottom: 10px;
}
.article-status-label {
  display: inline-block;
  margin-right: 10px;
  font-size: $label-xs-size;
  // font-weight: $heading-weight;
  color: $brown;
  // padding-inline: 6px;
  // border: 0.5px solid $brown;
}
#review-status,
.status-select {
  cursor: pointer;
  color: $brown;
  width: 70px;
  font-size: $label-xs-size;
  appearance: none;
  background: transparent
    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' fill='none' stroke='%23674949' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E")
    no-repeat right 6px center / 10px;
  border: none;
  outline: none;
  padding-left: 6px;
  padding-right: 20px;
  padding-block: 6px;
  border-radius: 5px;
  background-color: rgb(251, 247, 235);
}
.select-wrapper {
  width: 100%;
  display: flex;
  justify-content: flex-end;
}
.status-select {
  margin-bottom: 10px;
  margin-right: 10px;
}

.action-btn {
  font-size: $label-xs-size;
}

@media (max-width: 960px) {
  .book-hero {
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
    margin-bottom: 20px;
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

  .review-action {
    justify-content: center;
    gap: 12px;
    margin-top: 10px;
    & span {
      font-size: $label-xs-size;
    }
  }

  hr {
    margin-inline: 12px;
    display: block;
    color: $brown;
    margin-block: 4px;
  }

  .icon {
    width: 20px;
    height: 20px;
  }
  .my-review-context {
    font-size: $p-xs-size;
  }
}
</style>

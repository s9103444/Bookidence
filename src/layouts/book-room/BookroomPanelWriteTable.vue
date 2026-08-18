<template>
  <div class="table-layout">
    <button class="btn-backto-prepare" @click="$emit('back')"></button>
    <BookRoomNavBar
      class="nav-backto-prepare-mb"
      color="brown"
      size="md"
      @click="$emit('back')"
      >回到我的書房</BookRoomNavBar
    >
    <button
      class="panel-close-btn"
      aria-label="關閉面板"
      @click="$emit('close')"
    >
      <AppIcon name="close" :size="20" />
    </button>
    <div class="layout">
      <div class="panel-titles">
        <img
          class="panel-title"
          src="../../assets/images/book-room-element/now-writting-title.png"
          alt="正在紀錄"
        />
        <img
          class="panel-title"
          src="../../assets/images/book-room-element/review-title.png"
          alt="心得區"
        />
      </div>
      <div class="wrtite-cotent">
        <div class="review-card">
          <div class="img-cover">
            <img :src="book.cover" alt="" />
          </div>
          <div>
            <span class="book-title">{{ book.title }}</span>
            <span class="book-author">{{ book.author }}</span>
            <BookCategoryTag class="tag" color="brown"
              >文學小說</BookCategoryTag
            >
          </div>
        </div>
        <div class="article-content">
          <div class="article-status-kit">
            <label class="article-status-label" for="article-status"
              >心得公開狀態</label
            >
            <select
              name="article-status"
              id="article-status"
              v-model="articleStatus"
            >
              <option value="public">公開</option>
              <option value="private">不公開</option>
            </select>
          </div>
          <textarea
            class="article-txt"
            placeholder="在此輸入心得"
            v-model="articleContent"
          ></textarea>
          <div class="article-actions">
            <AppButton
              size="xs"
              class="act-btn trans"
              color="brown"
              variant="outlined"
              @click="handleSaveDraft"
              >儲存草稿區</AppButton
            >
            <AppButton
              size="xs"
              class="act-btn"
              color="brown"
              @click="handlePublish"
              >發佈心得</AppButton
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BookCategoryTag from "../../components/common/BookCategoryTag.vue";
import AppButton from "../../components/common/AppButton.vue";
import AppIcon from "../../components/common/AppIcon.vue";
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import { useBookStore } from "../../stores/book.js";
export default {
  components: { BookCategoryTag, AppButton, AppIcon, BookRoomNavBar },
  props: {
    book: { type: Object, required: true },
  },
  emits: ["back", "publish", "close"],
  data() {
    const existingDraft = useBookStore().draftBooks.find(
      (d) => d.id === this.book.id
    );
    return {
      articleStatus: existingDraft?.status ?? "public",
      articleContent: existingDraft?.content ?? "",
    };
  },
  computed: {
    bookStore() {
      return useBookStore();
    },
  },
  methods: {
    handleSaveDraft() {
      this.bookStore.upsertDraft({
        id: this.book.id,
        content: this.articleContent,
        status: this.articleStatus,
        lastUpdated: this.formatNow(),
      });
      this.$emit("back");
    },
    handlePublish() {
      if (!this.articleContent.trim()) {
        alert("輸入內容不得為空白！");
        return;
      }
      if (confirm("是否確認發送心得？")) {
        this.bookStore.removeDraft(this.book.id);
        this.$emit("publish", this.book);
      }
    },
    formatNow() {
      const pad = (n) => String(n).padStart(2, "0");
      const d = new Date();
      return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    },
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.table-layout {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 20;
  margin: auto;
  background-image: url(../../assets/images/book-room-element/my-write-table.png);
  background-size: contain;
  aspect-ratio: 979 / 640;
  width: 90%;
  max-width: 1000px;
  background-repeat: no-repeat;
  display: flex;
  flex-direction: column;
}
.btn-backto-prepare {
  position: absolute;
  top: 50%;
  left: -60px;
  width: 60px;
  aspect-ratio: 70 / 246;
  background-repeat: no-repeat;
  background-size: contain;
  background-image: url(../../assets/button/backto-prepare-write.png);
  transform: translateY(-50%);
}

.nav-backto-prepare-mb {
  display: none;
}

.panel-close-btn {
  position: absolute;
  top: 5%;
  right: 5%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $brown;
  z-index: 5;

  &:hover {
    opacity: 0.8;
  }
}

.layout {
  display: flex;
  flex-direction: column;
  width: 54%;
  margin-inline: auto;
  margin-top: 16%;
  flex: 1;
  min-height: 0;
}
.wrtite-cotent {
  width: 100%;
  display: flex;
  gap: 40px;
  flex: 1;
  min-height: 0;
}
.article-content {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  --btn-surface: #f5efe6;
}
.panel-titles {
  display: flex;
  gap: 40%;
}
.review-card {
  flex: 1;
  margin-top: 12%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.img-cover {
  aspect-ratio: unquote($book-cover-ratio);
  width: 50%;
  overflow: hidden;
  margin-bottom: 20px;
}
.tag {
  font-size: 10px;
}
.book-title {
  font-weight: $heading-weight;
  font-size: 90%;
  color: $brown;
}
.book-author {
  display: block;
  font-size: $label-xs-size;
  color: $brown;
  margin-bottom: 4px;
}
.article-status-kit {
  margin-left: auto;
  margin-bottom: 10px;
}
.article-status-label {
  display: inline-block;
  margin-right: 10px;
  font-size: 10px;
  font-weight: $heading-weight;
  color: $brown;
}
#article-status {
  cursor: pointer;
  color: $brown;
  width: 60px;
  font-size: 10px;
  appearance: none;
  background: transparent
    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' fill='none' stroke='%23674949' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E")
    no-repeat right center / 10px;
  padding-right: 14px;
  border: none;
  border-bottom: 1px solid $brown;
  outline: none;
}
.article-txt {
  background: rgb(250, 239, 228);
  padding: 4px;
  flex: 1;
  border: 1px solid $brown-light;
  margin-bottom: 5%;
  resize: none;
  outline: none;
}
.article-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-bottom: 100px;
}
.act-btn {
  white-space: nowrap;

  font-size: 10px;
}
.trans {
  --btn-surface: rgb(250, 241, 215);
}

//RWD
@media (max-width: 960px) {
  .table-layout {
    display: flex;
    background-image: url(../../assets/images/book-room-element/my-write-table-mb.png);
    background-size: contain;
    aspect-ratio: 334 / 479;
    width: 420px;

    background-repeat: no-repeat;
  }
  .layout {
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    width: 65%;
    margin-inline: auto;
  }
  .btn-backto-prepare {
    display: none;
  }
  .nav-backto-prepare-mb {
    display: inline-flex;
    position: absolute;
    top: 4%;
    left: 12%;
    z-index: 5;
  }
  .panel-close-btn {
    top: 2%;
    right: 4%;
    width: 20px;
    height: 20px;
    color: $neutral-100;
  }
  .wrtite-cotent {
    flex: 1;
    min-height: 0;
    flex-direction: column;
    gap: 10px;
  }
  .panel-titles {
    display: none;
  }

  .review-card {
    margin-top: 0;
    flex: none;
    display: flex;
    flex-direction: row;
    align-items: center;
  }
  .img-cover {
    display: flex;
    align-items: center;
    width: 40px;
    margin-right: 10px;
    margin-bottom: 0;
    & img {
      width: 100%;
    }
  }
  .book-title {
    font-size: $label-sm-size;
  }
  .book-author {
    font-size: $label-xs-size;
    margin-bottom: 0;
  }
  .tag {
    display: none;
  }
  .article-status-kit {
    display: flex;
  }
  .article-content {
    min-height: 0;
  }
  .article-txt {
    flex: 1;
    margin-bottom: 14px;
    min-height: 0;
  }
  .article-actions {
    justify-content: center;
    margin-bottom: 60px;
  }
  .act-btn {
    padding-inline: 24px;
    white-space: nowrap;
    font-size: 8px;
  }
}
</style>

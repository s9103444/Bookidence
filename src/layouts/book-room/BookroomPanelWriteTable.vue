<template>
  <div class="table-layout">
    <button class="btn-backto-prepare" @click="$emit('back')"></button>
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
            <img src="../../assets/images/little-prince-cover.png" alt="" />
          </div>
          <div>
            <span class="book-title">我是這本書的書名</span>
            <span class="book-author">我是作者名</span>
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
            <select name="article-status" id="article-status">
              <option value="public">公開</option>
              <option value="private">不公開</option>
            </select>
          </div>
          <textarea class="article-txt" placeholder="在此輸入心得"></textarea>
          <div class="article-actions">
            <AppButton
              size="xs"
              class="act-btn"
              color="brown"
              variant="outlined"
              >儲存草稿區</AppButton
            >
            <AppButton size="xs" class="act-btn" color="brown"
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
export default {
  components: { BookCategoryTag, AppButton },
  emits: ["back"],
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

.layout {
  display: flex;
  flex-direction: column;
  width: 54%;
  margin-inline: auto;
  margin-top: 16%;
}
.wrtite-cotent {
  width: 100%;
  display: flex;
  gap: 40px;
}
.article-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  --btn-surface: #f5efe6;
}
.panel-titles {
  display: flex;
  gap: 40%;
  margin-bottom: 5%;
}
.review-card {
  flex: 1;
  margin-top: 6%;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.img-cover {
  aspect-ratio: 3 / 4;
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
}
.article-txt {
  background: rgb(250, 239, 228);
  flex: 1;
  border: 1px solid $brown-light;
  margin-bottom: 10%;
  resize: none;
}
.article-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
}
.act-btn {
  white-space: nowrap;
  font-size: 8px;
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
    margin-top: 4%;
    margin-bottom: 14%;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    width: 65%;
    margin-inline: auto;
  }
  .btn-backto-prepare {
    left: -12.2%;
    width: 14%;
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
  }
  .act-btn {
    padding-inline: 24px;
    white-space: nowrap;
    font-size: 8px;
  }
}
</style>

<template>
  <div class="card">
    <div class="content-wrapper">
      <div class="book-cover">
        <img
          :src="`${apiStatic}/uploads/${book.bc_image}`"
          alt="book-cover"
        />
      </div>
      <div>
        <div class="content">
          <span class="title">{{ book.title }}</span>
          <span class="sub-title">上次更新時間</span>
          <span class="lastest-time">2026-03-14 09:27:53</span>
        </div>
      </div>
    </div>
    <div class="functions">
      <button @click="$emit('book-select', book)">繼續編輯</button>
      <button @click="$emit('delete-draft', book.book_id)">刪除草稿</button>
    </div>
  </div>
</template>

<script>
import { API_STATIC } from "../../common/api";

export default {
  props: {
    book: {
      type: Object,
      required: true,
    },
  },
  computed: {
    apiStatic() {
      return API_STATIC;
    },
  },
  emits: ["book-select", "delete-draft"],
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.card {
  display: flex;
  justify-content: space-between;
  width: 100%;
  // border: 1px solid red;
  padding: $spacing-md;
  border-radius: $btn-radius-std;
  background-color: #f9f2e9;
  border: 1px solid rgb(231, 217, 195);
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(1px);
}

.book-cover {
  margin-left: $label-xs-size;
  width: 80px;
  aspect-ratio: unquote($book-cover-ratio);
  overflow: hidden;

  & img {
    width: 100%;
  }
}

.content-wrapper {
  display: flex;
  align-items: center;
}

.content {
  display: flex;
  margin-left: $label-lg-size;
  flex-direction: column;
  justify-content: center;

  & .title {
    display: block;
    color: $brown;
    font-weight: $heading-weight;
    font-size: $p-lg-size;
    margin-bottom: $label-xs-size;
  }
}

.sub-title {
  font-weight: $text-weight;
  font-size: $label-xxs-size;
  color: $brown-light;
}

.lastest-time {
  color: $brown-light;
  font-size: $label-xxs-size;
}

.functions {
  display: flex;
  gap: 20px;
  font-size: $p-xs-size;
  font-weight: $text-weight;
  margin-top: auto;

  & button {
    color: $brown;
  }

  & button:hover {
    color: $brown-light;
  }
}

@media (max-width: 960px) {
  .card {
    padding: $spacing-md;
  }

  .book-cover {
    margin-left: 0;
    width: 70px;
  }

  .content {
    margin-left: $label-sm-size;

    margin-right: $label-lg-size;

    & .title {
      font-size: $p-md-size;
      margin-bottom: $p-md-size;
    }
  }

  .functions {
    flex-direction: column;
    gap: 4px;
    font-size: $p-xs-size;
  }
}
</style>

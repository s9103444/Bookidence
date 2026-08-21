<template>
  <div class="book-card" @click="onClick">
    <img
      v-if="selected"
      class="select-icon"
      src="../../assets/images/book-select-icon.png"
      alt="selected"
    />
    <div class="book-img">
      <img :src="`${apiStatic}/src/common/uploads/${book.bc_image}`" alt="bookimg" />
    </div>
    <span class="reading-status">{{ book.r_status }}</span>
    <div class="book-title">
      <h2>{{ book.title }}</h2>
      <div>
        <span>{{ book.author }}</span
        ><span>著作</span>
      </div>
    </div>
    <BookCategoryTag
      class="book-category-tag"
      size="xs"
      color="brown"
      variant="outlined"
      >尚未串定類別API</BookCategoryTag
    >
  </div>
</template>

<script>
import BookCategoryTag from "../../components/common/BookCategoryTag.vue";
import { useBookStore } from "../../stores/book.js";
import { API_STATIC } from "../../common/api.js";
export default {
  props: ["book", "selected"],
  emits: ["select"],
  components: {
    BookCategoryTag,
  },
  computed: {
    apiStatic() {
      return API_STATIC;
    },
  },
  methods: {
    onClick() {
      this.$emit("select", this.book);
    },
  },
};
</script>

<style lang="scss" scoped>
@use "@/assets/scss/abstracts/variables" as *;

.book-card {
  position: relative;
  width: 160px;
  height: fit-content;
  padding: $spacing-md;
  display: flex;
  gap: 2px;
  flex-direction: column;
  cursor: pointer;
  transition: transform 0.2s;
}

.book-card:hover {
  transform: translateY(2px);
}

.select-icon {
  position: absolute;
  bottom: 100px;
  right: 3px;
  width: 60px;
  height: auto;
  z-index: 1;
  pointer-events: none;
}

.book-img {
  width: 120px;
  aspect-ratio: unquote($book-cover-ratio);
  overflow: hidden;
  margin-bottom: 10px;

  & img {
    width: 100%;
  }
}

.reading-status {
  position: absolute;
  top: 24px;
  right: 28px;
  font-size: $label-xxs-size;
  padding-inline: $spacing-sm;
  width: fit-content;
  background-color: rgba(0, 0, 0, 0.6);
  color: $neutral-100;
  border-radius: $btn-radius-rnd;
}

h2 {
  font-size: $p-md-size;
  font-weight: $heading-weight;
  margin-bottom: -4px;
}

span {
  color: $brown;
  display: inline-block;
  margin-right: 2px;
  font-size: $p-xs-size;
}

@media (max-width: 960px) {
  .book-card {
    width: 100%;
  }

  .book-img {
    margin-right: auto;
    width: 120px;
  }

  h2 {
    font-size: $p-md-size;
  }

  .reading-status {
    right: 28px;
  }
}
</style>

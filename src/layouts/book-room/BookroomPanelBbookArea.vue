<template>
  <div class="book-area">
    <SearchBar class="search" color="brown"></SearchBar>
    <div class="btns">
      <AppButton
        class="btn trans"
        color="brown"
        size="xs"
        variant="outlined"
        @click="$emit('switch-tab', 5)"
        >心得草稿區</AppButton
      >
      <AppButton
        class="btn"
        size="xs"
        color="brown"
        @click="$emit('switch-tab', 6)"
        >新增藏書</AppButton
      >
    </div>
    <div class="book-list">
      <BookroomCardStraight
        v-for="book in bookStore.books"
        :key="book.id"
        :book="book"
        @select="$emit('select-book', $event)"
      />
    </div>
  </div>
</template>

<script>
import AppButton from "../../components/common/AppButton.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomCardStraight from "../../components/front/BookroomCardStraight.vue";
import AppIcon from "../../components/common/AppIcon.vue";
import { useBookStore } from "../../stores/book.js";
export default {
  components: {
    AppButton,
    SearchBar,
    BookroomCardStraight,
    AppIcon,
  },
  computed: {
    bookStore() {
      return useBookStore();
    },
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.book-area {
  height: 100%;
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto 1fr;
  grid-template-areas:
    "search btns"
    "list list";
  row-gap: 10px;
}

.search {
  grid-area: search;
}

.btns {
  grid-area: btns;
  display: flex;
  align-items: center;
  margin-left: 10px;
  gap: 12px;
}

.book-list {
  grid-area: list;
  min-height: 0;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: $spacing-md;
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge

  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}

.trans {
  mix-blend-mode: multiply;
}

//RWD
@media (max-width: 960px) {
  .book-area {
    grid-template-columns: 1fr;
    grid-template-rows: auto 1fr auto;
    grid-template-areas:
      "search"
      "list"
      "btns";
    margin: auto;
  }

  .book-list {
    gap: $spacing-sm;
    // margin-left: 1%;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .btns {
    justify-content: center;
    gap: 20px;
  }
}
</style>

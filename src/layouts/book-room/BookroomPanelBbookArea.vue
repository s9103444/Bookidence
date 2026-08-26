<template>
  <div class="book-area">
    <SearchBar class="search" color="brown"></SearchBar>
    <select
      class="status-select"
      v-model="selectedStatus"
      @change="handleStatusChange"
    >
      <option value="全部藏書">全部藏書</option>
      <option value="未閱讀">未閱讀</option>
      <option value="閱讀中">閱讀中</option>
      <option value="已完讀">已完讀</option>
    </select>
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
        v-for="book in bookStore.myBooks"
        :key="book.book_id"
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
  data() {
    return {
      selectedStatus: "全部藏書",
    };
  },
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
  mounted() {
    this.bookStore.fetchMyBooks(this.selectedStatus);
  },
  methods: {
    handleStatusChange() {
      this.bookStore.fetchMyBooks(this.selectedStatus);
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
  grid-template-rows: auto auto 1fr;
  grid-template-areas:
    "search btns"
    "select select"
    "list list";
  row-gap: 10px;
}

.search {
  grid-area: search;
}

.btns {
  grid-area: btns;
  display: flex;
  align-items: flex-start;
  margin-left: 10px;
  gap: 12px;
}

.status-select {
  grid-area: select;
  margin-left: auto;
  cursor: pointer;
  color: $brown;
  width: 76px;
  font-size: $label-sm-size;
  appearance: none;
  background: transparent
    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' fill='none' stroke='%23674949' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E")
    no-repeat right center / 10px;
  padding-right: 14px;
  border: none;
  border-bottom: 1px solid $brown;
  outline: none;
}

.btn.trans {
  --btn-surface: rgb(243, 235, 213);
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

//RWD
@media (max-width: 960px) {
  .book-area {
    grid-template-columns: auto 1fr;
    grid-template-rows: auto auto 1fr;
    grid-template-areas:
      "search search"
      "btns select"
      "list list";
    margin: auto;
  }

  .book-list {
    gap: $spacing-sm;
    // margin-left: 1%;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .btns {
    justify-content: flex-start;
    gap: 20px;
  }
}
</style>

<template>
  <div class="script-layout">
    <div>
      <BookRoomNavBar
        class="nav"
        color="brown"
        size="md"
        @click="$emit('switch-tab', 2)"
        >心得草稿區</BookRoomNavBar
      >
      <SearchBar class="search" color="brown" />
    </div>
    <div class="book-list">
      <BookRoomScriptCard
        v-for="book in draftBookList"
        :key="book.book_id"
        :book="book"
        @book-select="$emit('edit-book', $event)"
        @delete-draft="handleDeleteDraft"
      />
    </div>
  </div>
</template>

<script>
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookRoomScriptCard from "../../components/front/BookRoomScriptCard.vue";
import { useBookStore } from "../../stores/book.js";
export default {
  components: {
    BookRoomNavBar,
    SearchBar,
    BookRoomScriptCard,
  },
  computed: {
    bookStore() {
      return useBookStore();
    },
    draftBookList() {
      return this.bookStore.myBookThoughtList;
    },
  },
  methods: {
    async handleDeleteDraft(id) {
      let r = confirm("確定要刪除草稿嗎？");
      if (r) {
        const result = await this.bookStore.deleteBookThought(id);
        if (result.success) {
          this.bookStore.fetchMyBookThoughtList();
        }
      }
    },
  },
  mounted() {
    this.bookStore.fetchMyBookThoughtList();
  },
  emits: ["switch-tab", "edit-book"],
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.nav {
  margin-bottom: $spacing-sm;
}

.script-layout {
  display: flex;
  flex-direction: column;
  gap: $spacing-md;
  height: 100%;
}

.script-layout > div:first-child {
  flex-shrink: 0;
}

.book-list {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  gap: $spacing-md;
  overflow-y: auto;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge

  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}

@media (max-width: 960px) {
  .nav {
    margin-bottom: $spacing-xs;
    margin-left: $spacing-sm;
  }

  .search {
    margin: $spacing-xs;
  }

  .book-list {
    margin-inline: auto;
  }
}
</style>

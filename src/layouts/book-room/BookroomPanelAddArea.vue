<template>
  <div class="layout">
    <div>
      <BookRoomNavBar class="nav" color="brown" size="md"
        >新增藏書</BookRoomNavBar
      >
      <SearchBar class="search" color="brown" />
    </div>
    <div class="book-list">
      <BookroomSearchCard></BookroomSearchCard>
      <BookroomSearchCard></BookroomSearchCard>
      <BookroomSearchCard></BookroomSearchCard>
      <BookroomSearchCard></BookroomSearchCard>
    </div>
  </div>
</template>

<script>
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomSearchCard from "../../components/front/BookroomSearchCard.vue";
export default {
  components: {
    BookRoomNavBar,
    SearchBar,
    BookroomSearchCard,
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;
.nav {
  margin-bottom: $spacing-sm;
}
.layout {
  display: flex;
  flex-direction: column;
  gap: $spacing-md;
  height: 100%;
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

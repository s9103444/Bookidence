<template>
  <div class="layout">
    <div>
      <BookRoomNavBar class="nav" color="brown" size="md">心得草稿區</BookRoomNavBar>
      <SearchBar class="search" color="brown" />
    </div>
    <div class="book-list">
      <BookRoomScriptCard />
      <BookRoomScriptCard />
      <BookRoomScriptCard />
      <BookRoomScriptCard />
      <BookRoomScriptCard />
    </div>
  </div>
</template>

<script>
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookRoomScriptCard from "../../components/front/BookRoomScriptCard.vue";
export default {
  components: {
    BookRoomNavBar,
    SearchBar,
    BookRoomScriptCard,
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

.layout > div:first-child {
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

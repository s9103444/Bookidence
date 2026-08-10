<template>
  <div class="layout">
    <div>
      <BookRoomNavBar 
      class="nav"
      color="brown" 
      size="md"
       @click="$emit('switch-tab', 2)"
      >心得草稿區</BookRoomNavBar>
      <SearchBar class="search" color="brown" />
    </div>
    <div class="book-list">
      <BookRoomScriptCard
      v-for="book in draftBooks"
      :key="book.id"
      :book="book"
      @book-select="$emit('edit-book', $event)"
      />
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
  data(){
    return{
      draftBooks:[
        {id:1,
        title:"小王子",
        author:"聖修伯里",
        lastUpdated: "2026-03-14 09:27:53",
        },
        {id:2,
        title:"小王子",
        author:"聖修伯里",
        lastUpdated: "2026-03-14 09:27:53",
        },
        {id:3,
        title:"小王子",
        author:"聖修伯里",
        lastUpdated: "2026-03-14 09:27:53",
        },
      ]
    }
  },
  emits: ["switch-tab", "edit-book"],
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

<template>
  <div style="display: flex">
    <h1>Test 測試元件預覽用頁面</h1>
    <SearchBar color="primary" size="sm"></SearchBar>
  </div>
  <br />
  <BookroomPanelBbookArea></BookroomPanelBbookArea>
</template>

<script>
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomCardStraight from "../../components/front/BookroomCardStraight.vue";
import BookroomPanelBbookArea from "../../layouts/book-room/BookroomPanelBbookArea.vue";

export default {
  components: {
    SearchBar,
    BookroomCardStraight,
    BookroomPanelBbookArea,
  },
};
</script>

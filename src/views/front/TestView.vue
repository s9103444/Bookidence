<template>
  <div style="display: flex">
    <h1>Test 測試元件預覽用頁面</h1>
    <SearchBar color="primary" size="md"></SearchBar>
  </div>
  <br />

  <BookroomPanelScriptArea></BookroomPanelScriptArea>
</template>

<script>
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomCardStraight from "../../components/front/BookroomCardStraight.vue";
import BookroomPanelBbookArea from "../../layouts/book-room/BookroomPanelBbookArea.vue";
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import BookroomPanelScriptArea from "../../layouts/book-room/BookroomPanelScriptArea.vue";

export default {
  components: {
    SearchBar,
    BookroomCardStraight,
    BookroomPanelBbookArea,
    BookRoomNavBar,
    BookroomPanelScriptArea,
  },
};
</script>

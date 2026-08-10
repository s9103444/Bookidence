<template>
  <div style="display: flex">
    <!-- <h1>Test 測試元件預覽用頁面</h1> -->
    <br />
  </div>
  <br />
  <BookroomSearchCard></BookroomSearchCard>
</template>

<script>
import BookroomPanelAddArea from "../../layouts/book-room/BookroomPanelAddArea.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomCardStraight from "../../components/front/BookroomCardStraight.vue";
import BookroomPanelBbookArea from "../../layouts/book-room/BookroomPanelBbookArea.vue";
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
import BookroomPanelScriptArea from "../../layouts/book-room/BookroomPanelScriptArea.vue";
import BookroomSearchCard from "../../components/front/BookroomSearchCard.vue";
import AppIcon from "../../components/common/AppIcon.vue";
import BookroomPanelProfileArea from "../../layouts/book-room/BookroomPanelProfileArea.vue";
import BookroomPanelAppearanceArea from "../../layouts/book-room/BookroomPanelAppearanceArea.vue";
import BookroomPanelWriteTable from "../../layouts/book-room/BookroomPanelWriteTable.vue";

export default {
  components: {
    SearchBar,
    BookroomCardStraight,
    BookroomPanelBbookArea,
    BookRoomNavBar,
    BookroomPanelScriptArea,
    BookroomSearchCard,
    AppIcon,
    BookroomPanelAddArea,
    BookroomPanelProfileArea,
    BookroomPanelAppearanceArea,
    BookroomPanelWriteTable,
  },
};
</script>

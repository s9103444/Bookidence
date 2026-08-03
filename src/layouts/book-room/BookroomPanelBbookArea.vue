<template>
  <div class="heading">
    <SearchBar color="brown"></SearchBar>
    <div class="btns">
      <AppButton class="btn trans" color="brown" variant="outlined"
        >心得草稿區</AppButton
      >
      <AppButton class="btn" color="brown">新增藏書</AppButton>
    </div>
  </div>
  <div class="book-list">
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
    <BookroomCardStraight />
  </div>
</template>

<script>
import AppButton from "../../components/common/AppButton.vue";
import SearchBar from "../../components/common/SearchBar.vue";
import BookroomCardStraight from "../../components/front/BookroomCardStraight.vue";
import AppIcon from "../../components/common/AppIcon.vue";
export default {
  components: {
    AppButton,
    SearchBar,
    BookroomCardStraight,
    AppIcon,
  },
};
</script>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;
.heading {
  display: flex;
  justify-content: space-between;
}
.book-list {
  margin-top: 10px;
  height: 420px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: $spacing-md;
  overflow: auto;
  scrollbar-width: none; // Firefox
  -ms-overflow-style: none; // 舊版 IE/Edge
  &::-webkit-scrollbar {
    // Chrome / Safari /新版 Edge
    display: none;
  }
}
.btn {
  margin-left: 18px;
}
.trans {
  mix-blend-mode: multiply;
}

//RWD
@media (max-width: 960px) {
  .heading {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .btns {
    display: flex;
  }
}
</style>

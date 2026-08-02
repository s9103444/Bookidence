<template>
  <div class="book-card">
    <div class="book-img">
      <img src="../../assets/images/little-prince-cover.png" alt="bookimg" />
    </div>
    <span class="reading-status">閱讀中</span>
    <div class="book-title">
      <h2>秘密中的秘密</h2>
      <div><span>丹・布朗</span><span>著作</span></div>
    </div>
    <BookCategoryTag
      class="book-category-tag"
      size="xs"
      color="brown"
      variant="outlined"
      >心理成長</BookCategoryTag
    >
  </div>
</template>

<script>
import BookCategoryTag from "../../components/common/BookCategoryTag.vue";
export default {
  components: {
    BookCategoryTag,
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/scss/abstracts/variables";

.book-card {
  position: relative;
  width: 160px;
  padding: $spacing-md;
  display: flex;
  gap: 2px;
  flex-direction: column;
}
.book-img {
  width: 120px;
  aspect-ratio: 3/4;
  object-fit: cover;
  margin-bottom: 10px;
  & img {
    width: 100%;
  }
}
.reading-status {
  position: absolute;
  top: 24px;
  right: 28px;
  font-size: $label-xxs-size;
  padding-inline: $spacing-sm;
  width: fit-content;
  background-color: rgba(0, 0, 0, 0.6);
  color: $neutral-100;
  border-radius: $btn-radius-rnd;
}

h2 {
  font-size: $p-md-size;
  font-weight: $heading-weight;
  margin-bottom: -4px;
}

span {
  color: $brown;
  display: inline-block;
  margin-right: 2px;
  font-size: $p-xs-size;
}
</style>

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
@use "@/assets/scss/abstracts/variables" as *;

.book-card {
  position: relative;
  width: 160px;
  padding: $spacing-md;
  display: flex;
  gap: 2px;
  flex-direction: column;
  cursor: pointer;
  transition: transform 0.2s;
}

.book-card:hover {
  transform: translateY(2px);
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

@media (max-width: 960px) {
  .book-card {
    width: 100%;
  }

  .book-img {
    margin-right: auto;
    width: 120px;
  }

  h2 {
    font-size: $p-md-size;
  }

  .reading-status {
    right: 28px;
  }
}
</style>

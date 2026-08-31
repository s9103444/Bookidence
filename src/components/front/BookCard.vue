<script setup>
import AppButton from "@/components/common/AppButton.vue";
import AppIcon from "@/components/common/AppIcon.vue";
import BookCategoryTag from "@/components/common/BookCategoryTag.vue";

defineProps({
  coverImage: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  author: {
    type: String,
    default: "",
  },
  categories: {
    type: Array,
    default: () => [],
  },
  description: {
    type: String,
    default: "",
  },
  bookId: {
    type: [String, Number],
    required: true,
  },
});
</script>

<template>
  <article class="book-card">
    <div class="book-card__cover">
      <img :src="coverImage" :alt="title" />
    </div>

    <div class="book-card__body">
      <h3 class="book-card__title">
        {{ title }}
      </h3>
      <p class="book-card__author">{{ author }}</p>
      <ul class="book-card__tags">
        <li v-for="category in categories" :key="category">
          <BookCategoryTag size="sm" color="primary" variant="outlined"
            >{{ category }}
          </BookCategoryTag>
        </li>
      </ul>
      <p class="book-card__desc">{{ description }}</p>
    </div>
    <AppButton color="primary" size="sm" :to="`/books/${bookId}`">
      查看詳情
      <AppIcon name="arrow-right"></AppIcon>
    </AppButton>
  </article>
</template>

<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.book-card {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
  height: 100%;
  padding: 16px;
  border-radius: 5px;
  max-width: 319px;
  background-color: #fff8ea;
}

.book-card__cover {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 237px;
  flex-shrink: 0;
}

.book-card__cover img {
  width: 159px;
  box-shadow: 0 4px 4px rgb(0 0 0 / 0.25);
}

.book-card__body {
  display: flex;
  flex-direction: column;
  gap: 4px;
  flex: 1;
}

.book-card__title {
  font-size: $label-lg-size;
  font-weight: $text-weight;
  color: $neutral-800;
  letter-spacing: $letter-spacing-base;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  overflow: hidden;
}

.book-card__author {
  font-size: $label-md-size;
  font-weight: $text-weight;
  color: $neutral-600;
  letter-spacing: $letter-spacing-base;
}

.book-card__tags {
  display: flex;
  gap: 8px;
  margin-top: 12px;
}

.book-card__desc {
  margin-top: 13px;
  font-size: $label-xs-size;
  font-weight: $text-weight;
  color: $neutral-700;
  letter-spacing: $letter-spacing-base;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  overflow: hidden;
}
</style>

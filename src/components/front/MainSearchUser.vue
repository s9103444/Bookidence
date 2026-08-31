<style lang="scss" scoped>
@use "../../assets/scss/abstracts/variables" as *;

.card {
  padding: 12px;
  display: flex;
  gap: 24px;
  align-items: center;
  position: relative;
  cursor: pointer;

  &::before {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    bottom: -2px;
    height: 1px;
    width: 100%;
    background-color: $neutral-300;
  }
}

.avatar {
  width: 100px;
  min-width: 100px;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background-color: $secondary-100;

  & canvas {
    object-fit: cover;
    transform: scale(0.9);
  }
}

.content {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: space-between;
  gap: $spacing-xs;
}

.title {
  display: block;
  color: $primary;
  font-weight: $heading-weight;
  font-size: $p-md-size;
}

hr {
  border: 0.5px solid $neutral-400;
  margin-block: 6px;
}

.info {
  color: $neutral-500;
  display: flex;
  gap: 10px;

  font-weight: $text-weight;
  font-size: $label-xs-size;
}

.categories {
  display: flex;
  gap: $spacing-xs;
}

@media (max-width: 960px) {
  .card {
    width: 100%;
  }
  .content {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }
  .info {
    flex-direction: column;
    gap: 0;
  }
}
</style>

<template>
  <div class="card" @click="$router.push({ name: 'study' })">
    <div class="avatar">
      <PhotoSticker :userId="user.user_id" :width="100" />
    </div>
    <div class="content">
      <div class="infos">
        <div>
          <span class="title">{{ user.nickname }}</span>
        </div>
        <div class="info">
          <span>{{ user.member_code }}</span>
        </div>
        <div class="categories">
          <BookCategoryTag
            v-for="category in user.favoriteCategories"
            :key="category"
            size="xxs"
            color="primary"
            variant="outlined"
            >{{ category }}</BookCategoryTag
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BookCategoryTag from "../common/BookCategoryTag.vue";
import PhotoSticker from "./PhotoSticker.vue";

export default {
  components: {
    BookCategoryTag,
    PhotoSticker,
  },
  props: {
    user: { type: Object, required: true },
  },
};
</script>

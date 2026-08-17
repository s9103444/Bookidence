<script setup>
import AppModal from "./AppModal.vue";
import featherImg from "../../assets/images/book-room-element/review-published-feather.png";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  book: {
    type: Object,
    default: null,
  },
});

defineEmits(["update:modelValue"]);
</script>

<template>
  <AppModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="review-published">
      <h3 class="review-published__title">心得發布成功！</h3>

      <!-- <div class="review-published__reward">
        <span class="review-published__reward-label">獎勵經驗值</span>
        <span class="review-published__reward-value">+500 epx</span>
      </div> -->

      <div class="review-published__art" v-if="book">
        <img class="review-published__cover" :src="book.cover" :alt="book.title" />
        <img class="review-published__feather" :src="featherImg" alt="" />
      </div>

      <p class="review-published__desc">
        翻完了一本書，也留下了一段思考。<br />
        繼續下一段閱讀旅程吧！
      </p>

      <RouterLink
        v-if="book"
        :to="`/books/${book.id}`"
        class="review-published__link"
        >看看其他人對於這本書的想法？</RouterLink
      >
    </div>
  </AppModal>
</template>

<style scoped lang="scss">
@use "../../assets/scss/abstracts/variables" as *;

.review-published {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  animation: review-published-in 0.35s ease-out;
}

@keyframes review-published-in {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.review-published__title {
  font-size: $h3-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
  margin-bottom: $spacing-lg;
}

.review-published__reward {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: $spacing-xs $spacing-md;
  border-radius: 5px;
  background-color: $secondary-500;
  font-size: $p-lg-size;
  font-weight: $heading-weight;
  letter-spacing: $letter-spacing-base;
  margin-bottom: $spacing-lg;
}

.review-published__reward-label {
  color: $primary;
}

.review-published__reward-value {
  color: $primary-300;
}

.review-published__art {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 145px;
  aspect-ratio: 145 / 176;
  margin-bottom: $spacing-lg;
}

.review-published__cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.review-published__feather {
  position: absolute;
  right: -34px;
  bottom: -20px;
  width: 73px;
  height: auto;
}

.review-published__desc {
  font-size: $p-xs-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;
  margin-bottom: $spacing-lg;
}

.review-published__link {
  font-size: $label-xxs-size;
  font-weight: $text-weight;
  letter-spacing: $letter-spacing-base;
  color: $neutral-500;
  text-decoration: underline;
}
</style>

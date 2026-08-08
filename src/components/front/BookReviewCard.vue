<!--
BookReviewCard 書籍評論卡（書籍詳情頁「書籍心得公開區」用）

用詞說明：設計稿標題寫「心得」，心智圖寫「評論」，程式裡一律用「評論 / review」，
標題那五個字照設計稿走。等 s9103444 統一用詞後再一起改。

用法：
<BookReviewCard
  :avatar="girlAvatar"
  username="Lora2412545"
  date="Jul 01, 2026 3:41 PM"
  content="這本書溫柔地敲醒了…"
  :like-count="20" />

按讚與檢舉目前只有外觀，點了會往上拋事件，之後接 API 再處理：
<BookReviewCard ... @like="..." @report="..." />
-->

<script setup>
import AppIcon from '@/components/common/AppIcon.vue';

defineProps({
  avatar: {
    type: String,
    default: '',
  },
  username: {
    type: String,
    required: true,
  },
  date: {
    type: String,
    default: '',
  },
  content: {
    type: String,
    default: '',
  },
  likeCount: {
    type: Number,
    default: 0,
  },
});

defineEmits(['like', 'report']);
</script>

<template>
  <article class="review-card">
    <header class="review-card__header">
      <img class="review-card__avatar" :src="avatar" :alt="username">
      <div class="review-card__meta">
        <p class="review-card__username">{{ username }}</p>
        <p class="review-card__date">{{ date }}</p>
      </div>
    </header>

    <p class="review-card__content">{{ content }}</p>

    <footer class="review-card__footer">
      <button type="button" class="review-card__like" @click="$emit('like')">
        <span class="review-card__like-icon">
          <AppIcon name="thumbs-up" :size="20"></AppIcon>
        </span>
        <span class="review-card__like-count">{{ likeCount }}</span>
      </button>

      <button type="button" class="review-card__report" aria-label="檢舉這則不當評論" @click="$emit('report')">
        <AppIcon name="flag" :size="24"></AppIcon>
      </button>
    </footer>
  </article>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;

.review-card {
  display: flex;
  flex-direction: column;
  gap: $spacing-md;
  width: 100%;
}

.review-card__header {
  display: flex;
  align-items: flex-start;
  gap: 12px; // 設計稿頭像與文字的間距
  padding: $spacing-sm;
}

.review-card__avatar {
  flex-shrink: 0;
  width: 49px;
  height: 49px;
  border-radius: $btn-radius-rnd;
  object-fit: cover;
  background-color: $neutral-300; // 沒有頭像時的底色
}

.review-card__meta {
  min-width: 0;
  font-size: $p-xs-size;
  line-height: 24px; // 設計稿是固定行高 24px，不是 1.5 倍
  color: $neutral-800;
}

.review-card__username {
  font-weight: $text-weight;
}

.review-card__content {
  padding: $spacing-sm;
  font-size: $p-xs-size;
  line-height: 24px;
  color: $neutral-800;
}

.review-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.review-card__like {
  display: flex;
  align-items: center;
  gap: $spacing-sm;
  background-color: transparent;
  cursor: pointer;
}

.review-card__like-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: $btn-radius-rnd;
  background-color: $neutral-200;
  color: $primary-300;
}

.review-card__like-count {
  font-size: $p-xs-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $primary;
}

.review-card__report {
  display: flex;
  align-items: center;
  background-color: transparent;
  color: $neutral-800;
  cursor: pointer;
}
</style>

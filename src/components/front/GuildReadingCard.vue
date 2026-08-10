<!--
GuildReadingCard 公會小卡（書籍詳情頁「這個公會正在讀…」用）

用法：
<GuildReadingCard
  :image="guildBackground"
  name="文青小時光"
  current-book="北歐時間：世界第一幸福國度教會我的事"
  :member-count="80"
  location="線上" />

跟 GuildCard.vue 的差別：那張是公會列表用的直式卡（有簡介、標籤、按鈕），
這張是橫式縮圖卡，只顯示「正在讀什麼書」，所以另外開一個元件。
-->

<script setup>
import AppIcon from '@/components/common/AppIcon.vue';

defineProps({
  image: {
    type: String,
    default: '',
  },
  name: {
    type: String,
    required: true,
  },
  currentBook: {
    type: String,
    default: '',
  },
  memberCount: {
    type: Number,
    default: 0,
  },
  location: {
    type: String,
    default: '線上',
  },
});
</script>

<template>
  <article class="guild-reading-card">
    <img class="guild-reading-card__image" :src="image" :alt="name">

    <div class="guild-reading-card__body">
      <h3 class="guild-reading-card__name">{{ name }}</h3>

      <ul class="guild-reading-card__info">
        <li class="guild-reading-card__info-item">
          <AppIcon name="book" :size="14"></AppIcon>
          <span class="guild-reading-card__book" :title="currentBook">
            正在讀《{{ currentBook }}》
          </span>
        </li>
        <li class="guild-reading-card__info-item">
          <AppIcon name="users" :size="18"></AppIcon>
          <span>{{ memberCount }}人</span>
          <span>正在讀</span>
        </li>
        <li class="guild-reading-card__info-item">
          <AppIcon name="map-pin" :size="18"></AppIcon>
          <span>{{ location }}</span>
        </li>
      </ul>
    </div>
  </article>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

.guild-reading-card {
  display: flex;
  align-items: center;
  gap: $spacing-md;
  width: 100%;
}

.guild-reading-card__image {
  flex-shrink: 0; // 圖片不能被右邊文字擠扁
  width: 204px;
  height: 150px;
  object-fit: cover;

  @include mobile {
    // 等比縮小（204/150 ≈ 1.36），手機版才塞得下文字
    width: 120px;
    height: 88px;
  }
}

.guild-reading-card__body {
  display: flex;
  flex-direction: column;
  gap: 10px; // 設計稿的書名與資訊列間距
  min-width: 0; // 讓長書名可以正常換行，不會把卡片撐寬
}

.guild-reading-card__name {
  font-size: $h6-size;
  font-weight: $heading-weight;
  line-height: $heading-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-800;

  @include mobile {
    font-size: $p-lg-size; // 手機版 24px 太重，縮一階
  }
}

.guild-reading-card__info {
  display: flex;
  flex-direction: column;
  gap: $spacing-xs;
  font-size: $p-xs-size;
  font-weight: $text-weight;
  line-height: $text-line-height;
  letter-spacing: $letter-spacing-base;
  color: $neutral-600;
}

.guild-reading-card__info-item {
  display: flex;
  // 用 flex-start 不用 center：文字萬一換行時，圖示要對齊第一行。
  // center 會讓圖示浮在整塊文字的垂直中央，看起來像跟第二行對齊
  align-items: flex-start;
  gap: $spacing-sm;

  // 圖示不要被文字壓縮，也不要跟著文字行高偏移
  svg {
    flex-shrink: 0;
    margin-top: 3px; // 對齊第一行的文字中線，不是行框的頂端
  }
}

// 書名截成一行加「…」。這是識別用的資訊不是拿來讀的，
// 而且這頁所有公會讀的都是同一本書，印完整書名四次只是噪音。
// 折行的話還會出現「同一項的行距比不同項的間距還大」，看起來會散掉。
// 完整書名靠 title 屬性，滑鼠移上去看得到。
.guild-reading-card__book {
  min-width: 0; // flex 子元素預設不會縮到比內容小，不寫就截不了
  @include text-ellipsis(1);
}
</style>

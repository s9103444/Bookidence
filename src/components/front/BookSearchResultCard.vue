<!--
BookSearchResultCard 搜尋結果的書籍卡片（橫的那種）

封面在左邊，書名、作者那一排資料、簡介和兩顆按鈕在右邊。
搜尋結果頁一本書一張，由上往下排。

推薦好書輪播用的是 BookCard（直的、封面在上面），兩支不一樣，不要互換。

怎麼用：

1. 先準備一本書的資料
2. 把資料一項一項填進去
3. 「加入我的藏書」要不要亮起來，由外面決定

    <BookSearchResultCard
      :book-id="book.id"
      :cover-image="book.cover"
      :title="book.title"
      :author="book.author"
      :category="book.category"
      :publisher="book.publisher"
      :publish-date="book.publishDate"
      :description="book.description"
      :is-collected="collectedIds.includes(book.id)"
      @toggle-collect="toggleCollect"
    />

可以傳什麼：

book-id       這本書的編號，「查看書籍」會用它組出網址（必填）
cover-image   封面圖（必填）
title         書名（必填）
author        作者
category      分類
publisher     出版社
publish-date  出版日期
description   簡介，太長會自動收成兩行加「…」
is-collected  true 的時候按鈕會變成「已加入藏書」

作者那一排的直線分隔號是自動加的，只有真的有填的欄位才會出現，
所以少填一兩項也不會跑出「作者｜｜出版社」這種空格。

@toggle-collect 是使用者按下收藏鈕時發出的通知，會把 book-id 一起帶出來。
卡片自己不會記住有沒有收藏 —— 收藏狀態要放在頁面上統一管，
否則同一本書出現在兩個地方時，兩張卡片會各記各的、對不起來。
-->
<script setup>
import { computed } from 'vue';
import AppButton from '@/components/common/AppButton.vue';
import AppIcon from '@/components/common/AppIcon.vue';

const props = defineProps({
  bookId: {
    type: [String, Number],
    required: true,
  },
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
    default: '',
  },
  category: {
    type: String,
    default: '',
  },
  publisher: {
    type: String,
    default: '',
  },
  publishDate: {
    type: String,
    default: '',
  },
  description: {
    type: String,
    default: '',
  },
  isCollected: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['toggle-collect']);

const metaText = computed(() =>
  [props.author, props.category, props.publisher, props.publishDate]
    .filter(Boolean)
    .join('｜')
);
</script>

<template>
  <article class="search-result-card">
    <div class="search-result-card__cover">
      <img :src="coverImage" :alt="title">
    </div>

    <div class="search-result-card__info">
      <div class="search-result-card__heading">
        <h3 class="search-result-card__title">{{ title }}</h3>
        <hr class="search-result-card__divider">
        <p class="search-result-card__meta">{{ metaText }}</p>
      </div>
      <p class="search-result-card__desc">{{ description }}</p>
    </div>

    <div class="search-result-card__actions">
      <AppButton
        class="search-result-card__collect"
        size="xs"
        color="primary"
        :variant="isCollected ? 'outlined' : 'filled'"
        @click="$emit('toggle-collect', bookId)">
        <AppIcon :name="isCollected ? 'heart-filled' : 'heart'"></AppIcon>
        {{ isCollected ? '已加入藏書' : '加入我的藏書' }}
      </AppButton>

      <AppButton size="xs" color="primary" variant="outlined" :to="`/front/books/${bookId}`">
        查看書籍
      </AppButton>
    </div>
  </article>
</template>

<style lang="scss" scoped>
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/abstracts/mixins' as *;

// 左邊封面、右邊文字，按鈕在文字下面。
// 用格線而不是 flex，是因為手機版要讓按鈕橫跨到封面底下 ——
// 按鈕如果包在右邊那一欄裡面，就只能待在文字的寬度內。
.search-result-card {
    display: grid;
    grid-template-columns: auto 1fr;
    column-gap: 32px; // 設計稿封面與內容的間距
    row-gap: 22px; // 設計稿內容與按鈕列的間距
    align-items: start;

    @include tablet {
        column-gap: $spacing-lg;
        row-gap: $spacing-md;
    }

    @include mobile {
        column-gap: $spacing-md;
        row-gap: $spacing-md;
    }
}

.search-result-card__cover {
    grid-row: 1 / 3; // 封面跨兩列，右邊文字和按鈕都排在它旁邊
    align-self: center;
    width: 102px;
    aspect-ratio: #{$book-cover-ratio};
    overflow: hidden;

    @include mobile {
        grid-row: 1; // 只佔第一列，第二列讓給整條按鈕
        width: 76px;
    }
}

.search-result-card__cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.search-result-card__info {
    grid-column: 2;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0; // 少了這行，長書名會把卡片撐破而不是換行
}

.search-result-card__heading {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.search-result-card__title {
    font-size: $label-md-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $primary;
}

.search-result-card__divider {
    border: none;
    border-top: 0.5px solid $primary;
}

.search-result-card__meta {
    font-size: $label-xs-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $primary;
}

.search-result-card__desc {
    font-size: $label-xs-size;
    font-weight: $text-weight;
    line-height: $text-line-height;
    letter-spacing: $letter-spacing-base;
    color: $neutral-800;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.search-result-card__actions {
    grid-column: 2;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;

    // 手機版按鈕橫跨封面那一欄，變成整張卡片的寬度。
    // 擠在文字那一欄的話只剩兩百多 px，兩顆會擠成長短不一的兩排。
    @include mobile {
        grid-column: 1 / -1;
        flex-direction: column;
        align-items: stretch;
        gap: $spacing-sm;
    }
}

// 寫死寬度：兩種狀態字數不一樣，不固定的話旁邊的「查看書籍」會左右跳。
// 改按鈕文字的話這個值要跟著調（一個中文字約 12px）
.search-result-card__collect {
    width: 168px;

    @include mobile {
        width: 100%;
    }
}
</style>

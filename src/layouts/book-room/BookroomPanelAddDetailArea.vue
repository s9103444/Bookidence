<template>
  <div class="layout">
    <div>
      <BookRoomNavBar class="nav" color="brown" size="md"
        >選擇結果</BookRoomNavBar
      >
      <section class="book-hero">
        <img class="book-hero__cover" :src="book.cover" :alt="book.title" />

        <div class="book-hero__info">
          <h1 class="book-hero__title">{{ book.title }}</h1>

          <ul class="book-hero__meta">
            <li>作者：{{ book.author }}</li>
            <li>譯者：{{ book.translator }}</li>
            <li>出版日期：{{ book.publishDate }}</li>
            <li>出版社：{{ book.publisher }}</li>
            <li>ISBN：{{ book.isbn }}</li>
          </ul>

          <ul class="book-hero__stats">
            <li>
              <AppIcon name="user" :size="20"></AppIcon>
              <span>{{ book.reviewCount }}人評論</span>
            </li>
            <li>
              <!-- 愛心跟下面「加入我的藏書」按鈕同一顆，把數字與按鈕串起來 -->
              <AppIcon name="heart" :size="20"></AppIcon>
              <span>{{ book.collectCount }}人加入藏書</span>
            </li>
          </ul>

          <button
            type="button"
            class="book-hero__collect"
            :class="{ 'book-hero__collect--active': isCollected }"
            @click="isCollected = !isCollected"
          >
            <AppIcon
              :name="isCollected ? 'heart-filled' : 'heart'"
              :size="24"
            ></AppIcon>
            {{ isCollected ? "已加入藏書" : "加入我的藏書" }}
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import BookRoomNavBar from "../../components/common/BookRoomNavBar.vue";
export default {
  components: {
    BookRoomNavBar,
  },
};
</script>

<style lang="scss" scoped></style>

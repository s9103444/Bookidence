<script>
import AppIcon from '@/components/common/AppIcon.vue';
import SearchBar from '@/components/common/SearchBar.vue';
import { API_STATIC } from '@/common/api';

export default {
  name: 'CreateGuildStep2',
  components: { AppIcon, SearchBar },
  props: {
    bookSearchKeyword: { type: String, default: '' },
    allBooks: { type: Array, default: () => [] },
    selectedBook: { type: Object, default: null },
    discussionBoards: { type: Array, default: () => [] },
    boardErrors: { type: Array, default: () => [] }
  },
  emits: [
    'update:book-search-keyword',
    'select-book',
    'add-board',
    'remove-board',
    'update-board'
  ],
  computed: {
    apiStatic() {
      return API_STATIC;
    },
    todayDateString() {
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      return `${yyyy}-${mm}-${dd}`;
    }
  },
  methods: {
    updateBoard(boardId, field, value) {
      this.$emit('update-board', { boardId, field, value });
    }
  }
};
</script>

<template>
  <div class="guild-create-step2">
    <div class="guild-create-step2__field">
      <label class="guild-create-step2__label">
        設定第一本讀物<span class="guild-create-step2__required">*</span>
      </label>
      <SearchBar
        size="md"
        color="primary"
        placeholder="搜尋書名"
        :model-value="bookSearchKeyword"
        @update:model-value="$emit('update:book-search-keyword', $event)"
      />

      <div v-if="allBooks.length" class="guild-create-step2__results">
        <div
          v-for="book in allBooks"
          :key="book.book_id"
          class="guild-create-step2__book-card"
          :class="{ 'guild-create-step2__book-card--selected': selectedBook && selectedBook.book_id === book.book_id }"
          @click="$emit('select-book', book)"
        >
          <img
            :src="`${apiStatic}/src/common/uploads/${book.bc_image}`"
            :alt="book.title"
            class="guild-create-step2__book-cover"
          />
          <div class="guild-create-step2__book-info">
            <h4 class="guild-create-step2__book-title">{{ book.title }}</h4>
            <p class="guild-create-step2__book-meta">
              {{ book.author }}｜{{ book.publisher }}｜{{ book.p_date }}
            </p>
          </div>
        </div>
      </div>

      <p class="guild-create-step2__apply-hint">
        找不到想要的書嗎?
        <router-link :to="{ name: 'book-apply' }">點我申請推薦書籍</router-link>
      </p>
    </div>

    <div class="guild-create-step2__field">
      <label class="guild-create-step2__label">
        設定討論版<span class="guild-create-step2__required">*</span>
      </label>

      <div class="guild-create-step2__boards">
        <div
          v-for="(board, index) in discussionBoards"
          :key="board.boardId"
          class="guild-create-step2__board-card"
        >
          <button
            v-if="discussionBoards.length > 1"
            class="guild-create-step2__board-remove"
            @click="$emit('remove-board', board.boardId)"
          >
            <AppIcon name="close" :size="16" />
          </button>

          <h4 class="guild-create-step2__board-title">
            章節分段討論板:{{ String(index + 1).padStart(2, '0') }}
          </h4>

          <label class="guild-create-step2__board-label">請輸入章節範圍</label>
          <div class="guild-create-step2__board-range">
            <input
              type="number"
              min="1"
              placeholder="請輸入數字"
              :value="board.chapterFrom"
              @input="updateBoard(board.boardId, 'chapterFrom', $event.target.value)"
            />
            <span>～</span>
            <input
              type="number"
              min="1"
              placeholder="請輸入數字"
              :value="board.chapterTo"
              @input="updateBoard(board.boardId, 'chapterTo', $event.target.value)"
            />
            <span>章節</span>
          </div>

          <label class="guild-create-step2__board-label">預計完讀日期</label>
          <input
            type="date"
            class="guild-create-step2__board-date"
            :min="todayDateString"
            :value="board.dueDate"
            @input="updateBoard(board.boardId, 'dueDate', $event.target.value)"
          />

          <div v-if="boardErrors[index] && boardErrors[index].length" class="guild-create-step2__board-errors">
            <p
              v-for="(errorMsg, errorIndex) in boardErrors[index]"
              :key="errorIndex"
              class="guild-create-step2__board-error"
            >
              {{ errorMsg }}
            </p>
          </div>
        </div>

        <button class="guild-create-step2__add-board" @click="$emit('add-board')">
          <AppIcon name="plus" :size="18" />
          點選新增討論區
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/abstracts/variables' as *;
@use '@/assets/scss/abstracts/mixins' as *;

.guild-create-step2 {
  display: flex;
  flex-direction: column;
  gap: $spacing-xl;

  &__field {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
  }

  &__label {
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__required {
    color: $color-danger;
    margin-left: 2px;
  }

  &__results {
    display: flex;
    flex-direction: column;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    overflow: hidden;
  }

  &__book-card {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    padding: $spacing-xs $spacing-sm;
    cursor: pointer;
    border-bottom: 1px solid $neutral-200;
    border-left: 3px solid transparent;
    border-radius: 0;

    &:last-child {
      border-bottom: none;
    }

    &--selected {
      border-left-color: $primary;
      background-color: $neutral-100;
    }
  }

  &__book-cover {
    width: 48px;
    height: 64px;
    object-fit: cover;
    flex-shrink: 0;
    border-radius: 4px;
  }

  &__book-title {
    color: $neutral-800;
    font-size: $p-sm-size;
  }

  &__book-meta {
    font-size: 12px;
    color: $neutral-600;
    margin-block: 2px;
  }

  &__apply-hint {
    text-align: right;
    font-size: $p-sm-size;
    color: $neutral-600;

    a {
      color: $primary-500;
      text-decoration: underline;
    }
  }

  &__boards {
    display: flex;
    flex-wrap: wrap;
    gap: $spacing-md;
    align-items: flex-start;
  }

  &__board-card {
    position: relative;
    width: 280px;
    padding: $spacing-md;
    background-color: $secondary-300;
    border-radius: $btn-radius-std;
    display: flex;
    flex-direction: column;
    gap: $spacing-xxs;
  }

  &__board-remove {
    position: absolute;
    top: $spacing-xxs;
    right: $spacing-xxs;
    background: none;
    border: none;
    cursor: pointer;
    color: $neutral-600;
  }

  &__board-title {
    background-color: $primary-300;
    color: $neutral-100;
    padding: $spacing-xxs;
    margin: calc($spacing-md * -1) calc($spacing-md * -1) $spacing-xs;
    border-radius: $btn-radius-std $btn-radius-std 0 0;
  }

  &__board-label {
    font-size: $p-sm-size;
    color: $neutral-600;
    margin-top: $spacing-xxs;
  }

  &__board-range {
    display: flex;
    align-items: center;
    gap: $spacing-xxs;

    input {
      @include form-field-base;
      width: 80px;              // 原本是 60px,套用 mixin 統一 padding 後框會變寬,調整避免擠壓數字
    }
  }

  &__board-date {
    @include form-field-base;
  }

  &__board-errors {
    margin-top: $spacing-xxs;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  &__board-error {
    font-size: 12px;
    color: $color-danger;
  }

  &__add-board {
    display: flex;
    align-items: center;
    gap: $spacing-xxs;
    background: none;
    border: none;
    color: $primary;
    cursor: pointer;
    align-self: center;
  }
}
</style>
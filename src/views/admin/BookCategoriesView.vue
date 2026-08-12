<script setup>
import { ref, computed } from 'vue'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AppButton from '@/components/common/AppButton.vue'

const adminBooksStore = useAdminBooksStore()

const newCategory = ref('')
const errorMessage = ref('')

// 「有幾本書在用」決定能不能刪，所以要跟著書籍列表變，不能只算一次
const categoryRows = computed(() =>
  adminBooksStore.categories.map((name) => ({
    name,
    bookCount: adminBooksStore.bookCountOf(name),
  })),
)

function handleAdd() {
  const name = newCategory.value.trim()

  if (!name) {
    errorMessage.value = '請先輸入分類名稱'
    return
  }

  if (!adminBooksStore.addCategory(name)) {
    errorMessage.value = `「${name}」已經在清單裡了`
    return
  }

  newCategory.value = ''
  errorMessage.value = ''
}

function handleRemove(name) {
  if (!adminBooksStore.removeCategory(name)) {
    errorMessage.value = `「${name}」還有書籍在使用，請先把那些書改成其他分類`
    return
  }

  errorMessage.value = ''
}
</script>

<template>
  <div class="admin-page categories">
    <header class="admin-page__head">
      <h1 class="admin-page__title">書籍分類管理</h1>
    </header>

    <AdminNotice>
      刪除分類前請確認無書籍使用該分類，否則將無法刪除（避免書籍變成無分類狀態）。
    </AdminNotice>

    <AdminPanel :title="`現有分類（${categoryRows.length}）`">
      <ul class="categories__list">
        <li v-for="row in categoryRows" :key="row.name" class="categories__item">
          <span class="categories__name">{{ row.name }}</span>
          <span class="categories__count">{{ row.bookCount }} 本</span>

          <button
            type="button"
            class="categories__remove"
            :disabled="row.bookCount > 0"
            :title="row.bookCount > 0 ? `還有 ${row.bookCount} 本書使用這個分類` : `刪除「${row.name}」`"
            @click="handleRemove(row.name)"
          >
            刪除
          </button>
        </li>
      </ul>

      <!-- ⚠️ @submit.prevent 不能省。這個表單只有一個文字輸入框，
           所以在裡面按 Enter 就會觸發瀏覽器原生的送出，整頁會重新載入。
           （欄位有兩個以上時反而不會，那是 HTML 的規則。） -->
      <form class="categories__add" @submit.prevent="handleAdd">
        <input
          v-model="newCategory"
          type="text"
          class="categories__input"
          placeholder="輸入新分類名稱"
        />
        <AppButton size="xs" type="submit">新增分類</AppButton>
      </form>

      <!-- 這一行永遠留在畫面上（沒訊息時是空的）。用 v-if 讓它跟訊息同時出現的話，
           有些螢幕閱讀器不會念出來 —— 它只盯著「已經存在的元素內容有沒有變」 -->
      <p class="categories__error" role="alert">{{ errorMessage }}</p>
    </AdminPanel>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/abstracts/variables' as *;

.categories {
  max-width: 640px;

  &__list {
    display: flex;
    flex-direction: column;
    gap: $spacing-sm;
    margin: 0 0 $spacing-md;
    padding: 0;
    list-style: none;
  }

  &__item {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    padding: $spacing-sm + $spacing-xxs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std + 1px;
  }

  &__name {
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__count {
    font-size: $p-xs-size;
    color: $neutral-400;
  }

  &__remove {
    margin-left: auto;
    padding: $spacing-xs + $spacing-xxs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    background: $neutral-100;
    font-family: inherit;
    font-size: $p-xs-size;
    color: $neutral-600;
    cursor: pointer;

    &:hover:not(:disabled) {
      border-color: $brown;
      color: $brown;
    }

    // 有書在用就不能刪
    &:disabled {
      border-color: $neutral-200;
      color: $neutral-400;
      cursor: not-allowed;
    }
  }

  &__add {
    display: flex;
    gap: $spacing-sm;
    padding-top: $spacing-md;
    border-top: 1px solid $neutral-200;
  }

  &__input {
    flex: 1;
    min-width: 0;
    padding: $spacing-sm + $spacing-xxs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std + 1px;
    font-family: inherit;
    font-size: $p-sm-size;
    color: $neutral-800;

    &::placeholder {
      color: $neutral-400;
    }

    &:focus-visible {
      outline: 2px solid $primary;
      outline-offset: -1px;
    }
  }

  &__error {
    margin: $spacing-sm 0 0;
    font-size: $p-xs-size;
    color: $brown;

    // 沒有訊息的時候不要留一段空白
    &:empty {
      margin: 0;
    }
  }
}
</style>

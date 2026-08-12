<script setup>
import { ref, computed } from 'vue'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'

const adminBooksStore = useAdminBooksStore()

const categoryRows = computed(() =>
  adminBooksStore.categories.map((name) => ({
    name,
    bookCount: adminBooksStore.bookCountOf(name),
  })),
)

const hasLockedCategory = computed(() => categoryRows.value.some((row) => row.bookCount > 0))

const newCategory = ref('')
const addError = ref('')

function handleAdd() {
  const name = newCategory.value.trim()

  if (!name) {
    addError.value = '請先輸入分類名稱'
    return
  }

  if (!adminBooksStore.addCategory(name)) {
    addError.value = `「${name}」已經在清單裡了`
    return
  }

  newCategory.value = ''
  addError.value = ''
}

const isRenameOpen = ref(false)
const renameTarget = ref('')
const renameInput = ref('')
const renameError = ref('')

function openRename(name) {
  renameTarget.value = name
  renameInput.value = name
  renameError.value = ''
  isRenameOpen.value = true
}

function handleRename() {
  const name = renameInput.value.trim()

  if (!name) {
    renameError.value = '請先輸入分類名稱'
    return
  }

  if (name === renameTarget.value) {
    isRenameOpen.value = false
    return
  }

  if (!adminBooksStore.renameCategory(renameTarget.value, name)) {
    renameError.value = `「${name}」已經在清單裡了，請換一個名稱`
    return
  }

  isRenameOpen.value = false
}

const isRemoveOpen = ref(false)
const removeTarget = ref('')

function openRemove(name) {
  if (adminBooksStore.bookCountOf(name) > 0) return

  removeTarget.value = name
  isRemoveOpen.value = true
}

function handleRemove() {
  adminBooksStore.removeCategory(removeTarget.value)
  isRemoveOpen.value = false
}
</script>

<template>
  <div class="admin-page categories">
    <header class="admin-page__head">
      <h1 class="admin-page__title">書籍分類管理</h1>
    </header>

    <AdminNotice v-if="hasLockedCategory">
      分類正在被書籍使用時無法刪除。點該分類的書籍數量可以查看是哪幾本，改完分類後就能刪除。
    </AdminNotice>

    <div class="categories__layout">
      <AdminPanel title="新增分類">
        <form class="categories__form" @submit.prevent="handleAdd">
          <label class="categories__field">
            <span class="categories__label">分類名稱</span>
            <input
              v-model="newCategory"
              type="text"
              class="categories__input"
              placeholder="例：飲食生活"
            />
          </label>

          <p class="categories__error" role="alert">{{ addError }}</p>

          <AdminButton type="submit">新增分類</AdminButton>
        </form>
      </AdminPanel>

      <AdminPanel flush>
        <div class="table-scroll">
          <table class="data-table">
            <caption class="categories__caption">
              現有分類（{{ categoryRows.length }}）
            </caption>

            <thead>
              <tr>
                <th scope="col">分類名稱</th>
                <th scope="col">使用中書籍</th>
                <th scope="col">操作</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="row in categoryRows" :key="row.name">
                <td class="data-table__key">{{ row.name }}</td>

                <td>
                  <RouterLink
                    v-if="row.bookCount > 0"
                    class="data-table__link categories__count"
                    :to="{ path: '/admin/books/list', query: { category: row.name } }"
                  >
                    {{ row.bookCount }} 本
                  </RouterLink>
                  <span v-else class="data-table__muted categories__count">0 本</span>
                </td>

                <td>
                  <span class="data-table__ops">
                    <button type="button" class="data-table__op" @click="openRename(row.name)">
                      重新命名
                    </button>

                    <button
                      type="button"
                      class="data-table__op data-table__op--icon data-table__op--danger"
                      :aria-disabled="row.bookCount > 0"
                      :aria-label="`刪除「${row.name}」`"
                      :title="row.bookCount > 0 ? `還有 ${row.bookCount} 本書使用這個分類，不能刪除` : `刪除「${row.name}」`"
                      @click="openRemove(row.name)"
                    >
                      <AppIcon name="trash" :size="14" />
                    </button>
                  </span>
                </td>
              </tr>

              <tr v-if="categoryRows.length === 0">
                <td colspan="3">
                  <p class="data-table__empty">目前沒有任何分類，請從左邊新增</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </AdminPanel>
    </div>

    <AppModal v-model="isRenameOpen" title="重新命名分類">
      <form class="categories__form" @submit.prevent="handleRename">
        <p class="categories__text">
          「{{ renameTarget }}」目前有
          {{ adminBooksStore.bookCountOf(renameTarget) }}
          本書使用，改名後那些書會跟著換成新名稱。
        </p>

        <label class="categories__field">
          <span class="categories__label">新的分類名稱</span>
          <input v-model="renameInput" type="text" class="categories__input" />
        </label>

        <p class="categories__error" role="alert">{{ renameError }}</p>

        <div class="categories__modal-actions">
          <AdminButton variant="outline" @click="isRenameOpen = false">取消</AdminButton>
          <AdminButton type="submit">儲存名稱</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isRemoveOpen" title="刪除分類">
      <p class="categories__text">確定要刪除「{{ removeTarget }}」嗎？</p>

      <div class="categories__modal-actions">
        <AdminButton variant="outline" @click="isRemoveOpen = false">取消</AdminButton>
        <AdminButton tone="danger" @click="handleRemove">確定刪除</AdminButton>
      </div>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.categories {
  &__layout {
    display: grid;
    grid-template-columns: 300px minmax(0, 600px);
    gap: $spacing-md;
    align-items: start;
  }

  &__form {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
    align-items: stretch;
  }

  &__field {
    display: block;
  }

  &__label {
    display: block;
    margin-bottom: $spacing-sm;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__input {
    width: 100%;
    box-sizing: border-box;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    background: $neutral-100;
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
    margin: 0;
    font-size: $p-xs-size;
    line-height: 1.6;
    color: $color-danger;

    &:empty {
      display: none;
    }
  }

  &__text {
    margin: 0;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-600;
  }

  &__caption {
    padding: $spacing-md $spacing-md $spacing-sm + $spacing-xs;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
    letter-spacing: 0.05em;
    text-align: left;
  }

  &__name {
    font-weight: $heading-weight;
  }

  &__count {
    font-variant-numeric: tabular-nums;
  }

  &__modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    margin-top: $spacing-sm;
  }
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAdminCategoriesStore } from '@/stores/adminCategories.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'
import { adminApi } from '@/common/adminApi.js'

const categoriesStore = useAdminCategoriesStore()

onMounted(() => {categoriesStore.ensureCategories()})

const hasLockedCategory = computed(() =>
  categoriesStore.categories.some((row) => row.bookCount > 0),
)

const newCategory = ref('')
const addError = ref('')

async function handleAdd() {
  const name = newCategory.value.trim()

  if (!name) {
    addError.value = '請先輸入分類名稱'
    return
  }

  if (categoriesStore.categories.some((row) => row.name === name)) {
    addError.value = `${name}已在現有分類裡`
    return
  }

  addError.value = ''

  try {
    await adminApi.post('/admin_category_create.php', { name })

    newCategory.value = ''

    await categoriesStore.fetchCategories()
  } catch (e) {
    console.error('[新增分類]', e)

    addError.value = e?.response?.data?.message || '新增失敗，請稍後再試'
  }
}

const isRenameOpen = ref(false)
const renameTarget = ref({ id: null, name: '', bookCount: 0 })
const renameInput = ref('')
const renameError = ref('')

function openRename(row) {
  renameTarget.value = row
  renameInput.value = row.name
  renameError.value = ''
  isRenameOpen.value = true
}

async function handleRename() {
  const name = renameInput.value.trim()

  if (!name) {
    renameError.value = '請先輸入分類名稱'
    return
  }

  if (name === renameTarget.value.name) {
    isRenameOpen.value = false
    return
  }

  if (categoriesStore.categories.some((row) => row.name === name)) {
    renameError.value = `「${name}」已在現有分類裡，請更換名稱`
    return
  }

  renameError.value = ''

  try {
    await adminApi.post('/admin_category_update.php', {
      bcg_id: renameTarget.value.id,
      name,
    })

    await categoriesStore.fetchCategories()
    isRenameOpen.value = false
  } catch (e) {
    console.error('[分類改名]', e)
    renameError.value = e.response?.data?.message || '改名失敗，請稍後再試'
  }
}

const isRemoveOpen = ref(false)
const removeTarget = ref({ id: null, name: '', bookCount: 0 })
const removeError = ref('')

function openRemove(row) {
  if (row.bookCount > 0) return

  removeTarget.value = row
  removeError.value = ''
  isRemoveOpen.value = true
}

async function handleRemove() {
  removeError.value = ''

  try {
    await adminApi.post('/admin_category_delete.php', { bcg_id:removeTarget.value.id})

    await categoriesStore.fetchCategories()
    isRemoveOpen.value = false
  } catch (e) {
    console.error('[分類刪除]', e)
    removeError.value = e.response?.data?.message || '刪除失敗，請稍後再試'
  }
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
              現有分類（{{ categoriesStore.categories.length }}）
            </caption>

            <thead>
              <tr>
                <th scope="col">分類名稱</th>
                <th scope="col">使用中書籍</th>
                <th scope="col">操作</th>
              </tr>
            </thead>

            <tbody>
              <template v-if="!categoriesStore.loading && !categoriesStore.error">
                <tr v-for="row in categoriesStore.categories" :key="row.id">
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
                      <button type="button" class="data-table__op" @click="openRename(row)">
                        重新命名
                      </button>

                      <button
                        type="button"
                        class="data-table__op data-table__op--icon data-table__op--danger"
                        :aria-disabled="row.bookCount > 0"
                        :aria-label="`刪除「${row.name}」`"
                        :title="row.bookCount > 0 ? `還有 ${row.bookCount} 本書使用這個分類，不能刪除` : `刪除「${row.name}」`"
                        @click="openRemove(row)"
                      >
                        <AppIcon name="trash" :size="14" />
                      </button>
                    </span>
                  </td>
                </tr>
              </template>

              <tr v-if="categoriesStore.loading">
                <td colspan="3">
                  <p class="data-table__empty">載入中…</p>
                </td>
              </tr>

              <tr v-else-if="categoriesStore.error">
                <td colspan="3">
                  <p class="data-table__empty">{{ categoriesStore.error }}</p>
                </td>
              </tr>

              <tr v-else-if="categoriesStore.categories.length === 0">
                <td colspan="3">
                  <p class="data-table__empty">目前沒有任何分類</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </AdminPanel>
    </div>

    <AppModal v-model="isRenameOpen" title="重新命名分類">
      <form class="categories__form" @submit.prevent="handleRename">
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
      <p class="categories__text">確定要刪除「{{ removeTarget.name }}」嗎？</p>

      <p class="categories__error" role="alert">{{ removeError }}</p>

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

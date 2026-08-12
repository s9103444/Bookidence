<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { BOOK_STATUS } from '@/data/adminBooks.js'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppButton from '@/components/common/AppButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const PER_PAGE = 10
const ALL = '全部'

const adminBooksStore = useAdminBooksStore()

const status = ref(ALL)
const keyword = ref('')
const page = ref(1)

const statusOptions = [
  { label: ALL, value: ALL },
  { label: BOOK_STATUS.listed, value: BOOK_STATUS.listed },
  { label: BOOK_STATUS.unlisted, value: BOOK_STATUS.unlisted },
]

const filtered = computed(() => {
  const search = keyword.value.trim().toLowerCase()

  return adminBooksStore.books
    .filter((book) => status.value === ALL || book.status === status.value)
    .filter((book) => {
      if (!search) return true
      return `${book.title} ${book.author} ${book.isbn}`.toLowerCase().includes(search)
    })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PER_PAGE)))

const pagedBooks = computed(() =>
  filtered.value.slice((page.value - 1) * PER_PAGE, page.value * PER_PAGE),
)

watch([status, keyword], () => {
  page.value = 1
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
}

// 同一個彈窗兩種用途：editingId 是 null 代表在新增一本，有值代表在改那一本
const isFormOpen = ref(false)
const editingId = ref(null)

const form = reactive({
  title: '',
  author: '',
  isbn: '',
  publisher: '',
  publishDate: '',
  coverUrl: '',
  summary: '',
  categories: [],
  status: BOOK_STATUS.listed,
})

const isCreating = computed(() => editingId.value === null)

// 書名、作者、ISBN 只有在「已下架」時才能改。上架中的書改掉 ISBN，
// 等於把讀者書架上的書換成另一本。
const isCoreLocked = computed(() => !isCreating.value && form.status === BOOK_STATUS.listed)

const trimmed = computed(() => ({
  title: form.title.trim(),
  author: form.author.trim(),
  isbn: form.isbn.trim(),
}))

const canSave = computed(
  () => Boolean(trimmed.value.title) && Boolean(trimmed.value.author) && Boolean(trimmed.value.isbn),
)

function openCreate() {
  editingId.value = null
  Object.assign(form, {
    title: '',
    author: '',
    isbn: '',
    publisher: '',
    publishDate: '',
    coverUrl: '',
    summary: '',
    categories: [],
    status: BOOK_STATUS.listed,
  })
  isFormOpen.value = true
}

function openEdit(book) {
  editingId.value = book.id
  Object.assign(form, {
    title: book.title,
    author: book.author,
    isbn: book.isbn,
    publisher: book.publisher ?? '',
    publishDate: book.publishDate ?? '',
    coverUrl: book.coverUrl ?? '',
    summary: book.summary ?? '',
    categories: [...book.categories],
    status: book.status,
  })
  isFormOpen.value = true
}

function handleSave() {
  if (!canSave.value) return

  const fields = {
    title: trimmed.value.title,
    author: trimmed.value.author,
    isbn: trimmed.value.isbn,
    publisher: form.publisher.trim(),
    publishDate: form.publishDate.trim(),
    coverUrl: form.coverUrl.trim(),
    summary: form.summary.trim(),
    categories: form.categories,
    status: form.status,
  }

  if (isCreating.value) {
    adminBooksStore.addBook(fields)
  } else {
    adminBooksStore.updateBook(editingId.value, fields)
  }

  isFormOpen.value = false
}

// 封面有兩種來源：程式裡 import 的圖檔，和管理員貼的網址
function coverOf(book) {
  return book.coverUrl || book.cover || null
}
</script>

<template>
  <div class="admin-page">
    <header class="admin-page__head">
      <h1 class="admin-page__title">正式書籍</h1>

      <AppButton size="xs" @click="openCreate">
        <AppIcon name="plus" :size="14" />
        新增上架書籍
      </AppButton>
    </header>

    <AdminNotice>
      書名、作者、ISBN 為核心識別資訊，上架中不可編輯；封面、簡介、出版社、出版日期等資料如有錯誤，可點擊「編輯」修正。
    </AdminNotice>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="admin-page__search">
        <SearchBar v-model="keyword" size="sm" placeholder="搜尋書名 / ISBN / 作者…" />
      </div>
    </div>

    <AdminPanel flush>
      <table class="data-table">
        <thead>
          <tr>
            <th scope="col">書籍</th>
            <th scope="col">ISBN</th>
            <th scope="col">分類</th>
            <th scope="col">狀態</th>
            <th scope="col" class="data-table__action">操作</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="book in pagedBooks" :key="book.id">
            <td>
              <div class="book-cell">
                <img v-if="coverOf(book)" :src="coverOf(book)" :alt="`${book.title} 封面`" class="book-cell__cover" />
                <span v-else class="book-cell__cover book-cell__cover--empty">封面缺</span>

                <span>
                  <span class="book-cell__title">{{ book.title }}</span>
                  <span class="book-cell__author">{{ book.author }}</span>
                </span>
              </div>
            </td>
            <td class="data-table__muted">{{ book.isbn }}</td>
            <td>
              <span v-if="book.categories.length" class="book-cell__tags">
                <AdminStatusTag v-for="category in book.categories" :key="category" :label="category" />
              </span>
              <span v-else class="data-table__muted">未分類</span>
            </td>
            <td>
              <AdminStatusTag
                :label="book.status"
                :tone="book.status === BOOK_STATUS.listed ? 'solid' : 'muted'"
              />
            </td>
            <td class="data-table__action">
              <button type="button" class="data-table__link book-cell__edit" @click="openEdit(book)">
                編輯
              </button>
            </td>
          </tr>

          <tr v-if="pagedBooks.length === 0">
            <td colspan="5">
              <p class="data-table__empty">
                {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的書籍` : '這個狀態底下目前沒有書籍' }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ filtered.length }} 本書籍</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>

    <AppModal
      v-model="isFormOpen"
      :title="isCreating ? '新增上架書籍' : `編輯書籍資料｜${form.title}`"
    >
      <p class="modal__text">
        <template v-if="isCreating">
          書名、作者、ISBN 為核心識別資訊，建立後上架中就不能再修改，請確認無誤。
        </template>
        <template v-else>
          書名、作者、ISBN 為核心識別資訊，鎖定不可修改；如需變更請先將狀態改為「已下架」。
        </template>
      </p>

      <div class="form">
        <fieldset class="form__field form__field--plain">
          <legend class="form__label">設定書籍狀態</legend>
          <AdminFilterTabs
            v-model="form.status"
            :options="[
              { label: BOOK_STATUS.listed, value: BOOK_STATUS.listed },
              { label: BOOK_STATUS.unlisted, value: BOOK_STATUS.unlisted },
            ]"
          />
        </fieldset>

        <label class="form__field">
          <span class="form__label">書名</span>
          <input v-model="form.title" type="text" class="form__input" :disabled="isCoreLocked" />
        </label>

        <label class="form__field">
          <span class="form__label">作者</span>
          <input v-model="form.author" type="text" class="form__input" :disabled="isCoreLocked" />
        </label>

        <label class="form__field">
          <span class="form__label">ISBN</span>
          <input v-model="form.isbn" type="text" class="form__input" :disabled="isCoreLocked" />
        </label>

        <div class="form__pair">
          <label class="form__field">
            <span class="form__label">出版社</span>
            <input v-model="form.publisher" type="text" class="form__input" placeholder="例：經濟新潮社" />
          </label>

          <label class="form__field">
            <span class="form__label">出版日期</span>
            <input v-model="form.publishDate" type="date" class="form__input" />
          </label>
        </div>

        <label class="form__field">
          <span class="form__label">封面連結</span>
          <input v-model="form.coverUrl" type="url" class="form__input" placeholder="目前無封面，貼上圖片網址" />
          <span class="form__hint">留空的話列表會顯示「封面缺」的灰框</span>
        </label>

        <label class="form__field">
          <span class="form__label">書籍簡介</span>
          <textarea
            v-model="form.summary"
            class="form__input form__input--area"
            rows="4"
            placeholder="簡短介紹本書內容"
          ></textarea>
        </label>

        <fieldset class="form__field form__field--plain">
          <legend class="form__label">分類（可複選）</legend>

          <div class="chips">
            <label v-for="category in adminBooksStore.categories" :key="category" class="chip">
              <input v-model="form.categories" type="checkbox" :value="category" class="chip__input" />
              <span class="chip__face">{{ category }}</span>
            </label>
          </div>
        </fieldset>
      </div>

      <div class="modal__actions">
        <AppButton variant="outlined" @click="isFormOpen = false">取消</AppButton>
        <AppButton :disabled="!canSave" @click="handleSave">
          {{ isCreating ? '新增書籍' : '儲存變更' }}
        </AppButton>
      </div>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.book-cell {
  display: flex;
  align-items: center;
  gap: $spacing-md;

  &__cover {
    flex-shrink: 0;
    width: 38px;
    aspect-ratio: #{$book-cover-ratio};
    border-radius: 3px;
    object-fit: cover;
    background: $neutral-200;

    // 沒有封面時是一個 <span>，要自己排版成一個灰框
    &--empty {
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px dashed $neutral-400;
      font-size: $label-xxs-size;
      color: $neutral-400;
      text-align: center;
    }
  }

  &__title {
    display: block;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__author {
    display: block;
    margin-top: $spacing-xxs;
    font-size: $p-xs-size;
    color: $neutral-500;
  }

  &__tags {
    display: flex;
    flex-wrap: wrap;
    gap: $spacing-xs;
  }

  // 「編輯」是開彈窗不是跳頁，所以是 <button>。
  // 要跟旁邊表格裡的連結長一樣，就得把瀏覽器的預設外觀清掉
  &__edit {
    border: 0;
    background: none;
    font-family: inherit;
    text-decoration: underline;
    cursor: pointer;
  }
}

.form {
  display: flex;
  flex-direction: column;
  gap: $spacing-md;

  &__field {
    display: block;

    &--plain {
      border: 0;
      margin: 0;
      padding: 0;
    }
  }

  &__pair {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
  }

  &__label {
    display: block;
    margin-bottom: $spacing-xs + $spacing-xxs;
    padding: 0;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__input {
    width: 100%;
    padding: $spacing-sm + $spacing-xxs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std + 1px;
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

    // 鎖住的欄位要看得出來是「現在不能改」，不是壞掉
    &:disabled {
      background: $neutral-200;
      color: $neutral-500;
      cursor: not-allowed;
    }

    &--area {
      resize: vertical;
      line-height: 1.8;
    }
  }

  &__hint {
    display: block;
    margin-top: $spacing-xs + $spacing-xxs;
    font-size: $label-xxs-size;
    color: $neutral-400;
  }
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: $spacing-sm;
}

.chip {
  cursor: pointer;

  &__input {
    position: absolute;
    clip-path: inset(50%);
    width: 1px;
    height: 1px;
  }

  &__face {
    display: inline-block;
    padding: $spacing-xs + $spacing-xxs $spacing-sm + $spacing-xxs;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__input:checked + &__face {
    background: $primary;
    border-color: $primary;
    color: $neutral-100;
  }

  &__input:focus-visible + &__face {
    outline: 2px solid $primary;
    outline-offset: 2px;
  }
}

.modal {
  &__text {
    margin: 0 0 $spacing-md;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-600;
  }

  &__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    margin-top: $spacing-lg;
  }
}
</style>

<script setup>
import { ref, reactive, computed, watch,onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { BOOK_STATUS } from '@/data/adminBooks.js'
import { useAdminCategoriesStore } from '@/stores/adminCategories.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import {API_STATIC} from '@/common/api.js'
import SearchBar from '@/components/common/SearchBar.vue'
import { adminApi } from '@/common/adminApi.js'

const ALL = '全部'

const categoriesStore = useAdminCategoriesStore()
const route = useRoute()
const router = useRouter()
const status = ref(ALL)
const keyword = ref('')
const page = ref(1)
const books=ref([])
const perPage=ref(10)
const total=ref(0)
const loading = ref(false)
const error = ref('')

// 亂打 ?category=a&category=b 時 query 會是陣列，只認單一個字串
const activeCategory = computed(() =>
  typeof route.query.category === 'string' ? route.query.category : '',
)

function clearCategory() {
  const query = { ...route.query }
  delete query.category
  router.replace({ path: route.path, query })
}

const statusOptions = [
  { label: ALL, value: ALL },
  { label: BOOK_STATUS.listed, value: BOOK_STATUS.listed },
  { label: BOOK_STATUS.unlisted, value: BOOK_STATUS.unlisted },
]

const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)))

watch([status, keyword, activeCategory], () => {
  page.value = 1
  fetchBooks()
})

watch(totalPages, (value) => {
  if (page.value > value) page.value = value
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
  fetchBooks()
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
  coverImage: '',
  summary: '',
  categories: [],
  status: BOOK_STATUS.listed,
})

const isCreating = computed(() => editingId.value === null)

// 書名、作者、ISBN 只有在「已下架」時才能改。
const isCoreLocked = computed(() => !isCreating.value && form.status === BOOK_STATUS.listed)

const trimmed = computed(() => ({
  title: form.title.trim(),
  author: form.author.trim(),
  isbn: form.isbn.trim(),
}))

const FIELD_LABELS = {
  title: '書名',
  author: '作者',
  isbn: 'ISBN',
  categories: '書籍分類',
}

const hasTriedSave = ref(false)
const saveError = ref('')
const uploading = ref(false)
const coverError = ref('')
const coverName = ref('')
const coverInput = ref(null)

function pickCover() {
  coverInput.value.click()
}

// 資料庫存的是相對路徑（book-covers/xxx.jpg），要接上主機位置才是能顯示的網址
function coverUrlOf(path) {
  return path ? `${API_STATIC}/uploads/${path}` : null
}

const coverPreview = computed(() => coverUrlOf(form.coverImage))

// 剛選的檔案顯示原始檔名，開編輯彈窗時顯示資料庫存的那個檔名
const coverLabel = computed(() => {
  if (coverName.value) return coverName.value
  if (form.coverImage) return form.coverImage.split('/').pop()
  return ''
})

async function handleCoverPick(event) {
  const file = event.target.files[0]
  if (!file) return

  coverError.value = ''
  uploading.value = true

  try {
    const formData = new FormData()
    formData.append('cover', file)

    const res = await adminApi.post('/admin_book_cover.php', formData)

    form.coverImage = res.data.bc_image
    coverName.value = file.name
  } catch (e) {
    console.error('[封面上傳]', e)
    coverError.value = e.response?.data?.message || '上傳失敗，請稍後再試'
  } finally {
    uploading.value = false
  }
}

const errors = computed(() => {
  const e = { title: '', author: '', isbn: '', categories: '' }
  if (!hasTriedSave.value) return e

  if (!trimmed.value.title) e.title = '請填寫書名'
  if (!trimmed.value.author) e.author = '請填寫作者'
  if (!trimmed.value.isbn) e.isbn = '請填寫 ISBN'
  if (!form.categories.length) e.categories = '請至少選一個分類'

  return e
})

const missingFields = computed(() =>
  Object.keys(FIELD_LABELS)
    .filter((key) => errors.value[key])
    .map((key) => FIELD_LABELS[key]),
)

const canSave = computed(
  () =>
    Boolean(trimmed.value.title) &&
    Boolean(trimmed.value.author) &&
    Boolean(trimmed.value.isbn) &&
    form.categories.length > 0,
)

function openCreate() {
  editingId.value = null
  hasTriedSave.value = false
  coverName.value = ''
  coverError.value = ''
  Object.assign(form, {
    title: '',
    author: '',
    isbn: '',
    publisher: '',
    publishDate: '',
    coverImage: '',
    summary: '',
    categories: [],
    status: BOOK_STATUS.listed,
  })
  isFormOpen.value = true
}

function openEdit(book) {
  editingId.value = book.id
  hasTriedSave.value = false
  coverName.value = ''
  coverError.value = ''
  Object.assign(form, {
    title: book.title,
    author: book.author,
    isbn: book.isbn,
    publisher: book.publisher ?? '',
    publishDate: book.publishDate ?? '',
    coverImage: book.coverImage ?? '',
    summary: book.summary ?? '',
    categories: [...book.categories],
    status: book.status,
  })
  isFormOpen.value = true
}

async function handleSave() {
  hasTriedSave.value = true
  if (!canSave.value) return

  saveError.value = ''

  //   左邊三格填「後端要的鍵名」——去 admin_book_create.php 第 6~13 行看它讀什麼。
  const body = {
    title: trimmed.value.title,
    author: trimmed.value.author,
    isbn: trimmed.value.isbn,
    publisher: form.publisher.trim(),
    p_date: form.publishDate.trim(),
    b_status: form.status,
    description: form.summary.trim(),
    bc_image: form.coverImage,
    categories: form.categories,
  }

  try {
    if (isCreating.value) {
      await adminApi.post('/admin_book_create.php', body)
    } else {
      await adminApi.post('/admin_book_update.php', { ...body, book_id: editingId.value })
    }

    await fetchBooks()

    isFormOpen.value = false
  } catch (e) {
    console.error('[書籍儲存]', e)
    saveError.value = e?.response?.data?.message || '儲存失敗，請稍後再試'
  }
}

function coverOf(book) {
  return book.cover
}

function toBook(row){
  return{
    id:row.book_id,
    title:row.title,
    author:row.author,
    isbn:row.isbn,
    publisher:row.publisher,
    publishDate:row.p_date,
    status:row.b_status,
    coverImage: row.bc_image,
    cover: coverUrlOf(row.bc_image),
    categories:row.categories ? row.categories.split(',') : [],
  }
}
async function fetchBooks(){
  loading.value = true
  error.value = ''

  try {
    const params = new URLSearchParams({
      page: page.value,
      status: status.value === ALL ? '' : status.value,
      keyword: keyword.value.trim(),
      category: activeCategory.value,
    })

    const res= await adminApi.get(`/admin_books.php?${params}`)


    const result =  res.data//axios的容器
    books.value = result.data.map(toBook)
    total.value = result.total
    perPage.value = result.perPage

  } catch (e) {
    console.error('[書籍列表]', e)
    error.value ='載入失敗，請稍後再試'
    books.value =[]
    total.value = 0

  } finally {
    loading.value = false
  }
}
onMounted(() => {
  fetchBooks()
  categoriesStore.ensureCategories()
})
</script>

<template>
  <div class="admin-page">
    <header class="admin-page__head">
      <h1 class="admin-page__title">正式書籍</h1>

      <AdminButton size="xs" @click="openCreate">
        <AppIcon name="plus" :size="14" />
        新增上架書籍
      </AdminButton>
    </header>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="admin-page__search">
        <SearchBar v-model="keyword" size="sm" placeholder="搜尋書名 / ISBN / 作者…" />
      </div>
    </div>

    <div v-if="activeCategory" class="category-filter">
      <span class="category-filter__text">
        只顯示分類「<strong>{{ activeCategory }}</strong>」底下的書籍
      </span>

      <button type="button" class="category-filter__clear" @click="clearCategory">
        清除分類篩選
      </button>
    </div>

    <AdminPanel flush>
      <table class="data-table">
        <thead>
          <tr>
            <th scope="col">書籍</th>
            <th scope="col">ISBN</th>
            <th scope="col">分類</th>
            <th scope="col">狀態</th>
            <th scope="col">操作</th>
          </tr>
        </thead>

        <tbody>
          <template v-if="!loading && !error">
          <tr v-for="book in books" :key="book.id">
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
            <td>
              <span class="data-table__ops">
                <button type="button" class="data-table__op" @click="openEdit(book)">編輯</button>
              </span>
            </td>
          </tr>
          </template>

          <tr v-if="loading">
            <td colspan="5">
              <p class="data-table__empty">載入中…</p>
            </td>
          </tr>

          <tr v-else-if="error">
            <td colspan="5">
              <p class="data-table__empty">{{ error }}</p>
            </td>
          </tr>

          <tr v-else-if="books.length === 0">
            <td colspan="5">
              <p class="data-table__empty">
                {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的書籍` : '沒有符合條件的書籍' }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ total}} 本書籍</p>

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
        <template v-else-if="isCoreLocked">
          書名、作者、ISBN 為核心識別資訊，上架中不能修改；把狀態改成「已下架」就能編輯。
        </template>
        <template v-else>
          這本書目前是已下架狀態，書名、作者、ISBN 可以修改。
        </template>
      </p>

      <!-- 送出鈕要在 <form> 裡面才算這張表單的送出鈕，所以下面的按鈕列也包進來了。
           「取消」不能加 type="submit"，不然點它會變成儲存 -->
      <form class="form" @submit.prevent="handleSave">
        <fieldset class="form__field form__field--plain">
          <legend class="form__label">設定書籍狀態</legend>

          <!-- 上下架是二選一，所以用 radio，不是表格上方那種篩選鈕。
               radio 才會告訴螢幕閱讀器「這組是幾選一、現在選的是哪個」 -->
          <div class="chips">
            <label
              v-for="option in [BOOK_STATUS.listed, BOOK_STATUS.unlisted]"
              :key="option"
              class="chip"
            >
              <input
                v-model="form.status"
                type="radio"
                name="book-status"
                :value="option"
                class="chip__input"
              />
              <span class="chip__face">{{ option }}</span>
            </label>
          </div>
        </fieldset>

        <label class="form__field" :class="{ 'form__field--error': errors.title }">
          <span class="form__label">書名<span class="form__required">必填</span></span>
          <input v-model="form.title" type="text" class="form__input" :disabled="isCoreLocked" />
          <span v-if="errors.title" class="form__error">{{ errors.title }}</span>
        </label>

        <label class="form__field" :class="{ 'form__field--error': errors.author }">
          <span class="form__label">作者<span class="form__required">必填</span></span>
          <input v-model="form.author" type="text" class="form__input" :disabled="isCoreLocked" />
          <span v-if="errors.author" class="form__error">{{ errors.author }}</span>
        </label>

        <label class="form__field" :class="{ 'form__field--error': errors.isbn }">
          <span class="form__label">ISBN<span class="form__required">必填</span></span>
          <input v-model="form.isbn" type="text" class="form__input" :disabled="isCoreLocked" />
          <span v-if="errors.isbn" class="form__error">{{ errors.isbn }}</span>
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

        <div class="form__field">
          <span class="form__label">封面圖片</span>

          <img v-if="coverPreview" :src="coverPreview" alt="封面預覽" class="form__cover" />

          <input
            ref="coverInput"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="form__file"
            @change="handleCoverPick"
          />

          <div class="form__file-row">
            <AdminButton size="xs" @click="pickCover">
              {{ coverPreview ? '更換封面' : '上傳封面' }}
            </AdminButton>

            <span v-if="uploading" class="form__hint">上傳中…</span>
            <span v-else-if="coverError" class="form__error">{{ coverError }}</span>
            <span v-else-if="coverLabel" class="form__hint">{{ coverLabel }}</span>
            <span v-else class="form__error">jpg / png / webp，5MB 以內</span>
          </div>
        </div>

        <label class="form__field">
          <span class="form__label">書籍簡介</span>
          <textarea
            v-model="form.summary"
            class="form__input form__input--area"
            rows="4"
            placeholder="簡短介紹本書內容"
          ></textarea>
        </label>

        <fieldset class="form__field form__field--plain" :class="{ 'form__field--error': errors.categories }">
          <legend class="form__label">
            分類（可複選）<span class="form__required">必填</span>
          </legend>

          <div class="chips">
            <label v-for="category in categoriesStore.categories" :key="category.id" class="chip">
              <input
                v-model="form.categories"
                type="checkbox"
                :value="category.name"
                class="chip__input"
              />
              <span class="chip__face">{{ category.name }}</span>
            </label>
          </div>

          <span v-if="errors.categories" class="form__error">{{ errors.categories }}</span>
        </fieldset>

        <p v-if="missingFields.length" class="modal__missing" role="alert">
          還缺 {{ missingFields.join('、') }}
        </p>

        <p v-if="saveError" class="modal__missing" role="alert">{{ saveError }}</p>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isFormOpen = false">取消</AdminButton>
          <AdminButton type="submit">
            {{ isCreating ? '新增書籍' : '儲存變更' }}
          </AdminButton>
        </div>
      </form>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.category-filter {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: $spacing-md;
  padding: $spacing-sm + $spacing-xs $spacing-md;
  border: 1px solid $primary;
  border-radius: $btn-radius-std;
  background: $primary-100;

  &__text {
    font-size: $p-xs-size;
    color: $neutral-700;
  }

  &__clear {
    padding: 0;
    border: 0;
    background: none;
    font-family: inherit;
    font-size: $p-xs-size;
    color: $primary;
    text-decoration: underline;
    text-underline-offset: 2px;
    cursor: pointer;
  }
}

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
    margin-bottom: $spacing-sm;
    padding: 0;
    font-size: $p-xs-size;
    color: $neutral-600;

  }

  &__input {
    width: 100%;
    padding: $spacing-sm + $spacing-xs $spacing-md;
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
    margin-top: $spacing-sm;
    font-size: $label-xxs-size;
    color: $neutral-400;
  }

  &__required {
    margin-left: $spacing-sm;
    padding: 0 $spacing-xs;
    border-radius: $btn-radius-std;
    background: color-mix(in srgb, #{$color-danger} 12%, #{$neutral-100});
    font-size: $label-xxs-size;
    font-weight: $text-weight;
    line-height: 16px;
    color: $color-danger;
  }

  &__cover {
    display: block;
    width: 90px;
    aspect-ratio: #{$book-cover-ratio};
    margin-bottom: $spacing-sm;
    object-fit: cover;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
  }

  &__file {
    display: none;
  }

  &__file-row {
    display: flex;
    align-items: center;
    gap: $spacing-sm + $spacing-xs;

    .form__hint,
    .form__error {
      margin-top: 0;
      font-size: $p-xs-size;
    }
  }

  &__error {
    display: block;
    margin-top: $spacing-sm;
    font-size: $p-xs-size;
    color: $color-danger;
  }

  &__field--error &__input {
    border-color: $color-danger;

    &:focus-visible {
      outline-color: $color-danger;
    }
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
    padding: $spacing-sm $spacing-sm + $spacing-xs;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    font-size: $p-xs-size;
    color: $neutral-600;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
  }

  &:hover &__face {
    background: $primary-100;
    border-color: $primary-300;
    color: $primary;
  }

  &__input:checked + &__face {
    background: $primary;
    border-color: $primary;
    color: $neutral-100;
  }

  &:hover &__input:checked + &__face {
    background: $primary-500;
    border-color: $primary-500;
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

  &__missing {
    margin: $spacing-md 0 0;
    font-size: $p-xs-size;
    color: $color-danger;
  }

  &__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    margin-top: $spacing-lg;
  }
}
</style>

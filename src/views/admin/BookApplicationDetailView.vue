<script setup>
import { ref, reactive, computed, nextTick, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { APPLICATION_STATUS } from '@/data/adminBooks.js'
import { useAdminCategoriesStore } from '@/stores/adminCategories.js'
import { adminApi } from '@/common/adminApi.js'
import { useAdminApplicationsStore } from '@/stores/adminApplications.js'
import { API_STATIC } from '@/common/api.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AdminResultBar from '@/components/admin/AdminResultBar.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()
const categoriesStore = useAdminCategoriesStore()
const applicationsStore = useAdminApplicationsStore()

const application = ref(null)
const loading = ref(true)
const loadError = ref('')

function toApplication(row) {
  return {
    id: row.book_ap_id,
    title: row.ap_title,
    author: row.ap_author,
    isbn: row.isbn,
    refUrl: row.book_url,
    applicant: row.nickname,
    applicantCode: row.member_code,
    appliedAt: row.created_at,
    reason: row.application_reason,
    status: row.ap_status,
    handledAt: row.resolved_at,
    handledBy: row.staff_name,
    rejectReason: row.reject_reason,
  }
}

async function fetchApplication() {
  loading.value = true
  loadError.value = ''

  try {
    const res = await adminApi.get(`/admin_applications.php?id=${route.params.id}`)
    const rows = res.data.data
    application.value = rows.length ? toApplication(rows[0]) : null
  } catch (e) {
    console.error('[申請詳情]', e)
    loadError.value = '載入失敗，請稍後再試'
    application.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchApplication()
  categoriesStore.ensureCategories()
})

const isPending = computed(() => application.value?.status === APPLICATION_STATUS.pending)
const isRejected = computed(() => application.value?.status === APPLICATION_STATUS.rejected)

const isEditingApplication = ref(false)
const editError = ref('')

const applicationEdit = reactive({
  title: '',
  author: '',
  isbn: '',
  refUrl: '',
})

function startEditing() {
  applicationEdit.title = application.value.title
  applicationEdit.author = application.value.author
  applicationEdit.isbn = application.value.isbn
  applicationEdit.refUrl = application.value.refUrl ?? ''
  editError.value = ''
  isEditingApplication.value = true
}

function cancelEditing() {
  isEditingApplication.value = false
}

async function saveEditing() {
  editError.value = ''

  try {
    await adminApi.post('/admin_application_update.php', {
      book_ap_id: application.value.id,
      ap_title: applicationEdit.title.trim(),
      ap_author: applicationEdit.author.trim(),
      isbn: applicationEdit.isbn.trim(),
      book_url: applicationEdit.refUrl.trim(),
    })

    await fetchApplication()
    isEditingApplication.value = false
  } catch (e) {
    console.error('[修正申請]', e)
    editError.value = e.response?.data?.message || '儲存失敗，請稍後再試'
  }
}

const adminFields = reactive({
  publisher: '',
  publishDate: '',
  coverImage: '',
  summary: '',
  categories: [],
})

const trimmed = computed(() => ({
  publisher: adminFields.publisher.trim(),
  publishDate: adminFields.publishDate.trim(),
  summary: adminFields.summary.trim(),
}))

const FIELD_LABELS = {
  publisher: '出版社',
  publishDate: '出版日期',
  summary: '書籍簡介',
  categories: '書籍分類',
}

const today = (() => {
  const now = new Date()
  const pad = (value) => String(value).padStart(2, '0')
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`
})()

const uploading = ref(false)
const coverError = ref('')
const coverName = ref('')
const coverInput = ref(null)

function pickCover() {
  coverInput.value.click()
}

function coverUrlOf(path) {
  return path ? `${API_STATIC}/uploads/${path}` : null
}

const coverPreview = computed(() => coverUrlOf(adminFields.coverImage))

const coverLabel = computed(() => {
  if (coverName.value) return coverName.value
  if (adminFields.coverImage) return adminFields.coverImage.split('/').pop()
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

    adminFields.coverImage = res.data.bc_image
    coverName.value = file.name
  } catch (e) {
    console.error('[封面上傳]', e)
    coverError.value = e.response?.data?.message || '上傳失敗，請稍後再試'
  } finally {
    uploading.value = false
  }
}

const touched = ref({
  publisher: false,
  publishDate: false,
  summary: false,
  categories: false,
})

function markTouched(field) {
  touched.value[field] = true
}

const errors = computed(() => {
  const e = { publisher: '', publishDate: '', summary: '', categories: '' }

  if (touched.value.publisher && !trimmed.value.publisher) e.publisher = '請填寫出版社'

  if (touched.value.publishDate) {
    if (!trimmed.value.publishDate) e.publishDate = '請選擇出版日期'
    else if (trimmed.value.publishDate > today) e.publishDate = '出版日期不能晚於今天'
  }

  if (touched.value.summary && !trimmed.value.summary) e.summary = '請填寫書籍簡介'

  if (touched.value.categories && !adminFields.categories.length) {
    e.categories = '請至少選擇一個分類'
  }

  return e
})

const canApprove = computed(
  () =>
    Boolean(trimmed.value.publisher) &&
    Boolean(trimmed.value.publishDate) &&
    trimmed.value.publishDate <= today &&
    Boolean(trimmed.value.summary) &&
    adminFields.categories.length > 0,
)

const invalidFieldNames = computed(() =>
  Object.keys(errors.value)
    .filter((field) => errors.value[field])
    .map((field) => FIELD_LABELS[field]),
)

async function handleApprove() {
  if (isEditingApplication.value) {
    document.getElementById('application-edit')?.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
    })
    return
  }

  if (!canApprove.value) {
    Object.keys(touched.value).forEach((field) => {
      touched.value[field] = true
    })
    await nextTick()

    const first = Object.keys(errors.value).find((field) => errors.value[field])
    const target = document.getElementById(`field-${first}`)
    target?.scrollIntoView({ behavior: 'smooth', block: 'center' })
    target?.querySelector('input, textarea')?.focus({ preventScroll: true })
    return
  }

  approveError.value = ''
  isApproveOpen.value = true
}

const isApproveOpen = ref(false)
const approving = ref(false)
const approveError = ref('')

const justHandled = ref('')

async function confirmApprove() {
  approving.value = true
  approveError.value = ''

  try {
    await adminApi.post('/admin_application_approve.php', {
      book_ap_id: application.value.id,
      publisher: trimmed.value.publisher,
      p_date: trimmed.value.publishDate,
      description: trimmed.value.summary,
      bc_image: adminFields.coverImage,
      categories: adminFields.categories,
    })

    await fetchApplication()
    await applicationsStore.fetchPendingCount()
    isApproveOpen.value = false
    justHandled.value = 'approved'
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (e) {
    console.error('[核准]', e)
    approveError.value = e.response?.data?.message || '核准失敗，請稍後再試'
  } finally {
    approving.value = false
  }
}

const isRejectOpen = ref(false)
const rejecting = ref(false)
const rejectError = ref('')
const rejectReason = ref('')

const canReject = computed(() => rejectReason.value.trim().length > 0)

async function handleReject() {
  if (!canReject.value) return

  rejecting.value = true
  rejectError.value = ''

  try {
    await adminApi.post('/admin_application_reject.php', {
      book_ap_id: application.value.id,
      reject_reason: rejectReason.value.trim(),
    })

    await fetchApplication()
    await applicationsStore.fetchPendingCount()
    isRejectOpen.value = false
    justHandled.value = 'rejected'
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (e) {
    console.error('[駁回]', e)
    rejectError.value = e.response?.data?.message || '駁回失敗，請稍後再試'
  } finally {
    rejecting.value = false
  }
}

const reopenError = ref('')

async function handleReopen() {
  reopenError.value = ''

  try {
    await adminApi.post('/admin_application_reopen.php', {
      book_ap_id: application.value.id,
    })

    await fetchApplication()
    await applicationsStore.fetchPendingCount()
    justHandled.value = ''
    rejectReason.value = ''
  } catch (e) {
    console.error('[重新審核]', e)
    reopenError.value = e.response?.data?.message || '操作失敗，請稍後再試'
  }
}
</script>

<template>
  <div class="admin-page">
    <template v-if="loading">
      <header class="admin-page__head">
        <h1 class="admin-page__title">載入中…</h1>
      </header>
    </template>

    <template v-else-if="loadError">
      <header class="admin-page__head">
        <h1 class="admin-page__title">{{ loadError }}</h1>
      </header>

      <AdminPanel>
        <AdminButton variant="outline" to="/admin/books/applications">回申請列表</AdminButton>
      </AdminPanel>
    </template>

    <template v-else-if="application">
      <header class="admin-page__head">
        <h1 class="admin-page__title">
          審核申請
          <span class="detail__divider" aria-hidden="true">｜</span>
          《{{ application.title }}》
        </h1>

        <AdminStatusTag
          :label="application.status"
          :tone="isPending ? 'solid' : 'muted'"
        />
      </header>

      <AdminResultBar
        v-if="!isPending"
        :tone="isRejected ? 'muted' : 'primary'"
        :label="application.status"
        :meta="`${application.handledAt} · 處理人 ${application.handledBy}`"
        :detail="application.rejectReason ? `原因：${application.rejectReason}` : ''"
        :announce="Boolean(justHandled)"
      >
        <AdminButton v-if="isRejected" variant="outline" size="xs" @click="handleReopen">
          重新審核
        </AdminButton>

        <span v-if="reopenError" class="detail__formerror">{{ reopenError }}</span>
      </AdminResultBar>

      <div class="detail__row" :class="{ 'detail__row--single': !isPending }">
        <AdminPanel
          :class="{ 'is-editing': isEditingApplication }"
          :title="isEditingApplication ? '申請內容（編輯中）' : '申請內容'"
          :sub="
            isEditingApplication
              ? '修正會員打錯的書名、作者或 ISBN'
              : '會員送出的申請內容'
          "
        >
          <template v-if="isPending" #actions>
            <AdminButton v-if="!isEditingApplication" size="xs" @click="startEditing">
              編輯
            </AdminButton>

            <span v-else class="detail__editactions">
              <AdminButton variant="outline" size="xs" @click="cancelEditing">取消</AdminButton>
              <AdminButton size="xs" type="submit" form="application-edit">儲存修正</AdminButton>
            </span>
          </template>

          <div v-if="!isEditingApplication" class="detail__list">
            <div class="detail__item">
              <span class="detail__term">申請書名</span>
              <span class="detail__value">{{ application.title }}</span>
            </div>
            <div class="detail__item">
              <span class="detail__term">申請作者</span>
              <span class="detail__value">{{ application.author }}</span>
            </div>
            <div class="detail__item">
              <span class="detail__term">ISBN</span>
              <span class="detail__value">{{ application.isbn }}</span>
            </div>
            <div class="detail__item">
              <span class="detail__term" title="會員送出時的紀錄，不開放修改">
                申請人
                <AppIcon name="lock-filled" :size="11" class="detail__lock" role="img" aria-label="不開放修改" />
              </span>
              <span class="detail__value">
                {{ application.applicant }}（{{ application.applicantCode }}）
              </span>
            </div>
            <div class="detail__item">
              <span class="detail__term">參考連結</span>
              <span class="detail__value">
                <a
                  v-if="application.refUrl"
                  :href="application.refUrl"
                  target="_blank"
                  rel="noopener"
                  class="detail__link"
                >
                  開新分頁查看
                </a>
                <span v-else class="detail__muted">未提供</span>
              </span>
            </div>
            <div class="detail__item">
              <span class="detail__term" title="會員送出時的紀錄，不開放修改">
                申請時間
                <AppIcon name="lock-filled" :size="11" class="detail__lock" role="img" aria-label="不開放修改" />
              </span>
              <span class="detail__value">{{ application.appliedAt }}</span>
            </div>
            <div class="detail__item detail__item--block">
              <span class="detail__term" title="會員送出時的紀錄，不開放修改">
                申請理由
                <AppIcon name="lock-filled" :size="11" class="detail__lock" role="img" aria-label="不開放修改" />
              </span>
              <span class="detail__value">「{{ application.reason }}」</span>
            </div>
          </div>

          <form v-else id="application-edit" class="detail__list" @submit.prevent="saveEditing">
            <div class="detail__item">
              <label class="detail__term" for="edit-title">申請書名</label>
              <input id="edit-title" v-model="applicationEdit.title" type="text" class="detail__input" />
            </div>
            <div class="detail__item">
              <label class="detail__term" for="edit-author">申請作者</label>
              <input id="edit-author" v-model="applicationEdit.author" type="text" class="detail__input" />
            </div>
            <div class="detail__item">
              <label class="detail__term" for="edit-isbn">ISBN</label>
              <input id="edit-isbn" v-model="applicationEdit.isbn" type="text" class="detail__input" />
            </div>
            <div class="detail__item">
              <label class="detail__term" for="edit-refurl">參考連結</label>
              <input
                id="edit-refurl"
                v-model="applicationEdit.refUrl"
                type="url"
                class="detail__input"
                placeholder="留空代表未提供"
              />
            </div>

            <div class="detail__item">
              <span class="detail__term">申請人</span>
              <span class="detail__value detail__value--locked">
                {{ application.applicant }}（{{ application.applicantCode }}）
              </span>
            </div>
            <div class="detail__item">
              <span class="detail__term">申請時間</span>
              <span class="detail__value detail__value--locked">{{ application.appliedAt }}</span>
            </div>
            <div class="detail__item detail__item--block">
              <span class="detail__term">申請理由</span>
              <span class="detail__value detail__value--locked">「{{ application.reason }}」</span>
            </div>

            <p v-if="editError" class="detail__formerror" role="alert">{{ editError }}</p>
          </form>
        </AdminPanel>

        <AdminPanel
          v-if="isPending"
          title="管理員補充資料"
          sub="這些資料會直接顯示在前台的書籍頁"
        >
          <div class="form">
            <label id="field-publisher" class="form__field" :class="{ 'form__field--error': errors.publisher }">
              <span class="form__label">出版社<span class="form__required">必填</span></span>
              <input
                v-model="adminFields.publisher"
                type="text"
                class="form__input"
                placeholder="例：方智出版"
                :aria-invalid="Boolean(errors.publisher)"
                @blur="markTouched('publisher')"
              />
              <span v-if="errors.publisher" class="form__error">{{ errors.publisher }}</span>
            </label>

            <label id="field-publishDate" class="form__field" :class="{ 'form__field--error': errors.publishDate }">
              <span class="form__label">出版日期<span class="form__required">必填</span></span>
              <input
                v-model="adminFields.publishDate"
                type="date"
                class="form__input"
                min="1900-01-01"
                :max="today"
                :aria-invalid="Boolean(errors.publishDate)"
                @blur="markTouched('publishDate')"
              />
              <span v-if="errors.publishDate" class="form__error">{{ errors.publishDate }}</span>
            </label>

            <div class="form__field">
              <span class="form__label">封面圖片<span class="form__optional">選填</span></span>

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
                <span v-else class="form__hint">留空的話之後可以在正式書籍列表補上</span>
              </div>
            </div>

            <label id="field-summary" class="form__field" :class="{ 'form__field--error': errors.summary }">
              <span class="form__label">書籍簡介<span class="form__required">必填</span></span>
              <textarea
                v-model="adminFields.summary"
                class="form__input form__input--area"
                rows="4"
                maxlength="500"
                placeholder="簡短介紹本書內容"
                :aria-invalid="Boolean(errors.summary)"
                @blur="markTouched('summary')"
              ></textarea>
              <span v-if="errors.summary" class="form__error">{{ errors.summary }}</span>
              <span v-else class="form__hint">最多 500 字</span>
            </label>

            <fieldset
              id="field-categories"
              class="form__field form__field--plain"
              :class="{ 'form__field--error': errors.categories }"
            >
              <legend class="form__label">
                分類（可複選）<span class="form__required">必填</span>
              </legend>

              <div class="chips">
                <label
                  v-for="category in categoriesStore.categories"
                  :key="category.id"
                  class="chip"
                >
                  <input
                    v-model="adminFields.categories"
                    type="checkbox"
                    :value="category.name"
                    class="chip__input"
                    @change="markTouched('categories')"
                  />
                  <span class="chip__face">{{ category.name }}</span>
                </label>
              </div>

              <span v-if="errors.categories" class="form__error">{{ errors.categories }}</span>
            </fieldset>
          </div>

        </AdminPanel>
      </div>

      <footer v-if="isPending" class="admin-page__actionbar">
        <p
          class="admin-page__actionbar-note"
          :class="{
            'admin-page__actionbar-note--alert': isEditingApplication || invalidFieldNames.length,
          }"
          role="status"
        >
          <template v-if="isEditingApplication">
            申請內容還在編輯中，請先儲存或取消
          </template>
          <template v-else-if="invalidFieldNames.length">
            請先修正：{{ invalidFieldNames.join('、') }}
          </template>
          <template v-else>核准後這本書會直接上架到書庫</template>
        </p>

        <AdminButton variant="outline" tone="danger" @click="isRejectOpen = true">駁回申請</AdminButton>
        <AdminButton @click="handleApprove">核准並上架</AdminButton>
      </footer>

      <footer v-else class="admin-page__actionbar">
        <p class="admin-page__actionbar-note">
          <template v-if="application.status === APPLICATION_STATUS.approved">
            這本書已經上架在書庫，之後要改資料請到
            <RouterLink to="/admin/books/list" class="detail__inlink">正式書籍列表</RouterLink>
          </template>
          <template v-else>駁回原因已經通知申請人</template>
        </p>

        <AdminButton to="/admin/books/applications">回申請列表</AdminButton>
      </footer>
    </template>

    <template v-else>
      <header class="admin-page__head">
        <h1 class="admin-page__title">找不到這筆申請</h1>
      </header>

      <AdminPanel>
        <p class="detail__muted detail__notfound">網址上的申請編號不存在，可能已經被移除。</p>
        <AdminButton variant="outline" to="/admin/books/applications">回申請列表</AdminButton>
      </AdminPanel>
    </template>

    <AppModal v-model="isApproveOpen" title="核准並上架">
      <p class="modal__text">會用以下資料建立書籍，並立刻上架到前台書庫。</p>

      <div v-if="application" class="detail__list detail__list--modal">
        <div class="detail__item">
          <span class="detail__term">書名</span>
          <span class="detail__value">{{ application.title }}</span>
        </div>
        <div class="detail__item">
          <span class="detail__term">作者</span>
          <span class="detail__value">{{ application.author }}</span>
        </div>
        <div class="detail__item">
          <span class="detail__term">ISBN</span>
          <span class="detail__value">{{ application.isbn }}</span>
        </div>
        <div class="detail__item">
          <span class="detail__term">出版社</span>
          <span class="detail__value">{{ trimmed.publisher }}</span>
        </div>
        <div class="detail__item">
          <span class="detail__term">出版日期</span>
          <span class="detail__value">{{ trimmed.publishDate }}</span>
        </div>
        <div class="detail__item">
          <span class="detail__term">分類</span>
          <span class="detail__value">{{ adminFields.categories.join('、') }}</span>
        </div>
      </div>

      <p v-if="approveError" class="detail__formerror" role="alert">{{ approveError }}</p>

      <div class="modal__actions">
        <AdminButton variant="outline" @click="isApproveOpen = false">再檢查一下</AdminButton>
        <AdminButton :disabled="approving" @click="confirmApprove">
          {{ approving ? '處理中…' : '確定核准' }}
        </AdminButton>
      </div>
    </AppModal>

    <AppModal v-model="isRejectOpen" title="駁回申請">
      <p class="modal__text">
        駁回後將通知申請人（{{ application?.applicant }}），此決定將寫入管理紀錄。
      </p>

      <form @submit.prevent="handleReject">
        <label class="form__field">
          <span class="form__label">駁回原因（將回饋給申請人）</span>
          <textarea
            v-model="rejectReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：此書已有相同 ISBN 書籍在庫，請改用書庫搜尋加入書架"
          ></textarea>
        </label>

        <p v-if="rejectError" class="detail__formerror" role="alert">{{ rejectError }}</p>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isRejectOpen = false">取消</AdminButton>
          <AdminButton tone="danger" :disabled="!canReject || rejecting" type="submit">
            {{ rejecting ? '處理中…' : '確認駁回' }}
          </AdminButton>
        </div>
      </form>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/abstracts/variables' as *;

.detail {
  &__divider {
    margin: 0 $spacing-xs;
    color: $neutral-400;
    font-weight: $text-weight;
  }

  &__row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    align-items: start;

    > *:first-child {
      position: sticky;
      top: $spacing-md;
    }

    .is-editing {
      border-color: $primary;
    }

    &--single {
      grid-template-columns: minmax(0, 720px);

      > *:first-child {
        position: static;
      }
    }
  }

  &__item {
    display: grid;
    grid-template-columns: 84px 1fr;
    gap: $spacing-md;
    align-items: baseline;
    padding: $spacing-sm + $spacing-xs 0;
    border-bottom: 1px solid $neutral-200;

    &--block {
      display: block;
      border-bottom: 0;
    }
  }

  &__term {
    display: inline-flex;
    align-items: center;
    gap: $spacing-xs;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__lock {
    color: $neutral-400;
  }

  &__value {
    font-size: $p-sm-size;
    color: $neutral-800;
  }

  &__item--block &__value {
    display: block;
    margin-top: $spacing-sm;
    line-height: 1.8;
  }

  &__list--modal {
    margin-bottom: $spacing-lg;

    .detail__item {
      padding: $spacing-sm 0;
    }
  }

  &__editactions {
    display: inline-flex;
    gap: $spacing-sm;
  }

  &__input {
    width: 100%;
    box-sizing: border-box;
    padding: $spacing-sm $spacing-sm + $spacing-xs;
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

  &__value--locked {
    color: $neutral-500;
  }


  &__link {
    font-size: $p-sm-size;
    color: $primary;
    text-underline-offset: 2px;
  }

  &__muted {
    margin: 0;
    font-size: $p-xs-size;
    color: $neutral-400;
    line-height: 1.8;
  }

  &__formerror {
    margin-top: $spacing-sm;
    font-size: $p-sm-size;
    color: $color-danger;
  }

  &__notfound {
    margin-bottom: $spacing-md;
  }

  &__inlink {
    color: $primary;
    text-decoration: underline;

    &:hover {
      text-decoration: none;
    }
  }
}

.form {
  &__cover {
    display: block;
    width: 96px;
    margin-bottom: $spacing-sm;
    border: 1px solid $neutral-300;
    border-radius: 4px;
  }

  &__file {
    display: none;
  }

  &__file-row {
    display: flex;
    align-items: center;
    gap: $spacing-sm + $spacing-xs;
  }

  display: flex;
  flex-direction: column;
  gap: $spacing-md;

  &__field {
    display: block;

    // fieldset 自帶邊框和內距，分類那組不需要
    &--plain {
      border: 0;
      margin: 0;
      padding: 0;
    }
  }

  &__label {
    display: block;
    margin-bottom: $spacing-sm;
    padding: 0;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__list--modal {
    margin-bottom: $spacing-lg;

    .detail__item {
      padding: $spacing-sm 0;
    }
  }

  &__editactions {
    display: inline-flex;
    gap: $spacing-sm;
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

  &__required,
  &__optional {
    margin-left: $spacing-sm;
    padding: 0 $spacing-xs;
    border-radius: $btn-radius-std;
    font-size: $label-xxs-size;
    font-weight: $text-weight;
    line-height: 16px;
  }

  &__required {
    background: color-mix(in srgb, #{$color-danger} 12%, #{$neutral-100});
    color: $color-danger;
  }

  &__optional {
    background: $neutral-200;
    color: $neutral-500;
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

  // 把 checkbox 縮到看不見但仍然存在，鍵盤 Tab 和螢幕閱讀器才找得到它
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
  }

  &__input:checked + &__face {
    background: $primary;
    border-color: $primary;
    color: $neutral-100;
  }

  // 看不見的 checkbox 被 Tab 選到時，把外框畫在看得見的那顆標籤上
  &__input:focus-visible + &__face {
    outline: 2px solid $primary;
    outline-offset: 2px;
  }
}

.modal {
  &__text {
    margin: 0 0 $spacing-md;
    font-size: $p-sm-size;
    line-height: 1.8;
    color: $neutral-700;
  }

  &__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    margin-top: $spacing-lg;
  }
}
</style>

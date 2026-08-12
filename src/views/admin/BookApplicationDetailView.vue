<script setup>
import { ref, reactive, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { APPLICATION_STATUS } from '@/data/adminBooks.js'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppButton from '@/components/common/AppButton.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()
const router = useRouter()
const adminBooksStore = useAdminBooksStore()

const application = computed(() => adminBooksStore.getApplication(route.params.id))
const isPending = computed(() => application.value?.status === APPLICATION_STATUS.pending)

// 會員填的那一區平常鎖住，要修正打錯的書名或 ISBN 才切成可編輯
const isEditingApplication = ref(false)

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
  isEditingApplication.value = true
}

function saveEditing() {
  adminBooksStore.updateApplication(application.value.id, {
    title: applicationEdit.title.trim(),
    author: applicationEdit.author.trim(),
    isbn: applicationEdit.isbn.trim(),
    refUrl: applicationEdit.refUrl.trim() || null,
  })
  isEditingApplication.value = false
}

const adminFields = reactive({
  publisher: '',
  publishDate: '',
  coverUrl: '',
  summary: '',
  categories: [],
})

// 判斷和送出都用去過空白的值，不然打三個空白鍵按鈕就會亮
// （'   ' 在 JavaScript 裡算「真」）
const trimmed = computed(() => ({
  publisher: adminFields.publisher.trim(),
  publishDate: adminFields.publishDate.trim(),
  coverUrl: adminFields.coverUrl.trim(),
  summary: adminFields.summary.trim(),
}))

// 封面可以留空，其餘都要補齊才能核准 —— 核准就直接上架了
const canApprove = computed(
  () =>
    Boolean(trimmed.value.publisher) &&
    Boolean(trimmed.value.publishDate) &&
    Boolean(trimmed.value.summary) &&
    adminFields.categories.length > 0,
)

function handleApprove() {
  if (!canApprove.value) return

  adminBooksStore.approve(application.value.id, {
    publisher: trimmed.value.publisher,
    publishDate: trimmed.value.publishDate,
    coverUrl: trimmed.value.coverUrl,
    summary: trimmed.value.summary,
    categories: adminFields.categories,
  })

  router.push('/admin/books/applications')
}

const isRejectOpen = ref(false)
const rejectReason = ref('')

// 駁回原因會回饋給申請人，必填
const canReject = computed(() => rejectReason.value.trim().length > 0)

function handleReject() {
  if (!canReject.value) return

  adminBooksStore.reject(application.value.id, rejectReason.value.trim())
  isRejectOpen.value = false
  router.push('/admin/books/applications')
}
</script>

<template>
  <div class="admin-page">
    <template v-if="application">
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

      <p v-if="!isPending" class="detail__handled">
        這筆申請已於 {{ application.handledAt }} 由 {{ application.handledBy }} 標記為{{ application.status }}。
        <template v-if="application.rejectReason">
          駁回原因：{{ application.rejectReason }}
        </template>
      </p>

      <div class="detail__row">
        <AdminPanel title="申請內容" :sub="isEditingApplication ? '修正會員填錯的書名、作者或 ISBN' : '此區塊為會員填寫，不可修改'">
          <template v-if="isPending" #actions>
            <AppButton v-if="!isEditingApplication" size="xs" @click="startEditing">
              切換編輯模式
            </AppButton>
            <AppButton v-else size="xs" color="secondary" @click="saveEditing">
              儲存修正
            </AppButton>
          </template>

          <dl v-if="!isEditingApplication" class="detail__list">
            <div class="detail__item">
              <dt>申請書名</dt>
              <dd>{{ application.title }}</dd>
            </div>
            <div class="detail__item">
              <dt>申請作者</dt>
              <dd>{{ application.author }}</dd>
            </div>
            <div class="detail__item">
              <dt>ISBN</dt>
              <dd>{{ application.isbn }}</dd>
            </div>
            <div class="detail__item">
              <dt>申請人</dt>
              <dd>{{ application.applicant }}（{{ application.applicantCode }}）</dd>
            </div>
            <div class="detail__item">
              <dt>參考連結</dt>
              <dd>
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
              </dd>
            </div>
            <div class="detail__item">
              <dt>申請時間</dt>
              <dd>{{ application.appliedAt }}</dd>
            </div>
            <div class="detail__item detail__item--block">
              <dt>申請理由</dt>
              <dd>「{{ application.reason }}」</dd>
            </div>
          </dl>

          <div v-else class="form">
            <label class="form__field">
              <span class="form__label">申請書名</span>
              <input v-model="applicationEdit.title" type="text" class="form__input" />
            </label>
            <label class="form__field">
              <span class="form__label">申請作者</span>
              <input v-model="applicationEdit.author" type="text" class="form__input" />
            </label>
            <label class="form__field">
              <span class="form__label">ISBN</span>
              <input v-model="applicationEdit.isbn" type="text" class="form__input" />
            </label>
            <label class="form__field">
              <span class="form__label">參考連結</span>
              <input v-model="applicationEdit.refUrl" type="url" class="form__input" placeholder="留空代表未提供" />
            </label>
          </div>
        </AdminPanel>

        <AdminPanel
          title="管理員補充資料"
          sub="核准前請手動查詢並填寫以下欄位（目前尚未串接自動抓取 API）"
        >
          <div v-if="isPending" class="form">
            <label class="form__field">
              <span class="form__label">出版社</span>
              <input v-model="adminFields.publisher" type="text" class="form__input" placeholder="例：方智出版" />
            </label>

            <label class="form__field">
              <span class="form__label">出版日期</span>
              <input v-model="adminFields.publishDate" type="date" class="form__input" />
            </label>

            <label class="form__field">
              <span class="form__label">封面連結</span>
              <input v-model="adminFields.coverUrl" type="url" class="form__input" placeholder="貼上封面圖片網址" />
              <span class="form__hint">找不到官方圖源時，可先留空，之後在正式書籍列表再補上</span>
            </label>

            <label class="form__field">
              <span class="form__label">書籍簡介</span>
              <textarea
                v-model="adminFields.summary"
                class="form__input form__input--area"
                rows="4"
                placeholder="簡短介紹本書內容"
              ></textarea>
            </label>

            <fieldset class="form__field form__field--plain">
              <legend class="form__label">分類（可複選）</legend>

              <!-- 藏起來的 checkbox + label 當按鈕，不是用 <button>：
                   button 不會告訴螢幕閱讀器「這是可以複選的、現在選了哪些」 -->
              <div class="chips">
                <label v-for="category in adminBooksStore.categories" :key="category" class="chip">
                  <input
                    v-model="adminFields.categories"
                    type="checkbox"
                    :value="category"
                    class="chip__input"
                  />
                  <span class="chip__face">{{ category }}</span>
                </label>
              </div>
            </fieldset>
          </div>

          <p v-else class="detail__muted">這筆申請已經處理完畢，補充資料請到正式書籍列表編輯。</p>
        </AdminPanel>
      </div>

      <footer v-if="isPending" class="detail__actions">
        <AppButton variant="outlined" @click="isRejectOpen = true">駁回申請</AppButton>
        <AppButton :disabled="!canApprove" @click="handleApprove">核准並上架</AppButton>

        <p v-if="!canApprove" class="detail__muted">
          出版社、出版日期、書籍簡介與至少一個分類補齊後才能核准
        </p>
      </footer>

      <footer v-else class="detail__actions">
        <AppButton variant="outlined" to="/admin/books/applications">回申請列表</AppButton>
      </footer>
    </template>

    <AdminPanel v-else title="找不到這筆申請">
      <p class="detail__muted">
        網址上的申請編號不存在，可能已經被移除。
      </p>
      <AppButton variant="outlined" to="/admin/books/applications">回申請列表</AppButton>
    </AdminPanel>

    <AppModal v-model="isRejectOpen" title="確認駁回申請">
      <p class="modal__text">
        駁回後將通知申請人（{{ application?.applicant }}），此決定將寫入管理紀錄。
      </p>

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

      <div class="modal__actions">
        <AppButton variant="outlined" @click="isRejectOpen = false">取消</AppButton>
        <AppButton :disabled="!canReject" @click="handleReject">確認駁回</AppButton>
      </div>
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
  }

  &__handled {
    margin: 0;
    padding: $spacing-sm + $spacing-xxs $spacing-md;
    border-left: 4px solid $neutral-400;
    background: $neutral-300;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-700;
  }

  &__list {
    margin: 0;
  }

  &__item {
    display: flex;
    justify-content: space-between;
    gap: $spacing-md;
    padding: $spacing-sm + $spacing-xxs 0;
    border-bottom: 1px solid $neutral-200;

    dt {
      flex-shrink: 0;
      font-size: $p-xs-size;
      color: $neutral-600;
    }

    dd {
      margin: 0;
      font-size: $p-sm-size;
      color: $neutral-800;
      text-align: right;
    }

    // 申請理由是一整段，靠右對齊很難讀，所以整列往下排
    &--block {
      display: block;
      border-bottom: 0;

      dd {
        margin-top: $spacing-sm;
        text-align: left;
        line-height: 1.8;
      }
    }
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

  &__actions {
    display: flex;
    align-items: center;
    gap: $spacing-md;
  }
}

.form {
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

  // 把 checkbox 縮到看不見但仍然存在，鍵盤 Tab 和螢幕閱讀器才找得到它
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

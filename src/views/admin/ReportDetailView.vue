<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { REPORT_STATUS, REPORT_ACTION, REPORT_TARGET } from '@/data/adminReports.js'
import { adminApi } from '@/common/adminApi.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AdminResultBar from '@/components/admin/AdminResultBar.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()
const reportsStore = useAdminReportsStore()

const report = ref(null)
const detail = ref({ punish_count: 0, upheld_count: 0, reports: [] })
const loading = ref(false)
const error = ref('')

// 後端欄位進畫面前轉成前端在用的名字
function toReport(row) {
  const source =
    row.target_type === REPORT_TARGET.thought
      ? `《${row.book_title ?? '未知書籍'}》書籍心得`
      : `公會「${row.guild_name ?? '未知公會'}」討論區`

  return {
    id: row.report_no,           // 畫面上顯示的編號
    reportId: row.report_id,     // 送 API 用的真編號
    targetType: row.target_type,
    reason: row.reason,
    reasonDetail: row.reason_detail,
    content: row.content,
    source,
    createdAt: row.created_at.slice(0, 16),
    status: row.status,
    actionTaken: row.action_taken,
    resolutionNotes: row.resolution_notes,
    resolvedAt: row.resolved_at ? row.resolved_at.slice(0, 16) : '',
    staffName: row.staff_name,
    reporterName: row.reporter_name,
    reporterCode: row.reporter_code,
    reportedName: row.reported_name,
    reportedCode: row.reported_code,
    reportedStatus: row.reported_status,
  }
}

async function fetchReport() {
  loading.value = true
  error.value = ''

  try {
    const res = await adminApi.get(`/admin_reports.php?id=${route.params.id}`)
    const rows = res.data.data

    if (rows.length === 0) {
      report.value = null
      return
    }

    report.value = toReport(rows[0])
    detail.value = res.data.detail ?? { punish_count: 0, upheld_count: 0, reports: [] }
  } catch (e) {
    console.error('[檢舉詳情]', e)
    error.value = '載入失敗，請稍後再試'
    report.value = null
  } finally {
    loading.value = false
  }
}

onMounted(fetchReport)

// 從列表點不同筆進來時網址會變，但元件不會重建，不重撈會停在舊的那筆
watch(() => route.params.id, fetchReport)

const isPending = computed(() => report.value?.status === REPORT_STATUS.pending)
const isUpheld = computed(() => report.value?.status === REPORT_STATUS.upheld)

const reportedName = computed(() => report.value?.reportedName ?? '')
const reporterName = computed(() => report.value?.reporterName ?? '')

const isReportedSuspended = computed(() => report.value?.reportedStatus === '停權')
const punishmentCount = computed(() => detail.value.punish_count)
const upheldAgainst = computed(() => detail.value.upheld_count)

// 同一則內容底下、除了現在看的這張以外的檢舉。
// 四個人都說「廣告」跟四個人各說不同理由，是完全不同的訊號，
// 所以摘要要帶次數，不是只列出有哪幾種
const otherReasons = computed(() => {
  const others = (detail.value.reports ?? []).filter(
    (row) => row.report_id !== report.value?.reportId,
  )

  if (others.length === 0) return ''

  const tally = new Map()

  for (const row of others) {
    tally.set(row.reason, (tally.get(row.reason) ?? 0) + 1)
  }

  const parts = [...tally]
    .sort((a, b) => b[1] - a[1])
    .map(([name, count]) => (count > 1 ? `${name} ×${count}` : name))

  return `另外 ${others.length} 人：${parts.join('、')}`
})

const targetWord = computed(() =>
  report.value?.targetType === REPORT_TARGET.message ? '留言' : '心得',
)

// 檢舉成立一定會下架那則內容，所以這裡選的只有「帳號要不要罰」。
// 已停權的人不用再罰一次，那兩個選項直接不出現
const actionOptions = computed(() => {
  const options = [
    {
      value: REPORT_ACTION.removeContent,
      label: '不處分帳號',
      desc: '只下架內容，不計入處分次數。',
    },
  ]

  if (!isReportedSuspended.value) {
    options.push(
      {
        value: REPORT_ACTION.warnUser,
        label: REPORT_ACTION.warnUser,
        desc: '通知對方，帳號仍可正常使用。',
      },
      {
        value: REPORT_ACTION.suspendUser,
        label: REPORT_ACTION.suspendUser,
        desc: '對方無法再登入平台。',
      },
    )
  }

  return options
})

// 處理完不跳走，留在原地把結果顯示出來
const justHandled = ref(false)
const submitting = ref(false)
const submitError = ref('')

// 判決送出去、成功之後重撈一次 —— 狀態、處分次數都變了
async function submitResolve(payload) {
  submitting.value = true
  submitError.value = ''

  try {
    await adminApi.post('/admin_report_resolve.php', {
      report_id: report.value.reportId,
      ...payload,
    })

    await fetchReport()
    await reportsStore.fetchPendingCount()
    justHandled.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
    return true
  } catch (e) {
    console.error('[檢舉判決]', e)
    submitError.value = e.response?.data?.message ?? '處理失敗，請稍後再試'
    return false
  } finally {
    submitting.value = false
  }
}

const isUpholdOpen = ref(false)
const action = ref('')
const upholdNotes = ref('')
const upholdTried = ref(false)

const canUphold = computed(() => Boolean(action.value) && upholdNotes.value.trim().length > 0)

// 只有停權會把人擋在外面，所以只有它是紅的
const confirmTone = computed(() =>
  action.value === REPORT_ACTION.suspendUser ? 'danger' : 'primary',
)

function openUphold() {
  action.value = isReportedSuspended.value ? REPORT_ACTION.removeContent : ''
  upholdNotes.value = ''
  upholdTried.value = false
  submitError.value = ''
  isUpholdOpen.value = true
}

async function handleUphold() {
  upholdTried.value = true
  if (!canUphold.value) return

  const ok = await submitResolve({
    status: REPORT_STATUS.upheld,
    action_taken: action.value,
    resolution_notes: upholdNotes.value.trim(),
  })

  if (ok) isUpholdOpen.value = false
}

// 只有真的開出處分（警告／停權）才需要先問一次 ——
// 「不處分帳號」和「不成立」都沒在對方身上留東西，退回不會影響到誰
const hasPunishment = computed(
  () =>
    report.value?.actionTaken === REPORT_ACTION.warnUser ||
    report.value?.actionTaken === REPORT_ACTION.suspendUser,
)

const isReopenOpen = ref(false)

function askReopen() {
  if (hasPunishment.value) {
    isReopenOpen.value = true
    return
  }
  handleReopen()
}

async function handleReopen() {
  submitting.value = true
  submitError.value = ''

  try {
    await adminApi.post('/admin_report_reopen.php', { report_id: report.value.reportId })
    await fetchReport()
    await reportsStore.fetchPendingCount()
    isReopenOpen.value = false
    justHandled.value = false
  } catch (e) {
    console.error('[重新處理]', e)
    submitError.value = e.response?.data?.message ?? '退回失敗，請稍後再試'
  } finally {
    submitting.value = false
  }
}

const isDismissOpen = ref(false)
const dismissNotes = ref('')

function openDismiss() {
  dismissNotes.value = ''
  submitError.value = ''
  isDismissOpen.value = true
}

async function handleDismiss() {
  const ok = await submitResolve({
    status: REPORT_STATUS.dismissed,
    resolution_notes: dismissNotes.value.trim(),
  })

  if (ok) isDismissOpen.value = false
}
</script>

<template>
  <div class="admin-page report">
    <template v-if="loading">
      <header class="admin-page__head">
        <h1 class="admin-page__title">載入中…</h1>
      </header>
    </template>

    <template v-else-if="error">
      <header class="admin-page__head">
        <h1 class="admin-page__title">載入失敗</h1>
      </header>
      <AdminPanel>
        <p class="report__muted report__notfound">{{ error }}</p>
        <AdminButton variant="outline" to="/admin/reports">回檢舉列表</AdminButton>
      </AdminPanel>
    </template>

    <template v-else-if="report">
      <header class="admin-page__head">
        <h1 class="admin-page__title">
          檢舉單
          <span class="report__divider" aria-hidden="true">｜</span>
          {{ report.id }}
        </h1>

        <AdminStatusTag :label="report.status" :tone="isPending ? 'solid' : 'muted'" />
      </header>

      <AdminResultBar
        v-if="!isPending"
        :tone="isUpheld ? 'primary' : 'muted'"
        :label="isUpheld ? `${report.status} · ${report.actionTaken}` : report.status"
        :meta="`${report.resolvedAt} · 處理人 ${report.staffName}`"
        :detail="report.resolutionNotes ? `處理紀錄：${report.resolutionNotes}` : ''"
        :announce="justHandled"
      >
        <AdminButton variant="outline" size="xs" @click="askReopen">重新處理</AdminButton>
      </AdminResultBar>

      <div class="report__row">
        <AdminPanel title="檢舉單資訊">
          <div class="report__list">
            <div class="report__item">
              <span class="report__term">檢舉類型</span>
              <span class="report__value">{{ report.targetType }}檢舉</span>
            </div>
            <div class="report__item">
              <span class="report__term">檢舉原因</span>
              <span class="report__value">
                {{ report.reason }}
                <span v-if="otherReasons" class="report__others">{{ otherReasons }}</span>
              </span>
            </div>
            <div class="report__item">
              <span class="report__term">檢舉人</span>
              <span class="report__value">
                <RouterLink :to="`/admin/members/${report.reporterCode}`" class="report__inlink">
                  {{ reporterName }}
                </RouterLink>
                <span class="report__code">{{ report.reporterCode }}</span>
              </span>
            </div>
            <div class="report__item">
              <span class="report__term">檢舉時間</span>
              <span class="report__value">{{ report.createdAt }}</span>
            </div>
            <div class="report__item report__item--block">
              <span class="report__term">檢舉說明</span>
              <span class="report__value">
                <template v-if="report.reasonDetail">「{{ report.reasonDetail }}」</template>
                <span v-else class="report__muted">檢舉人沒有填寫</span>
              </span>
            </div>
          </div>
        </AdminPanel>

        <div class="report__col">
          <AdminPanel title="被檢舉內容" :sub="report.source">
            <blockquote class="report__content">{{ report.content }}</blockquote>
          </AdminPanel>

          <AdminPanel title="被檢舉人">
            <template #actions>
              <AdminButton variant="outline" size="xs" :to="`/admin/members/${report.reportedCode}`">
                檢視會員
              </AdminButton>
            </template>

            <div class="report__list">
              <div class="report__item">
                <span class="report__term">暱稱</span>
                <span class="report__value">
                  {{ reportedName }}
                  <span class="report__code">{{ report.reportedCode }}</span>
                </span>
              </div>
              <div class="report__item">
                <span class="report__term">帳號狀態</span>
                <span class="report__value">
                  <AdminStatusTag
                    :label="report.reportedStatus"
                    :tone="isReportedSuspended ? 'muted' : 'solid'"
                  />
                </span>
              </div>
            </div>

            <div class="report__metrics">
              <div class="report__metric">
                <span class="report__metric-value">{{ punishmentCount }}</span>
                <span class="report__metric-label">累計處分次數</span>
              </div>
              <div class="report__metric">
                <span class="report__metric-value">{{ upheldAgainst }}</span>
                <span class="report__metric-label">過往成立的檢舉</span>
              </div>
            </div>
          </AdminPanel>
        </div>
      </div>

      <footer v-if="isPending" class="admin-page__actionbar report__actionbar--end">
        <AdminButton variant="outline" @click="openDismiss">檢舉不成立</AdminButton>
        <AdminButton @click="openUphold">檢舉成立</AdminButton>
      </footer>

      <footer
        v-else
        class="admin-page__actionbar"
        :class="{ 'report__actionbar--end': !isUpheld }"
      >
        <p v-if="isUpheld" class="admin-page__actionbar-note">
          處分記在
          <RouterLink :to="`/admin/members/${report.reportedCode}`" class="report__inlink">
            {{ reportedName }} 的處分紀錄
          </RouterLink>
        </p>

        <AdminButton to="/admin/reports">回檢舉列表</AdminButton>
      </footer>
    </template>

    <template v-else>
      <header class="admin-page__head">
        <h1 class="admin-page__title">找不到這筆檢舉</h1>
      </header>

      <AdminPanel>
        <p class="report__muted report__notfound">網址上的檢舉編號不存在，可能已經被移除。</p>
        <AdminButton variant="outline" to="/admin/reports">回檢舉列表</AdminButton>
      </AdminPanel>
    </template>

    <AppModal v-model="isUpholdOpen" title="檢舉成立">
      <form @submit.prevent="handleUphold">
        <div class="uphold">
          <span class="uphold__term">內容處置</span>
          <span class="uphold__value">這則{{ targetWord }}會下架</span>
        </div>

        <fieldset class="form__field form__field--plain">
          <legend class="form__label">
            帳號處分<span class="form__required">必填</span>
          </legend>

          <p v-if="isReportedSuspended" class="modal__warn">
            {{ reportedName }} 已經停權，這次不再加重。
          </p>

          <div v-else class="choice">
            <label
              v-for="option in actionOptions"
              :key="option.value"
              class="choice__item"
              :class="{ 'choice__item--on': action === option.value }"
            >
              <input
                v-model="action"
                type="radio"
                name="report-action"
                :value="option.value"
                class="choice__input"
              />
              <span class="choice__body">
                <span class="choice__title">{{ option.label }}</span>
                <span class="choice__desc">{{ option.desc }}</span>
              </span>
            </label>
          </div>

          <span v-if="upholdTried && !action" class="form__error">請選擇要不要處分帳號</span>
        </fieldset>

        <label
          class="form__field"
          :class="{ 'form__field--error': upholdTried && !upholdNotes.trim() }"
        >
          <span class="form__label">
            處分原因（會讓對方看到）<span class="form__required">必填</span>
          </span>
          <textarea
            v-model="upholdNotes"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：心得內容為課程廣告，已下架該則心得"
          ></textarea>
          <span v-if="upholdTried && !upholdNotes.trim()" class="form__error">請填寫處分原因</span>
        </label>

        <p v-if="submitError" class="form__error">{{ submitError }}</p>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isUpholdOpen = false">取消</AdminButton>
          <AdminButton :tone="confirmTone" type="submit">
            {{ submitting ? '處理中…' : '確認執行' }}
          </AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isDismissOpen" title="檢舉不成立">
      <p class="modal__text">{{ reportedName }} 不會留下任何紀錄。</p>

      <form @submit.prevent="handleDismiss">
        <label class="form__field">
          <span class="form__label">處理紀錄<span class="form__optional">選填</span></span>
          <textarea
            v-model="dismissNotes"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：內容為負評但未涉及人身攻擊或違規，屬正常表達意見"
          ></textarea>
        </label>

        <p v-if="submitError" class="form__error">{{ submitError }}</p>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isDismissOpen = false">取消</AdminButton>
          <AdminButton type="submit">確認不成立</AdminButton>
        </div>
      </form>
    </AppModal>
    <AppModal v-model="isReopenOpen" title="重新處理">
      <p class="modal__text">
        {{ reportedName }} 因為這筆檢舉受到的「{{ report?.actionTaken }}」會一併撤銷，不計入處分次數。
      </p>

      <p v-if="submitError" class="form__error">{{ submitError }}</p>

      <div class="modal__actions">
        <AdminButton variant="outline" @click="isReopenOpen = false">取消</AdminButton>
        <AdminButton @click="handleReopen">
          {{ submitting ? '處理中…' : '確認退回' }}
        </AdminButton>
      </div>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/abstracts/variables' as *;

.report {
  &__divider {
    margin: 0 $spacing-sm;
    color: $neutral-400;
    font-weight: $text-weight;
  }

  &__row {
    display: grid;
    grid-template-columns: 360px minmax(0, 1fr);
    gap: $spacing-md;
    align-items: start;
  }

  // 左欄比右欄短，捲動被檢舉內容時讓檢舉單資訊留在畫面上
  &__row > *:first-child {
    position: sticky;
    top: $spacing-md;
  }

  &__col {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
  }

  &__list {
    display: flex;
    flex-direction: column;
  }

  &__item {
    display: grid;
    grid-template-columns: 84px 1fr;
    gap: $spacing-md;
    align-items: baseline;
    padding: $spacing-sm + $spacing-xs 0;
    border-bottom: 1px solid $neutral-200;

    &:first-child {
      padding-top: 0;
    }

    &:last-child {
      border-bottom: 0;
    }

    &--block {
      display: block;
    }
  }

  &__term {
    font-size: $p-xs-size;
    color: $neutral-500;
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

  &__code {
    margin-left: $spacing-sm;
    font-size: $label-xxs-size;
    color: $neutral-400;
  }

  // 被檢舉的原文照原樣顯示，換行也保留
  &__content {
    margin: 0;
    padding: $spacing-md;
    border-radius: $btn-radius-std;
    background: $neutral-200;
    font-size: $p-sm-size;
    line-height: 2;
    color: $neutral-800;
    white-space: pre-wrap;
  }

  &__metrics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-sm + $spacing-xs;
    margin-top: $spacing-md;
  }

  &__metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: $spacing-xs;
    padding: $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
  }

  &__metric-value {
    font-size: $h6-size;
    font-weight: $heading-weight;
    color: $neutral-800;
    font-variant-numeric: tabular-nums;
  }

  &__metric-label {
    font-size: $label-xxs-size;
    color: $neutral-500;
  }

  &__muted {
    margin: 0;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-400;
  }

  // 其他人也檢舉了同一則內容時，補在原因底下的一行摘要
  &__others {
    display: block;
    margin-top: $spacing-xs;
    font-size: $p-sm-size;
    color: $neutral-600;
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

  // 動作列沒有說明文字時，靠這個把按鈕推到右邊 ——
  // 平常是那段說明的 flex: 1 在撐
  &__actionbar--end {
    justify-content: flex-end;
  }
}

// 內容一定會下架，那不是選項，所以做成一條唯讀的敘述而不是打勾的格子
.uphold {
  margin-bottom: $spacing-md;
  padding: $spacing-sm + $spacing-xs $spacing-md;
  border-radius: $btn-radius-std;
  background: $neutral-200;

  &__term {
    display: block;
    font-size: $p-xs-size;
    color: $neutral-500;
  }

  &__value {
    display: block;
    margin-top: $spacing-xs;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }
}

.choice {
  display: flex;
  flex-direction: column;
  gap: $spacing-sm;

  &__item {
    display: flex;
    align-items: flex-start;
    gap: $spacing-sm + $spacing-xs;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    cursor: pointer;

    &:hover {
      border-color: $primary;
    }

    &--on {
      border-color: $primary;
      background: $primary-100;
    }
  }

  &__input {
    margin: 3px 0 0;
    accent-color: $primary;
    flex-shrink: 0;
  }

  &__body {
    display: block;
  }

  &__title {
    display: block;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__desc {
    display: block;
    margin-top: $spacing-xs;
    font-size: $p-xs-size;
    line-height: 1.7;
    color: $neutral-600;
  }
}

.modal {
  &__text {
    margin: 0 0 $spacing-md;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-600;
  }

  &__warn {
    margin: 0 0 $spacing-md;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border-radius: $btn-radius-std;
    background: $neutral-200;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-800;
  }

  &__actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
    margin-top: $spacing-lg;
  }
}

.form {
  &__field {
    display: block;
    margin-bottom: $spacing-md;

    &--plain {
      border: 0;
      margin-inline: 0;
      padding: 0;
    }

    &:last-of-type {
      margin-bottom: 0;
    }
  }

  &__label {
    display: block;
    margin-bottom: $spacing-sm;
    padding: 0;
    font-size: $p-xs-size;
    color: $neutral-600;
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

  &__input {
    width: 100%;
    box-sizing: border-box;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std + 1px;
    background: $neutral-100;
    font-family: inherit;
    font-size: $p-sm-size;
    line-height: 1.8;
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
    }
  }

  &__hint {
    display: block;
    margin-top: $spacing-sm;
    font-size: $label-xxs-size;
    color: $neutral-400;
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
</style>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { REPORT_STATUS, REPORT_ACTION, REPORT_TARGET } from '@/data/adminReports.js'
import { MEMBER_STATUS, punishmentsOf } from '@/data/adminMembers.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AdminResultBar from '@/components/admin/AdminResultBar.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()
const adminReportsStore = useAdminReportsStore()
const adminMembersStore = useAdminMembersStore()

const report = computed(() => adminReportsStore.getReport(route.params.id))
const isPending = computed(() => report.value?.status === REPORT_STATUS.pending)
const isUpheld = computed(() => report.value?.status === REPORT_STATUS.upheld)

const reporter = computed(() => adminMembersStore.getMember(report.value?.reporterId))
const reported = computed(() => adminMembersStore.getMember(report.value?.reportedUserId))

const reportedName = computed(() => reported.value?.nickname ?? report.value?.reportedUserId)
const reporterName = computed(() => reporter.value?.nickname ?? report.value?.reporterId)

const isReportedSuspended = computed(() => reported.value?.status === MEMBER_STATUS.suspended)
const punishmentCount = computed(() => (reported.value ? punishmentsOf(reported.value).length : 0))

// 這個人過去被判成立過幾次。判斷處分輕重要看前科，所以這個數字要在畫面上。
const upheldAgainst = computed(() =>
  report.value
    ? adminReportsStore
        .reportsAgainst(report.value.reportedUserId)
        .filter((item) => item.id !== report.value.id && item.status === REPORT_STATUS.upheld).length
    : 0,
)

const targetWord = computed(() =>
  report.value?.targetType === REPORT_TARGET.message ? '留言' : '心得',
)

// 檢舉成立一定會下架那則內容，所以這裡選的只有「帳號要不要罰」。
// 第一個選項存進去仍然是「刪除內容」—— action_taken 記的是最重的那個處置，
// 沒有加罰帳號時，最重的就是下架內容。
//
// 已停權的人不用再罰一次，那兩個選項直接不出現（不做成灰掉的，
// 那樣要讓管理員自己猜為什麼不能選）。
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

// 處理完不跳走，留在原地把結果顯示出來。剛處理完的那一次要讓螢幕閱讀器念出來，
// 所以只有這一次才掛 role="status"
const justHandled = ref(false)

const isUpholdOpen = ref(false)
const action = ref('')
const upholdNotes = ref('')
const upholdTried = ref(false)

const canUphold = computed(() => Boolean(action.value) && upholdNotes.value.trim().length > 0)

// 送出鈕跟著選到的處分變色。只有停權會把人擋在外面，所以只有它是紅的 ——
// 內容下架每一種都會做，拿它當紅色的理由等於三顆都紅，紅色就沒有意思了
const confirmTone = computed(() =>
  action.value === REPORT_ACTION.suspendUser ? 'danger' : 'primary',
)

function openUphold() {
  // 已停權的人只剩一種可能，沒有東西要選，直接幫他填好
  action.value = isReportedSuspended.value ? REPORT_ACTION.removeContent : ''
  upholdNotes.value = ''
  upholdTried.value = false
  isUpholdOpen.value = true
}

function handleUphold() {
  upholdTried.value = true
  if (!canUphold.value) return

  adminReportsStore.resolve(report.value.id, {
    status: REPORT_STATUS.upheld,
    actionTaken: action.value,
    notes: upholdNotes.value.trim(),
  })

  isUpholdOpen.value = false
  justHandled.value = true
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const isDismissOpen = ref(false)
const dismissNotes = ref('')

function openDismiss() {
  dismissNotes.value = ''
  isDismissOpen.value = true
}

function handleDismiss() {
  adminReportsStore.resolve(report.value.id, {
    status: REPORT_STATUS.dismissed,
    actionTaken: REPORT_ACTION.dismiss,
    notes: dismissNotes.value.trim(),
  })

  isDismissOpen.value = false
  justHandled.value = true
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const isReopenOpen = ref(false)

// 不成立的檢舉沒有開出任何處分，退回去不會影響到誰，就不用先問一次。
// 要確認的是「會撤銷掉一筆處分」這件事，沒有處分就沒有要確認的。
function askReopen() {
  if (isUpheld.value) {
    isReopenOpen.value = true
    return
  }
  handleReopen()
}

function handleReopen() {
  adminReportsStore.reopen(report.value.id)
  isReopenOpen.value = false
  justHandled.value = false
}
</script>

<template>
  <div class="admin-page report">
    <template v-if="report">
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
        :meta="`${report.resolvedAt} · 處理人 ${report.staffAccount}`"
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
              <span class="report__value">{{ report.reason }}</span>
            </div>
            <div class="report__item">
              <span class="report__term">檢舉人</span>
              <span class="report__value">
                <RouterLink :to="`/admin/members/${report.reporterId}`" class="report__inlink">
                  {{ reporterName }}
                </RouterLink>
                <span class="report__code">{{ report.reporterId }}</span>
              </span>
            </div>
            <div class="report__item">
              <span class="report__term">檢舉時間</span>
              <span class="report__value">{{ report.createdAt }}</span>
            </div>
            <div class="report__item report__item--block">
              <span class="report__term">補充說明</span>
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
              <AdminButton variant="outline" size="xs" :to="`/admin/members/${report.reportedUserId}`">
                檢視會員
              </AdminButton>
            </template>

            <div class="report__list">
              <div class="report__item">
                <span class="report__term">暱稱</span>
                <span class="report__value">
                  {{ reportedName }}
                  <span class="report__code">{{ report.reportedUserId }}</span>
                </span>
              </div>
              <div class="report__item">
                <span class="report__term">帳號狀態</span>
                <span class="report__value">
                  <AdminStatusTag
                    :label="reported?.status ?? '查無此會員'"
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
          <RouterLink :to="`/admin/members/${report.reportedUserId}`" class="report__inlink">
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

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isUpholdOpen = false">取消</AdminButton>
          <AdminButton :tone="confirmTone" type="submit">確認執行</AdminButton>
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

      <div class="modal__actions">
        <AdminButton variant="outline" @click="isReopenOpen = false">取消</AdminButton>
        <AdminButton @click="handleReopen">確認退回</AdminButton>
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

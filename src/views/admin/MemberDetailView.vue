<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import {
  MEMBER_STATUS,
  ACTION_TYPE,
  punishmentsOf,
  openReportsOf,
  actionsOf,
  currentSuspension,
} from '@/data/adminMembers.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()
const adminMembersStore = useAdminMembersStore()

const member = computed(() => adminMembersStore.getMember(route.params.id))
const isSuspended = computed(() => member.value?.status === MEMBER_STATUS.suspended)

const ledGuilds = computed(() => member.value?.guilds.filter((guild) => guild.role === '會長') ?? [])

const punishments = computed(() => (member.value ? punishmentsOf(member.value) : []))
const openReports = computed(() => (member.value ? openReportsOf(member.value) : []))
const actions = computed(() => (member.value ? actionsOf(member.value) : []))
const suspension = computed(() => (member.value ? currentSuspension(member.value) : null))

function toneOf(type) {
  return type === ACTION_TYPE.restore ? 'lift' : 'hit'
}

const isSuspendOpen = ref(false)
const suspendReason = ref('')
const suspendTried = ref(false)

const canSuspend = computed(() => suspendReason.value.trim().length > 0)

function openSuspend() {
  suspendReason.value = ''
  suspendTried.value = false
  isSuspendOpen.value = true
}

function handleSuspend() {
  suspendTried.value = true
  if (!canSuspend.value) return

  adminMembersStore.suspend(member.value.id, suspendReason.value.trim())
  isSuspendOpen.value = false
}

const isRestoreOpen = ref(false)
const restoreReason = ref('')

function openRestore() {
  restoreReason.value = ''
  isRestoreOpen.value = true
}

function handleRestore() {
  adminMembersStore.restore(member.value.id, restoreReason.value.trim())
  isRestoreOpen.value = false
}

const isWarnOpen = ref(false)
const warnReason = ref('')
const warnTried = ref(false)

const canWarn = computed(() => warnReason.value.trim().length > 0)

function openWarn() {
  warnReason.value = ''
  warnTried.value = false
  isWarnOpen.value = true
}

function handleWarn() {
  warnTried.value = true
  if (!canWarn.value) return

  adminMembersStore.warn(member.value.id, warnReason.value.trim())
  isWarnOpen.value = false
}
</script>

<template>
  <div class="admin-page member">
    <template v-if="member">
      <header class="admin-page__head">
        <h1 class="admin-page__title">
          會員詳情
          <span class="member__divider" aria-hidden="true">｜</span>
          {{ member.nickname }}
        </h1>

        <AdminStatusTag :label="member.status" :tone="isSuspended ? 'muted' : 'solid'" />
      </header>

      <div v-if="suspension" class="member__result">
        <div class="member__result-body">
          <span class="member__result-verb">已停權</span>
          <span class="member__result-meta">{{ suspension.at }} · 處理人 {{ suspension.by }}</span>
          <p class="member__result-reason">原因：{{ suspension.reason }}</p>
        </div>

        <AdminButton variant="outline" size="xs" @click="openRestore">解除停權</AdminButton>
      </div>

      <div class="member__row">
        <AdminPanel title="基本資料">
          <div class="member__list">
            <div class="member__item">
              <span class="member__term">暱稱</span>
              <span class="member__value">{{ member.nickname }}</span>
            </div>
            <div class="member__item">
              <span class="member__term">會員編號</span>
              <span class="member__value">{{ member.id }}</span>
            </div>
            <div class="member__item">
              <span class="member__term">信箱</span>
              <span class="member__value">{{ member.email }}</span>
            </div>
            <div class="member__item">
              <span class="member__term">註冊時間</span>
              <span class="member__value">{{ member.joinedAt }}</span>
            </div>
          </div>

          <div class="member__metric">
            <span class="member__metric-value">{{ punishments.length }}</span>
            <span class="member__metric-label">累計處分次數</span>
          </div>

          <div v-if="!isSuspended" class="member__actions">
            <AdminButton variant="outline" @click="openWarn">發送警告</AdminButton>
            <AdminButton tone="danger" @click="openSuspend">停權此帳號</AdminButton>
          </div>
        </AdminPanel>

        <div class="member__col">
          <AdminPanel title="所屬公會">
            <ul v-if="member.guilds.length" class="member__guilds">
              <li v-for="guild in member.guilds" :key="guild.name" class="member__guild">
                <span class="member__guild-name">{{ guild.name }}</span>
                <AdminStatusTag :label="guild.role" />
              </li>
            </ul>

            <p v-else class="member__muted">尚未加入任何公會</p>
          </AdminPanel>

          <AdminPanel
            v-if="openReports.length"
            :title="`處理中的檢舉（${openReports.length}）`"
            sub="還沒查證，不計入處分次數"
          >
            <ul class="member__reports">
              <li v-for="report in openReports" :key="report.id" class="member__report">
                <div class="member__report-head">
                  <span class="member__report-id">檢舉單 {{ report.id }}</span>
                  <span class="member__report-meta">
                    {{ report.targetType }}檢舉 · {{ report.reason }} · {{ report.createdAt }}
                  </span>
                </div>

                <p class="member__report-excerpt">「{{ report.excerpt }}」</p>

                <AdminStatusTag :label="report.action" tone="solid" />
              </li>
            </ul>
          </AdminPanel>

          <AdminPanel :title="`處分紀錄（${actions.length}）`">
            <ol v-if="actions.length" class="member__acts">
              <li v-for="action in actions" :key="action.id" class="member__act">
                <div class="member__act-head">
                  <span class="member__act-type" :class="`member__act-type--${toneOf(action.type)}`">
                    {{ action.type }}
                  </span>
                  <span class="member__report-meta">
                    {{ action.at }} · 處理人 {{ action.by }}
                  </span>
                </div>

                <p class="member__report-excerpt">{{ action.reason || '（未填原因）' }}</p>

                <span class="member__act-src">
                  {{ action.reportId ? `來源：檢舉單 ${action.reportId}` : '管理員主動處分' }}
                </span>
              </li>
            </ol>

            <p v-else class="member__muted">沒有任何處分紀錄</p>
          </AdminPanel>
        </div>
      </div>
    </template>

    <template v-else>
      <header class="admin-page__head">
        <h1 class="admin-page__title">找不到這位會員</h1>
      </header>

      <AdminPanel>
        <p class="member__muted member__notfound">網址上的會員編號不存在，可能已經被移除。</p>
        <AdminButton variant="outline" to="/admin/members">回會員列表</AdminButton>
      </AdminPanel>
    </template>

    <AppModal v-model="isWarnOpen" title="發送警告">
      <p class="modal__text">警告會通知對方，帳號仍然可以正常使用。</p>

      <form @submit.prevent="handleWarn">
        <label class="form__field" :class="{ 'form__field--error': warnTried && !canWarn }">
          <span class="form__label">警告原因（會讓對方看到）</span>
          <textarea
            v-model="warnReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：申請好書推薦時連續送出重複書單，請勿灌爆審核佇列"
          ></textarea>
          <span v-if="warnTried && !canWarn" class="form__error">請填寫警告原因</span>
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isWarnOpen = false">取消</AdminButton>
          <AdminButton type="submit">發送警告</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isSuspendOpen" title="停權此帳號">
      <p class="modal__text">停權後這位會員無法登入平台。</p>

      <p v-if="ledGuilds.length" class="modal__warn">
        這位會員是{{ ledGuilds.map((guild) => `「${guild.name}」` ).join('、') }}的會長，停權後該公會會沒有管理者。
      </p>

      <form @submit.prevent="handleSuspend">
        <label class="form__field" :class="{ 'form__field--error': suspendTried && !canSuspend }">
          <span class="form__label">停權原因（供申覆時查核）</span>
          <textarea
            v-model="suspendReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：累計被檢舉 10 次，多次人身攻擊與抄襲行為"
          ></textarea>
          <span v-if="suspendTried && !canSuspend" class="form__error">請填寫停權原因</span>
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isSuspendOpen = false">取消</AdminButton>
          <AdminButton tone="danger" type="submit">確認停權</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isRestoreOpen" title="解除停權">
      <form @submit.prevent="handleRestore">
        <label class="form__field">
          <span class="form__label">解除原因（選填）</span>
          <textarea
            v-model="restoreReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：申覆成立，經查該篇心得為本人原創"
          ></textarea>
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isRestoreOpen = false">取消</AdminButton>
          <AdminButton type="submit">確認解除</AdminButton>
        </div>
      </form>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/abstracts/variables' as *;

.member {
  &__divider {
    margin: 0 $spacing-sm;
    color: $neutral-400;
    font-weight: $text-weight;
  }

  &__result {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: $spacing-md;
    padding: $spacing-sm + $spacing-xs $spacing-md;
    border-left: 4px solid $color-danger;
    border-radius: $btn-radius-std;
    background: rgba($color-danger, 0.07);
    font-size: $p-sm-size;
    line-height: 1.6;
    color: $neutral-700;

    .admin-button {
      border-color: $neutral-400;
    }
  }

  &__result-body {
    min-width: 0;
  }

  &__result-verb {
    display: block;
    font-weight: $heading-weight;
    color: $color-danger;
  }

  &__result-meta {
    display: block;
    margin-top: $spacing-xs;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  &__result-reason {
    margin: $spacing-xs 0 0;
    color: $neutral-800;
  }

  &__row {
    display: grid;
    grid-template-columns: 360px minmax(0, 1fr);
    gap: $spacing-md;
    align-items: start;
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
  }

  &__term {
    font-size: $p-xs-size;
    color: $neutral-500;
  }

  &__value {
    font-size: $p-sm-size;
    color: $neutral-800;
    word-break: break-all;
  }

  &__metric {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: $spacing-xs;
    margin-top: $spacing-md;
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

  &__actions {
    display: flex;
    flex-direction: column;
    gap: $spacing-sm;
    margin-top: $spacing-md;

    .admin-button {
      width: 100%;
    }
  }

  &__guilds,
  &__reports {
    display: flex;
    flex-direction: column;
    gap: $spacing-sm + $spacing-xs;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  &__guild {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: $spacing-md;
    padding-bottom: $spacing-sm + $spacing-xs;
    border-bottom: 1px solid $neutral-200;

    &:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }
  }

  &__guild-name {
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__report {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: $spacing-sm;
    padding-bottom: $spacing-sm + $spacing-xs;
    border-bottom: 1px solid $neutral-200;

    &:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }
  }

  &__report-head {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: $spacing-sm;
  }

  &__report-id {
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__report-meta {
    font-size: $p-xs-size;
    color: $neutral-500;
  }

  &__report-excerpt {
    margin: 0;
    font-size: $p-sm-size;
    line-height: 1.7;
    color: $neutral-700;
  }

  &__acts {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  &__act {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: $spacing-sm;
    padding-bottom: $spacing-md;
    border-bottom: 1px solid $neutral-200;

    &:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }
  }

  &__act-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: $spacing-sm;
  }

  &__act-type {
    padding: 0 $spacing-sm;
    border: 1px solid;
    border-radius: $btn-radius-std;
    font-size: $label-xxs-size;
    font-weight: $heading-weight;
    line-height: 20px;

    &--hit {
      border-color: $color-danger;
      color: $color-danger;
    }

    &--lift {
      border-color: $primary;
      color: $primary;
    }
  }

  &__act-src {
    font-size: $label-xxs-size;
    color: $neutral-400;
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
    background: rgba($color-danger, 0.07);
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

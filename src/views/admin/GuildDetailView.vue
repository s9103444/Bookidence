<script setup>
import { ref, reactive, computed } from 'vue'
import { useRoute } from 'vue-router'
import {
  adminGuilds,
  GUILD_STATUS,
  GUILD_MODE,
  GUILD_MEMBER_ROLE,
  leaderOf,
  deputyOf,
  isOfficerRole,
  reportCountOf,
  avgAttendanceOf,
} from '@/data/adminGuilds.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminButton from '@/components/admin/AdminButton.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AdminResultBar from '@/components/admin/AdminResultBar.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AppModal from '@/components/common/AppModal.vue'

const route = useRoute()

// 目前只是把假資料複製一份存在畫面裡，之後接 API 就換成打 GET /admin/guilds/:id
const guilds = ref(structuredClone(adminGuilds))
const guild = computed(() => guilds.value.find((item) => item.id === route.params.id))

const isSuspended = computed(() => guild.value?.status === GUILD_STATUS.suspended)
const isDeleted = computed(() => guild.value?.status === GUILD_STATUS.deleted)

function now() {
  const date = new Date()
  const pad = (value) => String(value).padStart(2, '0')
  return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
}

// ---------------------------------------------------------------- 分頁 tab
const TABS = [
  { value: 'members', label: '成員' },
  { value: 'events', label: '活動與出席' },
  { value: 'messages', label: '留言檢舉紀錄' },
]
const activeTab = ref('members')

// ------------------------------------------------------------ 成員列表篩選
const ALL = '全部'
const OFFICERS = '幹部'
const REGULAR = '一般成員'

const memberRoleFilter = ref(ALL)
const memberRoleOptions = [
  { label: ALL, value: ALL },
  { label: OFFICERS, value: OFFICERS },
  { label: REGULAR, value: REGULAR },
]

const filteredMembers = computed(() => {
  if (!guild.value) return []
  if (memberRoleFilter.value === OFFICERS) {
    return guild.value.members.filter((member) => isOfficerRole(member.role))
  }
  if (memberRoleFilter.value === REGULAR) {
    return guild.value.members.filter((member) => member.role === GUILD_MEMBER_ROLE.member)
  }
  return guild.value.members
})

// ------------------------------------------------------------ 活動場次篩選
const eventModeFilter = ref(ALL)
const eventModeOptions = [
  { label: ALL, value: ALL },
  { label: GUILD_MODE.online, value: GUILD_MODE.online },
  { label: GUILD_MODE.offline, value: GUILD_MODE.offline },
]

const filteredEvents = computed(() => {
  if (!guild.value) return []
  if (eventModeFilter.value === ALL) return guild.value.events
  return guild.value.events.filter((event) => event.mode === eventModeFilter.value)
})

function attendeesFor(event) {
  return guild.value.members.map((member, index) => ({
    ...member,
    present: index < event.attendeeCount,
  }))
}

// 這個分頁只給稽核用，只列出被檢舉過的留言——一般日常留言不歸後台管
const flaggedMessages = computed(() => (guild.value ? guild.value.messages.filter((message) => message.flagged) : []))

// 被檢舉的留言先遮起來，只留一小段預覽
function displayContent(message) {
  return `${message.content.slice(0, 20)}……（內容已暫時隱藏）`
}

// -------------------------------------------------------- 編輯公會資料
const isEditOpen = ref(false)
const editTried = ref(false)
const editForm = reactive({
  name: '',
  description: '',
  currentBook: '',
  currentChapter: '',
})

const canSaveEdit = computed(() => editForm.name.trim().length > 0)

function openEdit() {
  editForm.name = guild.value.name
  editForm.description = guild.value.description
  editForm.currentBook = guild.value.currentBook ?? ''
  editForm.currentChapter = guild.value.currentChapter ?? ''
  editTried.value = false
  isEditOpen.value = true
}

function submitEdit() {
  editTried.value = true
  if (!canSaveEdit.value) return

  guild.value.name = editForm.name.trim()
  guild.value.description = editForm.description.trim()
  guild.value.currentBook = editForm.currentBook.trim() || null
  guild.value.currentChapter = guild.value.currentBook ? editForm.currentChapter.trim() : null
  isEditOpen.value = false
}

// -------------------------------------------------------- 指派會長／副會長
const isAssignOpen = ref(false)
const assignTried = ref(false)
const assignForm = reactive({ leaderId: '', deputyId: '' })

const canAssign = computed(
  () => Boolean(assignForm.leaderId) && (!assignForm.deputyId || assignForm.deputyId !== assignForm.leaderId),
)

function openAssign() {
  assignForm.leaderId = leaderOf(guild.value)?.id ?? ''
  assignForm.deputyId = deputyOf(guild.value)?.id ?? ''
  assignTried.value = false
  isAssignOpen.value = true
}

function submitAssign() {
  assignTried.value = true
  if (!canAssign.value) return

  guild.value.members.forEach((member) => {
    if (member.id === assignForm.leaderId) member.role = GUILD_MEMBER_ROLE.leader
    else if (assignForm.deputyId && member.id === assignForm.deputyId) member.role = GUILD_MEMBER_ROLE.deputy
    else member.role = GUILD_MEMBER_ROLE.member
  })
  isAssignOpen.value = false
}

// -------------------------------------------------------- 停權／解除停權
const isSuspendOpen = ref(false)
const suspendReason = ref('')
const suspendTried = ref(false)
const canSuspend = computed(() => suspendReason.value.trim().length > 0)

function openSuspend() {
  suspendReason.value = ''
  suspendTried.value = false
  isSuspendOpen.value = true
}

function submitSuspend() {
  suspendTried.value = true
  if (!canSuspend.value) return

  guild.value.status = GUILD_STATUS.suspended
  guild.value.suspendedAt = now()
  guild.value.suspendedBy = '書芸'
  guild.value.suspendReason = suspendReason.value.trim()
  isSuspendOpen.value = false
}

const isRestoreOpen = ref(false)

function submitRestore() {
  guild.value.status = GUILD_STATUS.active
  guild.value.suspendedAt = null
  guild.value.suspendedBy = null
  guild.value.suspendReason = null
  isRestoreOpen.value = false
}

// -------------------------------------------------------------- 刪除公會
const isDeleteOpen = ref(false)
const deleteReason = ref('')
const deleteTried = ref(false)
const canDelete = computed(() => deleteReason.value.trim().length > 0)

function openDelete() {
  deleteReason.value = ''
  deleteTried.value = false
  isDeleteOpen.value = true
}

function submitDelete() {
  deleteTried.value = true
  if (!canDelete.value) return

  guild.value.status = GUILD_STATUS.deleted
  guild.value.deletedAt = now()
  guild.value.deletedBy = '書芸'
  guild.value.deleteReason = deleteReason.value.trim()
  isDeleteOpen.value = false
}

// -------------------------------------------------------------- 檢視明細
const isAttendanceOpen = ref(false)
const attendanceEvent = ref(null)

function openAttendance(event) {
  attendanceEvent.value = event
  isAttendanceOpen.value = true
}
</script>

<template>
  <div class="admin-page guild">
    <template v-if="guild">
      <header class="admin-page__head">
        <h1 class="admin-page__title">
          公會詳情
          <span class="guild__divider" aria-hidden="true">｜</span>
          {{ guild.name }}
        </h1>

        <AdminStatusTag :label="guild.status" :tone="guild.status === GUILD_STATUS.active ? 'solid' : 'muted'" />
      </header>

      <AdminResultBar
        v-if="isSuspended"
        tone="danger"
        label="已停權"
        :meta="`${guild.suspendedAt} · 處理人 ${guild.suspendedBy}`"
        :detail="`原因：${guild.suspendReason}`"
      >
        <span class="guild__resultbar-actions">
          <AdminButton variant="outline" size="xs" @click="isRestoreOpen = true">解除停權</AdminButton>
          <AdminButton variant="outline" tone="danger" size="xs" @click="openDelete">刪除公會</AdminButton>
        </span>
      </AdminResultBar>

      <AdminResultBar
        v-else-if="isDeleted"
        tone="muted"
        label="已刪除"
        :meta="`${guild.deletedAt} · 處理人 ${guild.deletedBy}`"
        :detail="`原因：${guild.deleteReason}`"
      />

      <AdminPanel class="guild__summary">
        <div class="guild__summary-main">
          <div class="guild__avatar" aria-hidden="true">{{ guild.name.charAt(0) }}</div>

          <div class="guild__summary-body">
            <div class="guild__summary-head">
              <h2 class="guild__name">{{ guild.name }}</h2>
            </div>

            <p class="guild__meta">
              {{ guild.description
              }}<template v-if="guild.currentBook">，目前書目《{{ guild.currentBook }}》讀至{{ guild.currentChapter }}</template>
              ・ 公會編號 {{ guild.id }} ・ 建立於 {{ guild.createdAt }}
            </p>

            <div class="guild__stats">
              <div class="guild__stat">
                <span class="guild__stat-value">{{ guild.members.length }}</span>
                <span class="guild__stat-label">成員</span>
              </div>
              <div class="guild__stat">
                <span class="guild__stat-value">{{ guild.events.length }}</span>
                <span class="guild__stat-label">累計活動場次</span>
              </div>
              <div class="guild__stat">
                <span class="guild__stat-value">{{ avgAttendanceOf(guild) }}%</span>
                <span class="guild__stat-label">近月平均出席率</span>
              </div>
              <div class="guild__stat">
                <span class="guild__stat-value">{{ guild.completedBooksCount }}</span>
                <span class="guild__stat-label">已完讀書目</span>
              </div>
              <div class="guild__stat">
                <span class="guild__stat-value">{{ reportCountOf(guild) }}</span>
                <span class="guild__stat-label">檢舉紀錄</span>
              </div>
            </div>
          </div>

          <div v-if="!isDeleted" class="guild__actions">
            <AdminButton variant="outline" @click="openEdit">編輯公會資料</AdminButton>
            <AdminButton variant="outline" @click="openAssign">指派會長／副會長</AdminButton>
            <AdminButton v-if="!isSuspended" tone="danger" @click="openSuspend">停權違規公會</AdminButton>
          </div>
        </div>
      </AdminPanel>

      <nav class="guild__tabs" aria-label="公會詳情分頁">
        <button
          v-for="tab in TABS"
          :key="tab.value"
          type="button"
          class="guild__tab"
          :class="{ 'guild__tab--active': activeTab === tab.value }"
          :aria-pressed="activeTab === tab.value"
          @click="activeTab = tab.value"
        >
          {{ tab.label }}<template v-if="tab.value === 'members'">（{{ guild.members.length }}）</template>
        </button>
      </nav>

      <AdminPanel flush>
        <template v-if="activeTab === 'members'">
          <div class="guild__panel-head">
            <h2 class="guild__panel-title">成員列表</h2>
            <AdminFilterTabs v-model="memberRoleFilter" :options="memberRoleOptions" />
          </div>

          <div class="table-scroll">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">暱稱</th>
                  <th scope="col">角色</th>
                  <th scope="col">加入日期</th>
                  <th scope="col">出席率</th>
                  <th scope="col">發言數</th>
                  <th scope="col">操作</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="member in filteredMembers" :key="member.id">
                  <td class="data-table__key">
                    <span class="guild__member-name">
                      {{ member.nickname }}
                      <AdminStatusTag v-if="member.flagged" label="有檢舉紀錄" tone="muted" />
                    </span>
                  </td>
                  <td>
                    <AdminStatusTag :label="member.role" :tone="isOfficerRole(member.role) ? 'solid' : 'outline'" />
                  </td>
                  <td class="data-table__muted">{{ member.joinedAt }}</td>
                  <td>{{ member.attendanceRate }}%</td>
                  <td>{{ member.messageCount }}</td>
                  <td>
                    <span class="data-table__ops">
                      <RouterLink :to="`/admin/members/${member.id}`" class="data-table__op">檢視會員</RouterLink>
                    </span>
                  </td>
                </tr>

                <tr v-if="filteredMembers.length === 0">
                  <td colspan="6"><p class="data-table__empty">這個篩選條件底下沒有成員</p></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="guild__panel-foot">
            <AdminNotice>管理員僅能檢視成員與指派幹部；成員的移除／退出由前台圈內機制處理，後台不代為操作。</AdminNotice>
          </div>
        </template>

        <template v-else-if="activeTab === 'events'">
          <div class="guild__panel-head">
            <h2 class="guild__panel-title">活動場次與出席紀錄</h2>
            <AdminFilterTabs v-model="eventModeFilter" :options="eventModeOptions" />
          </div>

          <div class="table-scroll">
            <table class="data-table">
              <thead>
                <tr>
                  <th scope="col">場次</th>
                  <th scope="col">形式</th>
                  <th scope="col">時間</th>
                  <th scope="col">活動名稱</th>
                  <th scope="col">出席</th>
                  <th scope="col">操作</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="event in filteredEvents" :key="event.id">
                  <td class="data-table__key">#{{ event.no }}</td>
                  <td><AdminStatusTag :label="event.mode" /></td>
                  <td class="data-table__muted">{{ event.time }}</td>
                  <td>{{ event.title }}</td>
                  <td>{{ event.attendeeCount }} / {{ event.capacity }}</td>
                  <td>
                    <span class="data-table__ops">
                      <button type="button" class="data-table__op" @click="openAttendance(event)">出席明細</button>
                    </span>
                  </td>
                </tr>

                <tr v-if="filteredEvents.length === 0">
                  <td colspan="6"><p class="data-table__empty">這個篩選條件底下沒有活動</p></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="guild__panel-foot">
            <AdminNotice>出席紀錄為經驗值發放依據；文字圈日常留言屬一般行為，不列入活動場次。</AdminNotice>
          </div>
        </template>

        <template v-else>
          <div class="guild__panel-head">
            <h2 class="guild__panel-title">留言檢舉紀錄</h2>
          </div>

          <ul class="guild__messages">
            <li v-for="message in flaggedMessages" :key="message.id" class="guild__message">
              <div class="guild__message-avatar" aria-hidden="true"></div>

              <div class="guild__message-body">
                <p class="guild__message-meta">
                  {{ message.authorNickname
                  }}<template v-if="message.authorRole">（{{ message.authorRole }}）</template>
                  ・ {{ message.thread }} ・ {{ message.time }}
                </p>
                <p class="guild__message-content">「{{ displayContent(message) }}」</p>
              </div>

              <RouterLink :to="`/admin/reports/${message.reportId}`" class="data-table__link guild__message-action">
                前往檢舉案 {{ message.reportId }}
              </RouterLink>
            </li>

            <li v-if="flaggedMessages.length === 0" class="data-table__empty">這個公會目前沒有被檢舉過的留言</li>
          </ul>

          <div class="guild__panel-foot">
            <AdminNotice>管理員檢視討論僅供檢舉稽核與異常查核；一般狀況下不介入圈內討論。</AdminNotice>
          </div>
        </template>
      </AdminPanel>
    </template>

    <template v-else>
      <header class="admin-page__head">
        <h1 class="admin-page__title">找不到這個公會</h1>
      </header>

      <AdminPanel>
        <p class="guild__muted">網址上的公會編號不存在，可能已經被移除。</p>
        <AdminButton variant="outline" to="/admin/guilds">回公會列表</AdminButton>
      </AdminPanel>
    </template>

    <AppModal v-model="isEditOpen" title="編輯公會資料">
      <form @submit.prevent="submitEdit">
        <label class="form__field" :class="{ 'form__field--error': editTried && !editForm.name.trim() }">
          <span class="form__label">公會名稱</span>
          <input v-model="editForm.name" type="text" class="form__input" maxlength="30" />
          <span v-if="editTried && !editForm.name.trim()" class="form__error">請填寫公會名稱</span>
        </label>

        <label class="form__field">
          <span class="form__label">公會簡介</span>
          <textarea v-model="editForm.description" class="form__input form__input--area" rows="2" maxlength="100"></textarea>
        </label>

        <label class="form__field">
          <span class="form__label">目前書目（留空代表沒有進行中的書目）</span>
          <input v-model="editForm.currentBook" type="text" class="form__input" maxlength="60" />
        </label>

        <label v-if="editForm.currentBook" class="form__field">
          <span class="form__label">目前進度</span>
          <input v-model="editForm.currentChapter" type="text" class="form__input" maxlength="20" placeholder="例：第 8 章" />
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isEditOpen = false">取消</AdminButton>
          <AdminButton type="submit">儲存修改</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isAssignOpen" title="指派會長／副會長">
      <p class="modal__text">選一位成員擔任會長，副會長可留空。原本掛職但這次沒被選上的人會退回一般成員。</p>

      <form @submit.prevent="submitAssign">
        <label class="form__field" :class="{ 'form__field--error': assignTried && !assignForm.leaderId }">
          <span class="form__label" id="assign-leader-label">會長</span>
          <select v-model="assignForm.leaderId" class="form__input" aria-labelledby="assign-leader-label">
            <option value="">請選擇</option>
            <option v-for="member in guild?.members ?? []" :key="member.id" :value="member.id">
              {{ member.nickname }}
            </option>
          </select>
          <span v-if="assignTried && !assignForm.leaderId" class="form__error">請選擇會長</span>
        </label>

        <label
          class="form__field"
          :class="{
            'form__field--error': assignTried && assignForm.deputyId && assignForm.deputyId === assignForm.leaderId,
          }"
        >
          <span class="form__label" id="assign-deputy-label">副會長（選填）</span>
          <select v-model="assignForm.deputyId" class="form__input" aria-labelledby="assign-deputy-label">
            <option value="">不指派</option>
            <option v-for="member in guild?.members ?? []" :key="member.id" :value="member.id">
              {{ member.nickname }}
            </option>
          </select>
          <span
            v-if="assignTried && assignForm.deputyId && assignForm.deputyId === assignForm.leaderId"
            class="form__error"
          >
            副會長不能跟會長是同一人
          </span>
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isAssignOpen = false">取消</AdminButton>
          <AdminButton type="submit">確認指派</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isSuspendOpen" title="停權違規公會">
      <p class="modal__text">停權後這個公會會從前台的公會列表下架，現有成員仍看得到既有紀錄，但無法繼續進行活動。</p>

      <form @submit.prevent="submitSuspend">
        <label class="form__field" :class="{ 'form__field--error': suspendTried && !canSuspend }">
          <span class="form__label">停權原因（供申覆時查核）</span>
          <textarea
            v-model="suspendReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：會長多次於留言區與其他讀者發生爭執，警告後仍未改善"
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
      <p class="modal__text">解除後這個公會會恢復正常，重新出現在前台的公會列表。</p>

      <div class="modal__actions">
        <AdminButton variant="outline" @click="isRestoreOpen = false">取消</AdminButton>
        <AdminButton @click="submitRestore">確認解除</AdminButton>
      </div>
    </AppModal>

    <AppModal v-model="isDeleteOpen" title="刪除違規公會">
      <p class="modal__text">刪除後這個公會會永久關閉，所有成員都會被移出公會，此動作無法復原。</p>

      <form @submit.prevent="submitDelete">
        <label class="form__field" :class="{ 'form__field--error': deleteTried && !canDelete }">
          <span class="form__label">刪除原因（供稽核查核）</span>
          <textarea
            v-model="deleteReason"
            class="form__input form__input--area"
            rows="3"
            maxlength="500"
            placeholder="例：累計多次違規停權，申覆逾期未回覆"
          ></textarea>
          <span v-if="deleteTried && !canDelete" class="form__error">請填寫刪除原因</span>
        </label>

        <div class="modal__actions">
          <AdminButton variant="outline" @click="isDeleteOpen = false">取消</AdminButton>
          <AdminButton tone="danger" type="submit">確認刪除</AdminButton>
        </div>
      </form>
    </AppModal>

    <AppModal v-model="isAttendanceOpen" :title="attendanceEvent ? `出席明細・第 ${attendanceEvent.no} 場` : '出席明細'">
      <ul v-if="attendanceEvent" class="guild__attendance">
        <li v-for="entry in attendeesFor(attendanceEvent)" :key="entry.id" class="guild__attendance-row">
          <span>{{ entry.nickname }}</span>
          <AdminStatusTag :label="entry.present ? '已出席' : '未出席'" :tone="entry.present ? 'solid' : 'muted'" />
        </li>
      </ul>
    </AppModal>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.guild {
  &__divider {
    margin: 0 $spacing-sm;
    color: $neutral-400;
    font-weight: $text-weight;
  }

  &__resultbar-actions {
    display: inline-flex;
    gap: $spacing-sm;
  }

  &__summary-main {
    display: flex;
    gap: $spacing-lg;
  }

  &__avatar {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: $btn-radius-std + 3px;
    background: $primary-100;
    color: $primary;
    font-size: $h6-size;
    font-weight: $heading-weight;
  }

  &__summary-body {
    flex: 1;
    min-width: 0;
  }

  &__summary-head {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: $spacing-sm;
  }

  &__name {
    margin: 0;
    font-size: $h6-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__meta {
    margin: $spacing-sm 0 0;
    font-size: $p-xs-size;
    line-height: 1.7;
    color: $neutral-500;
  }

  &__stats {
    display: flex;
    flex-wrap: wrap;
    gap: $spacing-lg;
    margin-top: $spacing-md;
  }

  &__stat {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
  }

  &__stat-value {
    font-size: $h6-size;
    font-weight: $heading-weight;
    color: $neutral-800;
    font-variant-numeric: tabular-nums;
  }

  &__stat-label {
    font-size: $label-xxs-size;
    color: $neutral-500;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    gap: $spacing-sm;
    width: 168px;

    .admin-button {
      width: 100%;
    }
  }

  &__tabs {
    display: flex;
    gap: $spacing-lg;
    border-bottom: 1px solid $neutral-300;
  }

  &__tab {
    padding: $spacing-sm + $spacing-xs 0;
    border: 0;
    border-bottom: 2px solid transparent;
    background: none;
    font-family: inherit;
    font-size: $p-sm-size;
    color: $neutral-500;
    cursor: pointer;

    &:hover {
      color: $primary;
    }

    &--active {
      border-bottom-color: $primary;
      color: $primary;
      font-weight: $heading-weight;
    }
  }

  &__panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: $spacing-md;
    padding: $spacing-md $spacing-lg;
  }

  &__panel-title {
    margin: 0;
    font-size: $p-sm-size;
    font-weight: $heading-weight;
    color: $neutral-800;
    letter-spacing: 0.05em;
  }

  &__panel-foot {
    padding: $spacing-md $spacing-lg $spacing-lg;
  }

  &__member-name {
    display: inline-flex;
    align-items: center;
    gap: $spacing-sm;
  }

  &__messages {
    display: flex;
    flex-direction: column;
    gap: 0;
    margin: 0;
    padding: 0 $spacing-lg;
    list-style: none;
  }

  &__message {
    display: flex;
    align-items: flex-start;
    gap: $spacing-md;
    padding: $spacing-md 0;
    border-bottom: 1px solid $neutral-200;

    &:last-child {
      border-bottom: 0;
    }
  }

  &__message-avatar {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    border-radius: $btn-radius-rnd;
    background: $neutral-300;
  }

  &__message-body {
    flex: 1;
    min-width: 0;
  }

  &__message-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: $spacing-sm;
    margin: 0;
    font-size: $p-xs-size;
    color: $neutral-500;
  }

  &__message-content {
    margin: $spacing-xs 0 0;
    font-size: $p-sm-size;
    line-height: 1.7;
    color: $neutral-800;
  }

  &__message-action {
    flex-shrink: 0;
  }

  &__attendance {
    display: flex;
    flex-direction: column;
    gap: $spacing-sm + $spacing-xs;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  &__attendance-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: $spacing-md;
    padding-bottom: $spacing-sm + $spacing-xs;
    border-bottom: 1px solid $neutral-200;
    font-size: $p-sm-size;
    color: $neutral-800;

    &:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }
  }

  &__muted {
    margin: 0 0 $spacing-md;
    font-size: $p-xs-size;
    line-height: 1.8;
    color: $neutral-400;
  }
}

.form {
  &__field {
    display: block;
    margin-bottom: $spacing-md;

    &:last-of-type {
      margin-bottom: 0;
    }
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

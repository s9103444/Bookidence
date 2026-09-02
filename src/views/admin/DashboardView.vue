<script setup>
import { ref, computed, onMounted } from 'vue'
import { BOOK_STATUS } from '@/data/adminBooks.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import { adminApi } from '@/common/adminApi.js'

const adminMembersStore = useAdminMembersStore()
const adminReportsStore = useAdminReportsStore()

const stats = {
  // ⚠️ book 表沒有「加入書庫的時間」，只有出版日期，所以這個數字算不出來
  newBooksThisMonth: 4,
}

const totalMembers = ref(0)
const newMembersThisWeek = ref(0)

// 近七天每天的註冊人數，最舊的排前面（長條圖由左往右）
const signups = ref([])

// 把會員的註冊時間攤成「近七天各有幾人」。日期比對只取到日，
// 時分秒不管 —— 圖上一根柱子就是一天
function buildSignups(members) {
  const days = []

  for (let i = 6; i >= 0; i--) {
    const d = new Date()
    d.setDate(d.getDate() - i)
    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
    days.push({ key, date: `${d.getMonth() + 1}/${d.getDate()}`, count: 0 })
  }

  for (const m of members) {
    const key = (m.created_at ?? '').slice(0, 10)
    const hit = days.find((day) => day.key === key)
    if (hit) hit.count++
  }

  return days
}

const pendingReportCount = computed(() => adminReportsStore.pendingCount)

// 只列最新五筆，剩下的用「查看全部」帶過
const latestReports = ref([])

const pendingCount = ref(0)

// 只要總筆數，所以借用書籍列表 API 篩「已上架」，讀它回傳的 total
const publishedCount = ref(0)

// 只列最近四筆，剩下的用「另有 N 筆」帶過
const pendingBooks = ref([])

onMounted(async () => {
  try {
    const res = await adminApi.get('/admin_members.php')
    const members = res.data.member ?? []

    totalMembers.value = members.length
    signups.value = buildSignups(members)
    newMembersThisWeek.value = signups.value.reduce((sum, day) => sum + day.count, 0)
  } catch (e) {
    console.error('[總覽：會員數]', e)
  }

  try {
    const params = new URLSearchParams({ status: BOOK_STATUS.listed })
    const res = await adminApi.get(`/admin_books.php?${params}`)
    publishedCount.value = res.data.total
  } catch (e) {
    console.error('[總覽：上架書籍數]', e)
  }

  try {
    const res = await adminApi.get('/admin_reports.php?status=尚未處理')

    adminReportsStore.setPendingCount(res.data.counts['尚未處理'])

    latestReports.value = res.data.data.slice(0, 5).map((row) => ({
      id: row.report_id,
      reportedName: row.reported_name,
      targetType: row.target_type,
      reason: row.reason,
      createdAt: row.created_at.slice(0, 16),
    }))
  } catch (e) {
    console.error('[總覽：待處理檢舉]', e)
  }

  try {
    const res = await adminApi.get('/admin_applications.php?status=待處理')
    pendingCount.value = res.data.total
    pendingBooks.value = res.data.data.slice(0, 4).map((row) => ({
      id: row.book_ap_id,
      title: row.ap_title,
      author: row.ap_author,
      applicant: row.nickname,
      appliedAt: row.created_at,
    }))
  } catch (e) {
    console.error('[總覽：待審申請]', e)
  }
})

// 長條圖是純 CSS 畫的，沒有裝任何圖表套件。
// 最高的那天固定佔 120px，其他天按比例縮。
// 沒有寫成 100% 是因為柱子上方還要放數字，寫滿的話數字會被擠出去。
const BAR_MAX_HEIGHT = 120
// 至少當成 1，否則資料還沒回來（空陣列）時 Math.max() 會是 -Infinity，
// 全部都是 0 的時候也會除以零，兩種都會讓柱子的高度變成 NaN
const maxCount = computed(() => Math.max(1, ...signups.value.map((day) => day.count)))

function barHeight(count) {
  return `${(count / maxCount.value) * BAR_MAX_HEIGHT}px`
}
</script>

<template>
  <div class="dashboard">
    <header class="dashboard__head">
      <h1 class="dashboard__title">總覽</h1>
      <p class="dashboard__summary">
        今天有 {{ pendingCount }} 本書等待審核、{{ pendingReportCount }} 筆檢舉待處理
      </p>
    </header>

    <ul class="stat-list">
      <li class="stat stat--action">
        <span class="stat__label"><span class="stat__dot" aria-hidden="true"></span>待審書籍</span>
        <span class="stat__value">{{ pendingCount }}</span>
        <RouterLink to="/admin/books/applications" class="stat__link">前往書籍管理 ›</RouterLink>
      </li>
      <li class="stat stat--action">
        <span class="stat__label"><span class="stat__dot" aria-hidden="true"></span>待處理檢舉</span>
        <span class="stat__value">{{ pendingReportCount }}</span>
        <RouterLink to="/admin/reports" class="stat__link">前往檢舉管理 ›</RouterLink>
      </li>
      <li class="stat">
        <span class="stat__label">總會員數</span>
        <span class="stat__value">{{ totalMembers.toLocaleString() }}</span>
        <span class="stat__foot">本週新增 {{ newMembersThisWeek }} 人</span>
      </li>
      <li class="stat">
        <span class="stat__label">已上架書籍</span>
        <span class="stat__value">{{ publishedCount }}</span>
        <span class="stat__foot">本月新增 {{ stats.newBooksThisMonth }} 本</span>
      </li>
    </ul>

    <div class="dashboard__row">
      <AdminPanel title="待審書籍" sub="會員申請新增之書籍，審核通過後正式上架">
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">書名</th>
                <th scope="col">申請人</th>
                <th scope="col">申請時間</th>
                <th scope="col">處理</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="book in pendingBooks" :key="book.id">
                <td class="data-table__key">《{{ book.title }}》</td>
                <td>{{ book.applicant }}</td>
                <td class="data-table__muted">{{ book.appliedAt }}</td>
                <td>
                  <span class="data-table__ops">
                    <RouterLink
                      :to="`/admin/books/applications/${book.id}`"
                      class="data-table__op"
                    >
                      審核
                    </RouterLink>
                  </span>
                </td>
              </tr>
              <tr v-if="!pendingBooks.length">
                <td colspan="4"><p class="data-table__empty">目前沒有待審書籍</p></td>
              </tr>
            </tbody>
            <tfoot v-if="pendingBooks.length">
              <tr>
                <td class="panel-more" colspan="4">
                  <RouterLink to="/admin/books/applications" class="data-table__link">
                    查看全部 ›
                  </RouterLink>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </AdminPanel>

      <AdminPanel title="最新檢舉" sub="待處理中最近送出的五筆">
        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th scope="col">被檢舉人</th>
                <th scope="col">類型</th>
                <th scope="col">原因</th>
                <th scope="col">時間</th>
                <th scope="col">處理</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="report in latestReports" :key="report.id">
                <td class="data-table__key">{{ report.reportedName }}</td>
                <td><AdminStatusTag :label="report.targetType" /></td>
                <td>{{ report.reason }}</td>
                <td class="data-table__muted">{{ report.createdAt }}</td>
                <td>
                  <span class="data-table__ops">
                    <RouterLink :to="`/admin/reports/${report.id}`" class="data-table__op">
                      審閱
                    </RouterLink>
                  </span>
                </td>
              </tr>
              <tr v-if="!latestReports.length">
                <td colspan="5"><p class="data-table__empty">目前沒有待處理的檢舉</p></td>
              </tr>
            </tbody>
            <tfoot v-if="latestReports.length">
              <tr>
                <td class="panel-more" colspan="5">
                  <RouterLink to="/admin/reports" class="data-table__link">查看全部 ›</RouterLink>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </AdminPanel>
    </div>

    <AdminPanel title="新增會員趨勢" sub="近 7 日每日註冊數">
      <ul class="chart">
        <li v-for="day in signups" :key="day.date" class="chart__col">
          <span class="chart__track">
            <span class="chart__value">{{ day.count }}</span>
            <span
              class="chart__bar"
              :class="{ 'chart__bar--strong': day.count === maxCount }"
              :style="{ height: barHeight(day.count) }"
            ></span>
          </span>
          <span class="chart__date">{{ day.date }}</span>
        </li>
      </ul>
    </AdminPanel>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/abstracts/variables' as *;
@use '../../assets/scss/admin/data-table' as *;

.dashboard {
  &__head {
    margin-bottom: $spacing-md;
  }

  &__title {
    margin: 0;
    font-size: $p-lg-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__summary {
    margin: $spacing-xs 0 0;
    font-size: $p-xs-size;
    color: $neutral-400;
  }

  &__row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-sm + $spacing-xs;
    margin-bottom: $spacing-sm + $spacing-xs;
  }
}

.stat-list {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: $spacing-sm + $spacing-xs;
  margin: 0 0 ($spacing-sm + $spacing-xs);
  padding: 0;
  list-style: none;
}

.stat {
  padding: $spacing-md $spacing-md;
  background: $neutral-100;
  border: 1px solid $neutral-300;
  border-radius: 10px;

  // 需要管理員動手的兩張用深色框，跟純看數字的兩張分開
  &--action {
    border-color: $primary;
  }

  &__label {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    margin: 0;
    font-size: $p-xs-size;
    color: $neutral-600;
    letter-spacing: 0.08em;
  }

  &__dot {
    width: 6px;
    height: 6px;
    border-radius: $btn-radius-rnd;
    background: $primary;
  }

  // 標籤、數字、註腳都是 <span>，所以要自己設 display: block 才會各佔一行。
  // 少了它們會擠成一行，而且 margin 的上下留白會被忽略
  &__value {
    display: block;
    margin: $spacing-sm 0 $spacing-sm;
    font-size: $h6-size;
    font-weight: $heading-weight;
    color: $neutral-800;
  }

  &__foot {
    display: block;
    font-size: $p-xs-size;
    color: $neutral-400;
  }

  &__link {
    font-size: $p-xs-size;
    color: $primary;
    text-underline-offset: 2px;
  }
}

.panel-more {
  text-align: center;
}

.chart {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin: 0;
  padding: 0;
  list-style: none;

  &__col {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  // 每一格的軌道都是滿寬，彼此貼齊，底線才會連成一條而不是七段
  &__track {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    height: 150px;
    border-bottom: 1px solid $neutral-300;
  }

  &__value {
    margin-bottom: $spacing-xs;
    font-size: $p-xs-size;
    color: $neutral-600;
  }

  // 圖表是整排滿版，不設上限的話視窗一寬柱子就胖成色塊
  &__bar {
    width: calc(100% - #{$spacing-lg});
    max-width: 88px;
    background: $neutral-300;
    border-radius: 3px 3px 0 0;

    &--strong {
      background: $primary;
    }
  }

  &__date {
    margin-top: $spacing-sm;
    font-size: $p-xs-size;
    color: $neutral-400;
  }
}
</style>

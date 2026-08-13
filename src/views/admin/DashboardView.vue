<script setup>
import { computed } from 'vue'
import { APPLICATION_STATUS, BOOK_STATUS } from '@/data/adminBooks.js'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'

const adminBooksStore = useAdminBooksStore()
const adminMembersStore = useAdminMembersStore()
const adminReportsStore = useAdminReportsStore()

const stats = {
  newMembersThisWeek: 3,
  newBooksThisMonth: 4,
}

const totalMembers = computed(() => adminMembersStore.members.length)

const pendingReportCount = computed(() => adminReportsStore.pendingCount)

// 檢舉裡存的是會員編號，畫面上要顯示暱稱，所以撈一次會員
function nicknameOf(userId) {
  return adminMembersStore.getMember(userId)?.nickname ?? userId
}

const latestReports = computed(() => adminReportsStore.pendingReports.slice(0, 5))

const pendingCount = computed(() => adminBooksStore.pendingApplicationCount)

const publishedCount = computed(
  () => adminBooksStore.books.filter((book) => book.status === BOOK_STATUS.listed).length,
)

// 只列最近四筆，剩下的用「另有 N 筆」帶過
const pendingBooks = computed(() =>
  adminBooksStore.applications
    .filter((application) => application.status === APPLICATION_STATUS.pending)
    .slice(0, 4),
)

const signups = [
  { date: '07/07', count: 4 },
  { date: '07/08', count: 7 },
  { date: '07/09', count: 3 },
  { date: '07/10', count: 6 },
  { date: '07/11', count: 5 },
  { date: '07/12', count: 11 },
  { date: '07/13', count: 13 },
]

// 長條圖是純 CSS 畫的，沒有裝任何圖表套件。
// 最高的那天固定佔 120px，其他天按比例縮。
// 沒有寫成 100% 是因為柱子上方還要放數字，寫滿的話數字會被擠出去。
const BAR_MAX_HEIGHT = 120
const maxCount = computed(() => Math.max(...signups.map((day) => day.count)))

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
        <span class="stat__foot">本週新增 {{ stats.newMembersThisWeek }} 人</span>
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
            </tbody>
            <tfoot>
              <tr>
                <td class="data-table__muted" colspan="3">
                  另有 {{ pendingCount - pendingBooks.length }} 筆待審中…
                </td>
                <td>
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
                <th scope="col">編號</th>
                <th scope="col">類型</th>
                <th scope="col">原因</th>
                <th scope="col">被檢舉人</th>
                <th scope="col">時間</th>
                <th scope="col">處理</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="report in latestReports" :key="report.id">
                <td class="data-table__key">{{ report.id }}</td>
                <td><AdminStatusTag :label="report.targetType" /></td>
                <td>{{ report.reason }}</td>
                <td>{{ nicknameOf(report.reportedUserId) }}</td>
                <td class="data-table__muted">{{ report.createdAt }}</td>
                <td>
                  <span class="data-table__ops">
                    <RouterLink :to="`/admin/reports/${report.id}`" class="data-table__op">
                      審閱
                    </RouterLink>
                  </span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td class="data-table__muted" colspan="5">
                  另有 {{ pendingReportCount - latestReports.length }} 筆待處理…
                </td>
                <td><RouterLink to="/admin/reports" class="data-table__link">查看全部 ›</RouterLink></td>
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

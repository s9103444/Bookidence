<script setup>
import { ref, computed, watch } from 'vue'
import { REPORT_STATUS, REPORT_TARGET } from '@/data/adminReports.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'
import AppIcon from '@/components/common/AppIcon.vue'

const PER_PAGE = 10
const ALL = '全部'

const adminReportsStore = useAdminReportsStore()
const adminMembersStore = useAdminMembersStore()

const status = ref(REPORT_STATUS.pending)
const targetType = ref(ALL)
const keyword = ref('')
const page = ref(1)

function nicknameOf(userId) {
  return adminMembersStore.getMember(userId)?.nickname ?? userId
}

// 待處理那一頁每一列的結果都是空的，整欄留著只是噪音，所以那一頁不畫這欄
const isPendingTab = computed(() => status.value === REPORT_STATUS.pending)

// 狀態就是 report.status 的三個值，一顆 chip 對一個值
const statusOptions = computed(() =>
  [REPORT_STATUS.pending, REPORT_STATUS.upheld, REPORT_STATUS.dismissed].map((value) => ({
    label: value,
    value,
    count: adminReportsStore.reports.filter((report) => report.status === value).length,
  })),
)

// 類型的數字算的是「目前這個狀態底下」有幾筆，不是全站總數 ——
// 點「尚未處理」時看到的心得 4 就真的是 4 筆待處理的心得檢舉
const inStatus = computed(() =>
  adminReportsStore.reports.filter((report) => report.status === status.value),
)

function countByType(value) {
  return inStatus.value.filter((report) => report.targetType === value).length
}

const typeOptions = computed(() => [
  { value: ALL, text: `所有類型（${inStatus.value.length}）` },
  {
    value: REPORT_TARGET.thought,
    text: `心得檢舉（${countByType(REPORT_TARGET.thought)}）`,
  },
  {
    value: REPORT_TARGET.message,
    text: `留言檢舉（${countByType(REPORT_TARGET.message)}）`,
  },
])

// 檢舉編號、兩邊的人、內容串成一句話再比對，打哪個都找得到
const filtered = computed(() => {
  const search = keyword.value.trim().toLowerCase()

  return inStatus.value
    .filter((report) => targetType.value === ALL || report.targetType === targetType.value)
    .filter((report) => {
      if (!search) return true
      const haystack = [
        report.id,
        nicknameOf(report.reportedUserId),
        nicknameOf(report.reporterId),
        report.reason,
        report.content,
      ].join(' ')
      return haystack.toLowerCase().includes(search)
    })
    .sort((a, b) => b.createdAt.localeCompare(a.createdAt))
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PER_PAGE)))

const pagedReports = computed(() =>
  filtered.value.slice((page.value - 1) * PER_PAGE, page.value * PER_PAGE),
)

watch([status, targetType, keyword], () => {
  page.value = 1
})

// 在最後一頁把檢舉處理掉，那一頁會變空的，所以頁碼要往回收
watch(totalPages, (value) => {
  if (page.value > value) page.value = value
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
}
</script>

<template>
  <div class="admin-page">
    <header class="admin-page__head">
      <h1 class="admin-page__title">檢舉管理</h1>
    </header>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="reports__tools">
        <span class="reports__select">
          <select v-model="targetType" class="reports__select-input" aria-label="檢舉類型">
            <option v-for="option in typeOptions" :key="option.value" :value="option.value">
              {{ option.text }}
            </option>
          </select>
          <AppIcon name="chevron-right" :size="12" class="reports__select-arrow" />
        </span>

        <div class="admin-page__search">
          <SearchBar v-model="keyword" size="sm" placeholder="搜尋檢舉編號 / 暱稱 / 內容…" />
        </div>
      </div>
    </div>

    <AdminPanel flush>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">編號</th>
              <th scope="col">類型</th>
              <th scope="col">被檢舉內容</th>
              <th scope="col">被檢舉人</th>
              <th scope="col">原因</th>
              <th scope="col">檢舉時間</th>
              <th v-if="!isPendingTab" scope="col">處理結果</th>
              <th scope="col">操作</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="report in pagedReports" :key="report.id">
              <td class="data-table__key">{{ report.id }}</td>
              <td><AdminStatusTag :label="report.targetType" /></td>

              <td>
                <span class="reports__excerpt" :title="report.content">{{ report.content }}</span>
              </td>

              <td>{{ nicknameOf(report.reportedUserId) }}</td>
              <td>{{ report.reason }}</td>
              <td class="data-table__muted">{{ report.createdAt }}</td>

              <td v-if="!isPendingTab">
                <AdminStatusTag :label="report.actionTaken" tone="muted" />
              </td>

              <td>
                <span class="data-table__ops">
                  <RouterLink :to="`/admin/reports/${report.id}`" class="data-table__op">
                    {{ report.status === REPORT_STATUS.pending ? '審閱' : '查看' }}
                  </RouterLink>
                </span>
              </td>
            </tr>

            <tr v-if="pagedReports.length === 0">
              <td :colspan="isPendingTab ? 7 : 8">
                <p class="data-table__empty">
                  {{
                    keyword.trim()
                      ? `找不到符合「${keyword.trim()}」的檢舉`
                      : `目前沒有${status}的檢舉`
                  }}
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ filtered.length }} 筆</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.reports {
  // stretch 讓下拉自己長到跟搜尋框一樣高。
  // 寫死高度的話，SearchBar 之後被改成別的內距就會歪掉
  &__tools {
    display: flex;
    align-items: stretch;
    gap: $spacing-sm + $spacing-xs;
  }

  // 類型是次要篩選，做成下拉才不會跟左邊那排狀態長得一樣
  &__select {
    position: relative;
    display: flex;
  }

  &__select-input {
    appearance: none;
    box-sizing: border-box;
    padding: 0 ($spacing-lg + $spacing-xs) 0 ($spacing-sm + $spacing-xs);
    border: 1px solid $neutral-300;
    border-radius: $btn-radius-std;
    background: $neutral-100;
    font-family: inherit;
    font-size: $p-sm-size;
    color: $neutral-700;
    cursor: pointer;

    &:hover {
      border-color: $primary;
    }

    &:focus-visible {
      outline: 2px solid $primary;
      outline-offset: -1px;
    }
  }

  // 箭頭是畫上去的，點到它要能穿透到底下的下拉
  &__select-arrow {
    position: absolute;
    top: 50%;
    right: $spacing-sm + $spacing-xs;
    color: $neutral-500;
    transform: translateY(-50%) rotate(90deg);
    pointer-events: none;
  }

  // 內容全文太長會把整張表撐爆，這裡只給一行，滑鼠停著看得到全文
  &__excerpt {
    display: block;
    max-width: 240px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: $neutral-600;
  }
}
</style>

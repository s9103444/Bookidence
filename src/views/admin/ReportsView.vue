<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { REPORT_STATUS, REPORT_TARGET } from '@/data/adminReports.js'
import { adminApi } from '@/common/adminApi.js'
import { useAdminReportsStore } from '@/stores/adminReports.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'
import AppIcon from '@/components/common/AppIcon.vue'

const ALL = '全部'

const reportsStore = useAdminReportsStore()

const status = ref(REPORT_STATUS.pending)
const targetType = ref(ALL)
const keyword = ref('')
const page = ref(1)

const reports = ref([])
const total = ref(0)
const perPage = ref(10)
const counts = ref({})
const typeCounts = ref({})
const loading = ref(false)
const error = ref('')

// 後端欄位進畫面前先轉成前端在用的名字，畫面那層就不用配合資料庫改名
function toReport(row) {
  return {
    id: row.report_id,
    no: row.report_no,
    // 同一則內容被幾個人檢舉。後端把它們併成一列了，這個數字就是原本的張數
    count: Number(row.report_count),
    // 多個人檢舉可能填不同理由，後端用「、」串起來
    reasons: row.reason ? row.reason.split('、') : [],
    targetType: row.target_type,
    reason: row.reason,
    content: row.content,
    reportedName: row.reported_name,
    reporterName: row.reporter_name,
    createdAt: row.created_at.slice(0, 16),
    status: row.status,
    actionTaken: row.action_taken,
  }
}

// 待處理那一頁每一列的結果都是空的，整欄留著只是噪音，所以那一頁不畫這欄
const isPendingTab = computed(() => status.value === REPORT_STATUS.pending)
const colCount = computed(() => (isPendingTab.value ? 7 : 8))

// 三顆鈕的數字直接讀後端算好的 counts，不從這一頁的資料數 ——
// 這一頁只有 10 筆，自己數會數成「這一頁有幾筆」
const statusOptions = computed(() =>
  [REPORT_STATUS.pending, REPORT_STATUS.upheld, REPORT_STATUS.dismissed].map((value) => ({
    label: value,
    value,
    count: counts.value[value] ?? 0,
  })),
)

const typeOptions = computed(() => {
  const thought = typeCounts.value[REPORT_TARGET.thought] ?? 0
  const message = typeCounts.value[REPORT_TARGET.message] ?? 0

  return [
    { value: ALL, text: `所有類型（${thought + message}）` },
    { value: REPORT_TARGET.thought, text: `心得檢舉（${thought}）` },
    { value: REPORT_TARGET.message, text: `留言檢舉（${message}）` },
  ]
})

const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)))

async function fetchReports() {
  loading.value = true
  error.value = ''

  try {
    const params = new URLSearchParams({
      page: page.value,
      status: status.value,
      type: targetType.value === ALL ? '' : targetType.value,
      keyword: keyword.value.trim(),
    })

    const res = await adminApi.get(`/admin_reports.php?${params}`)
    const result = res.data

    reports.value = result.data.map(toReport)
    total.value = result.total
    perPage.value = result.perPage
    counts.value = result.counts
    typeCounts.value = result.typeCounts
    reportsStore.setPendingCount(result.counts[REPORT_STATUS.pending])
  } catch (e) {
    console.error('[檢舉列表]', e)
    error.value = '載入失敗，請稍後再試'
    reports.value = []
    total.value = 0
  } finally {
    loading.value = false
  }
}

watch([status, targetType, keyword], () => {
  page.value = 1
  fetchReports()
})

// 在最後一頁把檢舉處理掉，那一頁會變空的，所以頁碼要往回收
watch(totalPages, (value) => {
  if (page.value > value) page.value = value
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
  fetchReports()
}

onMounted(fetchReports)
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
          <SearchBar v-model="keyword" size="sm" placeholder="搜尋暱稱或內容…" />
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
            <tr v-if="loading">
              <td :colspan="colCount"><p class="data-table__empty">載入中…</p></td>
            </tr>

            <tr v-else-if="error">
              <td :colspan="colCount"><p class="data-table__empty">{{ error }}</p></td>
            </tr>

            <tr v-else-if="reports.length === 0">
              <td :colspan="colCount">
                <p class="data-table__empty">
                  {{
                    keyword.trim()
                      ? `找不到符合「${keyword.trim()}」的檢舉`
                      : `目前沒有${status}的檢舉`
                  }}
                </p>
              </td>
            </tr>

            <template v-else>
              <tr v-for="report in reports" :key="report.id">
                <td class="data-table__key">
                  <span>#{{ report.no }}</span>
                  <span v-if="report.count > 1" class="reports__count">
                    {{ report.count }} 人檢舉
                  </span>
                </td>
                <td><AdminStatusTag :label="report.targetType" /></td>

                <td>
                  <span class="reports__excerpt" :title="report.content">{{ report.content }}</span>
                </td>

                <td>{{ report.reportedName }}</td>
                <td>
                  <span :title="report.reasons.join('、')">
                    {{ report.reasons[0] }}
                    <span v-if="report.reasons.length > 1" class="reports__more">
                      +{{ report.reasons.length - 1 }}
                    </span>
                  </span>
                </td>
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
            </template>
          </tbody>
        </table>
      </div>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ total }} 筆</p>

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

  // 同一則內容被多人檢舉時，編號底下補一行說明有幾個人
  &__count {
    display: block;
    margin-top: 2px;
    font-size: $p-xs-size;
    font-weight: $text-weight;
    color: $neutral-600;
  }

  // 理由不只一種時，多的那幾種收成 +N
  &__more {
    color: $neutral-500;
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

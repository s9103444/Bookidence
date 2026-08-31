<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { APPLICATION_STATUS } from '@/data/adminBooks.js'
import { adminApi } from '@/common/adminApi.js'
import { useAdminApplicationsStore } from '@/stores/adminApplications.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const applicationsStore = useAdminApplicationsStore()

const status = ref(APPLICATION_STATUS.pending)
const keyword = ref('')
const page = ref(1)

const applications = ref([])
const total = ref(0)
const perPage = ref(5)
const counts = ref({ 待處理: 0, 已核准: 0, 已駁回: 0 })
const loading = ref(false)
const error = ref('')

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
  }
}

async function fetchApplications() {
  loading.value = true
  error.value = ''

  try {
    const params = new URLSearchParams({
      page: page.value,
      status: status.value,
      keyword: keyword.value.trim(),
    })

    const res = await adminApi.get(`/admin_applications.php?${params}`)
    const result = res.data

    applications.value = result.data.map(toApplication)
    total.value = result.total
    perPage.value = result.perPage
    counts.value = result.counts
    applicationsStore.setPendingCount(result.counts['待處理'])
  } catch (e) {
    console.error('[申請列表]', e)
    error.value = '載入失敗，請稍後再試'
    applications.value = []
    total.value = 0
    counts.value = { 待處理: 0, 已核准: 0, 已駁回: 0 }
  } finally {
    loading.value = false
  }
}

onMounted(fetchApplications)

function countByStatus(value) {
  return counts.value[value]
}

// 數字會隨著審核而變，所以要是 computed，不能只算一次
const statusOptions = computed(() => [
  {
    label: '待處理',
    value: APPLICATION_STATUS.pending,
    count: countByStatus(APPLICATION_STATUS.pending),
  },
  {
    label: '已核准',
    value: APPLICATION_STATUS.approved,
    count: countByStatus(APPLICATION_STATUS.approved),
  },
  {
    label: '已駁回',
    value: APPLICATION_STATUS.rejected,
    count: countByStatus(APPLICATION_STATUS.rejected),
  },
])

const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)))

// 換狀態或改關鍵字之後，筆數會變少。停在第 3 頁的話畫面會空白，所以推回第 1 頁。
watch([status, keyword], () => {
  page.value = 1
  fetchApplications()
})

watch(totalPages, (value) => {
  if (page.value > value) page.value = value
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
  fetchApplications()
}
</script>

<template>
  <div class="admin-page">
    <header class="admin-page__head">
      <h1 class="admin-page__title">書籍申請審核</h1>
    </header>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="admin-page__search">
        <SearchBar v-model="keyword" size="sm" placeholder="搜尋申請書名 / ISBN / 申請人…" />
      </div>
    </div>

    <AdminPanel flush>
      <table class="data-table">
        <thead>
          <tr>
            <th scope="col">申請書名</th>
            <th scope="col">作者</th>
            <th scope="col">ISBN</th>
            <th scope="col">申請人</th>
            <th scope="col">參考連結</th>
            <th scope="col">申請時間</th>
            <th scope="col">操作</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="7"><p class="data-table__empty">載入中…</p></td>
          </tr>

          <tr v-else-if="error">
            <td colspan="7"><p class="data-table__empty">{{ error }}</p></td>
          </tr>

          <tr v-else-if="applications.length === 0">
            <td colspan="7">
              <p class="data-table__empty">
                {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的申請` : `目前沒有${status}的申請` }}
              </p>
            </td>
          </tr>

          <template v-else>
          <tr v-for="application in applications" :key="application.id">
            <td class="data-table__key">《{{ application.title }}》</td>
            <td>{{ application.author }}</td>
            <td class="data-table__muted">{{ application.isbn }}</td>
            <td>{{ application.applicant }}</td>
            <td>
              <a
                v-if="application.refUrl"
                :href="application.refUrl"
                target="_blank"
                rel="noopener"
                class="data-table__link"
              >
                查看連結
              </a>
              <span v-else class="data-table__muted">未提供</span>
            </td>
            <td class="data-table__muted">{{ application.appliedAt }}</td>
            <td>
              <span class="data-table__ops">
                <RouterLink :to="`/admin/books/applications/${application.id}`" class="data-table__op">
                  {{ status === APPLICATION_STATUS.pending ? '審核' : '查看' }}
                </RouterLink>
              </span>
            </td>
          </tr>
          </template>
        </tbody>
      </table>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ total }} 筆{{ status }}</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
</style>

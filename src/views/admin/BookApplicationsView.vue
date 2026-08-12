<script setup>
import { ref, computed, watch } from 'vue'
import { APPLICATION_STATUS } from '@/data/adminBooks.js'
import { useAdminBooksStore } from '@/stores/adminBooks.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminNotice from '@/components/admin/AdminNotice.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const PER_PAGE = 5

const adminBooksStore = useAdminBooksStore()

const status = ref(APPLICATION_STATUS.pending)
const keyword = ref('')
const page = ref(1)

function countByStatus(value) {
  return adminBooksStore.applications.filter((application) => application.status === value).length
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

// 幾個欄位串成一句話再比對，打書名、ISBN 或申請人都找得到
const filtered = computed(() => {
  const search = keyword.value.trim().toLowerCase()

  return adminBooksStore.applications
    .filter((application) => application.status === status.value)
    .filter((application) => {
      if (!search) return true
      const haystack = `${application.title} ${application.author} ${application.isbn} ${application.applicant}`
      return haystack.toLowerCase().includes(search)
    })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PER_PAGE)))

const pagedApplications = computed(() =>
  filtered.value.slice((page.value - 1) * PER_PAGE, page.value * PER_PAGE),
)

// 換狀態或改關鍵字之後，筆數會變少。停在第 3 頁的話畫面會空白，所以推回第 1 頁。
watch([status, keyword], () => {
  page.value = 1
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
}
</script>

<template>
  <div class="admin-page">
    <header class="admin-page__head">
      <h1 class="admin-page__title">書籍申請審核</h1>
    </header>

    <AdminNotice>
      會員申請新增書籍時僅填寫基本資訊；核准時需由管理員手動補齊出版社、簡介、出版日期、封面等資料，核准後書籍將直接上架。
    </AdminNotice>

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
          <tr v-for="application in pagedApplications" :key="application.id">
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

          <tr v-if="pagedApplications.length === 0">
            <td colspan="7">
              <p class="data-table__empty">
                {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的申請` : `目前沒有${status}的申請` }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ filtered.length }} 筆{{ status }}</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
</style>

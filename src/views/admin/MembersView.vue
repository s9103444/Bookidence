<script setup>
import { ref, computed, watch } from 'vue'
import { MEMBER_STATUS, violationsOf } from '@/data/adminMembers.js'
import { useAdminMembersStore } from '@/stores/adminMembers.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const PER_PAGE = 10
const ALL = '全部'

const adminMembersStore = useAdminMembersStore()

const status = ref(ALL)
const keyword = ref('')
const page = ref(1)
const sortByReports = ref(false)

function countByStatus(value) {
  if (value === ALL) return adminMembersStore.members.length
  return adminMembersStore.members.filter((member) => member.status === value).length
}

const statusOptions = computed(() => [
  { label: ALL, value: ALL, count: countByStatus(ALL) },
  {
    label: MEMBER_STATUS.active,
    value: MEMBER_STATUS.active,
    count: countByStatus(MEMBER_STATUS.active),
  },
  {
    label: MEMBER_STATUS.suspended,
    value: MEMBER_STATUS.suspended,
    count: countByStatus(MEMBER_STATUS.suspended),
  },
])

const filtered = computed(() => {
  const search = keyword.value.trim().toLowerCase()

  const rows = adminMembersStore.members
    .filter((member) => status.value === ALL || member.status === status.value)
    .filter((member) => {
      if (!search) return true
      return `${member.nickname} ${member.id} ${member.email}`.toLowerCase().includes(search)
    })

  if (!sortByReports.value) return rows

  return [...rows].sort((a, b) => violationsOf(b).length - violationsOf(a).length)
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PER_PAGE)))

const pagedMembers = computed(() =>
  filtered.value.slice((page.value - 1) * PER_PAGE, page.value * PER_PAGE),
)

watch([status, keyword, sortByReports], () => {
  page.value = 1
})

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
      <h1 class="admin-page__title">會員管理</h1>
    </header>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="admin-page__search">
        <SearchBar v-model="keyword" size="sm" placeholder="搜尋暱稱 / 會員編號 / 信箱…" />
      </div>
    </div>

    <AdminPanel flush>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">暱稱</th>
              <th scope="col">會員編號</th>
              <th scope="col">註冊時間</th>
              <th scope="col">
                <button
                  type="button"
                  class="members__sort"
                  :class="{ 'members__sort--on': sortByReports }"
                  :aria-pressed="sortByReports"
                  @click="sortByReports = !sortByReports"
                >
                  違規次數
                  <span aria-hidden="true">{{ sortByReports ? '↓' : '⇅' }}</span>
                </button>
              </th>
              <th scope="col">狀態</th>
              <th scope="col">操作</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="member in pagedMembers" :key="member.id">
              <td class="data-table__key">{{ member.nickname }}</td>
              <td class="data-table__muted">{{ member.id }}</td>
              <td class="data-table__muted">{{ member.joinedAt }}</td>

              <td>
                <span
                  class="members__count"
                  :class="{ 'members__count--high': violationsOf(member).length >= 3 }"
                >
                  {{ violationsOf(member).length }}
                </span>
              </td>

              <td>
                <AdminStatusTag
                  :label="member.status"
                  :tone="member.status === MEMBER_STATUS.suspended ? 'muted' : 'solid'"
                />
              </td>

              <td>
                <span class="data-table__ops">
                  <RouterLink :to="`/admin/members/${member.id}`" class="data-table__op">
                    檢視會員
                  </RouterLink>
                </span>
              </td>
            </tr>

            <tr v-if="pagedMembers.length === 0">
              <td colspan="6">
                <p class="data-table__empty">
                  {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的會員` : '這個狀態底下目前沒有會員' }}
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ filtered.length }} 位會員</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
@use '../../assets/scss/abstracts/variables' as *;

.members {
  &__sort {
    display: inline-flex;
    align-items: center;
    gap: $spacing-xs;
    padding: 0;
    border: 0;
    background: none;
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
    cursor: pointer;

    &:hover {
      color: $primary;
    }

    &--on {
      color: $primary;
    }
  }

  &__count {
    font-variant-numeric: tabular-nums;

    &--high {
      font-weight: $heading-weight;
      color: $color-danger;
    }
  }
}
</style>

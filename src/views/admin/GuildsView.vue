<script setup>
import { ref, computed, watch } from 'vue'
import { adminGuilds, GUILD_STATUS, leaderOf } from '@/data/adminGuilds.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const PER_PAGE = 10
const ALL = '全部'

// 目前只是把假資料複製一份存在畫面裡，之後接 API 就換成打 GET /admin/guilds
const guilds = ref(structuredClone(adminGuilds))

const status = ref(ALL)
const keyword = ref('')
const page = ref(1)

function countByStatus(value) {
  if (value === ALL) return guilds.value.length
  return guilds.value.filter((guild) => guild.status === value).length
}

const statusOptions = computed(() => [
  { label: ALL, value: ALL, count: countByStatus(ALL) },
  {
    label: GUILD_STATUS.active,
    value: GUILD_STATUS.active,
    count: countByStatus(GUILD_STATUS.active),
  },
  {
    label: GUILD_STATUS.suspended,
    value: GUILD_STATUS.suspended,
    count: countByStatus(GUILD_STATUS.suspended),
  },
  {
    label: GUILD_STATUS.deleted,
    value: GUILD_STATUS.deleted,
    count: countByStatus(GUILD_STATUS.deleted),
  },
])

function statusTone(guildStatus) {
  if (guildStatus === GUILD_STATUS.active) return 'solid'
  if (guildStatus === GUILD_STATUS.suspended) return 'muted'
  return 'outline'
}

// 公會編號是 G-0027 這種格式，數字越大代表越晚建立
function idNumber(guild) {
  return Number(guild.id.replace(/\D/g, ''))
}

const filtered = computed(() => {
  const search = keyword.value.trim().toLowerCase()

  return guilds.value
    .filter((guild) => status.value === ALL || guild.status === status.value)
    .filter((guild) => {
      if (!search) return true
      const leader = leaderOf(guild)
      return `${guild.name} ${guild.id} ${leader?.nickname ?? ''}`.toLowerCase().includes(search)
    })
    .sort((a, b) => idNumber(b) - idNumber(a))
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / PER_PAGE)))

const pagedGuilds = computed(() =>
  filtered.value.slice((page.value - 1) * PER_PAGE, page.value * PER_PAGE),
)

watch([status, keyword], () => {
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
      <h1 class="admin-page__title">公會管理</h1>
    </header>

    <div class="admin-page__toolbar">
      <AdminFilterTabs v-model="status" :options="statusOptions" />

      <div class="admin-page__search">
        <SearchBar v-model="keyword" size="sm" placeholder="搜尋公會名稱 / 公會編號 / 會長暱稱…" />
      </div>
    </div>

    <AdminPanel flush>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">公會名稱</th>
              <th scope="col">公會編號</th>
              <th scope="col">會長</th>
              <th scope="col">成員數</th>
              <th scope="col">建立時間</th>
              <th scope="col">狀態</th>
              <th scope="col">操作</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="guild in pagedGuilds" :key="guild.id">
              <td class="data-table__key">{{ guild.name }}</td>
              <td class="data-table__muted">{{ guild.id }}</td>
              <td>{{ leaderOf(guild)?.nickname ?? '（無會長）' }}</td>
              <td>{{ guild.members.length }}</td>
              <td class="data-table__muted">{{ guild.createdAt }}</td>

              <td>
                <AdminStatusTag :label="guild.status" :tone="statusTone(guild.status)" />
              </td>

              <td>
                <span class="data-table__ops">
                  <RouterLink :to="`/admin/guilds/${guild.id}`" class="data-table__op">
                    檢視公會
                  </RouterLink>
                </span>
              </td>
            </tr>

            <tr v-if="pagedGuilds.length === 0">
              <td colspan="7">
                <p class="data-table__empty">
                  {{ keyword.trim() ? `找不到符合「${keyword.trim()}」的公會` : '這個狀態底下目前沒有公會' }}
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminPanel>

    <footer class="admin-page__foot">
      <p class="admin-page__count">共 {{ filtered.length }} 個公會</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
</style>

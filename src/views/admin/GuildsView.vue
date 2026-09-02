<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { GUILD_STATUS, dbGuildStatusToUi, uiGuildStatusToDb } from '@/data/adminGuilds.js'
import { adminApi } from '@/common/adminApi.js'
import AdminPanel from '@/components/admin/AdminPanel.vue'
import AdminFilterTabs from '@/components/admin/AdminFilterTabs.vue'
import AdminStatusTag from '@/components/admin/AdminStatusTag.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import SearchBar from '@/components/common/SearchBar.vue'

const ALL = '全部'

const status = ref(ALL)
const keyword = ref('')
const page = ref(1)

const guilds = ref([])
const perPage = ref(10)
const total = ref(0)
const statusCounts = ref({ 正常: 0, 停權: 0, 已解散: 0 })
const loading = ref(false)
const error = ref('')

const statusOptions = computed(() => {
  const activeCount = statusCounts.value['正常'] ?? 0
  const suspendedCount = statusCounts.value['停權'] ?? 0
  const deletedCount = statusCounts.value['已解散'] ?? 0

  return [
    { label: ALL, value: ALL, count: activeCount + suspendedCount + deletedCount },
    { label: GUILD_STATUS.active, value: GUILD_STATUS.active, count: activeCount },
    { label: GUILD_STATUS.suspended, value: GUILD_STATUS.suspended, count: suspendedCount },
    { label: GUILD_STATUS.deleted, value: GUILD_STATUS.deleted, count: deletedCount },
  ]
})

function statusTone(guildStatus) {
  if (guildStatus === GUILD_STATUS.active) return 'solid'
  if (guildStatus === GUILD_STATUS.suspended) return 'muted'
  return 'outline'
}

const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)))

function toGuild(row) {
  return {
    id: row.guild_id,
    code: row.guild_code,
    name: row.guild_name,
    status: dbGuildStatusToUi(row.guild_status),
    leaderNickname: row.leader_nickname,
    memberCount: Number(row.member_count),
    createdAt: row.founded_at,
  }
}

async function fetchGuilds() {
  loading.value = true
  error.value = ''

  try {
    const params = new URLSearchParams({
      page: page.value,
      status: status.value === ALL ? '' : uiGuildStatusToDb(status.value),
      keyword: keyword.value.trim(),
    })

    const res = await adminApi.get(`/admin_guilds.php?${params}`)
    const result = res.data

    guilds.value = result.data.map(toGuild)
    total.value = result.total
    perPage.value = result.perPage
    statusCounts.value = result.statusCounts
  } catch (e) {
    console.error('[公會列表]', e)
    error.value = '載入失敗，請稍後再試'
    guilds.value = []
    total.value = 0
  } finally {
    loading.value = false
  }
}

watch([status, keyword], () => {
  page.value = 1
  fetchGuilds()
})

watch(totalPages, (value) => {
  if (page.value > value) page.value = value
})

function goToPage(target) {
  page.value = target
  window.scrollTo({ top: 0 })
  fetchGuilds()
}

onMounted(() => {
  fetchGuilds()
})
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
            <template v-if="!loading && !error">
              <tr v-for="guild in guilds" :key="guild.id">
                <td class="data-table__key">{{ guild.name }}</td>
                <td class="data-table__muted">{{ guild.code }}</td>
                <td>{{ guild.leaderNickname ?? '（無會長）' }}</td>
                <td>{{ guild.memberCount }}</td>
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
            </template>

            <tr v-if="loading">
              <td colspan="7"><p class="data-table__empty">載入中…</p></td>
            </tr>

            <tr v-else-if="error">
              <td colspan="7"><p class="data-table__empty">{{ error }}</p></td>
            </tr>

            <tr v-else-if="guilds.length === 0">
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
      <p class="admin-page__count">共 {{ total }} 個公會</p>

      <AppPagination :current-page="page" :total-pages="totalPages" @change="goToPage" />
    </footer>
  </div>
</template>

<style scoped lang="scss">
@use '../../assets/scss/admin/page' as *;
@use '../../assets/scss/admin/data-table' as *;
</style>

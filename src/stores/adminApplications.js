// 側邊欄「申請審核」那顆紅色 badge 的數字。
//
// 怎麼用：
//   1. 進後台時 AdminLayout 會呼叫 fetchPendingCount() 拿一次
//   2. 申請列表頁本來就會撈到這個數字，直接用 setPendingCount() 塞進來，不用多打一次 API
//   3. 核准／駁回／重新審核之後呼叫 fetchPendingCount()，數字才會跟著變

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { adminApi } from '@/common/adminApi.js'

export const useAdminApplicationsStore = defineStore('adminApplications', () => {
  const pendingCount = ref(0)

  function setPendingCount(value) {
    pendingCount.value = value
  }

  async function fetchPendingCount() {
    try {
      const res = await adminApi.get('/admin_applications.php?status=待處理')
      pendingCount.value = res.data.counts['待處理']
    } catch (e) {
      console.error('[待審數量]', e)
    }
  }

  return { pendingCount, setPendingCount, fetchPendingCount }
})

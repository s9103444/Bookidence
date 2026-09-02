// 側邊欄「檢舉管理」那顆紅色 badge 的數字。
//
// 怎麼用：
//   1. 進後台時 AdminLayout 會呼叫 fetchPendingCount() 拿一次
//   2. 檢舉列表頁本來就會撈到這個數字，直接用 setPendingCount() 塞進來，不用多打一次 API
//   3. 判決／退回之後呼叫 fetchPendingCount()，數字才會跟著變

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { adminApi } from '@/common/adminApi.js'

export const useAdminReportsStore = defineStore('adminReports', () => {
  const pendingCount = ref(0)

  function setPendingCount(value) {
    pendingCount.value = value
  }

  async function fetchPendingCount() {
    try {
      const res = await adminApi.get('/admin_reports.php?status=尚未處理')
      pendingCount.value = res.data.counts['尚未處理']
    } catch (e) {
      console.error('[待處理檢舉數]', e)
    }
  }

  return { pendingCount, setPendingCount, fetchPendingCount }
})

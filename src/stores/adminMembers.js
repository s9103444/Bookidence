// 後台會員管理的狀態。停權、復權、警告都寫進 localStorage，重新整理不會回到原點。
//
// ⚠️ 要改資料一律經過這裡，不要去改 data/adminMembers.js 匯入的那個陣列 ——
// 那是普通陣列，改了畫面不會更新。
//
// 之後接後端 API，就把 suspend / restore 換成打 API，頁面不用改。

import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'
import { adminMembers, MEMBER_STATUS } from '@/data/adminMembers.js'

const STORAGE_KEY = 'adminMemberList'

function load() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (!saved) return structuredClone(adminMembers)
    const parsed = JSON.parse(saved)
    return Array.isArray(parsed) ? parsed : structuredClone(adminMembers)
  } catch {
    return structuredClone(adminMembers)
  }
}

function save(value) {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
  } catch {
    // 無痕模式或容量滿了會寫不進去，不影響當下操作
  }
}

export const useAdminMembersStore = defineStore('adminMembers', () => {
  const members = ref(load())

  watch(members, (value) => save(value), { deep: true })

  const suspendedCount = computed(
    () => members.value.filter((member) => member.status === MEMBER_STATUS.suspended).length,
  )

  function getMember(id) {
    return members.value.find((member) => member.id === id)
  }

  function now() {
    const date = new Date()
    const pad = (value) => String(value).padStart(2, '0')
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
  }

  function suspend(id, reason) {
    const member = getMember(id)
    if (!member || member.status === MEMBER_STATUS.suspended) return

    member.status = MEMBER_STATUS.suspended
    member.suspendedAt = now()
    member.suspendReason = reason
    member.suspendedBy = '書芸'
  }

  function restore(id) {
    const member = getMember(id)
    if (!member || member.status !== MEMBER_STATUS.suspended) return

    member.status = MEMBER_STATUS.active
    member.suspendedAt = ''
    member.suspendReason = ''
    member.suspendedBy = ''
  }

  return {
    members,
    suspendedCount,
    getMember,
    suspend,
    restore,
  }
})

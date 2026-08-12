// 後台書籍管理的狀態：申請、正式書籍、分類。側邊欄的數字和四個頁面都讀這裡，
// 每次改動都寫進 localStorage，重新整理不會回到原點。
//
// ⚠️ 要改資料一律經過這裡，不要去改 data/adminBooks.js 匯入的那幾個陣列 ——
// 那是普通陣列，改了畫面不會更新，Vue 只會盯著 ref / reactive 包過的東西。
//
// 之後接後端 API，就把 approve / reject / updateBook 換成打 API，頁面不用改。

import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'
import {
  bookApplications,
  adminBooks,
  defaultCategories,
  APPLICATION_STATUS,
  BOOK_STATUS,
} from '@/data/adminBooks.js'

const STORAGE_KEYS = {
  applications: 'adminBookApplications',
  books: 'adminBookList',
  categories: 'adminBookCategories',
}

// 讀 localStorage。裡面的東西可能是舊版的格式或被手動改壞了，
// 所以一律用 try / catch 包住，壞掉就退回預設值而不是讓整頁掛掉。
function load(key, fallback) {
  try {
    const saved = localStorage.getItem(key)
    if (!saved) return structuredClone(fallback)
    const parsed = JSON.parse(saved)
    return Array.isArray(parsed) ? parsed : structuredClone(fallback)
  } catch {
    return structuredClone(fallback)
  }
}

function save(key, value) {
  try {
    localStorage.setItem(key, JSON.stringify(value))
  } catch {
    // 無痕模式或容量滿了會寫不進去。存不進去只是重整後回到預設值，
    // 不影響當下操作，所以不用特別提示
  }
}

// 封面是 import 進來的圖片路徑，打包後檔名會帶一串雜湊。
// 存進 localStorage 之後那串雜湊可能已經過期，所以封面一律回頭拿程式裡的那份，
// 只有管理員自己貼的網址（coverUrl）才從存檔讀。
function withFreshCovers(savedBooks) {
  return savedBooks.map((book) => {
    const source = adminBooks.find((item) => item.id === book.id)
    return { ...book, cover: book.coverUrl ? null : (source?.cover ?? null) }
  })
}

export const useAdminBooksStore = defineStore('adminBooks', () => {
  const applications = ref(load(STORAGE_KEYS.applications, bookApplications))
  const books = ref(withFreshCovers(load(STORAGE_KEYS.books, adminBooks)))
  const categories = ref(load(STORAGE_KEYS.categories, defaultCategories))

  // deep: true 是因為我們會改陣列「裡面某一筆的某個欄位」，
  // 不加的話只有整個陣列被換掉才會存
  watch(applications, (value) => save(STORAGE_KEYS.applications, value), { deep: true })
  watch(books, (value) => save(STORAGE_KEYS.books, value), { deep: true })
  watch(categories, (value) => save(STORAGE_KEYS.categories, value), { deep: true })

  const pendingApplicationCount = computed(
    () => applications.value.filter((item) => item.status === APPLICATION_STATUS.pending).length,
  )

  function getApplication(id) {
    return applications.value.find((item) => item.id === id)
  }

  function getBook(id) {
    return books.value.find((item) => item.id === Number(id))
  }

  // 某個分類底下有幾本書。分類管理頁要靠這個數字決定能不能刪。
  function bookCountOf(category) {
    return books.value.filter((book) => book.categories.includes(category)).length
  }

  function now() {
    const date = new Date()
    const pad = (value) => String(value).padStart(2, '0')
    return `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
  }

  // 核准 = 申請單標記已核准 + 同時建一本正式書籍直接上架。
  // 這兩件事一定要一起做，只改申請單的話管理員會發現書沒有出現在書庫裡。
  function approve(id, adminFields) {
    const application = getApplication(id)
    if (!application || application.status !== APPLICATION_STATUS.pending) return

    application.status = APPLICATION_STATUS.approved
    application.handledAt = now()
    application.handledBy = '書芸'

    const nextId = Math.max(0, ...books.value.map((book) => book.id)) + 1

    books.value.unshift({
      id: nextId,
      title: application.title,
      author: application.author,
      isbn: application.isbn,
      publisher: adminFields.publisher,
      publishDate: adminFields.publishDate,
      cover: null,
      coverUrl: adminFields.coverUrl || '',
      summary: adminFields.summary,
      categories: [...adminFields.categories],
      status: BOOK_STATUS.listed,
    })
  }

  function reject(id, reason) {
    const application = getApplication(id)
    if (!application || application.status !== APPLICATION_STATUS.pending) return

    application.status = APPLICATION_STATUS.rejected
    application.handledAt = now()
    application.handledBy = '書芸'
    application.rejectReason = reason
  }

  // 管理員修正會員填錯的書名 / 作者 / ISBN / 參考連結。
  // 申請人、申請時間、申請理由不開放改，那些是紀錄，改掉等於偽造。
  function updateApplication(id, fields) {
    const application = getApplication(id)
    if (!application) return
    Object.assign(application, fields)
  }

  function updateBook(id, fields) {
    const book = getBook(id)
    if (!book) return
    Object.assign(book, fields)
  }

  // 管理員直接建一本書（不經過會員申請）。用 unshift 放到最前面，
  // 剛新增的那本才會出現在第一頁。
  function addBook(fields) {
    const nextId = Math.max(0, ...books.value.map((book) => book.id)) + 1

    books.value.unshift({
      id: nextId,
      cover: null,
      coverUrl: '',
      ...fields,
      categories: [...fields.categories],
    })
  }

  function addCategory(name) {
    const trimmed = name.trim()
    if (!trimmed || categories.value.includes(trimmed)) return false
    categories.value.push(trimmed)
    return true
  }

  function renameCategory(oldName, newName) {
    const trimmed = newName.trim()
    if (!trimmed || !categories.value.includes(oldName)) return false
    if (trimmed !== oldName && categories.value.includes(trimmed)) return false

    categories.value = categories.value.map((item) => (item === oldName ? trimmed : item))

    books.value.forEach((book) => {
      book.categories = book.categories.map((item) => (item === oldName ? trimmed : item))
    })

    return true
  }

  // 還有書在用的分類不能刪，不然那些書會變成無分類狀態。
  function removeCategory(name) {
    if (bookCountOf(name) > 0) return false
    categories.value = categories.value.filter((item) => item !== name)
    return true
  }

  return {
    applications,
    books,
    categories,
    pendingApplicationCount,
    getApplication,
    getBook,
    bookCountOf,
    approve,
    reject,
    updateApplication,
    updateBook,
    addBook,
    addCategory,
    renameCategory,
    removeCategory,
  }
})

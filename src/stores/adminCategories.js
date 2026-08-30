// 後台的書籍分類清單。三個地方要用同一份：分類管理頁、書籍編輯彈窗的分類 chips、
// 申請核准彈窗的分類 chips。放在這裡是為了讓那三處讀到同一份，不會各自過期。
//
// 用法：頁面在 onMounted 呼叫 ensureCategories()，之後讀 categories 就好。
// 分類很少變，所以第一個進來的頁面撈一次，後面的頁面直接用現成的。

import { ref } from 'vue'
import { defineStore } from 'pinia'
import { adminApi } from '@/common/adminApi.js'

export const useAdminCategoriesStore = defineStore('adminCategories', () => {
  const categories = ref([])
  const loading = ref(false)
  const error = ref('')
  const loaded = ref(false)

  function toCategory(row) {
    return {
      id: row.bcg_id,
      name: row.bcg_name,
      bookCount: row.book_count,
    }
  }

  async function fetchCategories() {
    loading.value = true
    error.value = ''

    try {
      const res = await adminApi.get('/admin_categories.php')

      categories.value = res.data.data.map(toCategory)

      loaded.value = true
    } catch (e) {
      console.error('[分類列表]', e)
      error.value = '分類載入失敗，請稍後再試'

      categories.value = []
    } finally {
      loading.value = false
    }
  }

  function ensureCategories() {
    if (loaded.value) return
    return fetchCategories()
  }

  return { categories, loading, error, fetchCategories, ensureCategories }
})

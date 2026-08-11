import AdminLayout from '../layouts/AdminLayout.vue'

// meta.title 是側邊欄反白和麵包屑要顯示的字，AdminLayout 會讀它。
// meta.group 只有「系統共同管理」底下那三頁要填，因為它們共用同一個側邊欄項目。
export default [
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('../views/admin/AdminLoginView.vue'),
  },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        path: '',
        name: 'admin-dashboard',
        meta: { title: '總覽' },
        component: () => import('../views/admin/DashboardView.vue'),
      },
      {
        path: 'books',
        name: 'admin-books',
        meta: { title: '書籍管理' },
        component: () => import('../views/admin/BooksView.vue'),
      },
      {
        path: 'members',
        name: 'admin-members',
        meta: { title: '會員管理' },
        component: () => import('../views/admin/MembersView.vue'),
      },
      {
        path: 'guilds',
        name: 'admin-guilds',
        meta: { title: '公會管理' },
        component: () => import('../views/admin/GuildsView.vue'),
      },
      {
        path: 'reports',
        name: 'admin-reports',
        meta: { title: '檢舉管理' },
        component: () => import('../views/admin/ReportsView.vue'),
      },
      {
        path: 'settings',
        name: 'admin-settings',
        meta: { title: '經驗值規則', group: '系統共同管理' },
        component: () => import('../views/admin/AdminSettingsView.vue'),
      },
      {
        path: 'announcements',
        name: 'admin-announcements',
        meta: { title: '輪播圖與最新消息', group: '系統共同管理' },
        component: () => import('../views/admin/AnnouncementsView.vue'),
      },
      {
        path: 'support',
        name: 'admin-support',
        meta: { title: '常見問題 FAQ', group: '系統共同管理' },
        component: () => import('../views/admin/SupportView.vue'),
      },
    ],
  },
]

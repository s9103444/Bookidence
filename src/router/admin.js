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
      // 書籍管理底下有三頁，共用「書籍管理」這個側邊欄項目（靠 meta.group 認）。
      // /admin/books 自己沒有畫面，進來就轉去申請審核 —— 側邊欄的「書籍管理」
      // 和總覽頁的連結都指向它，少了這條轉址會變成空白頁。
      {
        path: 'books',
        redirect: '/admin/books/applications',
      },
      {
        path: 'books/applications',
        name: 'admin-book-applications',
        meta: { title: '申請審核', group: '書籍管理' },
        component: () => import('../views/admin/BookApplicationsView.vue'),
      },
      {
        path: 'books/applications/:id',
        name: 'admin-book-application-detail',
        meta: { title: '審核申請', group: '書籍管理' },
        component: () => import('../views/admin/BookApplicationDetailView.vue'),
      },
      {
        path: 'books/list',
        name: 'admin-book-list',
        meta: { title: '正式書籍', group: '書籍管理' },
        component: () => import('../views/admin/BookListView.vue'),
      },
      {
        path: 'books/categories',
        name: 'admin-book-categories',
        meta: { title: '書籍分類', group: '書籍管理' },
        component: () => import('../views/admin/BookCategoriesView.vue'),
      },
      {
        path: 'members',
        name: 'admin-members',
        meta: { title: '會員管理', group: '會員管理' },
        component: () => import('../views/admin/MembersView.vue'),
      },
      {
        path: 'members/:id',
        name: 'admin-member-detail',
        meta: { title: '會員詳情', group: '會員管理' },
        component: () => import('../views/admin/MemberDetailView.vue'),
      },
      {
        path: 'guilds',
        name: 'admin-guilds',
        meta: { title: '公會管理', group: '公會管理' },
        component: () => import('../views/admin/GuildsView.vue'),
      },
      {
        path: 'guilds/:id',
        name: 'admin-guild-detail',
        meta: { title: '公會詳情', group: '公會管理' },
        component: () => import('../views/admin/GuildDetailView.vue'),
      },
      {
        path: 'reports',
        name: 'admin-reports',
        meta: { title: '檢舉管理', group: '檢舉管理' },
        component: () => import('../views/admin/ReportsView.vue'),
      },
      {
        path: 'reports/:id',
        name: 'admin-report-detail',
        meta: { title: '檢舉詳情', group: '檢舉管理' },
        component: () => import('../views/admin/ReportDetailView.vue'),
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

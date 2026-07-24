import AdminLayout from '../layouts/AdminLayout.vue'

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
      { path: '', name: 'admin-dashboard', component: () => import('../views/admin/DashboardView.vue') },
      { path: 'staff', name: 'admin-staff', component: () => import('../views/admin/StaffView.vue') },
      { path: 'announcements', name: 'admin-announcements', component: () => import('../views/admin/AnnouncementsView.vue') },
      { path: 'settings', name: 'admin-settings', component: () => import('../views/admin/AdminSettingsView.vue') },
      { path: 'support', name: 'admin-support', component: () => import('../views/admin/SupportView.vue') },
      { path: 'members', name: 'admin-members', component: () => import('../views/admin/MembersView.vue') },
      { path: 'guilds', name: 'admin-guilds', component: () => import('../views/admin/GuildsView.vue') },
      { path: 'books', name: 'admin-books', component: () => import('../views/admin/BooksView.vue') },
      { path: 'reports', name: 'admin-reports', component: () => import('../views/admin/ReportsView.vue') },
    ],
  },
]

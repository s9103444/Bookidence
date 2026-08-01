import { createRouter, createWebHistory } from 'vue-router'
import frontRoutes from './front'
import adminRoutes from './admin'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    ...frontRoutes,
    ...adminRoutes,
  ],
})

export default router

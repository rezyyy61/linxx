import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import guestRoutes from './routes/guest'
import publicRoutes from './routes/public'
import protectedRoutes from './routes/protected'
import adminRoutes from './routes/admin'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    ...guestRoutes,
    ...publicRoutes,
    ...protectedRoutes,
    ...adminRoutes
  ]
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  if (auth.token && !auth.user) {
    try {
      await auth.fetchUser()
    } catch (e) {
      auth.clearAuth()
    }
  }

  if (to.meta.requiresAuth && !auth.user) {
    return next({ name: 'login' })
  }

  if (to.meta.guestOnly && auth.user) {
    return next({ name: 'dashboard' })
  }

  if (to.meta.requiresAdmin) {
    const token = localStorage.getItem('admin_token')
    const userRaw = localStorage.getItem('admin_user')
    const user = userRaw ? JSON.parse(userRaw) : null

    const isAdmin = user?.roles?.includes('admin') || user?.roles?.includes('super-admin')

    if (!token || !user || !isAdmin) {
      return next({ name: 'admin.login' })
    }

    if (to.meta.guestOnly && isAdmin) {
      return next({ name: 'admin.dashboard' })
    }
  }

  return next()
})

export default router

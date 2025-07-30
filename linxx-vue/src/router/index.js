import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const guestRoutes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/pages/auth/RegisterPage.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('@/pages/auth/ForgotPassword.vue'),
    meta: { guestOnly: true }
  }
]

const publicRoutes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/pages/home/HomePage.vue'),
    children: [
      {
        path: '', // default tab
        name: 'feed',
        component: () => import('@/pages/home/components/modules/FeedCard.vue')
      },
      {
        path: 'parties',
        name: 'parties',
        component: () => import('@/pages/home/components/modules/PartiesCard.vue')
      },
      {
        path: 'articles',
        name: 'articles',
        component: () => import('@/pages/home/components/modules/ArticlesCard.vue')
      },
      {
        path: 'media',
        name: 'media',
        component: () => import('@/pages/home/components/modules/MediaCard.vue')
      },
      {
        path: 'campaigns',
        name: 'campaigns',
        component: () => import('@/pages/home/components/modules/CampaignsCard.vue')
      },
      {
        path: 'profiles',
        name: 'profiles',
        component: () => import('@/pages/home/components/modules/ProfilesCard.vue')
      },
      {
        path: 'events',
        name: 'events',
        component: () => import('@/pages/home/components/modules/EventsCard.vue')
      },
      {
        path: 'announcements',
        name: 'announcements',
        component: () => import('@/pages/home/components/modules/AnnouncementsCard.vue')
      },
      {
        path: '/party/:slug',
        name: 'party_profile',
        component: () => import('@/pages/home/components/modules/party-profile/PartyProfile.vue')
      },
        {
            path: '/:slug',
            name: 'users.show',
            component: () => import('@/pages/home/components/modules/Profile/UserProfile.vue'),
            props: true
        },
    ]
  }

]

const protectedRoutes = [
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/pages/admin/DashboardPage.vue'),
    children: [
      {
        path: 'dashboard-home',
        name: 'dashboard_home',
        component: () => import('@/pages/admin/sections/DashboardHome.vue')
      },
      {
        path: 'new-post',
        name: 'new_post',
        component: () => import('@/pages/admin/sections/NewPost.vue')
      },
      {
        path: 'archive',
        name: 'archive',
        component: () => import('@/pages/admin/sections/Archive.vue')
      },
      {
        path: 'myposts',
        name: 'my_posts',
        component: () => import('@/pages/admin/sections/MyPosts.vue')
      },
      {
        path: 'political-profile',
        name: 'political_profile',
        component: () => import('@/pages/admin/sections/PoliticalProfile.vue')
      },
        {
            path: 'publications',
            name: 'publications',
            component: () => import('@/pages/admin/sections/publications.vue')
        },
      {
        path: 'settings',
        name: 'settings',
        component: () => import('@/pages/admin/sections/Settings.vue')
      }
    ],
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes: [...guestRoutes, ...publicRoutes, ...protectedRoutes]
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

  return next()
})

export default router

export default [
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
            },
            {
                path: 'events',
                name: 'events',
                component: () => import('@/pages/admin/sections/EventsPage.vue')
            }
        ],
        meta: { requiresAuth: true }
    }
]

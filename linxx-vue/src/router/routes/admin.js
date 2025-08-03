export default [
    {
        path: '/admin/login',
        name: 'admin.login',
        component: () => import('@/admin/pages/Auth/AdminLogin.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/admin',
        component: () => import('@/admin/layout/AdminLayout.vue'),
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: () => import('@/admin/pages/DashboardPage.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'users',
                name: 'admin.users',
                component: () => import('@/admin/pages/UsersPage.vue'),
                meta: { requiresAdmin: true }
            },
            {
                path: 'books',
                name: 'admin.books',
                component: () => import('@/admin/pages/book/BookIndex.vue'),
                meta: { requiresAdmin: true },
                children: [
                    {
                        path: 'create',
                        name: 'admin.books.create',
                        component: () => import('@/admin/pages/book/BookCreate.vue')
                    },
                    {
                        path: 'suggestions',
                        name: 'AdminSuggestedBooks',
                        component: () => import('@/admin/pages/book/SuggestedBooksPage.vue')
                    }

                ]
            }

        ]
    }
]

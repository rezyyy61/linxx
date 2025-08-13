export default [
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
        path: '/verify-email',
        name: 'verify-email',
        component: () => import('@/pages/auth/VerifyEmail.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/auth/ForgotPassword.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/social-login/callback',
        name: 'social-login-callback',
        component: () => import('@/pages/auth/SocialLoginCallback.vue'),
        meta: { guestOnly: true }
    }

]

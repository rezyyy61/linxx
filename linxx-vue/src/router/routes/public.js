export default [
    {
        path: '/',
        name: 'home',
        component: () => import('@/pages/home/HomePage.vue'),
        children: [
            {
                path: '',
                name: 'feed',
                component: () => import('@/pages/home/components/modules/Feed/FeedCard.vue')
            },
            {
                path: 'parties',
                name: 'parties',
                component: () => import('@/pages/home/components/modules/PartiesLayout.vue')
            },
            {
                path: 'media',
                name: 'media',
                component: () => import('@/pages/home/components/modules/MediaCard.vue')
            },
            {
                path: 'books',
                name: 'books',
                component: () => import('@/pages/home/components/modules/BooksCard.vue')
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
                name: 'public_events',
                component: () => import('@/pages/home/components/modules/EventsCard.vue')
            },
            {
                path: '/events/:idOrSlug',
                name: 'public_event',
                component: () => import('@/pages/home/components/modules/events/EventShow.vue'),
                props: true
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
            {
                path: '/watch/:id',
                name: 'watch',
                component: () => import('@/pages/home/components/modules/VideoCard/WatchPage.vue'),
                props: true
            },
            {
                path: '/books/:slug',
                name: 'book.show',
                component: () => import('@/pages/home/components/modules/books/BookShow.vue'),
                props: true
            },
            {
                path: '/books/:slug/read',
                name: 'book.read',
                component: () => import('@/pages/home/components/modules/books/BookReader.vue'),
            }


        ]
    }
]

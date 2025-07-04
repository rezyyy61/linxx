<template>
    <nav
        class="sticky top-[64px] z-40 bg-white/80 dark:bg-gray-900/80 border-y border-gray-200 dark:border-gray-700 backdrop-blur text-[15px] font-semibold shadow-sm"
    >
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 overflow-x-auto scrollbar-hide">
            <ul class="flex whitespace-nowrap gap-10 py-4 md:justify-center">
                <li v-for="item in items" :key="item.path">
                    <router-link
                        :to="item.path"
                        class="relative group px-1 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition"
                        :class="{ 'text-red-600 dark:text-red-400': isActive(item.path) }"
                    >
                        {{ $t(item.label) }}
                        <span
                            class="absolute -bottom-1 left-1/2 w-0 h-[2px] bg-red-600 dark:bg-red-400 transition-all duration-300 group-hover:w-full group-hover:left-0"
                            :class="{ 'w-full left-0': isActive(item.path) }"
                        ></span>
                    </router-link>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
import { useRoute } from 'vue-router'

export default {
    name: 'SecondaryNavbar',
    setup() {
        const route = useRoute()

        const items = [
            { path: '/organizations', label: 'home.adnav.parties' },
            { path: '/articles', label: 'home.adnav.articles' },
            { path: '/topics', label: 'home.adnav.topics' },
            { path: '/media', label: 'home.adnav.media' },
            { path: '/campaigns', label: 'home.adnav.campaigns' },
            { path: '/profiles', label: 'home.adnav.profiles' },
            { path: '/events', label: 'home.adnav.events' },
            { path: '/faq', label: 'home.adnav.faq' },
            { path: '/about', label: 'home.adnav.about' },
            { path: '/announcements', label: 'home.adnav.announcements' },
            { path: '/trending', label: 'home.adnav.trending' },
            { path: '/support', label: 'home.adnav.support' },
            { path: '/contact', label: 'home.adnav.contact' }
        ]

        const isActive = (path) => route.path.startsWith(path)

        return { items, isActive }
    }
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

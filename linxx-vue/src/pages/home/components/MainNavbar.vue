<template>
    <div>
        <!-- Navbar -->
        <header
            class="sticky top-0 z-40 backdrop-blur bg-white/70 dark:bg-gray-900/80 shadow-sm border-b border-gray-200 dark:border-gray-700"
            dir="ltr"
        >
            <div class="w-full px-8 py-4 flex justify-between items-center">
                <!-- Brand -->
                <router-link
                    to="/"
                    class="font-playfair text-3xl font-extrabold tracking-wide text-red-700 dark:text-red-400 hover:opacity-90 transition"
                >
                    Linxx
                </router-link>

                <!-- Right Side: Actions -->
                <div class="flex items-center gap-6">
                    <!-- Locale Toggle -->
                    <button
                        @click="toggleLocale"
                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:underline transition"
                    >
                        {{ currentLocale.toUpperCase() }}
                    </button>

                    <!-- Theme Toggle -->
                    <button
                        @click="toggleTheme"
                        class="text-xl text-gray-600 dark:text-gray-300 hover:text-red-500 transition"
                    >
                        <Icon :icon="isDark ? moonIcon : sunIcon" class="w-6 h-6" />
                    </button>

                    <!-- Auth Section -->
                    <div class="hidden sm:flex gap-4 items-center">
                        <template v-if="!auth.user">
                            <router-link to="/login" class="nav-btn flex items-center gap-1">
                                <Icon :icon="loginIcon" class="w-5 h-5" />
                                {{ $t('home.nav.login') }}
                            </router-link>
                            <router-link
                                to="/register"
                                class="nav-btn bg-red-600 text-white hover:bg-red-700 flex items-center gap-1"
                            >
                                <Icon :icon="registerIcon" class="w-5 h-5" />
                                {{ $t('home.nav.register') }}
                            </router-link>
                        </template>

                        <template v-else>
                            <!-- Create Post Icon -->
                            <button
                                @click="showCreatePostModal = true"
                                class="text-red-600 dark:text-red-400 hover:opacity-80 transition"
                                title="Create Post"
                            >
                                <Icon :icon="createPostIcon" class="w-6 h-6" />
                            </button>

                            <!-- User Dropdown -->
                            <UserDropdown />
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Modal OUTSIDE header -->
        <CreatePostModal
            v-if="showCreatePostModal"
            @close="showCreatePostModal = false"
        />
    </div>
</template>

<script>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import createPostIcon from '@iconify-icons/mdi/pencil-plus'
import sunIcon from '@iconify-icons/mdi/white-balance-sunny'
import moonIcon from '@iconify-icons/mdi/moon-waning-crescent'
import loginIcon from '@iconify-icons/mdi/login'
import registerIcon from '@iconify-icons/mdi/account-plus'

import { usePreferencesStore } from '@/stores/preferences'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import UserDropdown from '@/pages/home/components/UserDropdown.vue'
import CreatePostModal from "@/pages/home/components/CreatePostModal.vue";

export default {
    name: 'MainNavbar',
    components: {
        Icon,
        UserDropdown,
        CreatePostModal
    },
    setup() {
        const prefs = usePreferencesStore()
        const auth = useAuthStore()
        const { isDark, locale: currentLocale } = storeToRefs(prefs)

        const showCreatePostModal = ref(false)

        return {
            toggleTheme: prefs.toggleTheme,
            toggleLocale: prefs.toggleLocale,
            currentLocale,
            isDark,
            auth,
            showCreatePostModal,
            createPostIcon,
            sunIcon,
            moonIcon,
            loginIcon,
            registerIcon
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap');

.font-playfair {
    font-family: 'Playfair Display', serif;
}

.nav-btn {
    @apply text-sm font-medium px-4 py-2 rounded-md border border-transparent transition
    text-gray-700 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-400;
}

.dropdown-item {
    @apply w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block transition;
}
</style>

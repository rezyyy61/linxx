<template>
    <header
        class="sticky top-0 z-50 backdrop-blur bg-white/70 dark:bg-gray-900/80 shadow-sm border-b border-gray-200 dark:border-gray-700"
        dir="ltr"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <!-- Brand -->
            <router-link
                to="/"
                class="text-3xl font-extrabold tracking-wide text-red-700 dark:text-red-400 hover:opacity-90 transition"
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
                    <span v-if="isDark">🌙</span>
                    <span v-else>☀️</span>
                </button>

                <!-- Auth Section -->
                <div class="hidden sm:flex gap-4 items-center">
                    <!-- If NOT logged in -->
                    <template v-if="!auth.user">
                        <router-link to="/login" class="nav-btn">
                            {{ $t('home.nav.login') }}
                        </router-link>
                        <router-link to="/register" class="nav-btn bg-red-600 text-white hover:bg-red-700">
                            {{ $t('home.nav.register') }}
                        </router-link>
                    </template>

                    <!-- If logged in -->
                    <template v-else>
                        <router-link to="/" class="nav-btn">
                            👤 {{ auth.user.name }}
                        </router-link>
                        <button @click="auth.logout" class="nav-btn text-red-600 hover:text-red-800">
                            {{ $t('home.nav.logout') }}
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import { useI18n } from 'vue-i18n'
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

export default {
    name: 'MainNavbar',
    setup() {
        const { locale } = useI18n()
        const isDark = ref(false)
        const auth = useAuthStore()

        const toggleLocale = () => {
            locale.value = locale.value === 'en' ? 'fa' : 'en'
        }

        const toggleTheme = () => {
            const html = document.documentElement
            html.classList.toggle('dark')
            isDark.value = html.classList.contains('dark')
        }

        onMounted(() => {
            isDark.value = document.documentElement.classList.contains('dark')
        })

        return {
            toggleLocale,
            toggleTheme,
            currentLocale: locale,
            isDark,
            auth
        }
    }
}
</script>

<style scoped>
.nav-btn {
    @apply text-sm font-medium px-4 py-2 rounded-md border border-transparent transition
    text-gray-700 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-400;
}
</style>

<template>
  <div>
    <!-- NAVBAR -->
    <header
        class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-[#0f1115]/90 border-b border-gray-200 dark:border-white/10"
        dir="ltr"
    >
      <div class=" mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
        <!-- Left: Brand (fully left) -->
        <router-link to="/" class="flex items-center gap-3 group">
          <!-- red circular logo -->
          <span class="inline-flex h-8 w-8 rounded-full bg-red-600 relative overflow-hidden">
            <span class="absolute inset-0 bg-gradient-to-br from-red-500/40 to-black/20"></span>
            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-5 w-5 rounded-full bg-white dark:bg-[#0f1115]"></span>
          </span>
          <span class="font-extrabold tracking-wide text-gray-900 dark:text-white text-2xl leading-none">
            Linxx
          </span>
        </router-link>

        <!-- Right: nav + toggles + auth (with bottom border like screenshot) -->
        <div class="ml-auto flex items-center gap-6 right-rail">
          <!-- Menu -->
          <nav class="hidden md:flex">
            <ul class="flex items-center gap-8">
              <li>
                <router-link to="/about" class="nav-link" exact-active-class="nav-link--active">
                  About Us
                </router-link>
              </li>
              <li>
                <router-link to="/contact" class="nav-link" exact-active-class="nav-link--active">
                  Contact Us
                </router-link>
              </li>
            </ul>
          </nav>

          <!-- Locale Toggle EN/FA -->
          <button
              @click="toggleLocale"
              class="text-sm font-semibold text-gray-700 dark:text-gray-300 hover:opacity-80 transition"
              :aria-label="`Switch language (current: ${currentLocale.toUpperCase()})`"
              title="Language"
          >
            {{ currentLocale.toUpperCase() === 'EN' ? 'EN' : 'FA' }}
          </button>

          <!-- Theme Toggle -->
          <button
              @click="toggleTheme"
              class="text-xl text-gray-700 dark:text-gray-300 hover:text-red-500 transition"
              aria-label="Toggle dark mode"
              title="Dark Mode"
          >
            <Icon :icon="isDark ? moonIcon : sunIcon" class="w-6 h-6" />
          </button>

          <!-- Auth -->
          <div class="hidden sm:flex gap-3 items-center">
            <template v-if="!auth.user">
              <router-link
                  to="/login"
                  class="inline-flex items-center gap-2 bg-white text-red-600 border border-gray-200 px-4 py-2 rounded-xl shadow-sm hover:shadow transition dark:bg-white/90"
              >
                <Icon :icon="loginIcon" class="w-5 h-5" />
                <span>{{ $t('home.nav.login') }}</span>
              </router-link>
              <router-link
                  to="/register"
                  class="inline-flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-xl shadow-sm hover:bg-red-700 transition"
              >
                <Icon :icon="registerIcon" class="w-5 h-5" />
                <span>{{ $t('home.nav.register') }}</span>
              </router-link>
            </template>
            <template v-else>
              <button
                  @click="showCreatePostModal = true"
                  class="p-2 rounded-full text-red-500 hover:text-red-400 hover:bg-black/5 dark:hover:bg-white/5 transition"
                  title="Create Post"
              >
                <Icon :icon="createPostIcon" class="w-6 h-6" />
              </button>
              <UserDropdown />
            </template>
          </div>

          <!-- Mobile: hamburger -->
          <button
              @click="mobileOpen = !mobileOpen"
              class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-black/5 dark:hover:bg-white/5"
              aria-label="Open menu"
          >
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor">
              <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile drawer -->
      <transition name="fade">
        <div v-if="mobileOpen" class="md:hidden border-t border-gray-200 dark:border-white/10">
          <div class="px-4 py-3 space-y-1">
            <router-link @click="mobileOpen=false" to="/about" class="mobile-link">About Us</router-link>
            <router-link @click="mobileOpen=false" to="/contact" class="mobile-link">Contact Us</router-link>

            <div class="flex items-center gap-4 pt-2">
              <button @click="toggleLocale; mobileOpen=false" class="mobile-link inline-flex items-center justify-center">
                {{ currentLocale.toUpperCase() === 'EN' ? 'EN' : 'FA' }}
              </button>
              <button @click="toggleTheme; mobileOpen=false" class="mobile-link inline-flex items-center gap-2">
                <Icon :icon="isDark ? moonIcon : sunIcon" class="w-5 h-5" />
                <span>Dark Mode</span>
              </button>
            </div>

            <div class="pt-2">
              <template v-if="!auth.user">
                <div class="flex gap-2">
                  <router-link
                      @click="mobileOpen=false"
                      to="/login"
                      class="flex-1 inline-flex justify-center items-center gap-2 bg-white text-red-600 font-semibold px-4 py-2 rounded-xl shadow-sm"
                  >
                    <Icon :icon="loginIcon" class="w-5 h-5" />
                    <span>{{ $t('home.nav.login') }}</span>
                  </router-link>
                  <router-link
                      @click="mobileOpen=false"
                      to="/register"
                      class="flex-1 inline-flex justify-center items-center gap-2 bg-red-600 text-white font-semibold px-4 py-2 rounded-xl shadow-sm hover:bg-red-700"
                  >
                    <Icon :icon="registerIcon" class="w-5 h-5" />
                    <span>{{ $t('home.nav.register') }}</span>
                  </router-link>
                </div>
              </template>
              <template v-else>
                <button
                    @click="showCreatePostModal = true; mobileOpen=false"
                    class="w-full inline-flex justify-center items-center gap-2 text-red-500 px-4 py-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/5"
                >
                  <Icon :icon="createPostIcon" class="w-5 h-5" />
                  <span>Create Post</span>
                </button>
                <div class="pt-2">
                  <UserDropdown />
                </div>
              </template>
            </div>
          </div>
        </div>
      </transition>
    </header>

    <!-- Modal OUTSIDE header -->
    <CreatePostModal v-if="showCreatePostModal" @close="showCreatePostModal = false" />
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
import CreatePostModal from '@/pages/home/components/CreatePostModal.vue'

export default {
  name: 'MainNavbar',
  components: { Icon, UserDropdown, CreatePostModal },
  setup () {
    const prefs = usePreferencesStore()
    const auth = useAuthStore()
    const { isDark, locale: currentLocale } = storeToRefs(prefs)

    const showCreatePostModal = ref(false)
    const mobileOpen = ref(false)

    return {
      toggleTheme: prefs.toggleTheme,
      toggleLocale: prefs.toggleLocale,
      currentLocale,
      isDark,
      auth,
      showCreatePostModal,
      mobileOpen,
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
/* link style – works in light & dark */
.nav-link {
  @apply text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition relative tracking-wide inline-flex items-center py-2;
}
.nav-link::after {
  content: "";
  @apply absolute left-0 -bottom-1 h-0.5 w-0 bg-red-500 transition-all;
}
.nav-link:hover::after { @apply w-full; }
.nav-link--active { @apply text-gray-900 dark:text-white; }
.nav-link--active::after { @apply w-full; }

/* right-rail underline like screenshot (only under the right group) */
.right-rail {
  @apply relative;
}
.right-rail::after {
  content: "";
  @apply absolute left-0 right-0 -bottom-[13px] h-px bg-gray-200 dark:bg-white/10;
}

/* mobile */
.mobile-link {
  @apply px-2 py-2 rounded-md text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5;
}

/* simple fade for mobile drawer */
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

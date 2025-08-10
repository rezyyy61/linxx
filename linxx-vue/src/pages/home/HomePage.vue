<template>
  <div
      class="font-sans min-h-screen bg-gray-100 dark:bg-gradient-to-b dark:from-[#1F2937] dark:to-[#374151] text-gray-900 dark:text-gray-100 transition-colors duration-500 ease-in-out"
  >
    <!-- ====== Top Navbar ====== -->
    <MainNavbar />

    <!-- ====== Hero Section ====== -->
    <HeroSection />

    <!-- ====== Category Tabs ====== -->
    <CategoryTabs />

    <div class="px-4 md:px-8 py-6">
      <router-view />
    </div>

    <!-- ====== Right Edge User Dock (fixed) ====== -->
    <EdgeUserDock
        :isDark="isDark"
        :locale="locale"
        @toggle-theme="toggleTheme"
        @toggle-locale="toggleLocale"
        @new-post="$emit && $emit('open-create-post')"
        @open-profile="$router.push({ name: 'profiles' })"
    />
  </div>
</template>

<script>
import MainNavbar from '@/pages/home/components/MainNavbar.vue'
import HeroSection from '@/pages/home/components/Hero/HeroSection.vue'
import CategoryTabs from '@/pages/home/components/CategoryTabs.vue'
import EdgeUserDock from '@/pages/home/components/edge/EdgeUserDock.vue'

import { usePreferencesStore } from '@/stores/preferences'
import { storeToRefs } from 'pinia'

export default {
  name: 'MainLayout',
  components: {
    MainNavbar,
    HeroSection,
    CategoryTabs,
    EdgeUserDock,
  },
  data () {
    return {
      isMobileSidebarOpen: false,
    }
  },
  setup () {
    const prefs = usePreferencesStore()
    const { isDark, locale } = storeToRefs(prefs)
    return {
      isDark,
      locale,
      toggleTheme: prefs.toggleTheme,
      toggleLocale: prefs.toggleLocale,
    }
  },
}
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 8px; }
.custom-scroll::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,.2); border-radius: 4px; }
.custom-scroll::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,.35); }

.fade-enter-active, .fade-leave-active { transition: opacity .3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform .3s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(-100%); }
</style>

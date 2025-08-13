<template>
    <div
        class="flex flex-col min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-all"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <!-- 🔺 Top Navbar -->
        <MainNavbar @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />

        <div class="flex flex-1 overflow-hidden">
            <!-- 🔹 Sidebar (Desktop) -->
            <aside class="hidden md:flex w-64 flex-col bg-white dark:bg-gray-800 shadow-md">
                <SidebarMenu />
            </aside>

            <!-- 🔹 Sidebar Drawer (Mobile) -->
            <Transition name="fade">
                <div
                    v-if="isSidebarOpen"
                    class="fixed inset-0 z-40 flex md:hidden"
                    @click.self="isSidebarOpen = false"
                >
                    <div class="w-64 bg-white dark:bg-gray-800 shadow-lg h-full" dir
                    >
                        <SidebarMenu />
                    </div>
                </div>
            </Transition>

            <!-- 🔸 Main Content Area -->
            <main class="flex-1 overflow-y-auto h-[calc(100vh-64px)]">

            <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import MainNavbar from '@/pages/home/components/MainNavbar.vue'
import SidebarMenu from './SidebarMenu.vue'

const isSidebarOpen = ref(false)


</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

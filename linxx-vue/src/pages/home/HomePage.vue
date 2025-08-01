<template>
    <div class="font-sans min-h-screen bg-gray-100 dark:bg-gradient-to-b dark:from-[#1F2937] dark:to-[#374151] text-gray-900 dark:text-gray-100 transition-colors duration-500 ease-in-out">
        <MainNavbar @toggle-sidebar="isMobileSidebarOpen = !isMobileSidebarOpen" />

        <!-- Mobile Sidebar Overlay -->
        <transition name="fade">
            <div
                v-if="isMobileSidebarOpen"
                class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
                @click="isMobileSidebarOpen = false"
            />
        </transition>

        <!-- Mobile Sidebar Drawer -->
        <transition name="slide">
            <aside
                v-if="isMobileSidebarOpen"
                class="fixed z-50 top-0 left-0 h-full w-64 bg-gray-100 dark:bg-gray-800 shadow-lg p-4 md:hidden overflow-y-auto custom-scroll"
            >
                <LeftSidebar />
            </aside>
        </transition>

        <div
            class="grid h-[calc(100vh-64px)]
    grid-cols-1
    md:grid-cols-[minmax(180px,1fr)_minmax(520px,3fr)]
    xl:grid-cols-[minmax(200px,300px)_minmax(700px,4fr)]"
        >
        <!-- Left Sidebar -->
            <aside class="hidden md:block h-full border-r ...">
                <LeftSidebar />
            </aside>

            <!-- Main Content -->
            <main class="h-full overflow-y-auto px-4 py-6 custom-scroll">
                <div class="w-full mx-auto">
                    <MainContent />
                </div>
            </main>
        </div>


    </div>
</template>

<script>
import MainNavbar from '@/pages/home/components/MainNavbar.vue'
import LeftSidebar from '@/pages/home/components/LeftSidebar.vue'
import MainContent from '@/pages/home/components/MainContent.vue'

export default {
    name: 'MainLayout',
    components: {
        MainNavbar,
        LeftSidebar,
        MainContent,
    },
    data() {
        return {
            isMobileSidebarOpen: false
        }
    }
}
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 8px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.35);
}
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
.slide-enter-active, .slide-leave-active {
    transition: transform 0.3s ease;
}
.slide-enter-from {
    transform: translateX(-100%);
}
.slide-leave-to {
    transform: translateX(-100%);
}
</style>

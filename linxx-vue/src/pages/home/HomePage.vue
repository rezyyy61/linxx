<template>
    <div class="font-sans h-screen bg-white dark:bg-gradient-to-b dark:from-[#1F2937] dark:to-[#374151] text-gray-900 dark:text-gray-100 transition-colors duration-500 ease-in-out overflow-hidden">
        <MainNavbar />

        <div class="flex w-full h-[calc(100vh-64px)]">
            <aside class="hidden lg:flex flex-col w-[360px] h-full space-y-6 px-4 border-l border-gray-200 dark:border-gray-600 overflow-y-auto custom-scroll bg-white/70 dark:bg-gray-800/70 backdrop-blur-md shadow">
                <LeftSidebar />
            </aside>

            <main class="flex-1 max-w-4xl h-full overflow-y-auto space-y-6 px-4 py-6 custom-scroll mx-auto">
                <MainContent />
            </main>

            <aside class="hidden lg:flex flex-col w-[360px] h-full space-y-6 px-4 border-r border-gray-200 dark:border-gray-600 overflow-y-auto custom-scroll bg-white/70 dark:bg-gray-800/70 backdrop-blur-md shadow">
                <RightSidebar />
            </aside>
        </div>
    </div>
</template>

<script>
import MainNavbar from '@/pages/home/components/MainNavbar.vue'
import LeftSidebar from '@/pages/home/components/LeftSidebar.vue'
import MainContent from '@/pages/home/components/MainContent.vue'
import RightSidebar from '@/pages/home/components/RightSidebar.vue'

export default {
    name: 'MainLayout',
    components: {
        MainContent,
        RightSidebar,
        LeftSidebar,
        MainNavbar
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
</style>

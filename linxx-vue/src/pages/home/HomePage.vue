<template>
    <div
        class="font-vazir min-h-screen bg-white dark:bg-gradient-to-b dark:from-gray-900 dark:to-[#1E293B] text-gray-900 dark:text-gray-100 transition-all duration-300"
        dir="ltr"
    >
        <!-- 🔺 Navbar -->
        <MainNavbar />

        <div class="h-screen flex justify-center px-4 py-6">
            <div class="w-full max-w-7xl flex flex-col lg:flex-row gap-8">
                <!-- Main Feed -->
                <div class="flex-1 space-y-6">
                    <MainContent />
                </div>

                <!-- Sidebar -->
                <aside
                    class="hidden lg:flex flex-col w-[280px] space-y-6 pl-6 border-l border-gray-300 dark:border-slate-700 h-screen sticky top-0"
                >
                    <RightSidebar />
                </aside>
            </div>
        </div>
    </div>
</template>

<script>
import MainNavbar from "@/pages/home/components/MainNavbar.vue";
import RightSidebar from "@/pages/home/components/RightSidebar.vue";
import MainContent from "@/pages/home/components/MainContent.vue";

export default {
    name: "MainLayout",
    components: {
        MainContent,
        RightSidebar,
        MainNavbar,
    },
};
</script>

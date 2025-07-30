<template>
    <div
        class="w-full px-1 sm:px-4 lg:px-6 xl:px-8 space-y-6"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <!-- Navigation Tabs -->
        <div
            class="flex overflow-x-auto border-b border-gray-200 dark:border-gray-600 text-sm font-medium bg-gray-100 dark:bg-gray-800/70 rounded-lg shadow-sm custom-scroll"
        >
            <router-link
                v-for="item in navItems"
                :key="item.key"
                :to="{ name: item.key }"
                class="px-4 py-2 whitespace-nowrap transition-all duration-200 flex-shrink-0"
                :class="{
          'text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400': $route.name === item.key,
          'text-gray-600 hover:text-red-600 dark:text-gray-300 dark:hover:text-red-400': $route.name !== item.key
        }"
            >
                {{ $t(`home.adnav.${item.key}`) }}
            </router-link>
        </div>

        <!-- Main Section (Content area) -->
        <div
            class="w-full bg-gray-200 dark:bg-gray-800/60 rounded-xl shadow p-2 sm:p-4
         sm:mx-auto "
        >

        <router-view />
        </div>

    </div>
</template>

<script>
export default {
    name: "MainContent",
    data() {
        return {
            navItems: [
                { key: "feed" },
                { key: "parties" },
                { key: "articles" },
                { key: "media" },
                { key: "campaigns" },
                { key: "profiles" },
                { key: "events" },
                { key: "announcements" },
            ],
        };
    },
};
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    height: 6px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.2);
    border-radius: 3px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background-color: rgba(100, 100, 100, 0.35);
}
</style>

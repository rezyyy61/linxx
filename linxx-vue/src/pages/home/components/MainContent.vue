<template>
    <div class="space-y-6">
        <!-- 🔹 Navigation Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700 overflow-x-auto text-sm font-medium">
            <router-link
                v-for="item in navItems"
                :key="item.key"
                :to="{ name: item.key }"
                class="px-4 py-2 whitespace-nowrap transition flex-shrink-0"
                :class="$route.name === item.key
                    ? 'text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400'
                    : 'text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400'"
            >
                {{ $t(`home.adnav.${item.key}`) }}
            </router-link>
        </div>

        <!-- 🔹 Dynamic Component Area -->
        <router-view />
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

<template>
    <div
        class="space-y-6 px-4"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <div
            class="flex border-b border-gray-200 dark:border-gray-600 overflow-x-auto text-sm font-medium bg-white dark:bg-gray-800/70 rounded-lg shadow-sm"
        >
            <router-link
                v-for="item in navItems"
                :key="item.key"
                :to="{ name: item.key }"
                class="px-4 py-2 whitespace-nowrap transition-all duration-200 flex-shrink-0"
                :class="$route.name === item.key
          ? 'text-red-600 dark:text-red-400 border-b-2 border-red-600 dark:border-red-400'
          : 'text-gray-600 hover:text-red-600 dark:text-gray-300 dark:hover:text-red-400'"
            >
                {{ $t(`home.adnav.${item.key}`) }}
            </router-link>
        </div>

        <div class="bg-white dark:bg-gray-800/60 rounded-xl shadow p-4">
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

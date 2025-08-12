<template>
  <div class="min-h-screen pt-6 pb-12 bg-gray-50 dark:bg-gray-900">
    <div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white border-b border-red-400 pb-2">لیست احزاب</h2>

        <div class="space-y-3">
            <div
                v-for="party in parties"
                :key="party.id"
                class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl shadow-sm hover:shadow-md hover:border-red-400 dark:hover:border-red-500 transition cursor-pointer"
                @click="goToParty(party.id)"
            >
                <div class="flex items-center gap-4">
                    <img
                        :src="party.logo_url"
                        class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-red-500 dark:border-red-400 shadow"
                    />
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">{{ party.group_name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ party.tagline }}</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 text-right leading-5 hidden sm:block">
                    📍 {{ party.location }}<br />
                    📅 {{ party.founded_year }}
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
export default {
    name: "PartiesList",
    methods: {

    },
};
</script>

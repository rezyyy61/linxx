<template>
    <div class="w-full">
        <div
            v-if="user"
            class="group flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 p-6 sm:p-8 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 hover:shadow-xl transition-all duration-300"
            dir="ltr"
        >
            <!-- Profile Info -->
            <div class="flex items-center gap-5 w-full sm:w-auto">
                <!-- Avatar -->
                <div class="relative">
                    <div
                        class="p-[3px] rounded-full bg-gradient-to-tr from-red-400 to-red-600 dark:from-red-500 dark:to-red-700"
                    >
                        <template v-if="user.logo_url">
                            <img
                                :src="user.logo_url"
                                class="w-20 h-20 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md transition-transform duration-300 group-hover:scale-105"
                                alt="logo"
                            />
                        </template>
                        <template v-else>
                            <div
                                class="w-20 h-20 rounded-full flex items-center justify-center font-bold text-white text-3xl border-4 border-white dark:border-gray-800 shadow-md transition-transform duration-300 group-hover:scale-105"
                                :style="{ backgroundColor: user.avatar_color || '#10B981' }"
                            >
                                {{ firstLetter }}
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Info -->
                <div>
                    <div
                        class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white group-hover:text-red-500 dark:group-hover:text-red-400 transition"
                    >
                        {{ user.group_name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ user.tagline }}
                    </div>
                </div>
            </div>

            <!-- Social / Contact Links -->
            <div
                class="flex items-center flex-wrap gap-3 mt-4 sm:mt-0 text-sm text-gray-600 dark:text-gray-300"
            >
                <a
                    v-for="(link, index) in filteredLinks"
                    :key="index"
                    :href="link.url"
                    target="_blank"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 hover:text-red-600 dark:hover:text-red-400 hover:border-red-400 transition"
                    @click.stop
                >
                    <Icon :icon="getIconName(link.title)" class="w-4 h-4" />
                    <span class="hidden sm:inline font-medium">{{ link.title }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { computed } from 'vue'

const { user } = defineProps({
    user: Object
})

const filteredLinks = computed(() => {
    return user?.links?.filter(link => link?.url)
})

const getIconName = (title) => {
    const map = {
        website: 'mdi:web',
        twitter: 'mdi:twitter',
        facebook: 'mdi:facebook',
        instagram: 'mdi:instagram',
        telegram: 'mdi:telegram',
        email: 'mdi:email-outline',
        phone: 'mdi:phone',
    }
    return map[title?.toLowerCase()] || 'mdi:link'
}

const firstLetter = computed(() => {
    return user?.group_name?.charAt(0)?.toUpperCase() || '?'
})
</script>

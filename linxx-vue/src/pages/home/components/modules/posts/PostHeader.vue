<template>
    <div class="flex items-center justify-between gap-4 p-4 border-b  bg-white dark:bg-gray-900" dir="ltr">
        <div class="flex items-center gap-4">
            <router-link
                :to="{ name: 'users.show', params: { slug: user.slug } }"
                class="flex items-center gap-4 group"
            >
                <!-- Avatar -->
                <UserAvatar
                    :src="user.logo_url"
                    :fallback="user.name"
                    :color="user.avatar_color"
                    size="sm"
                    class="w-9 h-9 shrink-0 rounded-full border border-gray-200 dark:border-gray-700"
                    style="display: inline-flex;"
                />
                <!-- User Info -->
                <div class="flex flex-col">
                    <p class="font-semibold text-gray-800 dark:text-gray-100">{{ user.name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formattedTime }}</p>
                </div>
            </router-link>
        </div>

        <!-- Dropdown Menu Button (if owner) -->
        <button
            @click="$emit('menu')"
            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition"
            aria-label="Options"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <circle cx="12" cy="5" r="1.5" />
                <circle cx="12" cy="12" r="1.5" />
                <circle cx="12" cy="19" r="1.5" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'
import relativeTimePlugin from 'dayjs/plugin/relativeTime'
import UserAvatar from "@/pages/auth/UserAvatar.vue";

dayjs.extend(relativeTimePlugin)

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    createdAt: {
        type: String,
        required: false,
        default: ''
    },
    isOwner: {
        type: Boolean,
        default: false
    }
})

const formattedTime = computed(() => {
    return dayjs(props.createdAt).fromNow()
})
</script>

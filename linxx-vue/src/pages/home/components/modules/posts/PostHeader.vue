<template>
    <div class="flex items-center justify-between gap-4 p-4 border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <!-- آواتار و نام کاربر -->
        <div class="flex items-center gap-4">
            <img
                :src="avatarUrl"
                alt="User avatar"
                loading="lazy"
                class="w-10 h-10 rounded-full object-cover shadow"
            />
            <div class="flex flex-col">
                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ user.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formattedTime }}</p>
            </div>
        </div>

        <!-- سه نقطه عمودی (نمایش فقط برای کاربر خودش) -->
        <button
            v-if="isOwner"
            @click="$emit('menu')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
            aria-label="Options"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
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

dayjs.extend(relativeTimePlugin)

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    createdAt: {
        type: String,
        required: true
    },
    isOwner: {
        type: Boolean,
        default: false
    }
})

const avatarUrl = computed(() => {
    return props.user.avatar
        ? props.user.avatar
        : `https://i.pravatar.cc/150?u=${props.user.id || props.user.name}`
})

const formattedTime = computed(() => {
    return dayjs(props.createdAt).fromNow()
})
</script>

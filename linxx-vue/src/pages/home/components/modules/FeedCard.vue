<template>
    <div class="feed-card bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-3xl p-6 shadow-lg space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <img :src="post.user.avatar" class="w-10 h-10 rounded-full object-cover" />
            <div>
                <p class="font-semibold text-gray-900 dark:text-white leading-none">{{ post.user.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ relativeTime }}</p>
            </div>
        </div>

        <!-- Text -->
        <p v-if="post.text" class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ post.text }}</p>

        <!-- Media -->
        <div v-if="post.media.length">
            <!-- Images Grid -->
            <template v-if="mediaType === 'image'">
                <div :class="['grid gap-2 overflow-hidden rounded-xl', post.media.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
                    <img
                        v-for="(img, i) in post.media"
                        :key="i"
                        :src="img.url"
                        class="w-full h-48 object-cover rounded-lg hover:brightness-90 transition"
                    />
                </div>
            </template>

            <!-- Video -->
            <template v-else-if="mediaType === 'video'">
                <video :src="post.media[0].url" controls class="w-full rounded-xl border dark:border-gray-700"></video>
            </template>

            <!-- Audio -->
            <template v-else-if="mediaType === 'audio'">
                <audio :src="post.media[0].url" controls class="w-full"></audio>
            </template>

            <!-- Files -->
            <template v-else-if="mediaType === 'file'">
                <ul class="space-y-1 text-sm text-blue-600 dark:text-blue-400">
                    <li v-for="(f, i) in post.media" :key="i" class="flex items-center gap-1">
                        📎 <span>{{ f.meta.original_name }}</span>
                    </li>
                </ul>
            </template>
        </div>

        <!-- Actions -->
        <div class="border-t dark:border-gray-700 pt-3 flex justify-between text-sm text-gray-600 dark:text-gray-400 select-none">
            <button class="flex items-center gap-1 hover:text-blue-600"><HeartIcon class="w-4 h-4" /> {{ $t('feed.like') }}</button>
            <button class="flex items-center gap-1 hover:text-blue-600"><MessageCircleIcon class="w-4 h-4" /> {{ $t('feed.comment') }}</button>
            <button class="flex items-center gap-1 hover:text-blue-600"><ShareIcon class="w-4 h-4" /> {{ $t('feed.share') }}</button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import {
    Heart as HeartIcon,
    MessageCircle as MessageCircleIcon,
    Share2 as ShareIcon
} from 'lucide-vue-next'

// --- helpers: ساده‌سازی زمان نسبی بدون وابستگی خارجی ---
function getRelativeTime(date) {
    const now = new Date()
    const diff = (now - new Date(date)) / 1000 // به ثانیه
    const rtf = new Intl.RelativeTimeFormat('fa', { numeric: 'auto' })
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60,
        second: 1
    }
    for (const [unit, seconds] of Object.entries(intervals)) {
        if (diff >= seconds || unit === 'second') {
            const value = Math.round(-diff / seconds)
            return rtf.format(value, unit)
        }
    }
}

// props (پست از API یا دادهٔ فیک)
const props = defineProps({ post: Object })

// دادهٔ فیک اگر prop نیامد
const fakePost = {
    id: 1,
    text: 'این یک پست نمونه است جهت پیش‌نمایش کارت فید.',
    created_at: new Date(Date.now() - 7200_000),
    user: {
        id: 7,
        name: 'کاربر نمونه',
        avatar: 'https://i.pravatar.cc/80?img=3'
    },
    media: [
        { type: 'image', url: 'https://picsum.photos/400/250?random=1', meta: { original_name: 'sample.jpg' } },
        { type: 'image', url: 'https://picsum.photos/400/250?random=2', meta: { original_name: 'sample2.jpg' } }
    ]
}

const post = computed(() => props.post || fakePost)
const mediaType = computed(() => (post.value.media.length ? post.value.media[0].type : null))
const relativeTime = computed(() => getRelativeTime(post.value.created_at))
</script>

<style scoped>
.feed-card img {
    @apply transition-transform duration-200 ease-in-out;
}
</style>

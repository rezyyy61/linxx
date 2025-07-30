<template>
    <div class="comment-container flex items-start relative">
        <!-- Avatar -->
        <UserAvatar
            :src="avatarSrc"
            :fallback="avatarFallback"
            :color="avatarColor"
            :size="depth > 0 ? 'sm' : 'md'"
            class="mt-1"
        />

        <!-- Content -->
        <div class="flex flex-col max-w-[85%] w-full font-sans text-[14px] leading-relaxed ml-3">
            <div class="bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-xl shadow-sm transition hover:shadow-md">
                <!-- Name + Time + Menu -->
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-2">
                    {{ displayName }}
                    <div class="text-gray-400 text-xs flex items-center gap-1">
                        • {{ formatTime(comment.created_at) }}
                        <CommentDropdown
                            :comment-id="comment.id"
                            :comment-content="rawText"
                            :comment-user-id="comment.user?.id ?? comment.comment_user_id"
                            :post-user-id="comment.post_user_id"
                            :current-user-id="auth.user?.id || auth.user?.data?.id"
                            :post-id="comment.post_id"
                            @edit="emit('edit', comment)"
                            @delete="emit('delete', comment)"
                            @report="$emit('report', comment)"
                        />
                    </div>
                </div>

                <!-- Text -->
                <p
                    :dir="detectDirection(rawText)"
                    class="text-[15px] leading-[1.8] text-gray-800 dark:text-gray-100 whitespace-pre-wrap break-words"
                >
                    <template v-if="hasMention">
                        <router-link
                            :to="`/user/${mentionName}`"
                            class="text-blue-600 text-sm font-normal hover:underline"
                        >
                            @{{ mentionName }}
                        </router-link>
                        {{ restOfText }}
                    </template>
                    <template v-else>
                        {{ rawText }}
                    </template>
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-5 text-[12px] text-gray-500 dark:text-gray-400 mt-2 px-1">
                <button
                    class="hover:underline flex items-center gap-1"
                    @click="isLoggedIn && $emit('like', comment)"
                    :disabled="!isLoggedIn"
                >
                    <ThumbsUp
                        class="w-4 h-4"
                        :class="{
              'text-blue-600 fill-blue-600': comment.liked || comment.liked_by_user,
              'text-gray-500': !(comment.liked || comment.liked_by_user)
            }"
                    />
                    <span>{{ comment.liked || comment.liked_by_user ? 'Liked' : 'Like' }}</span>
                    <span v-if="comment.likes || comment.like_count">
            ({{ comment.likes || comment.like_count }})
          </span>
                </button>

                <button class="hover:underline" @click="replyTo(comment)" :disabled="!isLoggedIn">
                    Reply <span v-if="comment.replies_count">({{ comment.replies_count }})</span>
                </button>

                <button
                    v-if="depth === 0 && hasReplies"
                    class="hover:underline text-blue-500 dark:text-blue-400"
                    @click="$emit('toggle-replies', comment.id)"
                >
                    {{ showReplies ? 'Hide Replies' : 'Show Replies' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { ThumbsUp } from 'lucide-vue-next'
import dayjs from 'dayjs'
import { useAuthStore } from '@/stores/auth'
import UserAvatar from '@/pages/auth/UserAvatar.vue'
import CommentDropdown from '@/pages/home/components/modules/posts/Footer/Comments/CommentDropdown.vue'

const props = defineProps({
    comment: Object,
    replies: { type: Array, default: () => [] },
    depth: { type: Number, default: 0 },
    showReplies: Boolean,
    hasReplies: Boolean
})

const emit = defineEmits(['like', 'reply', 'toggle-replies', 'edit', 'delete', 'report'])

const auth = useAuthStore()
const isLoggedIn = computed(() => !!auth.user)

// Computed display values
const rawText = computed(() => props.comment.text || props.comment.content || '')
const displayName = computed(() => props.comment.name || props.comment.user?.name || 'Anonymous')

const hasMention = computed(() => rawText.value.startsWith('@'))
const mentionName = computed(() =>
    hasMention.value ? rawText.value.split(' ')[0]?.substring(1) : ''
)
const restOfText = computed(() =>
    hasMention.value ? rawText.value.split(' ').slice(1).join(' ') : rawText.value
)

// Avatar helpers
const avatarSrc = computed(() => props.comment.avatar || props.comment.user?.avatar || null)
const avatarFallback = computed(() => displayName.value[0] ?? 'U')
const avatarColor = computed(() =>
    props.comment.avatarColor || props.comment.user?.avatar_color || '#10B981'
)

function formatTime(time) {
    return dayjs(time).fromNow()
}

function detectDirection(text = '') {
    const rtlPattern = /[\u0600-\u06FF]/
    return rtlPattern.test(text) ? 'rtl' : 'ltr'
}

function replyTo(comment) {
    const id = comment.parent_id ?? comment.id
    emit('reply', { id, name: displayName.value })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

.comment-container {
    font-family: Vazirmatn, Inter, sans-serif;
}

[dir="ltr"] {
    font-family: Inter, sans-serif;
}

[dir="rtl"] {
    font-family: Vazirmatn, sans-serif;
}
</style>

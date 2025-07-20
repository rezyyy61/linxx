<template>
    <div
        class="comment-container"
        :class="[
      'flex items-start gap-3 relative',
      isReply ? 'ml-6 pl-3 border-l border-gray-300 dark:border-gray-600' : ''
    ]"
    >
        <!-- Avatar -->
        <img
            :src="comment.avatar || `https://i.pravatar.cc/40?u=${comment.user?.id || 'guest'}`"
            alt="avatar"
            :class="isReply ? 'w-7 h-7' : 'w-9 h-9'"
            class="rounded-full object-cover mt-1"
        />

        <!-- Content -->
        <div class="flex flex-col max-w-[85%] w-full font-sans text-[14px] leading-relaxed">
            <div class="bg-white dark:bg-gray-800 px-4 py-3 rounded-xl shadow-sm relative">
                <!-- Edit/Delete buttons -->
                <div
                    v-if="comment.user?.id === auth.user?.id"
                    class="absolute -top-2 right-2 flex space-x-1 z-10"
                >
                    <button
                        @click="startEdit(comment)"
                        class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </button>

                    <button
                        @click="confirmDelete"
                        class="p-1.5 rounded-full hover:bg-red-100 dark:hover:bg-red-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Edit Mode -->
                <div v-if="editingId === comment.id">
          <textarea
              v-model="editText"
              class="w-full bg-transparent text-sm text-gray-800 dark:text-gray-100 outline-none resize-none"
              rows="2"
          />
                    <div class="flex gap-3 mt-2 text-xs">
                        <button class="text-blue-600 font-medium" @click="saveEdit(comment)" :disabled="saving">
                            {{ saving ? 'Saving...' : 'Save' }}
                        </button>
                        <button class="text-gray-400 hover:underline" @click="cancelEdit">Cancel</button>
                    </div>
                    <p v-if="error" class="text-red-500 text-xs mt-1">Failed to save comment.</p>
                </div>

                <!-- Normal View -->
                <div v-else>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ comment.name || comment.user?.name || 'Anonymous' }}
                    </p>
                    <p
                        :dir="detectDirection(rawText)"
                        class="text-[16px] leading-relaxed text-gray-800 dark:text-gray-100 whitespace-pre-wrap break-words"
                    >
                        <template v-if="hasMention">
                            <span class="text-blue-600 font-semibold">@{{ mentionName }}</span>
                            {{ restOfText }}
                        </template>
                        <template v-else>
                            {{ rawText }}
                        </template>
                    </p>
                </div>
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
                        :class="(comment.liked || comment.liked_by_user) ? 'text-blue-600 fill-blue-600' : 'text-gray-500'"
                    />
                    <span>{{ (comment.liked || comment.liked_by_user) ? 'Liked' : 'Like' }}</span>
                    <span v-if="comment.likes || comment.like_count">({{ comment.likes || comment.like_count }})</span>
                </button>

                <button class="hover:underline" @click="replyTo(comment)" :disabled="!isLoggedIn">
                    Reply <span v-if="comment.replies_count">({{ comment.replies_count }})</span>
                </button>

                <span class="opacity-70">{{ formatTime(comment.created_at) }}</span>

                <button
                    v-if="comment.children && comment.children.length"
                    class="hover:underline text-blue-500 dark:text-blue-400"
                    @click="toggleReplies"
                >
                    {{ showReplies ? 'Hide Replies' : 'Show Replies' }}
                </button>
            </div>

            <!-- Nested Replies -->
            <div
                v-if="Array.isArray(comment.children) && comment.children.length && showReplies"
                class="mt-4 space-y-4"
            >
                <CommentItem
                    v-for="reply in comment.children"
                    :key="reply.id"
                    :comment="reply"
                    :is-reply="true"
                    @like="$emit('like', $event)"
                    @reply="$emit('reply', $event)"
                    @edit="startEdit"
                    @delete="$emit('delete', $event)"
                    @refresh="$emit('refresh')"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ThumbsUp } from 'lucide-vue-next'
import dayjs from 'dayjs'
import { useAuthStore } from '@/stores/auth'
import { useCommentActions } from '@/stores/comments/useCommentActions'
import CommentItem from './CommentItem.vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()


const props = defineProps({
    comment: Object,
    isReply: { type: Boolean, default: false }
})

const emit = defineEmits(['like', 'reply', 'edit', 'delete', 'refresh'])

const auth = useAuthStore()
const isLoggedIn = computed(() => !!auth.user)

const showReplies = ref(true)

function toggleReplies() {
    showReplies.value = !showReplies.value
}

function replyTo(comment) {
    showReplies.value = true
    emit('reply', {
        id: comment.id,
        name: comment.name || comment.user?.name || 'Anonymous'
    })
}

function detectDirection(text = '') {
    const rtlPattern = /[\u0600-\u06FF]/
    return rtlPattern.test(text) ? 'rtl' : 'ltr'
}

function formatTime(time) {
    return dayjs(time).fromNow()
}

// === Mention Handling ===
const rawText = computed(() => props.comment.text || props.comment.content || '')

const hasMention = computed(() => rawText.value.startsWith('@'))

const mentionName = computed(() => {
    if (!hasMention.value) return ''
    return rawText.value.split(' ')[0]?.substring(1) || ''
})

const restOfText = computed(() => {
    if (!hasMention.value) return rawText.value
    return rawText.value.split(' ').slice(1).join(' ')
})

function confirmDelete() {
    const message = t('post.deleteConfirm')
    if (window.confirm(message)) {
        emit('delete', props.comment.id)
    }
}


const {
    editingId,
    editText,
    saving,
    error,
    startEdit,
    cancelEdit,
    saveEdit
} = useCommentActions()
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

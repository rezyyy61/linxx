<template>
    <div class="w-full h-full flex flex-col overflow-hidden relative">
        <!-- Scrollable Content -->
        <div
            ref="scrollEl"
            class="flex-1 overflow-y-auto px-4 pt-4 pb-4 space-y-6 custom-scroll"
        >
            <!-- Post Content -->
            <div class="space-y-4">
                <PostHeader
                    :user="post.user"
                    :created-at="post.created_at"
                    :is-owner="false"
                />
                <PostTextClamp
                    v-if="post.text"
                    :text="post.text"
                    :truncate="true"
                    :limits="{ sm: 160, md: 320, lg: 600 }"
                />
                <PostMedia :post="post" />
            </div>

            <!-- Comments List -->
            <div class="space-y-3">
                <PostCommentList
                    :comments="comments"
                    :editing-id="editingId"
                    :edit-text="editText"
                    :expanded-replies="expandedReplies"
                    :collapsed-comments="collapsedComments"
                    @like="$emit('like', $event)"
                    @reply="handleReply"
                    @cancel-reply="clearReply"
                    @edit="startEdit"
                    @cancel-edit="cancelEdit"
                    @save-edit="saveEdit"
                    @toggle-collapse="toggleCollapse"
                    @delete="$emit('delete', $event)"
                />

                <p v-if="!comments.length" class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                    {{ $t('post.noComments') || 'No comments yet.' }}
                </p>

                <div ref="bottomSpacerEl" class="h-4"></div>
            </div>
        </div>

        <!-- Comment Input (Fixed Bottom) -->
        <div
            ref="inputEl"
            class="sticky bottom-0 bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-3"
        >
            <PostCommentInput
                :post-id="post.id"
                v-model="inputVal"
                :replyingTo="replyTo"
                @send="handleSend"
                @cancel-reply="clearReply"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import PostCommentInput from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentInput.vue'
import PostCommentList from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentList.vue'
import { useCommentActions } from '@/stores/comments/useCommentActions'

const props = defineProps({
    post: Object,
    comments: Array,
    modelValue: String,
    visible: Boolean
})

const emit = defineEmits([
    'update:modelValue',
    'send',
    'like',
    'delete'
])

// Local input & reply state
const inputVal = ref(props.modelValue || '')
const replyTo = ref(null)

// Watchers for external model sync
watch(() => props.modelValue, (v) => {
    if (v !== inputVal.value) inputVal.value = v
})
watch(inputVal, (val) => {
    emit('update:modelValue', val)
})

// Comment Actions (Edit, Reply, etc.)
const {
    editingId,
    editText,
    startEdit,
    cancelEdit,
    saveEdit,
    expandedReplies,
    expandReply,
    collapseReply,
    clearReply: clearReplyList,
    collapsedComments,
    toggleCollapse,
} = useCommentActions(ref(props.comments))

function handleSend(payload) {
    emit('send', payload)
    inputVal.value = ''
    clearReply()
    nextTick(() => scrollToBottom(true))
}

function handleReply(comment) {
    replyTo.value = comment
    expandReply(comment.id)
    nextTick(() => scrollToBottom(true))
}

function clearReply() {
    replyTo.value = null
    clearReplyList()
}

// Scroll handling
const scrollEl = ref(null)
const bottomSpacerEl = ref(null)
const inputEl = ref(null)

function scrollToBottom(smooth = false) {
    const spacer = bottomSpacerEl.value
    spacer?.scrollIntoView({ behavior: smooth ? 'smooth' : 'auto' })
}

watch(() => props.comments.length, () => {
    nextTick(() => scrollToBottom(true))
})

onMounted(() => {
    nextTick(() => scrollToBottom(false))
})

defineExpose({ scrollEl })
</script>

<style scoped>
.custom-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.35) rgba(0,0,0,0.08);
}
.custom-scroll::-webkit-scrollbar {
    width: 8px;
}
.custom-scroll::-webkit-scrollbar-track {
    background-color: rgba(0,0,0,0.08);
    border-radius: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.35);
    border-radius: 4px;
}
.custom-scroll:hover::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.55);
}
</style>

<template>
    <div
        class="w-full h-full min-h-0 lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(400px,620px)]
         lg:divide-x lg:divide-gray-200 dark:lg:divide-gray-700"
    >
        <!-- Left side: post content -->
        <div class="hidden lg:block overflow-y-auto p-4 xl:p-6 space-y-4">
            <PostHeader
                :user="post.user"
                :created-at="post.created_at"
                :is-owner="false"
            />
            <PostTextClamp
                v-if="post.text"
                :text="post.text"
                :truncate="true"
                :limits="{ sm: 100, md: 100, lg: 200 }"
            />
            <PostMedia :post="post" base-url="http://localhost:8080" />

        </div>

        <!-- Right side: comments -->
        <div class="flex flex-col h-full min-h-0 overflow-hidden lg:bg-gray-50/60 dark:lg:bg-gray-900/40">
            <!-- Header -->
            <div
                class="hidden lg:flex shrink-0 items-center justify-between px-4 xl:px-6 py-3 border-b dark:border-gray-700 text-sm sticky top-0 z-10 bg-inherit backdrop-blur-sm"
            >
                <span class="font-medium">
                    {{ $t('post.comments') }} ({{ totalCount }})
                </span>
            </div>

            <!-- Comment list -->
            <div
                ref="scrollEl"
                class="overflow-y-auto max-h-[50vh] px-4 xl:px-6 pt-4 space-y-3 pb-10 custom-scroll"
            >
                <PostCommentList
                    :comments="comments"
                    :editing-id="editingId"
                    :edit-text="editText"
                    :expanded-replies="expandedReplies"
                    :collapsed-comments="collapsedComments"
                    @like="$emit('like', $event)"
                    @reply="expandReply"
                    @cancel-reply="clearReply"
                    @edit="startEdit"
                    @cancel-edit="cancelEdit"
                    @save-edit="saveEdit"
                    @toggle-collapse="toggleCollapse"
                    @delete="$emit('delete', $event)"
                />
            </div>

            <!-- Input -->
            <PostCommentInput
                :post-id="post.id"
                v-model="inputVal"
                :replyingTo="replyToComment"
                @send="handleSend"
                @cancel-reply="clearReply(replyToComment?.id)"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import PostCommentInput from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentInput.vue'
import PostCommentList from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentList.vue'

import { useCommentActions } from '@/stores/comments/useCommentActions'

const props = defineProps({
    post: Object,
    comments: Array,
    modelValue: String,
    totalCount: Number
})

const emit = defineEmits([
    'like',
    'send',
    'update:modelValue',
    'delete'
])

const inputVal = ref(props.modelValue || '')
const replyToComment = ref(null)

watch(() => props.modelValue, (val) => {
    if (val !== inputVal.value) inputVal.value = val
})

watch(inputVal, (val) => {
    emit('update:modelValue', val)
})

// === Comment Actions ===
const {
    editingId,
    editText,
    saving,
    error,
    startEdit,
    cancelEdit,
    saveEdit,
    expandedReplies,
    expandReply,
    collapseReply,
    clearReply,
    isReplyOpen,
    collapsedComments,
    toggleCollapse,
    isCollapsed
} = useCommentActions(ref(props.comments))

function handleSend(payload) {
    emit('send', payload)
    replyToComment.value = null
}

const scrollEl = ref(null)
defineExpose({ scrollEl })
</script>

<style scoped>
:deep(.custom-scroll) {
    scrollbar-gutter: stable both-edges;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.35) rgba(0, 0, 0, 0.08);
}
:deep(.custom-scroll::-webkit-scrollbar) {
    width: 12px;
    height: 12px;
}
:deep(.custom-scroll::-webkit-scrollbar-track) {
    background-color: rgba(0, 0, 0, 0.08);
    border-radius: 4px;
}
:deep(.custom-scroll::-webkit-scrollbar-thumb) {
    background-color: rgba(0, 0, 0, 0.35);
    border-radius: 4px;
}
:deep(.custom-scroll:hover::-webkit-scrollbar-thumb) {
    background-color: rgba(0, 0, 0, 0.55);
}
</style>

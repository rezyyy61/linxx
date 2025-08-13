<template>
    <div class="fixed inset-0 z-50 flex flex-col bg-white dark:bg-gray-900 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center px-4 py-3 border-b dark:border-gray-700 bg-white dark:bg-gray-800 shrink-0">
            <button @click="$emit('close')" class="text-gray-500 hover:text-black dark:text-gray-300 text-xl">
                ←
            </button>
            <span class="ml-4 font-medium text-base">{{ $t('post.comments') }}</span>
        </div>

        <!-- Scrollable: post + comments (single scroll area) -->
        <div class="flex-1 overflow-y-auto px-4 space-y-6 custom-scroll pb-28">
            <!-- Post section -->
            <div class="pt-4 pb-4 border-b border-gray-200 dark:border-gray-700 space-y-4">
                <PostHeader :user="post.user" :created-at="post.created_at" :is-owner="false" />
                <PostTextClamp v-if="post.text" :text="post.text" :truncate="false" />
                <PostMedia :post="post" base-url="http://localhost:8080" />
                <PostFooter :post="post" @like="noop" @comment="noop" @share="noop" />
            </div>

            <!-- Comments + Input -->
            <div>
                <CommentThread :post-id="post.id" />
            </div>
        </div>
    </div>
</template>

<script setup>
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostFooter from '@/pages/home/components/modules/posts/PostFooter.vue'
import CommentThread from '@/pages/home/components/modules/posts/Footer/Comments/CommentThread.vue'

const props = defineProps({
    post: Object
})

function noop() {
    // no action inside modal for footer buttons
}
</script>

<style scoped>
:deep(.custom-scroll) {
    scrollbar-gutter: stable both-edges;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.35) rgba(0, 0, 0, 0.08);
}
:deep(.custom-scroll::-webkit-scrollbar) {
    width: 10px;
}
:deep(.custom-scroll::-webkit-scrollbar-thumb) {
    background-color: rgba(0, 0, 0, 0.35);
    border-radius: 4px;
}
</style>

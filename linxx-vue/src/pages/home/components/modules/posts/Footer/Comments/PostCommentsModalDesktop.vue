<template>
    <div
        class="w-full h-full min-h-0 lg:grid lg:grid-cols-[minmax(0,1.5fr)_minmax(400px,720px)]
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
        <div class="flex w-full flex-col h-full min-h-0 overflow-hidden lg:bg-gray-50/60 dark:lg:bg-gray-900/40">
            <!-- Header -->
            <div
                class="hidden lg:flex shrink-0 items-center justify-between px-4 xl:px-6 py-3 border-b dark:border-gray-700 text-sm sticky top-0 z-10 bg-inherit backdrop-blur-sm"
            >
        <span class="font-medium">
  {{ $t('post.comments') }}
  <span v-if="totalCount > 0" class="text-gray-500 dark:text-gray-400 font-normal">({{ totalCount }})</span>
</span>
            </div>

            <!-- Comments -->
            <div class="flex-1 w-full overflow-y-auto px-4 xl:px-6 pt-4 space-y-3 pb-10 custom-scroll">
                <CommentThread :postId="post.id" />
            </div>
        </div>
    </div>
</template>

<script setup>
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import CommentThread from "@/pages/home/components/modules/posts/Footer/Comments/CommentThread.vue";
import {useComments} from "@/stores/comments/useComments";


const props = defineProps({
    post: Object,
    totalCount: Number
})

const { totalCount } = useComments(props.post.id)
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

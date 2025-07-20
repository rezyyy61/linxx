<template>
    <div
        class="flex justify-around items-center px-4 py-2 border-t dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 select-none"
    >
        <button
            class="flex items-center gap-1 hover:text-red-500 transition-all duration-200"
            @click="$emit('like')"
        >
            <ThumbsUp class="w-5 h-5" />
            <span>Like</span>
        </button>
        <button
            class="flex items-center gap-1 hover:text-blue-500 transition-all duration-200"
            @click="$emit('comment')"
        >
            <MessageCircle class="w-5 h-5" />
            <span>Comment</span>
        </button>
        <button
            class="flex items-center gap-1 hover:text-green-500 transition-all duration-200"
            @click="$emit('share')"
        >
            <Share2 class="w-5 h-5" />
            <span>Share</span>
        </button>
    </div>
</template>

<script setup>
import { ThumbsUp, MessageCircle, Share2 } from 'lucide-vue-next'
defineProps({ post: Object })
defineEmits(['like', 'comment', 'share'])
</script>

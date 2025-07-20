<template>
    <div
        class="bg-white/90 dark:bg-gray-800/70 border border-gray-200 dark:border-gray-600 rounded-2xl shadow-sm dark:shadow-md transition-all duration-300 overflow-hidden"
    >
        <PostHeader
            :user="post.user"
            :created-at="post.created_at"
            :is-owner="currentUserId && post.user.id === currentUserId"
        />

        <div class="p-4 space-y-4">
            <PostTextClamp
                v-if="post.text"
                :text="post.text"
                v-model:expanded="showFull"
                :limits="{ sm: 100, md: 300, lg: 400 }"
                :truncate="true"
            />


            <PostMedia :post="post" base-url="http://localhost:8080" />

        </div>
        <PostFooter
            :post="post"
            @like="handleLike"
            @comment="toggleComments"
            @share="handleShare"
        />
        <PostCommentsModal
            :visible="showModal"
            :post="post"
            @close="showModal = false"
        />
    </div>
</template>

<script setup>
import {ref, computed, watch} from 'vue'
import { useAuthStore } from '@/stores/auth'

import PostCommentsModal from "@/pages/home/components/modules/posts/Footer/Comments/PostCommentsModal.vue";
import PostFooter from "@/pages/home/components/modules/posts/PostFooter.vue";
import PostMedia from "@/pages/home/components/modules/posts/PostMedia.vue";
import PostHeader from "@/pages/home/components/modules/posts/PostHeader.vue";
import PostTextClamp from "@/pages/home/components/modules/posts/PostTextClamp.vue";
import {CommentAPI} from "@/stores/comments";


const props = defineProps({ post: Object })
const authStore = useAuthStore()

const currentUserId = computed(() => authStore.user?.id ?? null)
const showFull = ref(false)
const showComments = ref(false)
const showModal = ref(false)
const mappedComments = ref([])

async function loadComments() {
    try {
        const response = await CommentAPI.list(props.post.id)
        const raw = response.data?.data || []

        mappedComments.value = raw.map(c => ({
            id: c.id,
            name: c.user?.name || 'Anonymous',
            avatar: c.user?.avatar || `https://i.pravatar.cc/40?u=${c.user?.id || 'guest'}`,
            text: c.content
        }))
    } catch (err) {
        console.error('Failed to load comments', err)
    }
}

watch(showComments, v => {
    if (v && !mappedComments.value.length) {
        loadComments()
    }
})

function toggleComments() {
    showModal.value = true
    if (!mappedComments.value.length) {
        loadComments()
    }
}
function handleLike() {}
function handleShare() {}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity .15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

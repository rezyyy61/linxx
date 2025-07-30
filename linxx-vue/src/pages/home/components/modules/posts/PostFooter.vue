<template>
    <!-- 👍 Like comment  summary -->
    <div class="flex justify-between items-center px-4 pt-3 text-xs text-gray-500 dark:text-gray-400">
        <!-- Likes -->
        <div v-if="likesCount > 0" class="flex items-center gap-1">
            <ThumbsUp class="w-4 h-4 text-red-500" />
            <span>
      <template v-for="(user, index) in previewNames" :key="user.id">
        {{ user.name }}<span v-if="index < previewNames.length - 1">, </span>
      </template>
      <span v-if="othersCount > 0"> and {{ othersCount }} others</span>
      liked this
    </span>
        </div>

        <!-- Comments -->
        <div v-if="commentsCount > 0" class="flex items-center gap-1">
            <MessageCircle class="w-4 h-4 text-blue-500" />
            <span>{{ commentsCount }} comment<span v-if="commentsCount > 1">s</span></span>
        </div>
    </div>


    <div class="mt-2 border-t dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 select-none">
        <!-- Action buttons -->
        <div class="flex justify-around items-center px-4 py-2">
            <!-- Like -->
            <button
                class="flex items-center gap-1 transition-all duration-200"
                :class="isLiked ? 'text-red-500' : 'hover:text-red-500'"
                @click="handleLike"
                :disabled="!isLoggedIn"
            >
                <ThumbsUp :class="['w-5 h-5 transition', isLiked ? 'fill-current text-red-500' : '']" />
            </button>

            <!-- Comment -->
            <button
                class="flex items-center gap-1 hover:text-blue-500 transition-all duration-200"
                @click="$emit('comment')"
            >
                <MessageCircle class="w-5 h-5" />
                <span>Comment</span>
            </button>

            <!-- Share -->
            <button
                ref="shareButton"
                class="flex items-center gap-1 hover:text-green-500 transition-all duration-200"
                @click="emitShare"
            >
                <Share2 class="w-5 h-5" />
                <span>Share</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import {ref, computed, watch} from 'vue'
import { useAuthStore } from '@/stores/auth'
import { ThumbsUp, MessageCircle, Share2 } from 'lucide-vue-next'
import { toggleLike } from '@/stores/likes'

const props = defineProps({
    post: {
        type: Object,
        required: true
    }
})

const authStore = useAuthStore()
const emit = defineEmits(['like', 'comment', 'share'])

const likesCount = ref(props.post.likes_count ?? 0)
const commentsCount = ref(props.post.comments_count ?? 0)
const likesPreview = ref(props.post.likes_preview ?? [])
const shareButton = ref(null)
const isLoggedIn = computed(() => !authStore.user?.id)

const handleLike = async () => {
    try {
        const response = await toggleLike(props.post.id)
        isLiked.value = response.liked
        likesCount.value = response.likes_count
    } catch (e) {
        console.error('❌ Like failed', e)
    }
}

function emitShare() {
    const rect = shareButton.value.getBoundingClientRect()
    emit('share', {
        top: rect.top + window.scrollY,
        left: rect.left + rect.width / 2,
    })
}

const previewNames = computed(() => likesPreview.value.slice(0, 2))
const othersCount = computed(() => likesCount.value - previewNames.value.length)
const initialIsLiked = ref(props.post.is_liked ?? false)
const isLiked = ref(initialIsLiked.value)

watch(() => props.post.is_liked, (val) => {
    console.log(props.post)
    isLiked.value = val
})

</script>

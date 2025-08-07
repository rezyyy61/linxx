<template>
    <div class="w-full bg-white/90 dark:bg-gray-800/70  dark:border-gray-600  dark:shadow-md transition-all duration-300 overflow-hidden">
        <PostHeader
            :user="post.user"
            v-bind="post.created_at ? { createdAt: post.created_at } : {}"
            :is-owner="currentUserId && post.user.id === currentUserId"
        />
        <div>
            <PostPublicationCard v-if="isPublication" :post="post" />
            <template v-else>
                <PostTextClamp
                    v-if="post.text"
                    :rich-text="post.richText"
                    :full-html="post.text"
                    v-model:expanded="showFull"
                    :limits="{ sm: 100, md: 300, lg: 400 }"
                    :truncate="true"
                    :hide-controls="false"
                />
                <PostMedia :post="post" :base-url="apiBaseUrl" />
            </template>
        </div>
        <PostFooter
            v-if="!props.embed"
            :post="post"
            @like="handleLike"
            @comment="toggleComments"
            @share="handleShare"
        />
        <PostCommentsModal
            :visible="showModal"
            :post="post"
            :position="modalPosition"
            @close="showModal = false"
        />
        <ShareModal
            :visible="showShare"
            :url="shareUrl"
            :position="modalPosition"
            @close="showShare = false"
            @embed="openEmbed"
        />
        <EmbedShareModal
            v-if="!props.embed"
            :visible="showEmbedModal"
            :shared-post="post"
            @close="showEmbedModal = false"
            @submitted="handleEmbedSubmit"
        />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import PostCommentsModal from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentsModal.vue'
import PostFooter from '@/pages/home/components/modules/posts/PostFooter.vue'
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import PostPublicationCard from '@/pages/home/components/modules/posts/media/PostPublicationCard.vue'
import ShareModal from '@/pages/home/components/modules/posts/Footer/share/ShareModal.vue'
import EmbedShareModal from '@/pages/home/components/modules/posts/Footer/share/EmbedShareModal.vue'

const props = defineProps({
    post: Object,
    embed: { type: Boolean, default: false }
})

const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id ?? null)

const showFull = ref(false)
const showModal = ref(false)
const mappedComments = ref([])
const showShare = ref(false)
const modalPosition = ref({ top: 0, left: 0 })
const showEmbedModal = ref(false)

const shareUrl = ref('')
const apiBaseUrl =
    (typeof import.meta !== 'undefined' &&
        import.meta.env &&
        import.meta.env.VITE_API_BASE_URL) ||
    process.env.VUE_APP_API_BASE_URL ||
    'http://localhost:8080'


function toggleComments(position) {
    if (position) modalPosition.value = position
    showModal.value = true
}

function handleLike() {
    console.log('Like clicked for post', props.post.id)
}

function handleShare(position) {
    if (!props.post?.id) return
    modalPosition.value = position
    if (navigator.share) {
        navigator
            .share({
                title: props.post.user?.name || 'Post',
                text: props.post.text?.slice(0, 100) || '',
                url: shareUrl.value
            })
            .catch(() => (showShare.value = true))
    } else {
        showShare.value = true
    }
}

function openEmbed(position) {
    showShare.value = false
    modalPosition.value = position
    showEmbedModal.value = true
}

function handleEmbedSubmit() {
    showEmbedModal.value = false
}

watch(
    () => props.post.id,
    () => {
        showFull.value = false
        mappedComments.value = []
        showModal.value = false
        showShare.value = false
        showEmbedModal.value = false
    }
)

const isPublication = computed(() => {
    const mediaArr = Array.isArray(props.post?.media) ? props.post.media : []
    const media = mediaArr[0]
    return media?.meta?.source === 'publication'
})

onMounted(() => {
    if (typeof window !== 'undefined') {
        shareUrl.value = `${window.location.origin}/posts/${props.post.id}`
    }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

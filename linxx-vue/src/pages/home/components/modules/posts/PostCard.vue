<template>
  <div class="w-full bg-white/90 dark:bg-gray-800/70 dark:border-gray-600 dark:shadow-md transition-all duration-300 overflow-hidden">
    <template v-if="post.user">
      <PostHeader
          :user="post.user"
          v-bind="post.created_at ? { createdAt: post.created_at } : {}"
          :is-owner="currentUserId && post.user && post.user.id === currentUserId"
      />
    </template>
    <template v-else>
      <div class="px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
        <div class="h-3 w-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
      </div>
    </template>

    <div>
      <template v-if="isReshare">
        <div v-if="post.text" :dir="dirOf(post.text)">
          <PostTextClamp
              :rich-text="post.richText"
              :full-html="post.text"
              v-model:expanded="showFull"
              :limits="{ sm: 100, md: 300, lg: 400 }"
              :truncate="true"
              :hide-controls="false"
          />
        </div>

        <div
            class="border rounded-2xl overflow-hidden bg-white dark:bg-neutral-900 m-3 cursor-pointer"
            @click="goToOriginal"
        >
          <PostCard v-if="sharedKind==='post' && sharedOriginal" :post="sharedOriginal" :embed="true" />
          <EventEmbedCard v-else-if="sharedKind==='event' && sharedOriginal" :event="sharedOriginal" />
          <BookEmbedCard  v-else-if="sharedKind==='book'  && sharedOriginal" :book="sharedOriginal" />
          <div v-else class="p-4 text-sm text-gray-500 dark:text-gray-400">Shared item is loading…</div>
        </div>
      </template>

      <template v-else>
        <PostPublicationCard v-if="isPublication" :post="post" />
        <template v-else>
          <div v-if="post.text" :dir="dirOf(post.text)">
            <PostTextClamp
                :rich-text="post.richText"
                :full-html="post.text"
                v-model:expanded="showFull"
                :limits="{ sm: 100, md: 300, lg: 400 }"
                :truncate="true"
                :hide-controls="false"
            />
          </div>
          <PostMedia :post="post" :base-url="apiBaseUrl" />
        </template>
      </template>
    </div>

    <PostFooter
        v-if="!props.embed"
        :post="post"
        @like="handleLike"
        @comment="toggleComments"
        @share="openSharePopover"
    />

    <PostCommentsModal
        :visible="showModal"
        :post="post"
        :position="modalPosition"
        @close="showModal = false"
    />

    <ShareModal
        v-model:modelValue="showShare"
        :position="modalPosition"
        @select="onShareSelect"
    />

    <EmbedShareModal
        v-if="!props.embed"
        :visible="showEmbedModal"
        :shared-post="shareTargetObject"
        :shared-type="shareTargetType"
        @close="showEmbedModal = false"
        @submitted="handleEmbedSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useShareStore } from '@/stores/share'
import { usePostStore } from '@/stores/post'
import PostCommentsModal from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentsModal.vue'
import PostFooter from '@/pages/home/components/modules/posts/PostFooter.vue'
import PostMedia from '@/pages/home/components/modules/posts/PostMedia.vue'
import PostHeader from '@/pages/home/components/modules/posts/PostHeader.vue'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'
import PostPublicationCard from '@/pages/home/components/modules/posts/media/PostPublicationCard.vue'
import ShareModal from '@/pages/home/components/modules/posts/Footer/share/ShareModal.vue'
import EmbedShareModal from '@/pages/home/components/modules/posts/Footer/share/EmbedShareModal.vue'
import EventEmbedCard from '@/pages/home/components/modules/events/EventEmbedCard.vue'
import BookEmbedCard from "@/pages/home/components/modules/books/BookEmbedCard.vue";

const router = useRouter()

const props = defineProps({
  post: Object,
  embed: { type: Boolean, default: false }
})

const authStore = useAuthStore()
const shareStore = useShareStore()
const postStore = usePostStore()
const currentUserId = computed(() => authStore.user?.id ?? null)

const showFull = ref(false)
const showModal = ref(false)
const showShare = ref(false)
const showEmbedModal = ref(false)
const modalPosition = ref({ top: 0, left: 0 })

const shareCache = ref({ link: '', slug: '', targetId: null, targetType: null })
const creatingShare = ref(false)

const apiBaseUrl =
    (typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.VITE_API_BASE_URL) ||
    process.env.VUE_APP_API_BASE_URL ||
    'http://localhost:8080'

const isReshare = computed(() => Boolean(props.post?.share_id || props.post?.share))
const sharedOriginal = computed(() => props.post?.share?.shareable || null)

const sharedKind = computed(() => {
  const explicit = props.post?.share?.type || props.post?.share?.shareable_type
  if (explicit) return explicit
  const s = sharedOriginal.value
  if (!s) return 'post'
  if ('starts_at' in s || 'ends_at' in s) return 'event'
  if ('author' in s || 'cover_url' in s || 'cover_image_url' in s) return 'book'
  return 'post'
})

const shareTargetObject = computed(() => (sharedOriginal.value ? sharedOriginal.value : props.post))
const shareTargetType = computed(() => (sharedOriginal.value ? sharedKind.value : 'post'))

function toggleComments(position) {
  if (position) modalPosition.value = position
  showModal.value = true
}

function handleLike() {}

function openSharePopover(position) {
  if (position) modalPosition.value = position
  showShare.value = true
}

function stripTags(html) { return (html || '').replace(/<[^>]*>/g, ' ') }
function hasRTL(s) { return /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test(s || '') }
function dirOf(html) { return hasRTL(stripTags(html)) ? 'rtl' : 'ltr' }

async function ensureShareLink() {
  const target = shareTargetObject.value
  const type = shareTargetType.value
  const id = target?.id
  if (!id) throw new Error('No target id')
  if (shareCache.value.link && shareCache.value.targetId === id && shareCache.value.targetType === type) return shareCache.value
  if (creatingShare.value) return shareCache.value
  creatingShare.value = true
  try {
    const share = await shareStore.create({
      shareable_type: type,
      shareable_id: id,
      scope: 'public'
    })
    const link = share.link || `${location.origin}/r/${share.slug}`
    shareCache.value = { link, slug: share.slug, targetId: id, targetType: type }
    return shareCache.value
  } finally {
    creatingShare.value = false
  }
}

async function onShareSelect({ key }) {
  showShare.value = false
  if (key === 'in-app') {
    try {
      await ensureShareLink()
      showEmbedModal.value = true
    } catch {
      alert('Could not prepare share')
    }
    return
  }
  try {
    const { link } = await ensureShareLink()
    switch (key) {
      case 'copy':
        if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(link)
        else prompt('Copy this link:', link)
        break
      case 'whatsapp':
        window.open(`https://wa.me/?text=${encodeURIComponent(link)}`, '_blank', 'noopener')
        break
      case 'telegram':
        window.open(`https://t.me/share/url?url=${encodeURIComponent(link)}`, '_blank', 'noopener')
        break
      case 'x':
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(link)}`, '_blank', 'noopener')
        break
      case 'facebook':
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(link)}`, '_blank', 'noopener')
        break
      default:
        break
    }
  } catch {
    alert('Could not prepare share')
  }
}

function handleEmbedSubmit(payload) {
  if (!shareCache.value.slug) return
  const text = (payload?.text || '').trim()
  postStore
      .submitReshare({ shareSlug: shareCache.value.slug, text, originalPost: shareTargetObject.value })
      .then(() => { showEmbedModal.value = false })
      .catch(() => { alert('Reshare failed') })
}

function goToOriginal() {
  const original = sharedOriginal.value
  if (!original) return
  if (sharedKind.value === 'event') {
    const key = original.slug || original.id
    if (router.hasRoute('public_event')) router.push({ name: 'public_event', params: { idOrSlug: key } })
    else router.push(`/events/${key}`)
    return
  }
  if (sharedKind.value === 'book') {
    const key = original.slug || original.id
    if (router.hasRoute('book.show')) router.push({ name: 'book.show', params: { slug: key } })
    else router.push(`/books/${key}`)
    return
  }
  if (router.hasRoute('post.show') && (original.slug || original.id)) {
    router.push({ name: 'post.show', params: { id: original.id, slug: original.slug || String(original.id) } })
  } else {
    router.push(`/posts/${original.id}`)
  }
}


watch(
    () => props.post.id,
    () => {
      showFull.value = false
      showModal.value = false
      showShare.value = false
      showEmbedModal.value = false
      shareCache.value = { link: '', slug: '', targetId: null, targetType: null }
    }
)

const isPublication = computed(() => {
  const mediaArr = Array.isArray(props.post?.media) ? props.post.media : []
  const media = mediaArr[0]
  return media?.meta?.source === 'publication'
})

defineExpose({ dirOf })
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

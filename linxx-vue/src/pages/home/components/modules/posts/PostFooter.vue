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
  <!-- Action buttons -->
  <div class="mt-2 border-t dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300 select-none">
    <div class="flex justify-around items-center px-4 py-2">
      <!-- Like -->
      <button
          class="flex items-center gap-1 transition-all duration-200"
          :class="isLiked ? 'text-red-500' : 'hover:text-red-500'"
          @click="handleLike"
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

  <!-- ✅ LOGIN MODAL -->
  <Transition name="fade">
    <div v-if="showLoginPrompt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl w-[90%] max-w-sm px-6 py-8 text-center relative">

        <!-- ✨ Icon -->
        <div class="flex justify-center mb-4">
          <Icon icon="solar:lock-keyhole-minimalistic-linear" class="text-blue-600 dark:text-blue-400" width="40" height="40" />
        </div>

        <!-- 📝 Text -->
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">Login Required</h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">
          Please <strong>log in</strong> to like this video and enjoy a personalized experience.
        </p>

        <!-- 🚀 Buttons -->
        <div class="mt-6 flex justify-center gap-3">
          <button
              @click="showLoginPrompt = false"
              class="px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-600 text-sm text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 transition"
          >
            Cancel
          </button>
          <button
              @click="router.push({ name: 'login' })"
              class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm transition"
          >
            Log in
          </button>
        </div>
      </div>
    </div>
  </Transition>

</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { ThumbsUp, MessageCircle, Share2 } from 'lucide-vue-next'
import { useLikesStore } from '@/stores/useLikesStore'
import {useRouter} from "vue-router";

// Props
const props = defineProps({
  post: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['like', 'comment', 'share'])

// Stores
const authStore = useAuthStore()
const likesStore = useLikesStore()
const router = useRouter()

// Refs
const showLoginPrompt = ref(false)
const shareButton = ref(null)
const isLoggedIn = computed(() => !!authStore.user && !!authStore.user.data?.id)

// Init likes in store
onMounted(() => {
  likesStore.setInitial(
      props.post.id,
      props.post.is_liked,
      props.post.likes_count,
      props.post.likes_preview
  )
})

// Reactive values from store
const isLiked = computed(() => likesStore.isLiked(props.post.id))
const likesCount = computed(() => likesStore.likesCount(props.post.id))
const previewNames = computed(() => likesStore.previewNames(props.post.id))
const othersCount = computed(() => likesCount.value - previewNames.value.length)

// Comments
const commentsCount = ref(props.post.comments_count ?? 0)

// Actions
const handleLike = () => {
  if (!isLoggedIn.value) {
    showLoginPrompt.value = true
    return
  }
  likesStore.toggle(props.post.id)
}

const emitShare = () => {
  const rect = shareButton.value.getBoundingClientRect()
  emit('share', {
    top: rect.top + window.scrollY,
    left: rect.left + rect.width / 2,
  })
}
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

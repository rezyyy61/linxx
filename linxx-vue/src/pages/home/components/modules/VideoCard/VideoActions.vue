<template>
  <div class="relative dark:border-zinc-800 pl-2 ml-6 transition">

    <!-- 🟦 User Info + Post Info -->
    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
      <div class="flex gap-4 shrink-0 w-full sm:w-[250px]">
        <img
            :src="video?.user?.avatar || '/default-avatar.png'"
            class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-500/30 dark:ring-zinc-700"
        />
        <div class="space-y-1">
          <h2 class="text-base font-semibold">
            {{ video?.user?.name || 'Unknown User' }}
          </h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ formatViews(video?.views) }} views • {{ formatDate(video?.created_at) }}
          </p>
        </div>
      </div>

      <!-- 📄 Description -->
      <div
          class="text-sm text-zinc-600 dark:text-zinc-300 sm:px-4 relative"
      >
        <PostTextClamp
            v-if="video?.text"
            v-model:expanded="showFull"
            :rich-text="video.richText"
            :full-html="video.text"
            :limits="{ sm: 60, md: 100, lg: 120 }"
            :truncate="true"
            :hide-controls="false"
        />
      </div>
    </div>

    <!-- ❤️ Like Preview Summary -->
    <div v-if="likesCount > 0" class="text-sm text-gray-500 dark:text-gray-400">
      <div class="flex items-center gap-2">
        <ThumbsUp class="w-4 h-4 text-red-500" />
        <span>
          <template v-for="(user, index) in previewNames" :key="user.id">
            {{ user.name }}<span v-if="index < previewNames.length - 1">, </span>
          </template>
          <span v-if="othersCount > 0">and {{ othersCount }} others</span>
          liked this
        </span>
      </div>
    </div>

    <!-- 🛠️ Actions -->
    <div class="flex flex-wrap items-center gap-4 pt-4">
      <button
          class="flex items-center gap-2 text-sm font-medium transition"
          :class="isLiked ? 'text-red-500' : 'hover:text-red-500'"
          @click="toggleLike"
      >
        <Icon :icon="isLiked ? 'solar:like-bold-duotone' : 'solar:like-line-duotone'" class="text-[20px]" />
        {{ likesCount }} Like
      </button>

      <button class="flex items-center gap-2 text-sm font-medium hover:text-blue-600 transition">
        <Icon icon="solar:share-outline" class="text-[20px]" />
        Share
      </button>

      <button class="flex items-center gap-2 text-sm font-medium hover:text-blue-600 transition">
        <Icon icon="solar:bookmark-line-duotone" class="text-[20px]" />
        Save
      </button>

      <button class="flex items-center gap-2 text-sm font-medium text-red-600 hover:underline transition">
        <Icon icon="solar:danger-triangle-line-duotone" class="text-[20px]" />
        Report
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
import { ThumbsUp } from 'lucide-vue-next'
import relativeTime from 'dayjs/plugin/relativeTime'
import dayjs from "dayjs"
import { ref, computed, onMounted } from "vue"
import { useLikesStore } from '@/stores/useLikesStore'
import PostTextClamp from "@/pages/home/components/modules/posts/PostTextClamp.vue"
import {useAuthStore} from "@/stores/auth";
import {useRouter} from "vue-router";

dayjs.extend(relativeTime)

const props = defineProps({
  video: {
    type: Object,
    required: true
  }
})

const likesStore = useLikesStore()
const authStore = useAuthStore()
const router = useRouter()
const showFull = ref(false)
const showLoginPrompt = ref(false)


onMounted(() => {
  likesStore.setInitial(
      props.video.id,
      props.video.is_liked,
      props.video.likes_count,
      props.video.likes_preview ?? []
  )
})

const isLiked = computed(() => likesStore.isLiked(props.video.id))
const likesCount = computed(() => likesStore.likesCount(props.video.id))
const previewNames = computed(() => likesStore.previewNames(props.video.id))
const othersCount = computed(() => likesCount.value - previewNames.value.length)
const isLoggedIn = computed(() => !!authStore.user && !!authStore.user.data?.id)

function toggleLike() {
  if (!isLoggedIn.value) {
    showLoginPrompt.value = true
    return
  }

  likesStore.toggle(props.video.id)
}

function formatViews(num) {
  if (!num) return '0'
  if (num >= 1_000_000) return (num / 1_000_000).toFixed(1) + 'M'
  if (num >= 1_000) return (num / 1_000).toFixed(1) + 'K'
  return num.toString()
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return dayjs(dateStr).fromNow()
}
</script>

<!-- 🔘 Reusable Button -->
<script>
export const ActionButton = {
    props: ['icon', 'label', 'color'],
    components: { Icon },
    template: `
    <button :class="['flex items-center gap-1 text-sm font-medium transition hover:text-blue-600', color]">
      <Icon :icon="icon" class="text-lg" />
      {{ label }}
    </button>
  `
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

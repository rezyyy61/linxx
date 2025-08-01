<template>
  <div class="space-y-5">
    <h3 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">
      Related Videos
    </h3>

    <!-- حالت لودینگ یا خالی -->
    <div v-if="isLoading" class="text-sm text-zinc-500 dark:text-zinc-400">Loading...</div>
    <div v-else-if="relatedVideos.length === 0" class="text-sm text-zinc-500 dark:text-zinc-400">
      No related videos found.
    </div>

    <!-- حالت افقی -->
    <div v-else-if="horizontal" class="flex overflow-x-auto gap-4 pb-2">
      <div
          v-for="video in relatedVideos"
          :key="video.id"
          @click="goToVideo(video.id)"
          class="flex-none w-48 cursor-pointer rounded-xl bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition group shadow-sm"
      >
        <div class="w-full h-28 rounded-t-xl overflow-hidden bg-zinc-200 dark:bg-zinc-700">
          <img
              :src="getThumbnail(video)"
              alt="Thumbnail"
              class="w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-300"
          />
        </div>
        <div class="p-3 space-y-1">
          <div class="text-sm font-medium text-zinc-800 dark:text-white leading-snug">
            <PostTextClamp
                v-if="video.text"
                :rich-text="video.richText"
                :full-html="video.text"
                :limits="{ sm: 50, md: 50, lg: 50 }"
                :truncate="true"
                :hide-controls="true"
            />
            <template v-else>
              Untitled video
            </template>
          </div>
          <p class="text-[13px] text-gray-500 dark:text-gray-400">
            {{ video.user?.name || 'Unknown' }}
          </p>
          <p class="text-[13px] text-gray-400 dark:text-gray-500">
            {{ formatViews(video.views) }} views
          </p>
        </div>
      </div>
    </div>

    <!-- حالت عمودی -->
    <div v-else class="space-y-4">
      <div
          v-for="video in relatedVideos"
          :key="video.id"
          @click="goToVideo(video.id)"
          class="flex items-start gap-4 p-3 rounded-xl cursor-pointer bg-white dark:bg-zinc-900 hover:shadow-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all group"
      >
        <!-- Thumbnail -->
        <div class="w-48 h-28 rounded-lg overflow-hidden flex-shrink-0 bg-zinc-200 dark:bg-zinc-700">
          <img
              :src="getThumbnail(video)"
              alt="Thumbnail"
              class="w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-300"
          />
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0 space-y-1">
          <div class="text-sm font-medium text-zinc-800 dark:text-white leading-snug">
            <PostTextClamp
                v-if="video.text"
                :rich-text="video.richText"
                :full-html="video.text"
                :limits="{ sm: 50, md: 50, lg: 50 }"
                :truncate="true"
                :hide-controls="true"
            />
            <template v-else>
              Untitled video
            </template>
          </div>
          <p class="text-[13px] text-gray-500 dark:text-gray-400">
            {{ video.user?.name || 'Unknown' }}
          </p>
          <p class="text-[13px] text-gray-400 dark:text-gray-500">
            {{ formatViews(video.views) }} views
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useVideoStore } from '@/stores/useVideoStore'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'

const props = defineProps({
  video: Object,
  horizontal: {
    type: Boolean,
    default: false
  }
})

const store = useVideoStore()
const router = useRouter()
const isLoading = ref(false)

onMounted(async () => {
  if (!store.videos.length) {
    isLoading.value = true
    await store.fetchVideos()
    isLoading.value = false
  }
})

const relatedVideos = computed(() => {
  if (!props.video?.id || !store.videos.length) return []
  return store.getRelatedVideos(props.video.id, 4)
})

function getThumbnail(video) {
  const meta = video?.media?.[0]?.meta
  const base = 'http://localhost:8080/storage/'
  if (!meta) return '/placeholder.jpg'
  return meta.thumb_large
      ? base + meta.thumb_large
      : meta.thumb_path
          ? base + meta.thumb_path
          : '/placeholder.jpg'
}

function formatViews(num) {
  if (!num) return '0'
  if (num >= 1_000_000) return (num / 1_000_000).toFixed(1) + 'M'
  if (num >= 1_000) return (num / 1_000).toFixed(1) + 'K'
  return num.toString()
}

function goToVideo(id) {
  router.push(`/watch/${id}`)
}
</script>

<style scoped>
/* optional custom scrollbar */
::-webkit-scrollbar {
  height: 6px;
}
::-webkit-scrollbar-thumb {
  background: rgba(100, 100, 100, 0.5);
  border-radius: 4px;
}
</style>

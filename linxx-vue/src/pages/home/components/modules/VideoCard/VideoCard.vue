<template>
  <div
      class="group w-full cursor-pointer transition-transform hover:-translate-y-1"
      @click="goToWatch"
  >
    <!-- ▶ Thumbnail -->
    <div
        :class="[
        // ارتفاع ثابت برای افقی؛ عمودی همان قبلی
        orientation === 'landscape'
          ? 'relative w-full h-[300px] overflow-hidden rounded-xl bg-black'
          : 'relative w-full h-full overflow-hidden rounded-xl bg-black',
      ]"
    >
      <img
          :src="thumbnail"
          :alt="video.text || 'Video thumbnail'"
          class="w-full h-full object-content"
      />
      <span
          v-if="duration"
          class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-1.5 py-0.5 rounded"
      >
        {{ formatDuration(duration) }}
      </span>
    </div>

    <!-- ▶ Meta -->
    <div class="flex items-start gap-3 mt-3 text-gray-800 dark:text-gray-200">
      <img
          v-if="video.user?.avatar"
          :src="video.user.avatar"
          alt="User avatar"
          class="w-10 h-10 rounded-full object-cover"
      />
      <div class="flex-1 min-w-0">
        <div class="text-sm font-medium hover:text-blue-600 transition leading-snug hide-controls">
          <PostTextClamp
              v-if="video.text"
              :rich-text="video.richText"
              :full-html="video.text"
              :limits="{ sm: 50, md: 50, lg: 50 }"
              :truncate="true"
              :hide-controls="true"
          />
          <template v-else>Untitled video</template>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ video.user?.name || 'Unknown' }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500">
          {{ views }} views • {{ createdAt }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import router from '@/router'
import PostTextClamp from '@/pages/home/components/modules/posts/PostTextClamp.vue'

const props = defineProps({
  video: Object,
  orientation: { type: String, default: 'landscape' }
})

function goToWatch () {
  const id = props.video?.id
  if (id) router.push(`/watch/${id}`)
}

const thumbnail = computed(() => {
  const meta = props.video?.media?.[0]?.meta || {}
  const base = 'http://localhost:8080/storage/'
  return meta.thumb_large
      ? base + meta.thumb_large
      : meta.thumb_path
          ? base + meta.thumb_path
          : '/placeholder.jpg'
})

const duration = computed(() => props.video?.media?.[0]?.meta?.duration)
const views = '1.2K'
const createdAt = '2 days ago'

function formatDuration (sec) {
  if (!sec) return ''
  const m = Math.floor(sec / 60)
  const s = (sec % 60).toString().padStart(2, '0')
  return `${m}:${s}`
}
</script>

<template>
  <section class="space-y-6">
    <!-- 🎥 Videos Gallery -->
    <div v-if="videos.length" class="p-4 rounded-xl bg-white dark:bg-gray-800 shadow">
      <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <Icon icon="mdi:video-outline" /> Videos
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
            v-for="video in videos"
            :key="video.id"
            class="aspect-video rounded-lg overflow-hidden shadow hover:shadow-lg transition duration-200 cursor-pointer"
        >
          <video
              controls
              class="w-full h-full object-cover rounded-lg"
              :src="video.media?.[0]?.original_url"
          />
        </div>
      </div>
    </div>

    <!-- No Videos -->
    <div v-else class="text-sm text-gray-500 dark:text-gray-400 p-6 text-center">
      <Icon icon="mdi:video-off-outline" class="inline-block w-5 h-5 mr-1" />
      No videos uploaded.
    </div>
  </section>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const props = defineProps({
  profile: Object
})

const videos = ref([])
const route = useRoute()

const fetchVideos = async () => {
  try {
    const { data } = await axios.get(`/api/profile/slug/${route.params.slug}/posts/videos`, {
      params: {
        type: 'video',
        per_page: 9
      }
    })
    videos.value = data.data
  } catch (err) {
    console.error('Failed to load videos', err)
  }
}

onMounted(() => {
  fetchVideos()
})
</script>

<style scoped>
video::-webkit-media-controls {
  background-color: transparent;
}
</style>

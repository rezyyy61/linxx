<template>
  <aside class="space-y-6 w-full px-4">
    <!-- 📸 Photo Gallery -->
    <section v-if="photos.length" class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow">
      <h2 class="text-md font-bold mb-4 flex items-center gap-2">
        <Icon icon="mdi:image-multiple" /> Photo Gallery
      </h2>
      <div class="grid grid-cols-3 gap-2">
        <img
            v-for="(photo, i) in photos"
            :key="i"
            :src="photo"
            class="rounded-md object-cover w-full h-28 cursor-pointer hover:opacity-80"
            @click="viewImage(photo)"
        />
      </div>
    </section>

    <!-- 🎥 Video Gallery -->
    <section v-if="videos.length" class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow">
      <h2 class="text-md font-bold mb-4 flex items-center gap-2">
        <Icon icon="mdi:video" /> Video Gallery
      </h2>
      <div class="grid grid-cols-3 gap-2">
        <video
            v-for="(video, i) in videos"
            :key="i"
            controls
            class="rounded-md w-full h-28 object-cover"
        >
          <source :src="video" type="video/mp4" />
        </video>
      </div>
    </section>

    <!-- 📊 Activity Stats -->
    <section class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow">
      <h2 class="text-md font-bold mb-4 flex items-center gap-2">
        <Icon icon="mdi:chart-line" /> Activity
      </h2>
      <ul class="text-sm space-y-1">
        <li>Posts: {{ stats.posts }}</li>
        <li>Videos: {{ stats.videos }}</li>
        <li>Files: {{ stats.files }}</li>
      </ul>
    </section>

    <!-- 📢 Campaigns -->
    <section v-if="campaigns.length" class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow">
      <h2 class="text-md font-bold mb-4 flex items-center gap-2">
        <Icon icon="mdi:flag-outline" /> Campaigns
      </h2>
      <ul class="text-sm space-y-1">
        <li v-for="(camp, i) in campaigns" :key="i">📢 {{ camp.title }}</li>
      </ul>
    </section>

    <!-- 🧠 Trending Topics -->
    <section v-if="trending.length" class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow">
      <h2 class="text-md font-bold mb-4 flex items-center gap-2">
        <Icon icon="mdi:fire" /> Trending Topics
      </h2>
      <div class="flex flex-wrap gap-2 text-xs">
        <span v-for="(tag, i) in trending" :key="i" class="px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-full">
          {{ tag }}
        </span>
      </div>
    </section>

    <!-- ✍️ Quote -->
    <section v-if="quote" class="p-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-xl shadow italic">
      <Icon icon="mdi:format-quote-close" /> "{{ quote }}"
    </section>
  </aside>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref } from 'vue'

// Sample placeholder data
const photos = ref([
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200',
  'https://via.placeholder.com/300x200'
])

const videos = ref([
  'https://www.w3schools.com/html/mov_bbb.mp4',
  'https://www.w3schools.com/html/movie.mp4',
  'https://www.w3schools.com/html/mov_bbb.mp4',
  'https://www.w3schools.com/html/movie.mp4',
  'https://www.w3schools.com/html/mov_bbb.mp4',
  'https://www.w3schools.com/html/movie.mp4',
  'https://www.w3schools.com/html/mov_bbb.mp4',
  'https://www.w3schools.com/html/movie.mp4',
  'https://www.w3schools.com/html/mov_bbb.mp4'
])

const stats = ref({
  posts: 12,
  videos: 9,
  files: 5
})

const trending = ref(['Social Justice', 'Election 2025', 'Freedom of Speech'])

const campaigns = ref([
  { title: 'Campaign for Press Law Reform' },
  { title: 'Participating in Local Elections' }
])

const quote = 'We fight to build a better future.'

const viewImage = (src) => {
  window.open(src, '_blank')
}
</script>

<style scoped>
.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}
</style>

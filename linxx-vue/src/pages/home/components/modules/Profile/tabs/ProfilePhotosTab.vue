<template>
  <section
      class="space-y-10 animate-fade-in-up overflow-y-auto max-h-[80vh] pr-1"
      @scroll.passive="handleScroll"
      ref="scrollContainer"
  >
    <!-- Gallery -->
    <div v-if="photos.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
      <template v-for="photo in photos" :key="photo.id">
        <div
            v-for="(image, i) in photo.media.filter(m => m.type === 'image')"
            :key="photo.id + '-' + i"
            class="relative group overflow-hidden rounded-2xl shadow-xl bg-gray-100 dark:bg-gray-800 hover:scale-[1.03] transition-all duration-300 cursor-pointer"
            @click="openPost(photo, image)"
        >
          <img
              :src="image.url"
              :alt="photo.caption || 'photo'"
              class="w-full h-48 object-cover group-hover:brightness-90 transition duration-300"
          />

          <!-- Caption overlay -->
          <div
              v-if="photo.caption"
              class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 opacity-0 group-hover:opacity-100 transition-opacity"
          >
            {{ photo.caption }}
          </div>
        </div>
      </template>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center text-sm text-gray-500 dark:text-gray-400 py-16">
      <Icon icon="mdi:image-off-outline" class="w-10 h-10 mx-auto mb-3" />
      <p>No photos available</p>
    </div>

    <!-- Modal with PostCard -->
    <div v-if="selectedPost" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center">
      <div class="relative w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-xl">
        <button @click="selectedPost = null" class="absolute top-3 right-3 z-10 text-white dark:text-gray-300 text-2xl">&times;</button>
        <PostCard :post="selectedPost" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import PostCard from "@/pages/home/components/modules/posts/PostCard.vue";

const props = defineProps({
  profile: Object,
})

const photos = ref([])
const page = ref(1)
const isLoading = ref(false)
const scrollContainer = ref(null)
const selectedPost = ref(null)

const openPost = (photo, image) => {
  selectedPost.value = photo
}

const fetchPhotos = async () => {
  if (isLoading.value) return
  isLoading.value = true

  try {
    const { data } = await axios.get(`/api/profile/slug/${props.profile.user.slug}/posts/images`, {
      params: {
        type: 'image',
        per_page: 12,
        page: page.value
      },
    })

    if (data.data?.length) {
      photos.value.push(...data.data)
      page.value++
    }
  } catch (e) {
    console.error('Failed to load photos:', e)
  } finally {
    isLoading.value = false
  }
}

const handleScroll = () => {
  const container = scrollContainer.value
  if (!container) return

  const scrollBottom = container.scrollTop + container.clientHeight
  const threshold = container.scrollHeight - 200

  if (scrollBottom >= threshold) {
    fetchPhotos()
  }
}

onMounted(() => {
  fetchPhotos()
})
</script>

<style scoped>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.6s ease-out;
}
</style>

<template>
  <section class="space-y-6">
    <!-- Files List -->
    <div v-if="files.length" class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow">
      <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
        <Icon icon="mdi:file-document-outline" /> Files
      </h2>

      <ul class="space-y-3 text-sm">
        <li
            v-for="file in files"
            :key="file.id"
            class="flex items-center gap-3 hover:text-red-500 hover:underline transition cursor-pointer"
        >
          <Icon icon="mdi:file-outline" class="text-gray-400 dark:text-gray-500 w-5 h-5" />
          <a :href="file.url" target="_blank" class="truncate">
            {{ file.name }}
          </a>
        </li>
      </ul>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center text-sm text-gray-500 dark:text-gray-400 py-10">
      <Icon icon="mdi:file-remove-outline" class="w-8 h-8 mx-auto mb-2" />
      <p>No files available</p>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'

const props = defineProps({
  profile: Object
})

const files = ref([])

const fetchFiles = async () => {
  try {
    const { data } = await axios.get(`/api/profile/slug/${props.profile.user.slug}/posts`, {
      params: {
        type: 'file',
        per_page: 15
      }
    })

    files.value = data.data || []
  } catch (err) {
    console.error('Failed to fetch files:', err)
  }
}

onMounted(() => {
  fetchFiles()
})
</script>

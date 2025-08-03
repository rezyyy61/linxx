<template>
  <div class="max-w-6xl mx-auto py-10 px-4">
    <!-- Reader Header -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-gray-800 dark:text-white">
        {{ book?.title || 'کتاب' }}
      </h2>
      <div class="flex items-center gap-3">
        <!-- Toggle Night Mode -->
        <button @click="nightMode = !nightMode" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
          <Icon :icon="nightMode ? 'mdi:white-balance-sunny' : 'mdi:weather-night'" class="w-4 h-4 mr-1" />
          {{ nightMode ? t('home.books.day_mode') : t('home.books.night_mode') }}
        </button>

        <!-- Download -->
        <a
            :href="book?.file_download_url"
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-rose-500 text-white hover:bg-rose-600 transition text-sm font-medium"
        >
          <Icon icon="mdi:download" class="w-4 h-4" />
          {{ t('home.books.download_now') }}
        </a>
      </div>
    </div>

    <!-- PDF Viewer -->
    <iframe
        v-if="book?.file_read_url"
        :src="`https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodeURIComponent(book.file_read_url)}`"
        class="w-full h-[80vh] border rounded-xl"
        :class="nightMode ? 'bg-gray-900' : 'bg-white'"
    />

    <div v-else class="text-center text-sm text-red-500">
      {{ t('home.books.reader_unavailable') || 'قابلیت خواندن کتاب در دسترس نیست.' }}
    </div>

    <!-- Reader Footer (Progress etc) -->
    <div class="mt-6 text-center text-sm text-gray-400">
      {{ t('home.books.reader_footer') }}
    </div>
  </div>
</template>

<script setup>
import {ref, onMounted, computed} from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'
import { usePublicBookStore } from '@/stores/usePublicBookStore'

const { t } = useI18n()
const route = useRoute()
const slug = route.params.slug
const nightMode = ref(false)

const bookStore = usePublicBookStore()
onMounted(() => {
  if (!bookStore.singleBook || bookStore.singleBook.slug !== slug) {
    bookStore.fetchBook(slug)
  }
})

const book = computed(() => bookStore.singleBook)
</script>

<style scoped>
iframe {
  transition: background-color 0.3s ease;
}
</style>

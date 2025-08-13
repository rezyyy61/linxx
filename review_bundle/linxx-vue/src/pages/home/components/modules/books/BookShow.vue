<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <!-- Back button -->
    <div class="mb-6">
      <router-link
          to="/books"
          class="inline-flex items-center gap-2 text-rose-500 hover:underline text-sm font-medium"
      >
        <Icon icon="mdi:arrow-left" class="w-4 h-4" />
        {{ t('home.books.back_to_list') }}
      </router-link>
    </div>

    <!-- loading -->
    <div v-if="bookLoading" class="text-center text-sm text-gray-500 dark:text-gray-300 animate-pulse">
      {{ t('home.books.loading_book') }}
    </div>

    <!-- error -->
    <div v-else-if="bookError" class="text-center text-sm text-red-500">
      {{ bookError }}
    </div>

    <!-- book content -->
    <div
        v-else-if="book"
        class="flex flex-col md:flex-row gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-700"
    >
      <!-- Cover -->
      <div class="w-full md:w-64 h-80 bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center border border-gray-300 dark:border-gray-600 p-2">
        <img
            :src="book.cover_image_url || fallbackCover"
            :alt="book.title"
            class="object-contain w-full h-full"
        />
      </div>

      <!-- Info -->
      <div class="flex-1 flex flex-col justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2" :dir="dir(book.title)">
            {{ book.title }}
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-300 mb-3" :dir="dir(book.author)">
            {{ book.author || t('home.books.unknown_author') }}
          </p>

          <!-- Rating -->
          <div class="flex items-center gap-1 mb-3 text-amber-500">
            <template v-for="n in 5" :key="n">
              <Icon
                  :icon="n <= (book.rating || 0) ? 'mdi:star' : 'mdi:star-outline'"
                  class="w-5 h-5"
              />
            </template>
            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
              {{ (book.rating || 0).toFixed(1) }} / 5 ({{ book.reviews_count || 0 }} {{ t('home.books.reviews') }})
            </span>
          </div>

          <!-- Description -->
          <p class="text-gray-700 dark:text-gray-300 leading-relaxed mt-2" :dir="dir(book.description)">
            {{ book.description }}
          </p>
        </div>

        <!-- Stats & Download -->
        <!-- Stats & Download & Read -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-6 text-sm text-gray-500 dark:text-gray-400">
          <div class="flex items-center gap-4">
            <span>👁️ {{ t('home.books.views') }} {{ book.view_count }}</span>
            <span>⬇️ {{ t('home.books.downloads') }} {{ book.download_count }}</span>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <!-- Download Button -->
            <button
                v-if="book.file_download_url"
                @click="downloadBook"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-500 text-white hover:bg-rose-600 transition text-sm font-medium shadow hover:scale-105"
            >
              <Icon icon="mdi:download" class="w-5 h-5" />
              {{ t('home.books.download_now') }}
            </button>

            <!-- Read Button -->
            <router-link
                v-if="book.file_download_url"
                :to="`/books/${book.slug}/read`"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium shadow hover:scale-105"
            >
              <Icon icon="mdi:book-open-page-variant" class="w-5 h-5" />
              {{ t('home.books.read_now') }}
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Reviews -->
    <BookReviewSection
        v-if="book"
        class="mt-10"
        :book-id="book.id"
    />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'
import { computed, onMounted } from 'vue'
import { usePublicBookStore } from '@/stores/usePublicBookStore'
import BookReviewSection from "@/pages/home/components/modules/books/BookReviewSection.vue"

const { t } = useI18n()
const route = useRoute()
const slug = route.params.slug

const bookStore = usePublicBookStore()
const { fetchBook } = bookStore

const fallbackCover = 'https://via.placeholder.com/300x400?text=No+Cover'

onMounted(() => {
  fetchBook(slug)
})

const book = computed(() => bookStore.singleBook)
const bookLoading = computed(() => bookStore.bookLoading)
const bookError = computed(() => bookStore.bookError)

const dir = txt =>
    /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test(txt?.trim() || '')
        ? 'rtl'
        : 'ltr'

const downloadBook = () => {
  if (book.value?.slug) {
    window.open(`/api/books/${book.value.slug}/download`, '_blank')
  }
}
</script>

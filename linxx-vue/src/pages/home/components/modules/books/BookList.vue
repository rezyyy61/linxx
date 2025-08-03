<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 auto-rows-fr">
    <router-link
        v-for="book in books"
        :key="book.id"
        :to="`/books/${book.slug}`"
        class="group flex flex-col md:flex-row overflow-hidden rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-2xl hover:scale-[1.01] transition-all duration-300"
    >
    <!-- Book Cover -->
      <div class="flex-shrink-0 w-full md:w-44 h-64 md:h-auto flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-2">
        <img
            :src="book.cover_image_url || fallbackCover"
            :alt="book.title"
            class="w-full h-full object-contain rounded-xl border border-gray-200 dark:border-gray-700"
        />
      </div>

      <!-- Book Content -->
      <div class="flex flex-col justify-between flex-1 p-5 min-h-[230px]">
        <!-- Title & Author -->
        <div>
          <div class="flex-1 min-w-0">
            <h3
                class="font-bold text-xl text-gray-900 dark:text-white break-words group-hover:text-rose-500 transition"
                :dir="dir(book.title)"
            >
              {{ book.title }}
            </h3>
          </div>
          <p
              class="text-sm text-gray-600 dark:text-gray-400 truncate"
              :dir="dir(book.author)"
          >
            {{ book.author || t('home.books.unknown_author') }}
          </p>

        </div>

        <!-- Description -->
        <p
            class="text-sm text-gray-700 dark:text-gray-300 mt-3 mb-4 leading-relaxed line-clamp-4"
            :dir="dir(book.description)"
        >
          {{ book.description }}
        </p>

        <!-- Stats + Rating -->
        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 font-medium mt-auto">
          <div class="flex items-center gap-2">
            <span>👁️ {{ t('home.books.views') }} {{ book.view_count }}</span>
            <span>⬇️ {{ t('home.books.downloads') }} {{ book.download_count }}</span>
          </div>
          <div class="flex items-center gap-1 text-amber-500 group/review">
            <!-- Star Icons -->
            <template v-for="n in 5" :key="n">
              <Icon
                  :icon="n <= (book.rating || 0) ? 'mdi:star' : 'mdi:star-outline'"
                  class="w-4 h-4 transition-transform duration-200 group-hover/review:scale-110"
              />
            </template>

            <!-- Numeric rating -->
            <span class="ml-1 text-[11px] font-semibold text-rose-500 group-hover/review:scale-105 transition-transform">
             {{ (book.rating || 0).toFixed(1) }}
           </span>

            <!-- Review count -->
            <span class="text-gray-500 dark:text-gray-400 text-[11px] ml-1">
              ({{ book.reviews_count || 0 }})
            </span>
          </div>
        </div>
      </div>
    </router-link>
  </div>

  <!-- Loading -->
  <p v-if="loading" class="text-center py-6 text-rose-500 animate-pulse">
    {{ t('home.books.loading_more') }}
  </p>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'

const props = defineProps({
  books: { type: Array, default: () => [] },
  loading: Boolean,
  error: String
})

const { t } = useI18n()
const fallbackCover = 'https://via.placeholder.com/300x400?text=No+Cover'

const dir = txt =>
    /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test(txt?.trim() || '')
        ? 'rtl'
        : 'ltr'
</script>

<template>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-2 auto-rows-fr">
    <article
        v-for="book in books"
        :key="book.id"
        class="group/card relative overflow-visible rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:shadow-2xl hover:-translate-y-0.5 dark:border-gray-700 dark:bg-gray-800 pb-8"
    >
      <RouterLink :to="`/books/${book.slug}`" class="block focus:outline-none">
        <div class="flex items-stretch gap-4 p-4 md:p-5">
          <div
              class="relative shrink-0 w-24 h-36 sm:w-28 sm:h-40 md:w-28 md:h-40 lg:w-32 lg:h-48 rounded-xl bg-gray-50 dark:bg-gray-900 ring-1 ring-inset ring-black/5 dark:ring-white/10"
          >
            <img
                :src="book.cover_image_url || fallbackCover"
                :alt="book.title"
                class="h-full w-full object-contain p-2"
                loading="lazy"
                decoding="async"
            />
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-3">
              <h3
                  class="truncate text-base md:text-lg font-bold text-gray-900 transition-colors group-hover/card:text-rose-600 dark:text-white"
                  :dir="dir(book.title)"
              >
                {{ book.title }}
              </h3>
              <span
                  class="shrink-0 hidden sm:inline-flex items-center gap-1 rounded-full bg-white/80 px-2 py-0.5 text-[10px] font-semibold text-zinc-700 ring-1 ring-inset ring-zinc-200 backdrop-blur dark:bg-white/10 dark:text-zinc-200 dark:ring-white/10"
              >
                📚 BOOK
              </span>
            </div>

            <p class="mt-0.5 truncate text-sm text-gray-600 dark:text-gray-400" :dir="dir(book.author)">
              {{ book.author || t('home.books.unknown_author') }}
            </p>

            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-gray-700 dark:text-gray-300" :dir="dir(book.description)">
              {{ book.description }}
            </p>

            <div class="mt-3 flex items-center justify-between text-[11px] font-medium text-gray-500 dark:text-gray-400">
              <div class="flex items-center gap-3">
                <span>👁️ {{ t('home.books.views') }} {{ book.view_count }}</span>
                <span>⬇️ {{ t('home.books.downloads') }} {{ book.download_count }}</span>
              </div>
              <div class="flex items-center gap-1 text-amber-500">
                <template v-for="n in 5" :key="n">
                  <Icon :icon="n <= (book.rating || 0) ? 'mdi:star' : 'mdi:star-outline'" class="h-4 w-4" />
                </template>
                <span class="ml-1 font-semibold text-rose-500">
                  {{ (book.rating || 0).toFixed(1) }}
                </span>
                <span class="ml-1 text-gray-500 dark:text-gray-400">
                  ({{ book.reviews_count || 0 }})
                </span>
              </div>
            </div>
          </div>
        </div>
      </RouterLink>

      <div class="absolute left-1/2 -translate-x-1/2 -bottom-4 z-[70] pointer-events-auto">
        <BookShareButton
            :book="book"
            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-rose-600 bg-white/90 backdrop-blur ring-1 ring-inset ring-rose-200/70 shadow-xl hover:bg-white hover:-translate-y-0.5 hover:shadow-2xl transition dark:text-rose-300 dark:bg-white/10 dark:ring-rose-400/30"
            @in-app="onInApp"
        />
      </div>
    </article>
  </div>

  <p v-if="loading" class="py-6 text-center text-rose-500">
    {{ t('home.books.loading_more') }}
  </p>

  <BookReshareModal
      v-if="showEmbed && selectedBook"
      :visible="showEmbed"
      :book="selectedBook"
      :shareSlug="selectedSlug"
      @close="showEmbed = false"
      @submitted="onSubmitReshare"
  />

</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'
import BookShareButton from '@/pages/home/components/modules/books/BookShareButton.vue'
import BookReshareModal from '@/pages/home/components/modules/books/BookReshareModal.vue'
import { usePostStore } from '@/stores/post'

const props = defineProps({
  books: { type: Array, default: () => [] },
  loading: Boolean,
  error: String
})

const { t } = useI18n()
const fallbackCover = 'https://via.placeholder.com/300x400?text=No+Cover'
const dir = (txt) => (/[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test((txt || '').trim()) ? 'rtl' : 'ltr')

const postStore = usePostStore()
const showEmbed = ref(false)
const selectedBook = ref(null)
const selectedSlug = ref('')

function onInApp({ slug, book }) {
  selectedSlug.value = slug
  selectedBook.value = book
  showEmbed.value = true
}

async function onSubmitReshare({ text, shareSlug, book }) {
  try {
    await postStore.submitReshare({ shareSlug, text, original: book })
    showEmbed.value = false
  } catch (e) {
    alert('انتشار ناموفق بود')
  }
}
</script>

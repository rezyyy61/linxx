<template>
  <div class="min-h-screen pt-6 pb-12 bg-gray-50 dark:bg-gray-900">

    <!-- Floating Suggestion Button -->
    <button
        @click="showForm = true"
        class="fixed bottom-6 right-6 z-50 bg-rose-500 hover:bg-rose-600 text-white px-4 py-3 rounded-full shadow-lg"
    >
      {{ t('home.books.suggest_book') }}
    </button>
    <div class="max-w-7xl mx-auto">
      <BooksHeader
          :categories="categories"
          @search="onSearch"
          @filter="onFilter"
      />
      <div class="px-4 mt-6">
        <BookList
            :books="store.books"
            :loading="store.loading"
            :error="store.error"
        />
        <p v-if="store.error" class="text-center py-6 text-red-600">
          {{ store.error }}
        </p>
      </div>
    </div>
  </div>
  <!-- Suggest Book Modal -->
  <SuggestBookModal
      v-if="showForm"
      @close="showForm = false"
      @submitted="submitSuggestion"
  />

  <!-- Success & Error Messages -->
  <transition name="fade">
    <div v-if="suggestionSuccess" class="fixed bottom-24 right-6 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
      {{ t('home.books.suggestion_success') || 'پیشنهاد شما با موفقیت ثبت شد.' }}
    </div>
  </transition>

  <transition name="fade">
    <div v-if="suggestionError" class="fixed bottom-24 right-6 z-50 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg">
      {{ suggestionError }}
    </div>
  </transition>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from '@/lib/axios'
import { usePublicBookStore } from '@/stores/usePublicBookStore'
import BooksHeader from './books/BooksHeader.vue'
import BookList from './books/BookList.vue'
import SuggestBookModal from "@/pages/home/components/modules/books/SuggestBookModal.vue"

const { t } = useI18n()
const store = usePublicBookStore()

const categories = ref([])
async function fetchCategories () {
  categories.value = await store.fetchCategories?.() ?? []
}

function onSearch (term) {
  store.setSearch(term)
  store.resetAndFetch()
}

function onFilter ({ category, pricing, sortBy }) {
  store.setFilters({ category, pricing, sortBy })
  store.resetAndFetch()
}

function onScroll () {
  const bottom =
      window.innerHeight + window.scrollY >= document.body.offsetHeight - 120
  if (bottom && !store.loading) store.loadMore()
}

const showForm = ref(false)
const suggestionSuccess = ref(false)
const suggestionError = ref(null)

async function submitSuggestion (formData) {
  suggestionError.value = null
  suggestionSuccess.value = false

  try {
    await axios.post('/api/books/suggestions', formData)
    suggestionSuccess.value = true

    setTimeout(() => {
      suggestionSuccess.value = false
      showForm.value = false
    }, 3000)
  } catch (err) {
    suggestionError.value = err.response?.data?.message || t('home.books.submit_failed')
    setTimeout(() => {
      suggestionError.value = null
    }, 4000)
  }
}

onMounted(async () => {
  await Promise.all([fetchCategories(), store.fetchBooks()])
  window.addEventListener('scroll', onScroll)
})

onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

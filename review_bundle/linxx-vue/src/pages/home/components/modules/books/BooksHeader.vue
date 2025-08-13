<template>
  <header
      class="w-full bg-white/70 backdrop-blur sticky top-0 z-30 border-b border-gray-200 dark:bg-gray-800/70 dark:border-gray-700 shadow-sm"
  >
    <div
        class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <!-- Title + underline -->
      <div class="relative">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
          {{ t('home.books.title') }}
        </h1>
        <div class="absolute left-0 bottom-0 w-16 h-1 bg-rose-500 rounded-full animate-pulse" />
      </div>

      <!-- Search box -->
      <div class="w-full md:max-w-lg relative">
        <input
            v-model.trim="q"
            @keyup.enter="emitSearch"
            :placeholder="t('home.books.search_placeholder')"
            class="w-full rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm px-12 py-2 focus:(outline-none ring-2 ring-rose-500) transition duration-200"
            :dir="getDir(q)"
        />
        <Icon
            icon="mdi:magnify"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        />
        <button
            v-if="q"
            @click="clear"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500 transition"
        >
          ✕
        </button>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2">
          <Icon icon="mdi:format-list-bulleted-type" class="text-gray-400" />
          <select
              v-model="selectedCategory"
              @change="emitFilter"
              class="rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm px-3 py-2 focus:(outline-none ring-2 ring-rose-500)"
          >
            <option value="all">{{ t('home.books.all_categories') }}</option>
            <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
            >
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <Icon icon="mdi:currency-usd" class="text-gray-400" />
          <select
              v-model="selectedPricing"
              @change="emitFilter"
              class="rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm px-3 py-2 focus:(outline-none ring-2 ring-rose-500)"
          >
            <option value="all">{{ t('home.books.sort_newest') }}</option>
            <option value="sort:popular">{{ t('home.books.sort_popular') }}</option>
            <option value="sort:downloads">{{ t('home.books.sort_downloads') }}</option>
            <option value="sort:rating">{{ t('home.books.sort_rating') }}</option>
<!--            <option value="all">{{ t('home.books.all_prices') }}</option>-->
<!--            <option value="free">{{ t('home.books.free') }}</option>-->
<!--            <option value="paid">{{ t('home.books.paid') }}</option>-->
          </select>

        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'

defineProps({
  categories: { type: Array, default: () => [] }
})
const emit = defineEmits(['search', 'filter'])
const { t } = useI18n()

const q = ref('')
const selectedCategory = ref('all')
const selectedPricing = ref('all')

let debounceTimeout = null

watch(q, (newVal) => {
  if (debounceTimeout) clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    emitSearch()
  }, 300)
})

function emitSearch () {
  emit('search', q.value.trim())
}
function emitFilter () {
  let pricing = 'all'
  let sortBy = 'newest'

  if (selectedPricing.value.startsWith('sort:')) {
    sortBy = selectedPricing.value.replace('sort:', '')
  } else {
    pricing = selectedPricing.value
  }

  emit('filter', {
    category: selectedCategory.value,
    pricing,
    sortBy
  })
}

function clear () {
  q.value = ''
  emitSearch()
}
function getDir (txt = '') {
  return /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/.test(txt.trim()) ? 'rtl' : 'ltr'
}
</script>

<style scoped>
header::after {
  content: '';
  position: absolute;
  inset: 0;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  pointer-events: none;
}
</style>

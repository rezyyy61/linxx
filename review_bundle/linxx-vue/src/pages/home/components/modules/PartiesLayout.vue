<template>
  <div class="min-h-screen pt-6 pb-12 bg-gray-50 dark:bg-gray-900">
    <header class="max-w-7xl mx-auto bg-white/70 backdrop-blur sticky top-0 z-30 border-b border-gray-200 dark:bg-gray-800/70 dark:border-gray-700 shadow-sm">
      <div class=" mx-auto px-4 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative">
          <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
            {{ t('home.organizations.title') }}
          </h1>
          <div class="absolute left-0 bottom-0 w-16 h-1 bg-rose-500 rounded-full animate-pulse" />
        </div>

        <div class="w-full md:max-w-lg relative">
          <input
              v-model="q"
              :placeholder="t('home.organizations.search')"
              class="w-full rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm px-12 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500 transition duration-200"
          />
          <Icon icon="mdi:magnify" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
          <button
              v-if="q"
              @click="clearSearch"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-500 transition"
          >
            ✕
          </button>
        </div>
      </div>
    </header>

    <section class="max-w-6xl mx-auto p-4 sm:p-6 space-y-6">
      <div v-if="loading" class="text-center py-10 text-gray-500 dark:text-gray-400">
        {{ t('loading') }}…
      </div>

      <template v-else>
        <div v-if="organizations.length" class="flex flex-col gap-6">
          <PartiesCard v-for="org in organizations" :key="org.id" :profile="org" />
        </div>

        <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
          {{ t('home.organizations.empty') }}
        </div>

        <div v-if="!allLoaded" class="flex justify-center pt-4">
          <button
              @click="fetchMore"
              :disabled="btnLoading"
              class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50"
          >
            <span v-if="btnLoading">{{ t('loading') }}…</span>
            <span v-else>{{ t('loadMore') }}</span>
          </button>
        </div>
      </template>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import publicAxios from '@/lib/axios'
import PartiesCard from '@/pages/home/components/modules/Profile/PartiesCard.vue'
import { Icon } from '@iconify/vue'

const { t } = useI18n()

const organizations = ref([])
const loading = ref(true)
const btnLoading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const allLoaded = ref(false)
const q = ref('')

async function load(page = 1, append = true) {
  const { data } = await publicAxios.get('/api/profile', {
    params: { page, types: ['party'], q: q.value || undefined }
  })
  const { data: items, meta } = data
  const exist = new Set(organizations.value.map(o => o.id))
  const next = items.filter(o => !exist.has(o.id))
  organizations.value = append ? [...organizations.value, ...next] : next
  currentPage.value = meta.current_page + 1
  lastPage.value = meta.last_page
  allLoaded.value = meta.current_page >= meta.last_page
}

async function fetchMore() {
  if (btnLoading.value || allLoaded.value) return
  btnLoading.value = true
  try {
    await load(currentPage.value)
  } finally {
    btnLoading.value = false
  }
}

function clearSearch() {
  q.value = ''
}

let timer
watch(q, () => {
  clearTimeout(timer)
  timer = setTimeout(async () => {
    loading.value = true
    try {
      currentPage.value = 1
      lastPage.value = 1
      allLoaded.value = false
      await load(1, false)
    } finally {
      loading.value = false
    }
  }, 300)
})

onMounted(async () => {
  try {
    await load(1)
  } finally {
    loading.value = false
  }
})
</script>

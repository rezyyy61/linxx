<template>
  <section class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-50">
      {{ $t('organizations.title', 'Political Organizations') }}
    </h1>

    <div v-if="loading" class="text-center py-10 text-gray-500 dark:text-gray-400">
      {{ $t('loading', 'Loading') }}…
    </div>

    <div v-else class="flex flex-col gap-6">
      <PartiesCard
          v-for="org in organizations"
          :key="org.id"
          :profile="org"
      />
    </div>

    <div v-if="!loading && !organizations.length" class="text-center py-10 text-gray-500 dark:text-gray-400">
      {{ $t('organizations.empty', 'No organizations found.') }}
    </div>

    <div v-if="!allLoaded" class="flex justify-center pt-4">
      <button
          @click="fetchMore"
          :disabled="btnLoading"
          class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50"
>
        <span v-if="btnLoading">{{ $t('loading', 'Loading') }}…</span>
        <span v-else>{{ $t('loadMore', 'Load more') }}</span>
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import publicAxios from '@/lib/publicAxios'
import PartiesCard from "@/pages/home/components/modules/party-profile/PartiesCard.vue";

const organizations = ref([])
const loading = ref(true)
const btnLoading = ref(false)
const currentPage = ref(1)
const lastPage = ref(null)
const allLoaded = ref(false)

async function load(page = 1) {
  const res = await publicAxios.get(`/api/political-profiles/organizations?page=${page}`)
  console.log(res.data)
  const { data, meta } = res.data
  organizations.value.push(...data)
  currentPage.value = meta.current_page + 1
  lastPage.value = meta.last_page
  if (currentPage.value > lastPage.value) allLoaded.value = true
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

onMounted(async () => {
  try { await load() } finally { loading.value = false }
})
</script>

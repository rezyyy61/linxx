<template>
  <div class="mx-auto max-w-7xl px-4 py-8">
    <EventsFiltersBar />

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <h2 class="text-2xl font-bold tracking-tight">{{ t('home.event.title') }}</h2>
      <button
          class="rounded-xl px-4 py-2 text-sm border hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
          :disabled="loading"
          @click="reload"
          aria-busy="loading ? 'true' : 'false'"
      >
        {{ t('home.event.refresh') }}
      </button>
    </div>

    <div v-if="loading && !list.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
      <div v-for="n in 6" :key="n" class="rounded-2xl overflow-hidden border border-zinc-200 dark:border-white/10">
        <div class="animate-pulse bg-zinc-100 dark:bg-white/5 aspect-[4/3]"></div>
        <div class="p-5 space-y-3">
          <div class="animate-pulse h-4 w-3/4 rounded bg-zinc-100 dark:bg-white/5"></div>
          <div class="animate-pulse h-3 w-2/3 rounded bg-zinc-100 dark:bg-white/5"></div>
          <div class="animate-pulse h-3 w-1/2 rounded bg-zinc-100 dark:bg-white/5"></div>
        </div>
      </div>
    </div>

    <div v-else-if="error && !list.length" class="py-16 text-center space-y-4">
      <p class="text-sm text-rose-600 dark:text-rose-400">{{ error }}</p>
      <button
          class="rounded-xl bg-zinc-900 text-white px-4 py-2.5 hover:bg-zinc-800 active:bg-zinc-900 transition dark:bg-white dark:text-[#0b0f1a] dark:hover:bg-zinc-200"
          @click="reload"
      >
        {{ t('home.event.retry') }}
      </button>
    </div>

    <div v-else-if="!list.length" class="py-16 text-center space-y-2">
      <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('home.event.empty') }}</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
      <EventCard
          v-for="(ev, i) in list"
          :key="ev.id"
          :ev="ev"
          :index="i"
          @share="openShare"
      />
    </div>

    <div ref="sentinel" class="h-1"></div>

    <div v-if="canLoadMore" class="mt-10 flex justify-center">
      <button
          class="rounded-xl px-5 py-2.5 text-sm border hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
          :disabled="loading"
          @click="loadMore"
      >
        {{ t('home.event.loadMore') }}
      </button>
    </div>

    <ShareModal v-model:modelValue="showShare" :position="position" @select="onSelect" />
  </div>

  <EmbedShareModalEvent
      :visible="showEmbedModal"
      :event="embedTarget"
      @close="showEmbedModal = false"
      @submitted="onEmbedSubmit"
  />
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePublicEventsStore } from '@/stores/events/public/public.store'
import EventsFiltersBar from '@/pages/home/components/modules/events/EventsFiltersBar.vue'
import EventCard from '@/pages/home/components/modules/events/EventCard.vue'
import ShareModal from '@/pages/home/components/modules/posts/Footer/share/ShareModal.vue'
import { useEventShare } from '@/composables/useEventShare'
import EmbedShareModalEvent from "@/pages/home/components/modules/events/EmbedShareModalEvent.vue";
import {usePostStore} from "@/stores/post";

const { showShare, position, openShare, onSelect, showEmbedModal, embedTarget, currentSlug } = useEventShare()
const postStore = usePostStore()

const { t } = useI18n({ useScope: 'global' })

const store = usePublicEventsStore()
const loading = computed(() => store.listLoading)
const error = computed(() => store.listError)
const list = computed(() => store.list)
const canLoadMore = computed(() => store.pagination.page < store.pagination.lastPage)

async function reload() { await store.fetchList({ page: 1 }) }
async function loadMore() {
  if (!canLoadMore.value || loading.value) return
  const next = store.pagination.page + 1
  await store.fetchList({ page: next })
}

const sentinel = ref(null)
let observer
onMounted(() => {
  if (!store.ids.length) reload()
  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((e) => { if (e.isIntersecting) loadMore() })
    }, { rootMargin: '800px 0px 800px 0px' })
    sentinel.value && observer.observe(sentinel.value)
  }
})

async function onEmbedSubmit(payload) {
  if (!currentSlug.value || !embedTarget.value) return
  const text = (payload?.text || '').trim()
  try {
    await postStore.submitReshare({
      shareSlug: currentSlug.value,
      text,
      originalEvent: embedTarget.value
    })
  } catch {
    alert('Reshare failed')
  }
}
onBeforeUnmount(() => { observer?.disconnect() })
</script>

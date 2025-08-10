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

    <!-- Loading skeletons -->
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

    <!-- Error state -->
    <div v-else-if="error && !list.length" class="py-16 text-center space-y-4">
      <p class="text-sm text-rose-600 dark:text-rose-400">{{ error }}</p>
      <button
          class="rounded-xl bg-zinc-900 text-white px-4 py-2.5 hover:bg-zinc-800 active:bg-zinc-900 transition dark:bg-white dark:text-[#0b0f1a] dark:hover:bg-zinc-200"
          @click="reload"
      >
        {{ t('home.event.retry') }}
      </button>
    </div>

    <!-- Empty state -->
    <div v-else-if="!list.length" class="py-16 text-center space-y-2">
      <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ t('home.event.empty') }}</p>
    </div>

    <!-- Cards -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
      <article
          v-for="(ev, i) in list"
          :key="ev.id"
          class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-[#0b0f1a]"
          :aria-labelledby="`ev-title-${ev.id}`"
      >

        <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-white/5 overflow-hidden rounded-t-2xl">
          <RouterLink :to="eventRoute(ev)" class="absolute inset-0 z-10" aria-label="Open event" tabindex="-1" />

          <img
              v-if="coverSrc(ev) && !broken[ev.id]"
              :src="coverSrc(ev)"
              :alt="ev.title || ''"
              :class="[coverClass, loaded[ev.id] ? 'opacity-100 blur-0' : 'opacity-80 blur-[10px]']"
              :sizes="imgSizes"
              :fetchpriority="i < 3 ? 'high' : 'auto'"
              decoding="async"
              loading="lazy"
              @load="markLoaded(ev.id)"
              @error="markBroken(ev.id)"
          />
          <img
              v-else
              :src="defaultCoverUrl"
              :alt="t('home.event.defaultCoverAlt') || 'Default event cover'"
              :class="coverClass"
              :sizes="imgSizes"
              decoding="async"
              loading="lazy"
          />

          <div class="absolute inset-0 z-20 pointer-events-none p-3 sm:p-4">
            <div class="flex items-center gap-2">
              <span v-if="evState(ev).state==='live'" class="chip chip-live">{{ t('home.event.live') }}</span>
              <span v-else-if="evState(ev).state==='upcoming'" class="chip chip-upcoming">
        {{ evState(ev).daysLeft > 0 ? t('home.event.daysLeft', { n: evState(ev).daysLeft }) : t('home.event.today') }}
      </span>
              <span v-else class="chip chip-ended">{{ t('home.event.ended') }}</span>
              <span v-if="ev.mode" class="chip chip-mode">{{ t('home.event.filters.mode.' + ev.mode) }}</span>
              <span v-if="ev.type" class="chip chip-type">{{ t('home.event.types.' + ev.type) }}</span>
            </div>
          </div>

          <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/35 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
        </div>

        <div class="p-5 space-y-3">
          <h3 :id="`ev-title-${ev.id}`" class="truncate text-lg font-semibold text-zinc-900 dark:text-white">{{ ev.title }}</h3>
          <p class="text-sm text-zinc-600 dark:text-zinc-300 line-clamp-2">{{ ev.description || t('home.event.descriptionFallback') }}</p>

          <div class="mt-2 grid gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
            <div class="flex items-center gap-2">
              <Icon icon="mdi:calendar" class="w-4 h-4 opacity-70" />
              <span class="truncate">{{ formatDateReadable(ev.starts_at, ev.ends_at) }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Icon icon="mdi:map-marker" class="w-4 h-4 opacity-70" />
              <span class="truncate">{{ ev.city || t('home.event.locationFallback') }}</span>
            </div>
          </div>

          <div class="pt-3 flex items-center gap-2">
            <RouterLink :to="eventRoute(ev)" class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">
              <Icon icon="mdi:open-in-new" class="w-4 h-4" />
              <span>{{ t('home.event.view') }}</span>
            </RouterLink>

            <!-- Add to calendar -->
            <button
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
                @click.stop="downloadICS(ev)"
                :aria-label="t('home.event.addToCalendar')"
            >
              <Icon icon="mdi:calendar-plus" class="w-4 h-4" />
              <span>{{ t('home.event.addToCalendarShort') || 'Add to calendar' }}</span>
            </button>

            <!-- Share (if supported) -->
            <button
                v-if="canShare"
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
                @click.stop="shareEvent(ev)"
                :aria-label="t('home.event.share')"
            >
              <Icon icon="mdi:share-variant" class="w-4 h-4" />
              <span>{{ t('home.event.share') }}</span>
            </button>
          </div>
        </div>
      </article>
    </div>

    <!-- Infinite scroll sentinel (keeps Load More button too) -->
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
  </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import { usePublicEventsStore } from '@/stores/events/public/public.store'
import { useRouter } from 'vue-router'
import EventsFiltersBar from '@/pages/home/components/modules/events/EventsFiltersBar.vue'
import defaultCoverNeutral from '@/assets/linxx-default-event-cover-neutral.svg'

const router = useRouter()
const i18n = useI18n({ useScope: 'global' })
const { t } = i18n

const store = usePublicEventsStore()

const loading = computed(() => store.listLoading)
const error = computed(() => store.listError)
const list = computed(() => store.list)
const canLoadMore = computed(() => store.pagination.page < store.pagination.lastPage)

// Default cover (neutral, no branding)
const defaultCoverUrl = computed(() => defaultCoverNeutral)

// Blur-up map
const loaded = ref({})
const broken = ref({})
const coverClass = computed(() => 'h-full w-full object-cover transition duration-300 ease-out')
const imgSizes = '(min-width:1280px) 384px, (min-width:640px) 50vw, 100vw'
function markLoaded(id) { loaded.value = { ...loaded.value, [id]: true } }
function coverSrc(ev) { return ev.cover || ev.cover_url || '' }
function markBroken(id) { broken.value = { ...broken.value, [id]: true } }

function toMs(iso) { try { return iso ? new Date(iso).getTime() : null } catch { return null } }
function evState(ev) {
  const now = Date.now()
  const s = toMs(ev.starts_at)
  const e = toMs(ev.ends_at)
  if (s != null && s > now) {
    const daysLeft = Math.max(0, Math.ceil((s - now) / 86400000))
    return { state: 'upcoming', daysLeft }
  }
  if (s != null && s <= now && (e == null || e >= now)) return { state: 'live', daysLeft: 0 }
  return { state: 'ended', daysLeft: 0 }
}

function formatDateReadable(startIso, endIso) {
  if (!startIso) return '—'
  try {
    const start = new Date(startIso)
    const end = endIso ? new Date(endIso) : null
    const opts = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' }
    const startStr = new Intl.DateTimeFormat('en', opts).format(start)
    if (!end) return startStr
    const sameDay = start.toDateString() === end.toDateString()
    const endOpts = sameDay ? { hour: '2-digit', minute: '2-digit' } : opts
    const endStr = new Intl.DateTimeFormat('en', endOpts).format(end)
    return sameDay ? `${startStr} – ${endStr}` : `${startStr} → ${endStr}`
  } catch { return '—' }
}

function eventRoute(ev) {
  const key = ev.slug || ev.id
  return { name: 'public_event', params: { idOrSlug: key } }
}

async function reload() { await store.fetchList({ page: 1 }) }
async function loadMore() {
  if (!canLoadMore.value || loading.value) return
  const next = store.pagination.page + 1
  await store.fetchList({ page: next })
}

// Share
const canShare = typeof navigator !== 'undefined' && !!navigator.share
async function shareEvent(ev) {
  try {
    const route = router.resolve(eventRoute(ev))
    const url = new URL(route.href, window.location.origin).toString()
    const text = ev.description?.slice(0, 140) || ''
    await navigator.share({ title: ev.title, text, url })
  } catch (_) {}
}

// Add to calendar (ICS)
function downloadICS(ev) {
  const dt = (iso) => new Date(iso).toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'
  // Escape text for ICS: newlines, backslashes, commas, semicolons
  const icsEscape = (s) => (s ?? '').toString()
      .replace(/\r?\n/g, ' ')
      .replace(/\\/g, '\\\\')
      .replace(/,/g, '\\,')
      .replace(/;/g, '\\;')

  const uid = `${ev.id || (crypto?.randomUUID?.() || Math.random().toString(36).slice(2))}@linxx`
  const lines = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//Linxx//Default ICS//EN',
    'BEGIN:VEVENT',
    `UID:${uid}`,
    ev.starts_at ? `DTSTART:${dt(ev.starts_at)}` : '',
    ev.ends_at ? `DTEND:${dt(ev.ends_at)}` : '',
    `SUMMARY:${icsEscape(ev.title || 'Event')}`,
    ev.description ? `DESCRIPTION:${icsEscape(ev.description)}` : '',
    ev.city ? `LOCATION:${icsEscape(ev.city)}` : '',
    'END:VEVENT',
    'END:VCALENDAR'
  ].filter(Boolean).join('\r\n')

  const blob = new Blob([lines], { type: 'text/calendar;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${(ev.slug || ev.id || 'event')}.ics`
  document.body.appendChild(a)
  a.click()
  a.remove()
  URL.revokeObjectURL(url)
}

// Infinite scroll
const sentinel = ref(null)
let observer
onMounted(() => {
  if (!store.ids.length) reload()
  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) loadMore()
      })
    }, { rootMargin: '800px 0px 800px 0px' })
    sentinel.value && observer.observe(sentinel.value)
  }
})

onBeforeUnmount(() => { observer?.disconnect() })
</script>

<style scoped>
.chip { @apply inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium; }
.chip-live { @apply bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300; }
.chip-upcoming { @apply bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300; }
.chip-ended { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-mode { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-type { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
</style>

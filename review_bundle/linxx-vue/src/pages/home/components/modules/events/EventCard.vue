<template>
  <article
      class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-[#0b0f1a]"
      :aria-labelledby="`ev-title-${ev.id}`"
  >
    <div class="relative aspect-[4/3] bg-zinc-100 dark:bg-white/5 overflow-hidden rounded-t-2xl">
      <RouterLink :to="eventRoute" class="absolute inset-0 z-10" aria-label="Open event" tabindex="-1" />
      <img
          v-if="cover && !broken"
          :src="cover"
          :alt="ev.title || ''"
          :class="['h-full w-full object-cover transition duration-300 ease-out', loaded ? 'opacity-100 blur-0' : 'opacity-80 blur-[10px]']"
          :sizes="imgSizes"
          :fetchpriority="index < 3 ? 'high' : 'auto'"
          decoding="async"
          loading="lazy"
          @load="loaded = true"
          @error="broken = true"
      />
      <img
          v-else
          :src="fallbackCover"
          :alt="'Default event cover'"
          class="h-full w-full object-cover"
          :sizes="imgSizes"
          decoding="async"
          loading="lazy"
      />

      <div class="absolute inset-0 z-20 pointer-events-none p-3 sm:p-4">
        <div class="flex items-center gap-2">
          <span v-if="state.state==='live'" class="chip chip-live">{{ $t('home.event.live') }}</span>
          <span v-else-if="state.state==='upcoming'" class="chip chip-upcoming">
            {{ state.daysLeft > 0 ? $t('home.event.daysLeft', { n: state.daysLeft }) : $t('home.event.today') }}
          </span>
          <span v-else class="chip chip-ended">{{ $t('home.event.ended') }}</span>
          <span v-if="ev.mode" class="chip chip-mode">{{ $t('home.event.filters.mode.' + ev.mode) }}</span>
          <span v-if="ev.type" class="chip chip-type">{{ $t('home.event.types.' + ev.type) }}</span>
        </div>
      </div>

      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/35 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
    </div>

    <div class="p-5 space-y-3">
      <h3 :id="`ev-title-${ev.id}`" class="truncate text-lg font-semibold text-zinc-900 dark:text-white">{{ ev.title }}</h3>
      <p class="text-sm text-zinc-600 dark:text-zinc-300 line-clamp-2">{{ ev.description || $t('home.event.descriptionFallback') }}</p>

      <div class="mt-2 grid gap-1.5 text-sm text-zinc-700 dark:text-zinc-300">
        <div class="flex items-center gap-2">
          <Icon icon="mdi:calendar" class="w-4 h-4 opacity-70" />
          <span class="truncate">{{ readableDate }}</span>
        </div>
        <div class="flex items-center gap-2">
          <Icon icon="mdi:map-marker" class="w-4 h-4 opacity-70" />
          <span class="truncate">{{ ev.city || $t('home.event.locationFallback') }}</span>
        </div>
      </div>

      <div class="pt-3 flex items-center gap-2">
        <RouterLink :to="eventRoute" class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">
          <Icon icon="mdi:open-in-new" class="w-4 h-4" />
          <span>{{ $t('home.event.view') }}</span>
        </RouterLink>

        <button
            class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
            @click.stop="downloadICS"
            :aria-label="$t('home.event.addToCalendar')"
        >
          <Icon icon="mdi:calendar-plus" class="w-4 h-4" />
          <span>{{ $t('home.event.addToCalendarShort') || 'Add to calendar' }}</span>
        </button>
        <button
            class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
            @click.stop="$emit('share', ev, $event)"
            aria-label="Share event"
        >
          <Icon icon="mdi:share-variant" class="w-4 h-4" />
          <span>Share</span>
        </button>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Icon } from '@iconify/vue'
import defaultCoverNeutral from '@/assets/linxx-default-event-cover-neutral.svg'

const props = defineProps({
  ev: { type: Object, required: true },
  index: { type: Number, default: 0 }
})

const imgSizes = '(min-width:1280px) 384px, (min-width:640px) 50vw, 100vw'
const loaded = ref(false)
const broken = ref(false)
const cover = computed(() => props.ev.cover || props.ev.cover_url || '')
const fallbackCover = computed(() => defaultCoverNeutral)

const eventRoute = computed(() => ({ name: 'public_event', params: { idOrSlug: props.ev.slug || props.ev.id } }))

function toMs(iso) { try { return iso ? new Date(iso).getTime() : null } catch { return null } }
const state = computed(() => {
  const now = Date.now()
  const s = toMs(props.ev.starts_at)
  const e = toMs(props.ev.ends_at)
  if (s != null && s > now) {
    const daysLeft = Math.max(0, Math.ceil((s - now) / 86400000))
    return { state: 'upcoming', daysLeft }
  }
  if (s != null && s <= now && (e == null || e >= now)) return { state: 'live', daysLeft: 0 }
  return { state: 'ended', daysLeft: 0 }
})

const readableDate = computed(() => {
  if (!props.ev.starts_at) return '—'
  try {
    const start = new Date(props.ev.starts_at)
    const end = props.ev.ends_at ? new Date(props.ev.ends_at) : null
    const opts = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' }
    const startStr = new Intl.DateTimeFormat('en', opts).format(start)
    if (!end) return startStr
    const sameDay = start.toDateString() === end.toDateString()
    const endOpts = sameDay ? { hour: '2-digit', minute: '2-digit' } : opts
    const endStr = new Intl.DateTimeFormat('en', endOpts).format(end)
    return sameDay ? `${startStr} – ${endStr}` : `${startStr} → ${endStr}`
  } catch { return '—' }
})

function downloadICS() {
  const dt = (iso) => new Date(iso).toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'
  const esc = (s) => (s ?? '').toString().replace(/\r?\n/g, ' ').replace(/\\/g, '\\\\').replace(/,/g, '\\,').replace(/;/g, '\\;')
  const uid = `${props.ev.id || (crypto?.randomUUID?.() || Math.random().toString(36).slice(2))}@linxx`
  const lines = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//Linxx//Default ICS//EN',
    'BEGIN:VEVENT',
    `UID:${uid}`,
    props.ev.starts_at ? `DTSTART:${dt(props.ev.starts_at)}` : '',
    props.ev.ends_at ? `DTEND:${dt(props.ev.ends_at)}` : '',
    `SUMMARY:${esc(props.ev.title || 'Event')}`,
    props.ev.description ? `DESCRIPTION:${esc(props.ev.description)}` : '',
    props.ev.city ? `LOCATION:${esc(props.ev.city)}` : '',
    'END:VEVENT',
    'END:VCALENDAR'
  ].filter(Boolean).join('\r\n')
  const blob = new Blob([lines], { type: 'text/calendar;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${(props.ev.slug || props.ev.id || 'event')}.ics`
  document.body.appendChild(a)
  a.click()
  a.remove()
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
.chip { @apply inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium; }
.chip-live { @apply bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300; }
.chip-upcoming { @apply bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300; }
.chip-ended { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-mode { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-type { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
</style>

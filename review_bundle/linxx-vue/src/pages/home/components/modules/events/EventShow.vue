<template>
  <div class="mx-auto max-w-5xl px-4 py-8">
    <!-- Header -->
    <div class="mb-4 flex items-center justify-between gap-3">
      <h1 class="text-2xl font-bold tracking-tight truncate">{{ ev?.title || '—' }}</h1>
      <RouterLink
          :to="{ name: 'public_events' }"
          class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm border hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
      >
        <Icon icon="mdi:arrow-left" class="w-4 h-4" />
        <span>{{ t('home.event.show.back') }}</span>
      </RouterLink>
    </div>

    <!-- Skeleton -->
    <div v-if="loading || !ev" class="rounded-2xl overflow-hidden border border-zinc-200 dark:border-white/10">
      <div class="animate-pulse bg-zinc-100 dark:bg-white/5 aspect-[16/9]"></div>
      <div class="p-5 space-y-3">
        <div class="animate-pulse h-5 w-1/2 rounded bg-zinc-100 dark:bg-white/5"></div>
        <div class="animate-pulse h-4 w-2/3 rounded bg-zinc-100 dark:bg-white/5"></div>
      </div>
    </div>

    <!-- Content -->
    <div v-else class="grid gap-6 md:grid-cols-[minmax(0,1fr),360px]">
      <!-- Main (left) -->
      <div class="rounded-2xl overflow-hidden border border-zinc-200 bg-white dark:border-white/10 dark:bg-[#0b0f1a]">
        <!-- Cover -->
        <div class="relative aspect-[16/9] bg-zinc-100 dark:bg-white/5 overflow-hidden">
          <img
              v-if="coverSrc(ev) && !broken[ev.id]"
              :src="coverSrc(ev)"
              :alt="ev.title || ''"
              class="h-full w-full object-cover"
              loading="lazy"
              decoding="async"
              @error="markBroken(ev.id)"
          />
          <img
              v-else
              :src="defaultCoverUrl"
              :alt="t('home.event.defaultCoverAlt') || 'Default event cover'"
              class="h-full w-full object-cover"
              loading="lazy"
              decoding="async"
          />

          <div class="absolute inset-0 z-10 pointer-events-none p-3 sm:p-4">
            <div class="flex items-center gap-2">
              <span v-if="evState(ev).state==='live'" class="chip chip-live">{{ t('home.event.live') }}</span>
              <span v-else-if="evState(ev).state==='upcoming'" class="chip chip-upcoming">
                {{ evState(ev).daysLeft > 0 ? t('home.event.daysLeft', { n: evState(ev).daysLeft }) : t('home.event.today') }}
              </span>
              <span v-else class="chip chip-ended">{{ t('home.event.ended') }}</span>
            </div>
          </div>

          <div class="absolute inset-x-0 bottom-0 z-10">
            <div class="h-24 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4 flex flex-wrap items-center gap-3 text-white/95">
              <div class="inline-flex items-center gap-2 text-sm">
                <Icon icon="mdi:calendar" class="w-4 h-4" />
                <span class="truncate">{{ heroDate }}</span>
              </div>
              <div v-if="displayPlace" class="inline-flex items-center gap-2 text-sm">
                <Icon icon="mdi:map-marker" class="w-4 h-4" />
                <span class="truncate">{{ displayPlace }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Description -->
        <div class="p-5">
          <template v-if="hasDescription">
            <div :class="[expandedDesc ? 'line-clamp-none' : 'line-clamp-6']">
              <p class="text-zinc-700 dark:text-zinc-300 leading-7 whitespace-pre-line">
                {{ ev.description }}
              </p>
            </div>
            <button
                v-if="isLongDesc"
                @click="expandedDesc = !expandedDesc"
                class="mt-2 text-sm inline-flex items-center gap-1 rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
            >
              <Icon :icon="expandedDesc ? 'mdi:chevron-up' : 'mdi:chevron-down'" class="w-4 h-4" />
              <span>{{ expandedDesc ? t('home.common.showLess') : t('home.common.showMore') }}</span>
            </button>
          </template>

          <template v-else>
            <div class="rounded-xl border p-4 text-sm text-zinc-600 dark:text-zinc-300 dark:border-white/10">
              {{ t('home.event.noDescription') }}
            </div>
          </template>
        </div>
      </div>

      <!-- Sidebar (right, sticky) -->
      <aside class="space-y-4 md:sticky md:top-6 h-fit">
        <RsvpPanel :event="ev" :current-user-id="auth.user?.id" />

        <!-- Organizer -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a]">
          <div class="font-medium mb-3">{{ t('home.event.show.organizer') }}</div>

          <component
              :is="organizer.slug ? 'RouterLink' : 'div'"
              :to="organizer.slug ? { path: `/${organizer.slug}` } : undefined"
              class="flex items-center gap-3 group"
              :class="organizer.slug ? 'cursor-pointer' : ''"
              aria-label="Organizer profile"
          >
            <div class="shrink-0 h-12 w-12 rounded-full overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
              <img
                  v-if="organizer.logoUrl"
                  :src="organizer.logoUrl"
                  :alt="organizer.name || 'Organizer'"
                  class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"
                  loading="lazy"
                  decoding="async"
              />
              <div
                  v-else
                  class="h-full w-full grid place-items-center text-white font-semibold transition-transform duration-200 group-hover:scale-105"
                  :style="{ background: organizer.avatarColor || '#ef4444' }"
              >
                {{ organizer.initials }}
              </div>
            </div>

            <div class="min-w-0">
              <div class="font-medium truncate group-hover:underline">{{ organizer.name || '—' }}</div>
              <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">@{{ organizer.slug || '—' }}</div>
              <div class="mt-1 flex flex-wrap gap-1">
                <span v-if="ev.type" class="chip chip-plain">{{ typeLabel(ev) }}</span>
                <span v-if="ev.mode" class="chip chip-plain">{{ modeLabel(ev) }}</span>
                <span v-if="ev.visibility" class="chip chip-plain">{{ ev.visibility }}</span>
              </div>
            </div>
          </component>
        </div>

        <!-- When (toggle event/local) -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a]">
          <div class="flex items-center justify-between gap-3 mb-2">
            <div class="font-medium">{{ t('home.event.show.when') || 'When' }}</div>
            <div class="inline-flex rounded-xl border dark:border-white/10 overflow-hidden text-sm">
              <button class="px-3 py-1.5" :class="timeMode==='event' ? 'bg-zinc-900 text-white dark:bg-white dark:text-[#0b0f1a]' : ''" @click="timeMode='event'">
                {{ t('home.event.show.timeModeEvent') || 'Event time' }}
              </button>
              <button class="px-3 py-1.5" :class="timeMode==='local' ? 'bg-zinc-900 text-white dark:bg-white dark:text-[#0b0f1a]' : ''" @click="timeMode='local'">
                {{ t('home.event.show.timeModeLocal') || 'Local time' }}
              </button>
            </div>
          </div>
          <div class="text-sm text-zinc-700 dark:text-zinc-300">
            <div>{{ timePrimary }}</div>
            <div v-if="timeSecondary" class="text-zinc-500 dark:text-zinc-400">{{ timeSecondary }}</div>
            <div v-if="durationText" class="mt-1 text-[12px] text-zinc-500">{{ durationText }}</div>
            <div class="mt-1 text-[12px] text-zinc-500">{{ timeZoneLabel }}</div>
          </div>
        </div>

        <!-- Where -->
        <div v-if="isOffline || isHybrid" class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a] text-sm">
          <div class="font-medium mb-1">{{ t('home.event.show.where') }}</div>
          <div class="text-zinc-700 dark:text-zinc-300">{{ displayPlace }}</div>
          <div v-if="ev.address" class="text-zinc-500 dark:text-zinc-400">{{ ev.address }}</div>
          <div class="pt-2 flex flex-wrap gap-2">
            <a :href="mapsUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">
              <Icon icon="mdi:map" class="w-4 h-4" />
              <span>{{ t('home.event.show.openMap') || 'Open in Maps' }}</span>
            </a>
            <button @click="copyAddress" class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5">
              <Icon icon="mdi:content-copy" class="w-4 h-4" />
              <span>{{ t('home.common.copy') || 'Copy' }}</span>
            </button>
          </div>
        </div>

        <!-- Online -->
        <div v-if="isOnline || isHybrid" class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a] text-sm">
          <div class="font-medium mb-1">{{ t('home.event.show.online') }}</div>
          <div v-if="ev.platform" class="text-zinc-700 dark:text-zinc-300">{{ ev.platform }}</div>
          <div v-if="ev.meeting_link" class="truncate">
            <a :href="ev.meeting_link" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline">{{ t('home.event.show.joinLink') }}</a>
          </div>
          <div v-if="ev.stream_url" class="truncate">
            <a :href="ev.stream_url" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline">{{ t('home.event.show.streamLink') }}</a>
          </div>
          <div v-if="ev.access_code" class="text-zinc-700 dark:text-zinc-300">
            {{ t('home.event.show.accessCode') }}: <span class="font-mono">{{ ev.access_code }}</span>
          </div>
        </div>

        <!-- Attachments (above Actions) -->
        <div v-if="attachment" class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a] text-sm">
          <div class="font-medium mb-2">{{ t('home.event.show.attachments') }}</div>
          <div class="flex items-center gap-3">
            <Icon :icon="attachment.icon" class="w-5 h-5 opacity-80" />
            <div class="min-w-0 flex-1">
              <div class="truncate">{{ attachment.label }}</div>
              <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ attachment.ext?.toUpperCase() }}</div>
            </div>
            <div class="flex gap-2">
              <a
                  :href="attachment.url"
                  target="_blank"
                  rel="noopener"
                  download
                  class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
              >
                <Icon icon="mdi:download" class="w-4 h-4" />
                <span>{{ t('home.event.show.download') }}</span>
              </a>
              <button
                  @click="copyAttachmentUrl"
                  class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
              >
                <Icon icon="mdi:link-variant" class="w-4 h-4" />
                <span>{{ t('home.common.copyLink') }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-[#0b0f1a]">
          <div class="font-medium mb-2">{{ t('home.event.show.actions') }}</div>
          <div class="flex flex-wrap gap-2">
            <button
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 disabled:opacity-50 dark:border-white/10 dark:hover:bg-white/5"
                :disabled="!ev.starts_at"
                @click="downloadICS(ev)"
            >
              <Icon icon="mdi:calendar-plus" class="w-4 h-4" />
              <span>{{ t('home.event.addToCalendarShort') }}</span>
            </button>
            <a
                :href="googleCalUrl"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
            >
              <Icon icon="mdi:google" class="w-4 h-4" />
              <span>{{ t('home.event.addToGoogle')}}</span>
            </a>
            <button
                v-if="canShare"
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
                @click="shareEvent"
            >
              <Icon icon="mdi:share-variant" class="w-4 h-4" />
              <span>{{ t('home.event.share') }}</span>
            </button>
            <button
                class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
                @click="copyLink"
            >
              <Icon icon="mdi:link-variant" class="w-4 h-4" />
              <span>{{ t('home.common.copyLink') }}</span>
            </button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { usePublicEventsStore } from '@/stores/events/public/public.store'
import defaultCoverUrl from '@/assets/linxx-default-event-cover-neutral.svg'
import { Icon } from '@iconify/vue'
import RsvpPanel from "@/pages/home/components/modules/events/RsvpPanel.vue";
import {useAuthStore} from "@/stores/auth";

const auth = useAuthStore()
const route = useRoute()
const key = route.params.idOrSlug
const { t } = useI18n({ useScope: 'global' })
const store = usePublicEventsStore()

const loading = computed(() => store.fetchingOne)
const ev = ref(null)
const broken = ref({})
const timeMode = ref('event') // 'event' | 'local'

// fetch
async function reload() { ev.value = await store.fetchOne(key, { force: true }) }
onMounted(() => { reload() })

// helpers
function coverSrc(e) { return e.cover || e.cover_url || '' }
function markBroken(id) { broken.value = { ...broken.value, [id]: true } }
function toMs(iso) { try { return iso ? new Date(iso).getTime() : null } catch { return null } }
function evState(e) {
  const now = Date.now(); const s = toMs(e?.starts_at); const en = toMs(e?.ends_at)
  if (s != null && s > now) { const d = Math.max(0, Math.ceil((s - now) / 86400000)); return { state: 'upcoming', daysLeft: d } }
  if (s != null && s <= now && (en == null || en >= now)) return { state: 'live', daysLeft: 0 }
  return { state: 'ended', daysLeft: 0 }
}
function fmtDate(iso, tz) {
  if (!iso) return '—'
  const opt = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' }
  try { return new Intl.DateTimeFormat('en', tz ? { ...opt, timeZone: tz } : opt).format(new Date(iso)) } catch { return '—' }
}

// hero date (always event tz)
const heroDate = computed(() => {
  const s = fmtDate(ev.value?.starts_at, ev.value?.timezone)
  const e = ev.value?.ends_at ? fmtDate(ev.value?.ends_at, ev.value?.timezone) : ''
  return e ? `${s} → ${e}` : s
})

// time block
const timePrimary = computed(() => timeMode.value === 'local'
    ? fmtDate(ev.value?.starts_at)
    : fmtDate(ev.value?.starts_at, ev.value?.timezone)
)
const timeSecondary = computed(() => ev.value?.ends_at
    ? (timeMode.value === 'local' ? fmtDate(ev.value?.ends_at) : fmtDate(ev.value?.ends_at, ev.value?.timezone))
    : ''
)
const timeZoneLabel = computed(() => timeMode.value === 'local'
    ? (new Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local')
    : (ev.value?.timezone || 'UTC')
)

// type/mode labels
function typeLabel(e) { return e?.type ? t(`home.event.types.${e.type}`) : (e?.type_custom || '—') }
function modeLabel(e) { return e?.mode ? t(`home.event.filters.mode.${e.mode}`) : '—' }

// where
const isOnline = computed(() => (ev.value?.mode || '').toLowerCase() === 'online')
const isOffline = computed(() => (ev.value?.mode || '').toLowerCase() === 'offline')
const isHybrid = computed(() => (ev.value?.mode || '').toLowerCase() === 'hybrid')
const displayPlace = computed(() => {
  const parts = [ev.value?.city, ev.value?.venue_name, ev.value?.country].filter(Boolean)
  return parts.join(' · ')
})
const mapsUrl = computed(() => {
  const q = [ev.value?.venue_name, ev.value?.address, ev.value?.city, ev.value?.country].filter(Boolean).join(', ')
  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`
})
function copyAddress() {
  const txt = [ev.value?.venue_name, ev.value?.address, ev.value?.city, ev.value?.country].filter(Boolean).join(', ')
  try { navigator.clipboard?.writeText(txt) } catch {}
}
function copyLink() { try { navigator.clipboard?.writeText(window.location.href) } catch {} }

// duration
const durationText = computed(() => {
  if (!ev.value?.starts_at || !ev.value?.ends_at) return ''
  const s = new Date(ev.value.starts_at).getTime(); const e = new Date(ev.value.ends_at).getTime()
  const mins = Math.max(0, Math.round((e - s) / 60000)); const h = Math.floor(mins / 60); const m = mins % 60
  return h ? `${h}h${m ? ' ' + m : ''}` : `${m}m`
})

// calendar
const googleCalUrl = computed(() => {
  if (!ev.value?.starts_at) return '#'
  const fmt = (iso) => new Date(iso).toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'
  const qs = new URLSearchParams({
    action: 'TEMPLATE',
    text: ev.value.title || 'Event',
    dates: ev.value.ends_at ? `${fmt(ev.value.starts_at)}/${fmt(ev.value.ends_at)}` : `${fmt(ev.value.starts_at)}`,
    details: ev.value.description || '',
    location: [ev.value.city, ev.value.venue_name, ev.value.country].filter(Boolean).join(', '),
    ctz: ev.value.timezone || 'UTC'
  })
  return `https://calendar.google.com/calendar/render?${qs.toString()}`
})
function downloadICS(e) {
  const dt = (iso) => new Date(iso).toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z'
  const esc = (s) => (s ?? '').toString().replace(/\r?\n/g, ' ').replace(/\\/g, '\\\\').replace(/,/g, '\\,').replace(/;/g, '\\;')
  const uid = `${e.id || (crypto?.randomUUID?.() || Math.random().toString(36).slice(2))}@linxx`
  const lines = [
    'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Linxx//Default ICS//EN', 'BEGIN:VEVENT',
    `UID:${uid}`,
    e.starts_at ? `DTSTART:${dt(e.starts_at)}` : '',
    e.ends_at ? `DTEND:${dt(e.ends_at)}` : '',
    `SUMMARY:${esc(e.title || 'Event')}`,
    e.description ? `DESCRIPTION:${esc(e.description)}` : '',
    [e.city, e.venue_name, e.country].some(Boolean) ? `LOCATION:${esc([e.city, e.venue_name, e.country].filter(Boolean).join(', '))}` : '',
    'END:VEVENT', 'END:VCALENDAR'
  ].filter(Boolean).join('\r\n')
  const blob = new Blob([lines], { type: 'text/calendar;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a'); a.href = url; a.download = `${(e.slug || e.id || 'event')}.ics`
  document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url)
}

// shares
const canShare = typeof navigator !== 'undefined' && !!navigator.share
async function shareEvent() { try { await navigator.share({ title: ev.value?.title, text: ev.value?.description, url: window.location.href }) } catch {} }

// description controls
const expandedDesc = ref(false)
const hasDescription = computed(() => !!(ev.value?.description || '').trim())
const isLongDesc = computed(() => {
  const txt = (ev.value?.description || '').trim()
  if (!txt) return false
  const lines = txt.split(/\r?\n/)
  return txt.length > 320 || lines.length > 6
})

// attachment (pretty label + icon, hide UUID)
const attachment = computed(() => {
  const url = ev.value?.attachment_url || ''
  if (!url) return null
  const nameRaw = (ev.value?.attachment_name || '').trim()
  const uuidLike = /^[0-9a-f-]{20,}\.[a-z0-9]+$/i.test(nameRaw)
  const extMatch = url.match(/\.([a-z0-9]{2,5})(?:\?|$)/i)
  const ext = (extMatch ? extMatch[1] : '').toLowerCase()
  const label = uuidLike || !nameRaw
      ? (t('home.event.attachment.generic', { ext: (ext || 'file').toUpperCase() }) || `Attachment (${(ext || 'FILE').toUpperCase()})`)
      : nameRaw
  return { url, label, ext, icon: fileIconByExt(ext) }
})
function fileIconByExt(ext) {
  switch ((ext || '').toLowerCase()) {
    case 'pdf': return 'mdi:file-pdf-box'
    case 'doc':
    case 'docx': return 'mdi:file-word-box'
    case 'ppt':
    case 'pptx': return 'mdi:file-powerpoint-box'
    case 'xls':
    case 'xlsx': return 'mdi:file-excel-box'
    case 'zip':
    case 'rar':
    case '7z': return 'mdi:folder-zip'
    case 'png':
    case 'jpg':
    case 'jpeg':
    case 'webp': return 'mdi:file-image'
    case 'mp4':
    case 'mov': return 'mdi:file-video'
    case 'mp3':
    case 'wav': return 'mdi:file-music'
    default: return 'mdi:paperclip'
  }
}
function copyAttachmentUrl() {
  const url = attachment.value?.url
  if (!url) return
  try { navigator.clipboard?.writeText(url) } catch {}
}

// organizer (creator or created_by)
const organizer = computed(() => {
  const c = ev.value?.creator || {}
  const cb = ev.value?.created_by || {}
  const base = (c && c.id) ? c : cb
  const name = base.name || '—'
  const slug = base.slug || ''
  const logoUrl = base.logo_url || ''
  const avatarColor = base.avatar_color || ''
  const initials = name ? name.trim().split(/\s+/).map(p => p[0]).slice(0, 2).join('').toUpperCase() : 'EV'
  return { name, slug, logoUrl, avatarColor, initials }
})
</script>

<style scoped>
.chip { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium; }
.chip-live { @apply bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300; }
.chip-upcoming { @apply bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300; }
.chip-ended { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-plain { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
</style>

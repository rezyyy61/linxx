<template>
  <article
      class="group relative rounded-2xl border border-zinc-200 bg-white/70 p-3 shadow-sm backdrop-blur-sm transition
           hover:bg-white/90 hover:shadow-md dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/[0.08]"
      :dir="dir"
  >
    <!-- accent -->
    <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-2xl" :class="status.accentClass"></div>

    <!-- HEADER -->
    <div class="mb-3 -mt-1">
      <div
          class="flex items-center justify-between rounded-lg px-3 py-1 ring-1 ring-inset
               bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent
               ring-emerald-500/20 dark:from-emerald-400/10 dark:ring-emerald-400/20"
      >
        <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v2H2V6a2 2 0 0 1 2-2h1V3a1 1 0 1 1 2 0v1Zm15 8v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9h20Z" /></svg>
          Event
        </span>

        <span v-if="priorityTag" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-inset ring-amber-500/20 dark:text-amber-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 9.878 8.146H3.5l5.178 3.763L6.556 18.5 12 14.738 17.444 18.5l-2.122-6.591L20.5 8.146h-6.378L12 2Z" /></svg>
          {{ priorityTag }}
        </span>
      </div>
    </div>

    <!-- BODY -->
    <div class="flex items-stretch gap-3">
      <!-- Cover -->
      <div class="relative w-44 h-28 shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-white/5">
        <div v-if="!imgLoaded && !imgError" class="absolute inset-0 animate-pulse bg-zinc-200/60 dark:bg-white/10"></div>
        <img
            v-if="cover && !imgError"
            :src="cover"
            :alt="event.title || 'Event cover'"
            class="h-full w-full object-cover transition motion-safe:duration-500 motion-safe:group-hover:scale-[1.02]"
            :class="imgLoaded ? 'opacity-100' : 'opacity-80 blur-[6px]'"
            loading="lazy"
            decoding="async"
            @load="onImgLoad"
            @error="onImgError"
        />
        <img
            v-else
            :src="defaultCoverUrl"
            alt="Default event cover"
            class="h-full w-full object-cover"
            loading="lazy"
            decoding="async"
        />
      </div>

      <!-- Meta -->
      <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
          <h4 class="truncate text-base font-semibold text-zinc-900 dark:text-white">
            {{ event.title }}
          </h4>
          <span
class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold tracking-wide"
                :class="status.badgeClass"
>
            {{ status.label }}
          </span>
        </div>

        <p class="mt-1 line-clamp-2 text-sm text-zinc-600 dark:text-zinc-300">
          {{ event.description || '' }}
        </p>

        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-zinc-700 dark:text-zinc-300">
          <span v-if="dateText" class="inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v2H2V6a2 2 0 0 1 2-2h1V3a1 1 0 1 1 2 0v1Zm15 8v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-9h20ZM7 14a1 1 0 0 0 0 2h2a1 1 0 1 0 0-2H7Z" /></svg>
            <span class="truncate">{{ dateText }}</span>
          </span>
          <span v-if="event.city" class="inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7Zm0 9.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" /></svg>
            <span class="truncate">{{ event.city }}</span>
          </span>
          <span v-if="event.user?.name" class="inline-flex items-center gap-1 text-zinc-500 dark:text-zinc-400">
            <span class="inline-grid h-4 w-4 place-items-center rounded-full bg-zinc-200 text-[10px] font-bold text-zinc-700 dark:bg-white/10 dark:text-zinc-200">
              {{ initials }}
            </span>
            <span class="truncate">{{ event.user.name }}</span>
          </span>
        </div>

        <!-- Attachment (outside cover) -->
        <div v-if="attachmentUrl" class="mt-2">
          <a
              :href="attachmentUrl"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-xs hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
              :download="downloadName"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a1 1 0 0 1 1 1v8.586l2.293-2.293 1.414 1.414L12 16.414l-4.707-4.707 1.414-1.414L11 12.586V4a1 1 0 0 1 1-1ZM5 19h14v2H5v-2Z" /></svg>
            <span class="max-w-[14rem] truncate">{{ attachmentLabel }}</span>
          </a>
        </div>

        <!-- Actions -->
        <div class="mt-3 flex items-center gap-2">
          <button
              class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-xs hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
              @click="$emit('open', event)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6v2H6v11h10v-6h2ZM20 2v8h-2V6.414l-9.293 9.293-1.414-1.414L16.586 5H14V3h6Z" /></svg>
            <span>Open</span>
          </button>

          <button
              v-if="canShare"
              class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-xs hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5"
              @click.stop="share()"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 6a4 4 0 1 0-3.874-5H9a3 3 0 0 0-2.684 1.659l-4 8A3 3 0 0 0 4.959 14l1.656-.552-.49 2.45A3 3 0 0 0 9.06 19h1.153l-.386 1.93a1 1 0 1 0 1.958.39L12.896 19H14a3 3 0 0 0 2.912-2.271l1-4A3 3 0 0 0 15.03 9h-3.2l.69-1.38A4 4 0 0 0 14 6Z" /></svg>
            <span>Share</span>
          </button>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from 'vue'
import defaultCoverNeutral from '@/assets/linxx-default-event-cover-neutral.svg'

defineEmits(['open'])
const props = defineProps({ event: { type: Object, required: true } })

function normalizeUrl(u) {
  if (!u || typeof u !== 'string') return null
  const s = u.trim()
  const lastHttp = Math.max(s.lastIndexOf('http://'), s.lastIndexOf('https://'))
  if (lastHttp > 0) return s.slice(lastHttp)
  try { return new URL(s, window.location.origin).toString() } catch { return s }
}

const cover = computed(() => normalizeUrl(props.event.cover_url || props.event.cover))
const attachmentUrl = computed(() => normalizeUrl(props.event.attachment_url))

const attachmentLabel = computed(() => {
  if (!attachmentUrl.value) return ''
  try {
    const u = new URL(attachmentUrl.value)
    const name = u.pathname.split('/').pop() || 'Attachment'
    return name.length > 28 ? name.slice(0, 25) + '…' : name
  } catch { return 'Attachment' }
})
const downloadName = computed(() => (props.event.slug || props.event.id || 'event') + '-attachment')

const imgLoaded = ref(false)
const imgError = ref(false)
function onImgLoad() { imgLoaded.value = true }
function onImgError() { imgError.value = true }

const defaultCoverUrl = defaultCoverNeutral

function fmt(date, opts) { try { return new Intl.DateTimeFormat('en', opts).format(date) } catch { return '' } }
const dateText = computed(() => {
  const s = props.event.starts_at
  const e = props.event.ends_at
  if (!s) return ''
  const start = new Date(s)
  const end = e ? new Date(e) : null
  const sStr = fmt(start, { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' })
  if (!end) return sStr
  const same = start.toDateString() === end.toDateString()
  const eStr = fmt(end, same ? { hour: '2-digit', minute: '2-digit' } : { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' })
  return same ? `${sStr} – ${eStr}` : `${sStr} → ${eStr}`
})

function toMs(iso) { try { return iso ? new Date(iso).getTime() : null } catch { return null } }
const status = computed(() => {
  const now = Date.now()
  const s = toMs(props.event.starts_at)
  const e = toMs(props.event.ends_at)
  if (s != null && s <= now && (e == null || e >= now)) {
    return { type: 'live', label: 'LIVE', badgeClass: 'bg-rose-600 text-white shadow-rose-600/30 shadow-sm', accentClass: 'bg-rose-500', daysLeft: 0 }
  }
  if (s != null && s > now) {
    const daysLeft = Math.max(0, Math.ceil((s - now) / 86400000))
    const label = daysLeft > 0 ? `In ${daysLeft} day${daysLeft > 1 ? 's' : ''}` : 'Today'
    return { type: 'upcoming', label, badgeClass: 'bg-amber-500 text-white shadow-amber-500/30 shadow-sm', accentClass: 'bg-amber-400', daysLeft }
  }
  return { type: 'ended', label: 'Ended', badgeClass: 'bg-zinc-800/80 text-white shadow-zinc-800/20 shadow-sm', accentClass: 'bg-zinc-400', daysLeft: 0 }
})

const priorityTag = computed(() => {
  if (status.value.type === 'live') return 'Live now'
  if (status.value.type === 'upcoming') {
    if (status.value.daysLeft === 0) return 'Starts today'
    if (status.value.daysLeft <= 3) return `Starts in ${status.value.daysLeft} day${status.value.daysLeft > 1 ? 's' : ''}`
  }
  return ''
})

const canShare = typeof navigator !== 'undefined' && !!navigator.share
async function share() {
  try {
    const url = new URL(`/events/${props.event.slug || props.event.id}`, window.location.origin).toString()
    const text = props.event.description?.slice(0, 140) || ''
    await navigator.share({ title: props.event.title, text, url })
  } catch {}
}

const initials = computed(() => {
  const n = props.event.user?.name || ''
  const parts = n.trim().split(/\s+/).slice(0, 2)
  return parts.map(p => p[0]).join('').toUpperCase() || 'U'
})

const dir = computed(() => /[\u0600-\u06FF]/.test(`${props.event.title} ${props.event.city || ''}`) ? 'rtl' : 'ltr')
</script>

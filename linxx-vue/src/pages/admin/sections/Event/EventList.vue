<template>
  <div class="rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#0b0f1a] shadow-sm" :dir="isRTL ? 'rtl' : 'ltr'">
    <div class="p-5 sm:p-6">
      <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <div v-for="n in 6" :key="n" class="rounded-2xl border overflow-hidden border-zinc-200 dark:border-white/10">
          <div class="animate-pulse bg-zinc-100 dark:bg-white/5 h-40"></div>
          <div class="p-4 space-y-2">
            <div class="animate-pulse h-4 w-2/3 rounded bg-zinc-100 dark:bg-white/5"></div>
            <div class="animate-pulse h-3 w-1/2 rounded bg-zinc-100 dark:bg-white/5"></div>
            <div class="animate-pulse h-3 w-1/3 rounded bg-zinc-100 dark:bg-white/5"></div>
          </div>
        </div>
      </div>

      <div v-else-if="error" class="flex flex-col items-center justify-center py-12 text-center gap-3">
        <p class="text-sm text-rose-600 dark:text-rose-400">{{ t('dashboard.common.retry_error') || error }}</p>
        <button @click="reload" class="rounded-xl bg-zinc-900 text-white px-4 py-2.5 hover:bg-zinc-800 active:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-700/60 dark:bg-white dark:text-[#0b0f1a] dark:hover:bg-zinc-200">
          {{ t('dashboard.common.retry') }}
        </button>
      </div>

      <div v-else-if="!filtered.length" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-zinc-300 dark:text-white/15" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m-7 4h8m-9 4h10M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z" />
          </svg>
        </div>
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ t('dashboard.event.list.empty.title') }}</h2>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ t('dashboard.event.list.empty.desc') }}</p>
        <button @click="$emit('create')" class="mt-6 rounded-xl bg-zinc-900 text-white px-4 py-2.5 hover:bg-zinc-800 active:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-700/60 dark:bg-white dark:text-[#0b0f1a] dark:hover:bg-zinc-200">
          {{ t('dashboard.event.list.empty.cta') }}
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <article
            v-for="ev in filtered"
            :key="ev.id"
            class="group relative rounded-2xl border border-zinc-200 bg-white transition hover:shadow-sm dark:border-white/10 dark:bg-[#0b0f1a]"
        >
          <div class="p-4 flex gap-4">
            <!-- Thumbnail -->
            <div class="w-28 shrink-0">
              <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-zinc-100 dark:bg-white/10">
                <img
                    v-if="coverSrc(ev) && !broken[ev.id]"
                    :src="coverSrc(ev)"
                    :alt="ev.title || ''"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    @error="markBroken(ev.id)"
                />
                <div v-else class="h-full w-full grid place-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-400 dark:text-zinc-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5.25 3A2.25...Z" />
                  </svg>
                </div>

                <!-- state chip روی تصویر کوچک -->
                <div class="absolute top-1 left-1">
                  <span v-if="state(ev).state==='live'" class="chip chip-live">Live</span>
                  <span v-else-if="state(ev).state==='upcoming'" class="chip chip-upcoming">
              {{ state(ev).daysLeft > 0 ? t('dashboard.event.state.days_left', { n: state(ev).daysLeft }) : (t('dashboard.event.state.today') || 'Today') }}
            </span>
                  <span v-else class="chip chip-ended">Ended</span>
                </div>
              </div>
            </div>

            <!-- Info -->
            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-2">
                <h3 class="truncate font-semibold text-zinc-900 dark:text-white leading-tight">{{ ev.title }}</h3>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px]" :class="statusClass(ev.status)">
            {{ t(`dashboard.event.statuses.${(ev.status || 'draft').toLowerCase()}`) }}
          </span>
              </div>

              <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400 truncate">
                {{ ev.city || t('dashboard.event.list.location_fallback') }}
              </div>

              <div class="mt-2 flex flex-wrap items-center gap-1">
                <span class="chip chip-date">{{ formatDateEn(ev.starts_at) }}</span>
                <span v-if="showJalali !== false" class="chip chip-date-alt">{{ formatDateFa(ev.starts_at) }}</span>
                <span class="chip bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300">
            {{ t(`dashboard.event.visibility.${(ev.visibility || 'public').toLowerCase()}`) }}
          </span>
              </div>

              <!-- Progress (اختیاری) -->
              <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                <div class="h-full rounded-full bg-gradient-to-r from-fuchsia-500 via-rose-500 to-orange-500 transition-[width]" :style="{ width: progress(ev) + '%' }" />
              </div>

              <!-- RSVP summary -->
              <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                <button
                    class="ml-auto rounded-lg border px-2.5 py-1.5 hover:bg-zinc-50 text-xs dark:border-white/10 dark:hover:bg-white/5"
                    @click="openAttendees(ev)"
                >
                  {{ t('dashboard.event.rsvp.view') || 'View attendees' }}
                </button>
              </div>

              <!-- Actions -->
              <div class="mt-3 flex flex-wrap items-center gap-2">
                <EventStatusMenu :event="ev" align="end" @changed="reload" />
                <EventInviteButton v-if="canInvite(ev)" :event="ev" @sent="handleInviteSent" />
                <button class="rounded-lg border px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-white/10 dark:hover:bg-white/5" @click="$emit('edit', ev)">
                  {{ t('dashboard.event.actions.edit') }}
                </button>
                <button
                    class="rounded-lg border px-3 py-1.5 text-sm text-rose-600 hover:bg-rose-50 dark:border-white/10 dark:hover:bg-rose-500/10"
                    :disabled="deletingId===ev.id"
                    @click="removeEvent(ev.id)"
                >
                  <span v-if="deletingId===ev.id">{{ t('dashboard.event.actions.deleting') }}</span>
                  <span v-else>{{ t('dashboard.event.actions.delete') }}</span>
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>
      <AttendeesDrawer
          v-if="attendeesFor"
          :event="attendeesFor"
          @close="attendeesFor=null"
      />

      <p v-if="deleteError" class="mt-4 text-sm text-rose-600 dark:text-rose-400">{{ deleteError }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useEventStore } from '@/stores/events/events.store'
import EventInviteButton from '@/pages/admin/sections/Event/sections/EventInviteButton.vue'
import EventStatusMenu from '@/pages/admin/sections/Event/sections/EventStatusMenu.vue'
import AttendeesDrawer from "@/pages/admin/sections/Event/sections/AttendeesDrawer.vue";

const props = defineProps({
  searchQuery: { type: String, default: '' },
  statusFilter: { type: String, default: 'all' },
  coverMode: { type: String, default: 'cover' },
  showJalali: { type: Boolean, default: undefined }
})
const emit = defineEmits(['edit', 'create', 'deleted'])

const attendeesFor = ref(null)
function openAttendees(ev) { attendeesFor.value = ev }

const { t, locale } = useI18n({ useScope: 'global' })
const isRTL = computed(() => ['fa', 'ar', 'he', 'ur', 'ps', 'ku'].includes(String(locale?.value || '').toLowerCase().split('-')[0]))
const store = useEventStore()
const isLoading = computed(() => store.listLoading)
const error = computed(() => store.listError)
const deleteError = computed(() => store.deleteError)
const events = computed(() => store.list)

const deletingId = ref(null)
const broken = ref({})

const coverClass = computed(() => ['h-full w-full', props.coverMode === 'contain' ? 'object-contain bg-white dark:bg-[#0b0f1a]' : 'object-cover'].join(' '))

function markBroken (id) {
  broken.value = { ...broken.value, [id]: true }
}
function coverSrc(ev) {
  const base = ev.cover || ev.cover_url || ev.coverUrl || (ev.cover_path ? `/storage/${ev.cover_path}` : '')
  if (!base) return ''
  const v = ev.cover_updated_at || ev.updated_at || ''
  return v ? `${base}${base.includes('?') ? '&' : '?'}v=${encodeURIComponent(v)}` : base
}

function statusClass (s) {
  const v = (s || 'draft').toLowerCase()
  if (v === 'published' || v === 'active') return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
  if (v === 'cancelled') return 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'
  return 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'
}

const filtered = computed(() => {
  const list = [...(events.value || [])]
  const q = props.searchQuery.trim().toLowerCase()
  const status = props.statusFilter
  const normalized = status === 'active' ? 'published' : status
  return list
      .filter((ev) => {
        const hay = [ev.title, ev.description, ev.venue_name, ev.address, ev.city, ev.country].filter(Boolean)
        const matchesQuery = !q || hay.some((v) => String(v).toLowerCase().includes(q))
        const matchesStatus = normalized === 'all' || (ev.status || '').toLowerCase() === normalized
        return matchesQuery && matchesStatus
      })
      .sort((a, b) => new Date(b.starts_at || 0) - new Date(a.starts_at || 0))
})

function toMs (iso) { try { return iso ? new Date(iso).getTime() : null } catch { return null } }
function state (ev) {
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
function canInvite (ev) {
  const now = Date.now()
  const s = toMs(ev.starts_at)
  const e = toMs(ev.ends_at)
  if (e != null) return e >= now
  if (s != null) return s >= now || s <= now
  return true
}
function progress (ev) {
  const s = toMs(ev.starts_at)
  const e = toMs(ev.ends_at)
  const now = Date.now()
  if (s == null || e == null || e <= s) return 0
  if (now <= s) return 0
  if (now >= e) return 100
  return Math.max(0, Math.min(100, ((now - s) / (e - s)) * 100))
}

function formatDateEn (iso) {
  if (!iso) return '—'
  try {
    const d = new Date(iso)
    return new Intl.DateTimeFormat('en-US-u-ca-gregory', { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(d)
  } catch { return '—' }
}
function formatDateFa (iso) {
  if (!iso) return '—'
  try {
    const d = new Date(iso)
    return new Intl.DateTimeFormat('fa-IR-u-ca-persian', { year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' }).format(d)
  } catch { return '—' }
}

const reload = () => store.fetchList().catch(() => {})
onMounted(() => { if (!store.ids.length) reload() })

async function removeEvent (id) {
  deletingId.value = id
  try {
    await store.remove(id)
    emit('deleted', id)
  } finally {
    deletingId.value = null
  }
}
function handleInviteSent () {}
</script>

<style scoped>
.chip { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium; }
.chip-live { @apply bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300; }
.chip-upcoming { @apply bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300; }
.chip-ended { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-300; }
.chip-date { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200; }
.chip-date-alt { @apply bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200; }
</style>

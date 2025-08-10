<template>
  <div class="mx-auto max-w-5xl px-4 py-6 text-[15px] md:text-base">
    <nav class="rounded-2xl bg-gradient-to-r from-rose-500 via-pink-500 to-red-500 p-[1px] shadow">
      <ol class="rounded-2xl bg-white dark:bg-zinc-900 grid grid-cols-4 divide-x divide-gray-100 dark:divide-white/10">
        <li :class="stepClass(1)" @click="go(1)"><Icon icon="mdi:clipboard-text-outline" class="w-5 h-5" /><span>{{ $t('dashboard.event.sections.general') }}</span></li>
        <li :class="stepClass(2)" @click="go(2)"><Icon icon="mdi:clock-outline" class="w-5 h-5" /><span>{{ $t('dashboard.event.sections.time') }}</span></li>
        <li :class="stepClass(3)" @click="go(3)"><Icon icon="mdi:map-marker-outline" class="w-5 h-5" /><span>{{ $t('dashboard.event.sections.location') }}</span></li>
        <li :class="stepClass(4)" @click="go(4)"><Icon icon="mdi:image-multiple-outline" class="w-5 h-5" /><span>{{ $t('dashboard.event.sections.media') }}</span></li>
      </ol>
    </nav>

    <div class="mt-8 grid gap-6 md:grid-cols-[1fr,340px]">
      <form class="space-y-6" @submit.prevent="emitSubmit">
        <GeneralSection v-if="step===1" v-model="generalModel" :title="$t('dashboard.event.sections.general')" />
        <TimeSection v-if="step===2" v-model="timeModel" :title="$t('dashboard.event.sections.time')" />
        <LocationSection v-if="step===3" v-model="locationModel" :mode="form.mode" :title="$t('dashboard.event.sections.location')" />
        <MediaSection v-if="step===4" v-model="media" />

        <div v-if="serverError" class="error-box">{{ serverError }}</div>

        <div class="sticky-actions justify-between">
          <button type="button" class="btn-secondary" @click="back" :disabled="isSubmitting || step===1">
            <Icon icon="mdi:chevron-left" class="w-4 h-4 mr-1" />
            {{ $t('dashboard.event.common.back') }}
          </button>
          <div class="flex items-center gap-3">
            <button v-if="step<4" type="button" class="btn-primary" @click="next" :disabled="isSubmitting">
              {{ $t('dashboard.event.common.next') }}
              <Icon icon="mdi:chevron-right" class="w-4 h-4 ml-1" />
            </button>
            <button v-else type="submit" class="btn-primary" :disabled="isSubmitting">
              <Icon icon="mdi:check-circle-outline" class="w-4 h-4 mr-1" />
              {{ form.id ? $t('dashboard.event.actions.save') : $t('dashboard.event.actions.create') }}
            </button>
          </div>
        </div>
      </form>

      <aside class="hidden md:block">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-900 sticky top-20">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">{{ $t('dashboard.event.preview.title') }}</h3>
            <span class="rounded-full bg-rose-100 text-rose-700 text-xs px-2 py-1 dark:bg-rose-500/20 dark:text-rose-200">{{ form.visibility }}</span>
          </div>
          <div class="mt-4 space-y-3 text-sm">
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <Icon icon="mdi:calendar" class="w-4 h-4" />
              <span>{{ prettyStart }}</span>
            </div>
            <div v-if="prettyEnd" class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <Icon icon="mdi:calendar-end" class="w-4 h-4" />
              <span>{{ prettyEnd }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
              <Icon icon="mdi:map-marker" class="w-4 h-4" />
              <span>{{ form.city || '-' }} · {{ form.venueName || '-' }}</span>
            </div>
            <div class="pt-2">
              <p class="text-base font-semibold truncate">{{ form.title || $t('dashboard.event.preview.placeholderTitle') }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3">{{ form.description || $t('dashboard.event.preview.placeholderDesc') }}</p>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, ref, watch } from 'vue'
import { Icon } from '@iconify/vue'
import { useEventStore } from '@/stores/events/events.store'
import TimeSection from '@/pages/admin/sections/Event/sections/TimeSection.vue'
import LocationSection from '@/pages/admin/sections/Event/sections/LocationSection.vue'
import MediaSection from '@/pages/admin/sections/Event/sections/MediaSection.vue'
import GeneralSection from '@/pages/admin/sections/Event/sections/GeneralSection.vue'

const props = defineProps({ modelValue: { type: Object, default: () => null } })
const emit = defineEmits(['submit', 'cancel', 'update:modelValue'])

const store = useEventStore()
const step = ref(1)
const isSubmitting = computed(() => store.creating || store.saving)
const serverError = computed(() => store.createError || store.saveError)

const form = reactive({
  id: null,
  title: '',
  description: '',
  visibility: 'public',
  mode: 'offline',
  type: 'rally_protest',
  typeCustom: '',
  timezone: 'Europe/Berlin',
  city: '',
  address: '',
  venueName: '',
  country: '',
  platform: '',
  streamUrl: '',
  meetingLink: '',
  accessCode: '',
  startDate: '',
  startHour: '08',
  startMinute: '00',
  endDate: '',
  endHour: '10',
  endMinute: '00'
})

const media = ref({
  cover: null,
  attachment: null,
  coverUrl: '',
  attachmentUrl: '',
  attachmentName: '',
  removeCover: false,
  removeAttachment: false
})

const typeKeys = ['rally_protest', 'town_hall', 'meeting_internal', 'training', 'webinar', 'fundraiser', 'custom']

watch(
    () => props.modelValue,
    v => {
      const src = v && typeof v === 'object' ? v : null
      if (!src) {
        Object.assign(form, {
          id: null,
          title: '',
          description: '',
          visibility: 'public',
          mode: 'offline',
          type: 'rally_protest',
          typeCustom: '',
          timezone: 'Europe/Berlin',
          city: '',
          address: '',
          venueName: '',
          country: '',
          platform: '',
          streamUrl: '',
          meetingLink: '',
          accessCode: '',
          startDate: '',
          startHour: '08',
          startMinute: '00',
          endDate: '',
          endHour: '10',
          endMinute: '00'
        })
        media.value = {
          cover: null,
          attachment: null,
          coverUrl: '',
          attachmentUrl: '',
          attachmentName: '',
          removeCover: false,
          removeAttachment: false
        }
        step.value = 1
        return
      }

      const toParts = iso => {
        if (!iso) return { date: '', hour: '00', minute: '00' }
        const s = String(iso)
        const [d, tf = '00:00'] = s.split('T')
        const t = tf.replace('Z', '').split(/[+-]/)[0]
        const [h = '00', m = '00'] = t.split(':')
        return { date: d, hour: h.padStart(2, '0'), minute: m.padStart(2, '0') }
      }

      const s = toParts(src.starts_at)
      const e = toParts(src.ends_at)
      const incomingType = String(src.type || '').trim()
      const isPreset = typeKeys.includes(incomingType)

      Object.assign(form, {
        id: src.id ?? null,
        title: src.title ?? '',
        description: src.description ?? '',
        visibility: src.visibility ?? 'public',
        mode: src.mode ?? 'offline',
        type: isPreset ? incomingType : 'custom',
        typeCustom: isPreset ? (src.type_custom || src.typeCustom || '') : (incomingType || ''),
        timezone: src.timezone ?? form.timezone,
        city: src.city ?? '',
        address: src.address ?? '',
        venueName: src.venue_name ?? src.venueName ?? '',
        country: src.country ?? '',
        platform: src.platform ?? '',
        streamUrl: src.stream_url ?? '',
        meetingLink: src.meeting_link ?? '',
        accessCode: src.access_code ?? '',
        startDate: s.date,
        startHour: s.hour,
        startMinute: s.minute,
        endDate: e.date,
        endHour: e.hour,
        endMinute: e.minute
      })

      const coverUrl = src.cover ?? src.cover_url ?? src.coverUrl ?? ''
      const attachmentUrl = src.attachment_url ?? src.attachmentUrl ?? ''
      const attachmentName = src.attachment_name ?? src.attachmentName ?? ''
      media.value = {
        cover: null,
        attachment: null,
        coverUrl,
        attachmentUrl,
        attachmentName,
        removeCover: false,
        removeAttachment: false
      }
      step.value = 1
    },
    { immediate: true }
)

const generalModel = computed({
  get: () => ({ title: form.title, description: form.description, type: form.type, typeCustom: form.typeCustom, visibility: form.visibility, mode: form.mode }),
  set: v => {
    if (!v) return
    form.title = v.title ?? form.title
    form.description = v.description ?? form.description
    form.type = v.type ?? form.type
    form.typeCustom = v.type === 'custom' ? (v.typeCustom || '') : ''
    form.visibility = v.visibility ?? form.visibility
    form.mode = v.mode ?? form.mode
  }
})

const timeModel = computed({
  get: () => ({
    start_date: form.startDate,
    start_hour: form.startHour,
    start_minute: form.startMinute,
    end_date: form.endDate,
    end_hour: form.endHour,
    end_minute: form.endMinute,
    timezone: form.timezone
  }),
  set: v => {
    if (!v) return
    form.startDate = v.start_date ?? form.startDate
    form.startHour = v.start_hour ?? form.startHour
    form.startMinute = v.start_minute ?? form.startMinute
    form.endDate = v.end_date ?? form.endDate
    form.endHour = v.end_hour ?? form.endHour
    form.endMinute = v.end_minute ?? form.endMinute
    form.timezone = v.timezone ?? form.timezone
  }
})

const locationModel = computed({
  get: () => ({
    city: form.city,
    address: form.address,
    venue_name: form.venueName,
    country: form.country,
    platform: form.platform,
    stream_url: form.streamUrl,
    meeting_link: form.meetingLink,
    access_code: form.accessCode
  }),
  set: v => {
    if (!v) return
    form.city = v.city ?? form.city
    form.address = v.address ?? form.address
    form.venueName = v.venue_name ?? form.venueName
    form.country = v.country ?? form.country
    form.platform = v.platform ?? form.platform
    form.streamUrl = v.stream_url ?? form.streamUrl
    form.meetingLink = v.meeting_link ?? form.meetingLink
    form.accessCode = v.access_code ?? form.accessCode
  }
})

function next() { step.value = Math.min(4, step.value + 1) }
function back() { step.value = Math.max(1, step.value - 1) }
function go(n) { step.value = n }

function emitSubmit() {
  const payload = {
    title: form.title,
    description: form.description,
    visibility: form.visibility,
    mode: form.mode,
    type: form.type,
    timezone: form.timezone,
    city: form.city || null,
    address: form.address || null,
    venue_name: form.venueName || null,
    country: form.country || null,
    platform: form.platform || null,
    stream_url: form.streamUrl || null,
    meeting_link: form.meetingLink || null,
    access_code: form.accessCode || null
  }

  if (form.type === 'custom' && form.typeCustom) payload.type_custom = form.typeCustom

  if (form.startDate && form.startHour && form.startMinute) {
    payload.starts_at = `${form.startDate} ${String(form.startHour).padStart(2, '0')}:${String(form.startMinute).padStart(2, '0')}:00`
  }
  if (form.endDate && form.endHour && form.endMinute) {
    payload.ends_at = `${form.endDate} ${String(form.endHour).padStart(2, '0')}:${String(form.endMinute).padStart(2, '0')}:00`
  }

  if (media.value.removeCover) payload.remove_cover = true
  if (media.value.removeAttachment) payload.remove_attachment = true

  const files = { cover: media.value.cover || null, attachment: media.value.attachment || null }
  emit('submit', { id: form.id, ...payload, files })
}

const pad = n => String(n).padStart(2, '0')
const prettyStart = computed(() => form.startDate ? `${form.startDate} ${pad(form.startHour)}:${pad(form.startMinute)} (${form.timezone})` : '-')
const prettyEnd = computed(() => form.endDate ? `${form.endDate} ${pad(form.endHour)}:${pad(form.endMinute)} (${form.timezone})` : '')

function stepClass(n) {
  return [
    'flex cursor-pointer items-center justify-center gap-2 px-3 py-2 text-sm',
    'transition',
    step.value === n ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-200' : 'hover:bg-gray-50 dark:hover:bg-white/5',
    n === 1 ? 'rounded-l-2xl' : '',
    n === 4 ? 'rounded-r-2xl' : ''
  ].join(' ')
}
</script>

<style scoped>
.card { @apply rounded-2xl border border-gray-200 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm space-y-4; }
.section-title { @apply text-xl font-semibold mb-2 flex items-center gap-2; }
.input { @apply w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-base transition focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-400 dark:bg-zinc-900 dark:border-white/10 dark:text-white; }
.input-lg { @apply py-3; }
.btn-primary { @apply inline-flex items-center rounded-xl bg-rose-600 px-4 py-2.5 text-base font-semibold text-white shadow-sm hover:bg-rose-700 disabled:opacity-60; }
.btn-secondary { @apply inline-flex items-center rounded-xl bg-gray-100 px-4 py-2.5 text-base font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/15 disabled:opacity-60; }
.error-box { @apply rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-300; }
.sticky-actions { @apply sticky bottom-4 z-10 flex items-center gap-3 rounded-2xl border border-gray-200 bg-white/95 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-zinc-900/85; }
</style>

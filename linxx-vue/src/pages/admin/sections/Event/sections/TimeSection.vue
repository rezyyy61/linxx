<template>
  <section class="card">
    <h2 class="section-title">
      <Icon icon="mdi:clock-outline" class="text-amber-500 w-5 h-5" />
      {{ title || $t('dashboard.event.sections.time') }}
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">
          {{ $t('dashboard.event.fields.start_date') }}
          <span v-if="required" class="text-red-500">*</span>
        </label>
        <VueDatePicker
            v-model="local.start"
            :enable-time="true"
            :is-24="true"
            :minutes-increment="5"
            :auto-apply="true"
            :clearable="true"
            :locale="dpLocale"
            :dir="isRtl ? 'rtl' : 'ltr'"
            class="input input-lg"
        >
          <template #clock-icon>
            <span class="dp-clock-chip" aria-hidden="true">
              <Icon icon="mdi:clock-time-four-outline" class="dp-clock-icon" />
            </span>
          </template>
        </VueDatePicker>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">
          {{ $t('dashboard.event.fields.end_date') }}
        </label>
        <VueDatePicker
            v-model="local.end"
            :enable-time="true"
            :is-24="true"
            :minutes-increment="5"
            :auto-apply="true"
            :clearable="true"
            :locale="dpLocale"
            :dir="isRtl ? 'rtl' : 'ltr'"
            class="input input-lg"
        >
          <template #clock-icon>
            <span class="dp-clock-chip" aria-hidden="true">
              <Icon icon="mdi:clock-time-four-outline" class="dp-clock-icon" />
            </span>
          </template>
        </VueDatePicker>
      </div>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">{{ $t('dashboard.event.fields.timezone') }}</label>
        <select v-model="local.timezone" class="input input-lg">
          <option v-for="z in zones" :key="z" :value="z">{{ z }}</option>
        </select>
      </div>
      <div class="self-end text-sm text-gray-600 dark:text-gray-300">
        <div v-if="local.start">{{ $t('dashboard.event.preview.start') }}: {{ pretty(local.start) }}</div>
        <div v-if="local.end">{{ $t('dashboard.event.preview.end') }}: {{ pretty(local.end) }}</div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { faIR, enUS } from 'date-fns/locale'

const props = defineProps({ modelValue: { type: Object, required: true }, title: { type: String, default: '' }, required: { type: Boolean, default: true } })
const emit = defineEmits(['update:modelValue'])
const { locale } = useI18n({ useScope: 'global' })

const local = reactive({ start: null, end: null, timezone: 'Europe/Berlin' })
const zones = ['Europe/Berlin', 'Europe/London', 'UTC', 'America/New_York', 'America/Los_Angeles', 'Asia/Tehran']
const isRtl = computed(() => ['fa', 'ar', 'he', 'ur', 'ps', 'ku'].includes(String(locale.value || '').split('-')[0].toLowerCase()))
const dpLocale = computed(() => (String(locale.value || '').startsWith('fa') ? faIR : enUS))

watch(() => props.modelValue, v => {
  local.start = toDate(v?.start_date, v?.start_hour, v?.start_minute)
  local.end = toDate(v?.end_date, v?.end_hour, v?.end_minute)
  local.timezone = v?.timezone || 'Europe/Berlin'
}, { immediate: true })

watch(local, () => {
  const s = fromDate(local.start)
  const e = fromDate(local.end)
  emit('update:modelValue', {
    start_date: s.date,
start_hour: s.hour,
start_minute: s.minute,
    end_date: e.date,
end_hour: e.hour,
end_minute: e.minute,
    timezone: local.timezone
  })
}, { deep: true })

function toDate(d, h, m) { if (!d) return null; const hh = String(h || '00').padStart(2, '0'); const mm = String(m || '00').padStart(2, '0'); return new Date(`${d}T${hh}:${mm}`) }
function fromDate(d) { if (!d) return {date: '', hour: '00', minute: '00'}; const y = d.getFullYear(); const mo = String(d.getMonth() + 1).padStart(2, '0'); const da = String(d.getDate()).padStart(2, '0'); const hh = String(d.getHours()).padStart(2, '0'); const mm = String(d.getMinutes()).padStart(2, '0'); return {date: `${y}-${mo}-${da}`, hour: hh, minute: mm} }
function pretty(d) { try { return new Intl.DateTimeFormat(locale.value || 'en', {year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit'}).format(d) + (local.timezone ? ` (${local.timezone})` : '') } catch { return '' } }
</script>

<style scoped>
.card{ @apply rounded-2xl border border-gray-200 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm space-y-4; }
.section-title{ @apply text-xl font-semibold mb-2 flex items-center gap-2; }
.input{ @apply w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-500/30 dark:bg-zinc-900 dark:border-white/10 dark:text-white; }
.input-lg{ @apply py-3; }
.dp-clock-chip{
  display:inline-flex; align-items:center; justify-content:center;
  width:28px; height:28px; border-radius:9999px;
  background: linear-gradient(135deg,#f43f5e 0%,#f97316 100%);
  color:#fff; box-shadow:0 2px 6px rgba(244,63,94,.25);
}
.dp-clock-icon{ width:16px; height:16px; }
</style>

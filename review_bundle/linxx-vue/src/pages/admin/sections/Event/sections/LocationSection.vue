<template>
  <section class="card">
    <h2 class="section-title">
      <Icon icon="mdi:map-marker-outline" class="text-emerald-500 w-5 h-5" />
      {{ $t('dashboard.event.sections.location') }}
    </h2>

    <div v-if="mode==='offline'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label>{{ $t('dashboard.event.fields.city') }}</label>
        <input v-model.trim="local.city" type="text" class="input input-lg" dir="auto"/>
      </div>
      <div>
        <label>{{ $t('dashboard.event.fields.venue_name') }}</label>
        <input v-model.trim="local.venue_name" type="text" class="input input-lg" dir="auto"/>
      </div>
      <div class="md:col-span-2">
        <label>{{ $t('dashboard.event.fields.address') }}</label>
        <input v-model.trim="local.address" type="text" class="input input-lg" dir="auto"/>
      </div>
      <div class="md:col-span-2">
        <label>{{ $t('dashboard.event.fields.country') }}</label>
        <input v-model.trim="local.country" class="input input-lg" placeholder="DE" dir="auto"/>
      </div>
    </div>

    <div v-else-if="mode==='online'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label>{{ $t('dashboard.event.fields.platform') }}</label>
        <select v-model="local.platform" class="input input-lg">
          <option v-for="p in platformOptions" :key="p" :value="p">{{ p }}</option>
        </select>
      </div>
      <div>
        <label>{{ $t('dashboard.event.fields.link') }}</label>
        <input v-model.trim="local.meeting_link" type="url" placeholder="https://…" class="input input-lg" dir="auto"/>
      </div>
      <div class="md:col-span-2">
        <label>{{ $t('dashboard.event.fields.access_code') }}</label>
        <input v-model.trim="local.access_code" type="text" class="input input-lg" dir="auto"/>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 gap-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label>{{ $t('dashboard.event.fields.city') }}</label>
          <input v-model.trim="local.city" type="text" class="input input-lg" dir="auto"/>
        </div>
        <div>
          <label>{{ $t('dashboard.event.fields.venue_name') }}</label>
          <input v-model.trim="local.venue_name" type="text" class="input input-lg" dir="auto"/>
        </div>
        <div class="md:col-span-2">
          <label>{{ $t('dashboard.event.fields.address') }}</label>
          <input v-model.trim="local.address" type="text" class="input input-lg" dir="auto"/>
        </div>
        <div class="md:col-span-2">
          <label>{{ $t('dashboard.event.fields.country') }}</label>
          <input v-model.trim="local.country" class="input input-lg" placeholder="DE" dir="auto"/>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label>{{ $t('dashboard.event.fields.platform') }}</label>
          <select v-model="local.platform" class="input input-lg">
            <option v-for="p in platformOptions" :key="p" :value="p">{{ p }}</option>
          </select>
        </div>
        <div>
          <label>{{ $t('dashboard.event.fields.link') }}</label>
          <input v-model.trim="local.meeting_link" type="url" placeholder="https://…" class="input input-lg" dir="auto"/>
        </div>
        <div class="md:col-span-2">
          <label>{{ $t('dashboard.event.fields.access_code') }}</label>
          <input v-model.trim="local.access_code" type="text" class="input input-lg" dir="auto"/>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { reactive, watch } from 'vue'

const props = defineProps({ modelValue: { type: Object, required: true }, mode: { type: String, default: 'offline' } })
const emit = defineEmits(['update:modelValue'])

const platformOptions = ['Zoom', 'Google Meet', 'Microsoft Teams', 'YouTube Live', 'X Live', 'Instagram Live', 'Custom']

const local = reactive({
  city: props.modelValue.city || '',
  venue_name: props.modelValue.venue_name || '',
  address: props.modelValue.address || '',
  country: props.modelValue.country || '',
  platform: props.modelValue.platform || '',
  meeting_link: props.modelValue.meeting_link || '',
  access_code: props.modelValue.access_code || '',
  stream_url: props.modelValue.stream_url || ''
})

watch(() => props.modelValue, v => Object.assign(local, v || {}))
watch(local, () => emit('update:modelValue', { ...local }), { deep: true })
</script>

<style scoped>
.card { @apply rounded-2xl border border-gray-200 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm space-y-4; }
.section-title { @apply text-xl font-semibold mb-2 flex items-center gap-2; }
.input { @apply w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-base transition focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-400 dark:bg-zinc-900 dark:border-white/10 dark:text-white; }
</style>

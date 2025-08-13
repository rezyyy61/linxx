<template>
  <section class="card">
    <h2 class="section-title">
      <Icon icon="mdi:clipboard-text-outline" class="text-rose-500 w-5 h-5" />
      {{ title }}
    </h2>

    <div class="space-y-4">
      <div>
        <label>{{ $t('dashboard.event.fields.title') }}</label>
        <span class="text-red-500">*</span>
        <input v-model="local.title" type="text" class="input input-lg" dir="auto"/>
      </div>

      <div>
        <label>{{ $t('dashboard.event.fields.description') }}</label>
        <textarea v-model="local.description" rows="4" class="input input-lg !h-auto" dir="auto"></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label>{{ $t('dashboard.event.fields.type') }}</label>
          <select v-model="local.type" class="input input-lg">
            <option v-for="opt in displayOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>

          <div v-if="local.type === 'custom'" class="mt-2">
            <input
                v-model.trim="local.typeCustom"
                type="text"
                class="input input-lg"
            />
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
              {{ $t('dashboard.event.fields.type_custom_hint') }}
            </p>
          </div>
        </div>

        <div>
          <label>{{ $t('dashboard.event.fields.visibility') }}</label>
          <select v-model="local.visibility" class="input input-lg">
            <option value="public">{{ $t('dashboard.event.visibility.public') }}</option>
            <option value="unlisted">{{ $t('dashboard.event.visibility.unlisted') }}</option>
            <option value="private">{{ $t('dashboard.event.visibility.private') }}</option>
          </select>
        </div>

        <div>
          <label>{{ $t('dashboard.event.fields.mode') }}</label>
          <select v-model="local.mode" class="input input-lg">
            <option value="offline">{{ $t('dashboard.event.mode.offline') }}</option>
            <option value="online">{{ $t('dashboard.event.mode.online') }}</option>
            <option value="hybrid">{{ $t('dashboard.event.mode.hybrid') }}</option>
          </select>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      title: '',
      description: '',
      type: 'rally_protest',
      typeCustom: '',
      visibility: 'public',
      mode: 'offline'
    })
  },
  title: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue'])
const { t } = useI18n({ useScope: 'global' })

const typeKeys = [
  'rally_protest',
  'town_hall',
  'meeting_internal',
  'training',
  'webinar',
  'fundraiser',
  'custom'
]

const displayOptions = computed(() =>
    typeKeys.map(v => ({ value: v, label: t(`dashboard.event.types.${v}`) }))
)

const local = reactive({
  title: '',
  description: '',
  type: 'rally_protest',
  typeCustom: '',
  visibility: 'public',
  mode: 'offline'
})

watch(
    () => props.modelValue,
    v => {
      const src = v || {}
      const incoming = String(src.type || '').trim()
      const isPreset = typeKeys.includes(incoming)
      local.title = src.title || ''
      local.description = src.description || ''
      local.type = isPreset ? incoming : 'custom'
      local.typeCustom = isPreset ? (src.typeCustom || '') : (incoming || '')
      local.visibility = src.visibility || 'public'
      local.mode = src.mode || 'offline'
    },
    { immediate: true }
)

watch(local, () => {
  emit('update:modelValue', { ...local })
}, { deep: true })
</script>

<style scoped>
.card { @apply rounded-2xl border border-gray-200 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm space-y-4; }
.section-title { @apply text-xl font-semibold mb-2 flex items-center gap-2; }
.input { @apply w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-base transition focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-400 dark:bg-zinc-900 dark:border-white/10 dark:text-white; }
.input-lg { @apply py-2.5; }
</style>

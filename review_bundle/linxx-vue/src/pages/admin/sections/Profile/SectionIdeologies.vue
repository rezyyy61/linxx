<!-- src/pages/admin/sections/Profile/SectionIdeologies.vue -->
<template>
  <div class="space-y-12">
    <!-- 🟥 Ideologies -->
    <div>
      <label class="form-label block text-lg mb-4">
        {{ $t('politicalProfile.ideologies.label') }}
      </label>

      <!-- predefined list -->
      <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <label
            v-for="opt in predefined"
            :key="opt.key"
            class="flex items-center gap-2 px-3 py-2 border rounded-lg transition bg-white dark:bg-gray-800"
            :class="checked(opt.key) ? 'border-red-500 shadow' : 'border-gray-300 dark:border-gray-600'"
        >
          <input v-model="selected" :value="opt.key" type="checkbox" class="checkbox" />
          <span class="text-sm font-medium">{{ opt.label }}</span>
        </label>
      </div>

      <!-- custom ideologies -->
      <label class="form-label block text-lg mb-4">
        {{ $t('politicalProfile.ideologies.customLabel') }}
      </label>

      <div class="flex flex-wrap gap-2 mb-4">
        <span
            v-for="item in customOnly"
            :key="item"
            class="chip chip-red"
        >
          {{ item }}
          <button @click="removeCustom(item)" class="chip-close">✕</button>
        </span>
      </div>

      <div class="flex gap-2">
        <input
            v-model="customInput"
            type="text"
            class="input-glass flex-1"
            :placeholder="$t('politicalProfile.placeholders.ideologies')"
        />
        <button @click="addCustom" class="btn-add">+</button>
      </div>
    </div>
    <!-- save -->
    <div class="flex justify-end mt-6">
      <button @click="save" class="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
        {{ $t('politicalProfile.general.save') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  ideologies: { type: Array, default: () => [] },
})
const emit = defineEmits(['save'])

const { t } = useI18n()

const baseKeys = [
  'socialism', 'feminism', 'anarchism', 'environmentalism',
  'antiCapitalism', 'marxism', 'humanRights'
]
const predefined = computed(() =>
    baseKeys.map(k => ({ key: k, label: t(`politicalProfile.ideologies.list.${k}`) }))
)

const selected = ref([])
const customInput = ref('')
const kwInput = ref('')

watch(() => props.ideologies, val => {
  selected.value = (val ?? []).map(v => typeof v === 'string' ? v : v.value)
}, { immediate: true })

const customOnly = computed(() =>
    selected.value.filter(k => !baseKeys.includes(k))
)
const checked = key => selected.value.includes(key)

function addCustom () {
  const v = customInput.value.trim()
  if (v && !checked(v)) {
    selected.value.push(v)
  }
  customInput.value = ''
}

function removeCustom (v) {
  selected.value = selected.value.filter(k => k !== v)
}

function save () {
  emit('save', {
    ideologies: selected.value.map(k => ({
      value: k,
      type: baseKeys.includes(k) ? 'predefined' : 'custom'
    })),
  })
}
</script>

<style scoped>
.form-label{ @apply font-medium text-gray-700 dark:text-gray-300 }
.checkbox{ @apply accent-red-600 w-4 h-4 }
.chip{ @apply flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium }
.chip-red{ @apply bg-red-100 text-red-700 dark:bg-red-800/40 dark:text-red-300 }
.chip-blue{ @apply bg-blue-100 text-blue-700 dark:bg-blue-800/40 dark:text-blue-300 }
.chip-close{ @apply text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 }
.input-glass{ @apply px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-red-500 }
.btn-add{ @apply px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold }
</style>

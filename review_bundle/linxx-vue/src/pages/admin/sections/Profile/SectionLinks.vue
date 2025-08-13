<template>
  <div class="space-y-8 pb-20">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
      {{ $t('politicalProfile.links.title') }}
    </h2>

    <div class="p-5 bg-white dark:bg-gray-900 rounded-xl shadow space-y-4">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
        <Icon icon="mdi:link-variant" class="text-red-500" />
        {{ $t('politicalProfile.links.officialLinks') }}
      </h3>

      <div class="grid md:grid-cols-2 gap-6">
        <div v-for="item in staticFields" :key="item.key" class="space-y-2">
          <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
            <Icon :icon="item.icon" class="w-5 h-5" />
            {{ $t(`politicalProfile.contact.${item.i18n}`) }}
          </label>
          <input
              :type="item.type"
              v-model="localLinks.static[item.key]"
              class="form-input"
              :placeholder="$t(`politicalProfile.contact.placeholders.${item.i18n}`)"
          />
        </div>
      </div>
    </div>

    <div class="p-5 bg-white dark:bg-gray-900 rounded-xl shadow space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
          <Icon icon="mdi:plus-circle" class="text-red-500" />
          {{ $t('politicalProfile.links.customLinks') }}
        </h3>
        <button @click="showAddForm = !showAddForm" class="text-sm text-blue-600 hover:underline">
          {{ showAddForm ? $t('politicalProfile.links.hideForm') : $t('politicalProfile.links.addNew') }}
        </button>
      </div>

      <div v-if="localLinks.custom.length" class="space-y-4">
        <div
            v-for="(link, idx) in localLinks.custom"
            :key="idx"
            class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="flex items-center gap-3">
            <Icon :icon="iconFor(link.url)" class="text-xl text-gray-600 dark:text-gray-300" />
            <div>
              <p class="font-medium text-gray-800 dark:text-white">{{ link.title }}</p>
              <a :href="link.url" target="_blank" class="text-sm text-blue-600 hover:underline break-all">
                {{ link.url }}
              </a>
            </div>
          </div>
          <button @click="removeCustom(idx)" class="text-red-500 hover:text-red-700 transition">
            ✕
          </button>
        </div>
      </div>

      <transition name="fade">
        <form v-if="showAddForm" @submit.prevent="addCustom" class="space-y-4 mt-4">
          <div class="grid md:grid-cols-2 gap-4">
            <input
                v-model="draft.title"
                type="text"
                class="form-input"
                :placeholder="$t('politicalProfile.links.placeholders.title')"
            />
            <input
                v-model="draft.url"
                type="url"
                class="form-input"
                :placeholder="$t('politicalProfile.links.placeholders.url')"
            />
          </div>
          <div class="flex items-center gap-4">
            <button type="submit" class="btn-red" :disabled="!validDraft">
              {{ $t('politicalProfile.links.addButton') }}
            </button>
            <span v-if="error" class="text-sm text-red-500">{{ error }}</span>
          </div>
        </form>
      </transition>
    </div>

    <div class="sticky bottom-4 flex justify-end">
      <button @click="save" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-lg transition">
        {{ $t('politicalProfile.general.save') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'

const { t } = useI18n()

/* ---------- props / emits ---------- */
const props = defineProps({
  /** exact structure returned from API (object: { static:{}, custom:[] }) */
  links: { type: Object, default: () => ({ static: {}, custom: [] }) }
})
const emit = defineEmits(['save'])

/* ---------- local reactive copy ---------- */
const localLinks = reactive({
  static: {
    website: '',
    twitter: '',
    facebook: '',
    instagram: '',
    telegram: '',
    youtube: '',
    email: '',
    phone: ''
  },
  custom: []
})


/* keep local state in-sync when parent data arrives */
watch(() => props.links, l => {
  Object.assign(localLinks.static, l?.static || {})
  localLinks.custom = [...(l?.custom || [])]
}, { immediate: true })

/* ---------- helpers ---------- */
const staticFields = [
  { key: 'website', type: 'url', i18n: 'website', icon: 'mdi:web' },
  { key: 'youtube', type: 'url', i18n: 'youtube', icon: 'mdi:youtube' },
  { key: 'facebook', type: 'url', i18n: 'facebook', icon: 'mdi:facebook' },
  { key: 'instagram', type: 'url', i18n: 'instagram', icon: 'mdi:instagram' },
  { key: 'telegram', type: 'url', i18n: 'telegram', icon: 'mdi:telegram' },
  { key: 'email', type: 'email', i18n: 'email', icon: 'mdi:email' },
  { key: 'twitter', type: 'url', i18n: 'twitter', icon: 'mdi:twitter' },
  { key: 'phone', type: 'text', i18n: 'phone', icon: 'mdi:phone' }
]

const showAddForm = ref(false)
const removedLinkIds = reactive([])
const draft = reactive({ title: '', url: '' })
const error = ref('')
const validDraft = computed(() => draft.title.trim() && /^https?:\/\/.+/i.test(draft.url))

function addCustom () {
  if (!validDraft.value) { error.value = t('politicalProfile.links.validationError'); return }
  localLinks.custom.push({ title: draft.title.trim(), url: draft.url.trim() })
  draft.title = draft.url = ''; error.value = ''; showAddForm.value = false
}
function removeCustom (i) {
  const link = localLinks.custom[i]
  if (link.id) {
    removedLinkIds.push(link.id)
  }
  localLinks.custom.splice(i, 1)
}

/* choose icon for preview */
function iconFor (u = '') {
  u = u.toLowerCase()
  if (u.includes('twitter')) return 'mdi:twitter'
  if (u.includes('facebook')) return 'mdi:facebook'
  if (u.includes('instagram')) return 'mdi:instagram'
  if (u.includes('t.me') || u.includes('telegram')) return 'mdi:telegram'
  if (u.includes('youtube')) return 'mdi:youtube'
  if (u.includes('linkedin')) return 'mdi:linkedin'
  return 'mdi:link-variant'
}

function save () {
  const out = []

  Object.entries(localLinks.static).forEach(([type, url]) => {
    if (url) out.push({ type, title: null, url })
  })

  localLinks.custom.forEach(l => {
    if (l.url) {
      out.push({
        id: l.id ?? null,
        type: 'custom',
        title: l.title || null,
        url: l.url
      })
    }
  })

  emit('save', { links: out, removed_links: [...removedLinkIds] })
  removedLinkIds.splice(0)
}

</script>


<style scoped>
.form-input{@apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 transition}
.btn-red{@apply px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition}
.fade-enter-active,.fade-leave-active{transition:opacity .3s ease}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>

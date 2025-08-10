<template>
  <section class="card">
    <h2 class="section-title">
      <Icon icon="mdi:image-multiple-outline" class="text-fuchsia-500 w-5 h-5" />
      {{ $t('dashboard.event.sections.media') }}
    </h2>

    <div class="grid gap-6">
      <div class="space-y-3">
        <label class="block text-sm font-medium mb-1">{{ $t('dashboard.event.fields.cover') }}</label>
        <div class="flex items-center gap-3">
          <input ref="coverInput" type="file" accept="image/*" class="hidden" @change="onCoverPick" />
          <button type="button" class="btn-secondary" @click="coverInput?.click()">
            {{ coverPreview ? $t('dashboard.event.common.replace') : $t('dashboard.event.common.choose_image') }}
          </button>
          <button v-if="coverPreview" type="button" class="btn-secondary" @click="clearCover">
            {{ $t('dashboard.event.common.remove') }}
          </button>
        </div>
        <div v-if="coverPreview" class="mt-2 rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10">
          <img :src="coverPreview" :alt="$t('dashboard.event.fields.cover')" class="w-full max-h-80 object-contain bg-white dark:bg-zinc-900" />
        </div>
      </div>

      <div class="space-y-3">
        <label class="block text-sm font-medium mb-1">{{ $t('dashboard.event.fields.attachment') }}</label>
        <div class="flex items-center gap-3">
          <input ref="attachInput" type="file" :accept="accept" class="hidden" @change="onAttachPick" />
          <button type="button" class="btn-secondary" @click="attachInput?.click()">
            {{ hasAttachment ? $t('dashboard.event.common.replace') : $t('dashboard.event.common.choose_file') }}
          </button>
          <button v-if="hasAttachment" type="button" class="btn-secondary" @click="clearAttachment">
            {{ $t('dashboard.event.common.remove') }}
          </button>
        </div>
        <div v-if="hasAttachment" class="mt-1 text-sm text-gray-600 dark:text-gray-300 truncate flex items-center gap-2">
          <Icon :icon="attachIcon" class="w-4 h-4" />
          <span class="truncate">{{ attachmentDisplayName }}</span>
          <span v-if="prettySize" class="text-xs text-gray-500">· {{ prettySize }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { reactive, ref, computed, watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ cover: null, attachment: null, coverUrl: '', attachmentUrl: '', attachmentName: '', removeCover: false, removeAttachment: false }) },
  accept: { type: String, default: '.pdf,.doc,.docx,.ppt,.pptx,.txt,.zip' }
})
const emit = defineEmits(['update:modelValue'])

const local = reactive({
  cover: props.modelValue.cover || null,
  attachment: props.modelValue.attachment || null,
  coverUrl: props.modelValue.coverUrl || props.modelValue.cover_url || props.modelValue.cover || '',
  attachmentUrl: props.modelValue.attachmentUrl || props.modelValue.attachment_url || '',
  attachmentName: props.modelValue.attachmentName || props.modelValue.attachment_name || '',
  removeCover: !!props.modelValue.removeCover,
  removeAttachment: !!props.modelValue.removeAttachment
})

const coverInput = ref(null)
const attachInput = ref(null)
const coverBlobUrl = ref(null)

const coverPreview = computed(() => coverBlobUrl.value || local.coverUrl || '')
const hasAttachment = computed(() => !!(local.attachment || local.attachmentUrl || local.attachmentName))

function onCoverPick(e) {
  const f = e.target.files?.[0] || null
  setCover(f)
}
function setCover(file) {
  if (coverBlobUrl.value) {
    URL.revokeObjectURL(coverBlobUrl.value)
    coverBlobUrl.value = null
  }
  local.cover = file
  local.coverUrl = file ? '' : local.coverUrl
  local.removeCover = !file && !local.coverUrl
  if (file) coverBlobUrl.value = URL.createObjectURL(file)
}
function clearCover() {
  if (coverBlobUrl.value) URL.revokeObjectURL(coverBlobUrl.value)
  coverBlobUrl.value = null
  local.cover = null
  local.coverUrl = ''
  local.removeCover = true
  if (coverInput.value) coverInput.value.value = ''
}

function onAttachPick(e) {
  const f = e.target.files?.[0] || null
  local.attachment = f
  if (f) {
    local.removeAttachment = false
    local.attachmentName = f.name || local.attachmentName
  } else if (!local.attachmentUrl && !local.attachmentName) {
    local.removeAttachment = true
  }
}
function clearAttachment() {
  local.attachment = null
  local.attachmentName = ''
  local.attachmentUrl = ''
  local.removeAttachment = true
  if (attachInput.value) attachInput.value.value = ''
}

const prettySize = computed(() => {
  if (!local.attachment?.size) return ''
  const s = local.attachment.size
  if (s < 1024) return s + ' B'
  if (s < 1024 * 1024) return (s / 1024).toFixed(1) + ' KB'
  return (s / 1024 / 1024).toFixed(1) + ' MB'
})

const attachIcon = computed(() => {
  const n = (local.attachment?.name || local.attachmentName || local.attachmentUrl || '').toLowerCase()
  if (n.endsWith('.pdf')) return 'mdi:file-pdf-box'
  if (n.endsWith('.doc') || n.endsWith('.docx')) return 'mdi:file-word-box'
  if (n.endsWith('.ppt') || n.endsWith('.pptx')) return 'mdi:file-powerpoint-box'
  if (n.endsWith('.zip') || n.endsWith('.rar')) return 'mdi:folder-zip'
  if (n.endsWith('.txt') || n.endsWith('.md')) return 'mdi:file-document-outline'
  return 'mdi:file-outline'
})

const attachmentDisplayName = computed(() => {
  if (local.attachment?.name) return local.attachment.name
  if (local.attachmentName) return local.attachmentName
  if (local.attachmentUrl) {
    try {
      const u = new URL(local.attachmentUrl, window.location.origin)
      const last = u.pathname.split('/').filter(Boolean).pop()
      return last || 'Attachment'
    } catch {
      const parts = local.attachmentUrl.split('?')[0].split('/')
      return parts[parts.length - 1] || 'Attachment'
    }
  }
  return ''
})

watch(
    () => props.modelValue,
    (v) => {
      const prevBlob = coverBlobUrl.value

      local.cover = v?.cover || null
      local.attachment = v?.attachment || null

      local.coverUrl = v?.coverUrl || v?.cover_url || v?.cover || ''
      local.attachmentUrl = v?.attachmentUrl || v?.attachment_url || ''
      local.attachmentName = v?.attachmentName || v?.attachment_name || ''

      local.removeCover = !!v?.removeCover
      local.removeAttachment = !!v?.removeAttachment

      if (prevBlob) {
        URL.revokeObjectURL(prevBlob)
        coverBlobUrl.value = null
      }
      if (local.cover instanceof File) {
        coverBlobUrl.value = URL.createObjectURL(local.cover)
      }
    },
    { deep: true, immediate: true }
)

watch(local, () => emit('update:modelValue', { ...local }), { deep: true })

onBeforeUnmount(() => { if (coverBlobUrl.value) URL.revokeObjectURL(coverBlobUrl.value) })
</script>

<style scoped>
.card { @apply rounded-2xl border border-gray-200 bg-white dark:border-white/10 dark:bg-zinc-900 p-6 shadow-sm space-y-4; }
.section-title { @apply text-xl font-semibold mb-2 flex items-center gap-2; }
.btn-secondary { @apply inline-flex items-center rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/15; }
</style>

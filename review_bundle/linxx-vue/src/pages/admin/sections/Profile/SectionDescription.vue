<template>
  <div class="space-y-6">
    <div
        v-for="field in fields"
        :key="field.key"
        class="border border-gray-300 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm"
    >
      <div
          class="flex items-center justify-between px-5 py-4 bg-gray-100 dark:bg-gray-800 cursor-pointer"
          @click="toggleOpen(field.key)"
      >
        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
          {{ $t(`politicalProfile.description.${field.key}`) }}
          <span
              v-if="localDesc[`${field.key}Files`]?.length"
              class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 px-2 py-0.5 rounded-full"
          >
    {{ localDesc[`${field.key}Files`].length }}
  </span>
        </h3>

      </div>

      <transition name="fade">
        <div v-if="isOpen(field.key)" class="p-5 bg-white dark:bg-gray-900">
          <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
            <button
                v-for="tab in ['text', 'files']"
                :key="tab"
                @click="activeTabs[field.key] = tab"
                class="px-4 py-2 text-sm font-medium"
                :class="activeTabs[field.key] === tab
                ? 'border-b-2 border-red-500 text-red-500'
                : 'text-gray-500 hover:text-red-500'"
            >
              {{ $t(`politicalProfile.description.tabs.${tab}`) }}
            </button>
          </div>

          <div v-if="activeTabs[field.key] === 'text'">
            <textarea
                v-model="localDesc[field.key]"
                class="rich-textarea"
                :placeholder="$t('politicalProfile.description.placeholder')"
            ></textarea>
          </div>

          <div v-else>
            <div
                class="upload-zone mb-4"
                @click="triggerFileInput(field.key)"
                @dragover.prevent
                @drop.prevent="e => handleDrop(e, field.key)"
            >
              <div class="text-center">
                <div class="text-3xl mb-2">📎</div>
                <p class="text-sm text-gray-500">
                  {{ $t('politicalProfile.description.uploadHint') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ localDesc[`${field.key}Files`]?.length || 0 }}/5
                  {{ $t('politicalProfile.description.files') }}
                </p>
              </div>
              <input
                  type="file"
                  multiple
                  accept=".pdf,.doc,.docx,.jpg,.png,.jpeg"
                  :ref="el => setFileRef(field.key, el)"
                  class="hidden"
                  @change="e => handleFiles(e, field.key)"
              />
            </div>

            <transition-group name="fade" tag="div" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div
                  v-for="(file, idx) in localDesc[`${field.key}Files`]"
                  :key="file.name + idx"
                  class="file-card"
              >
                <img
                    v-if="isImage(file)"
                    :src="fileUrl(file)"
                    class="object-cover w-full h-full rounded-lg"
                />
                <div v-else class="flex flex-col items-center justify-center h-full text-center px-2">
                  📄
                  <a
                      :href="file.url ?? fileUrl(file)"
                      target="_blank"
                      class="mt-1 text-xs text-blue-600 hover:underline break-all"
                  >
                    {{ file.name }}
                  </a>
                </div>
                <button class="remove-btn" @click="removeFile(field.key, idx)">✕</button>
              </div>
            </transition-group>
          </div>
        </div>
      </transition>
    </div>

    <div class="sticky bottom-4 flex justify-end">
      <button
          @click="save"
          class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-lg transition"
      >
        {{ $t('politicalProfile.general.save') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  description: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['save'])

const fields = [
  { key: 'about' },
  { key: 'goals' },
  { key: 'activities' },
  { key: 'structure' }
]

const localDesc = reactive({})
const activeTabs = reactive({})
const openSections = reactive({})
const fileInputs = reactive({})

fields.forEach(f => {
  localDesc[f.key] = ''
  localDesc[`${f.key}Files`] = []
  activeTabs[f.key] = 'text'
  openSections[f.key] = false
})

watch(
    () => props.description,
    val => {
      fields.forEach(f => {
        localDesc[f.key] = val?.[f.key] ?? ''
        localDesc[`${f.key}Files`] =
            (val?.files ?? []).filter(file => file.section === f.key)
                .concat(val?.[`${f.key}Files`] ?? [])
      })
    },
    { immediate: true, deep: true }
)


const isOpen = k => openSections[k]
const toggleOpen = k => (openSections[k] = !openSections[k])

const setFileRef = (k, el) => { if (el) fileInputs[k] = el }
const triggerFileInput = k => fileInputs[k]?.click()

function handleFiles(e, k) {
  const files = Array.from(e.target.files || [])
  addFiles(files, k)
}

function handleDrop(e, k) {
  const files = Array.from(e.dataTransfer.files || [])
  addFiles(files, k)
}

function addFiles(files, k) {
  const existing = localDesc[`${k}Files`]
  const space = 5 - existing.length
  localDesc[`${k}Files`] = [...existing, ...files.slice(0, space)]
}

const removedFileIds = reactive(new Set())

function removeFile (sectionKey, idx) {
  const arr = localDesc[`${sectionKey}Files`]
  const file = arr[idx]

  if (!(file instanceof File) && file.id) {
    removedFileIds.add(file.id)
  }
  arr.splice(idx, 1)
}


const isImage = f =>
    (f instanceof File && f.type.startsWith('image/')) ||
    (f.mime_type && f.mime_type.startsWith('image/'))

const fileUrl = f => (f instanceof File ? URL.createObjectURL(f) : f.url)

function save() {
  const files = []
  for (const section of ['about', 'goals', 'activities', 'structure']) {
    const sectionFiles = localDesc[`${section}Files`] || []
    sectionFiles.forEach(file => {
      if (file instanceof File) {
        files.push({ section, file })
      }
    })
  }

  emit('save', {
    about: localDesc.about || '',
    goals: localDesc.goals || '',
    activities: localDesc.activities || '',
    structure: localDesc.structure || '',
    files,
    removed_files: Array.from(removedFileIds)
  })
  removedFileIds.clear()
}
</script>


<style scoped>
.rich-textarea{@apply w-full px-5 py-4 border border-gray-300 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 resize-y transition min-h-[140px] shadow-sm}
.upload-zone{@apply border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 p-6 cursor-pointer hover:border-red-500 transition flex justify-center items-center}
.file-card{@apply relative w-full h-28 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm overflow-hidden bg-white dark:bg-gray-800}
.remove-btn{@apply absolute top-1 right-1 bg-white dark:bg-gray-700 text-red-500 hover:text-red-700 rounded-full w-6 h-6 flex items-center justify-center text-sm shadow}
.fade-enter-active,.fade-leave-active{transition:all .3s ease}
.fade-enter-from,.fade-leave-to{opacity:0;transform:scale(.95)}
</style>

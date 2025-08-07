<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

    <!-- Logo Upload -->
    <div class="flex flex-col items-center gap-4">
      <div
          class="relative w-40 h-40 md:w-48 md:h-48 border-2 border-dashed rounded-xl cursor-pointer transition flex items-center justify-center overflow-hidden group"
          :class="previewSrc ? 'border-transparent' : 'border-gray-300 dark:border-gray-600'"
          :style="previewBgStyle"
          @click="triggerFileInput"
          @dragover.prevent
          @drop.prevent="handleDrop"
      >
        <transition name="fade">
          <template v-if="previewSrc">
            <img
                :src="previewSrc"
                alt="Logo preview"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 select-none"
                draggable="false"
                key="logo"
            />
          </template>
          <template v-else>
            <span class="flex flex-col items-center gap-2 text-center text-sm sm:text-base text-gray-400">
              <Icon icon="mdi:image-plus" class="text-4xl" />
              {{ $t('politicalProfile.general.logoPlaceholder') }}
            </span>
          </template>
        </transition>

        <input
            type="file"
            id="logoFileInput"
            class="hidden"
            @change="handleLogoUpload"
            accept="image/*"
        />

        <button
            v-if="previewSrc"
            @click.stop="removeLogo"
            class="absolute top-2 right-2 bg-black/60 text-white rounded-full p-1 hover:bg-black/80 transition"
        >
          <Icon icon="mdi:close" class="text-lg" />
        </button>
      </div>

      <button
          type="button"
          @click="triggerFileInput"
          class="w-full px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium transition flex items-center justify-center gap-2"
      >
        <Icon icon="mdi:upload" class="text-lg" />
        {{ previewSrc ? $t('politicalProfile.general.changeLogo') : $t('politicalProfile.general.uploadButton') }}
      </button>

      <span v-if="general.logo" class="text-xs text-gray-500 truncate max-w-full text-center">
        {{ general.logo.name }}
      </span>
    </div>

    <!-- General Info Fields -->
    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Name -->
      <div class="col-span-2">
        <label class="form-label flex items-center gap-2">
          <Icon icon="mdi:account-group" />
          {{ $t('politicalProfile.general.groupName') }}
        </label>
        <input
            type="text"
            v-model="general.groupName"
            class="form-input"
            :placeholder="$t('politicalProfile.general.groupNamePlaceholder')"
        />
      </div>

      <!-- Slogan -->
      <div class="col-span-2">
        <label class="form-label flex items-center gap-2">
          <Icon icon="mdi:format-quote-close" />
          {{ $t('politicalProfile.general.tagline') }}
        </label>
        <input
            type="text"
            v-model="general.tagline"
            class="form-input"
            :placeholder="$t('politicalProfile.general.taglinePlaceholder')"
        />
      </div>

      <!-- Entity Type -->
      <div class="col-span-2">
        <label class="form-label flex items-center gap-2 mb-2">
          <Icon icon="mdi:label" />
          {{ $t('politicalProfile.general.entityType') }}
        </label>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div
              v-for="type in entityTypes"
              :key="type.value"
              @click="general.entityType = type.value"
              :class="[
              'cursor-pointer rounded-lg border p-4 flex flex-col items-center gap-2 transition text-center',
              general.entityType === type.value
                ? 'border-red-500 bg-red-50 dark:bg-red-900/20'
                : 'border-gray-300 dark:border-gray-700 hover:border-red-400'
            ]"
          >
            <Icon :icon="type.icon" class="text-3xl" />
            <span class="font-medium">{{ $t(type.labelKey) }}</span>

            <span
                v-if="['party', 'collective', 'media'].includes(type.value)"
                class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100"
            >
              {{ $t('politicalProfile.general.requiresApproval') }}
            </span>
          </div>
        </div>

        <transition name="fade">
          <div
              v-if="requiresApproval"
              class="p-3 mt-3 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 rounded-lg text-sm flex items-start gap-2"
          >
            <Icon icon="mdi:shield-alert" class="text-xl flex-shrink-0" />
            <span>
              {{ $t('politicalProfile.general.entityTypeApprovalNotice') }}
              <br />
              <strong>({{ general.entityType }})</strong>
            </span>
          </div>
        </transition>
      </div>

      <!-- Location -->
      <div>
        <label class="form-label flex items-center gap-2">
          <Icon icon="mdi:map-marker" />
          {{ $t('politicalProfile.general.location') }}
        </label>
        <input
            type="text"
            v-model="general.location"
            class="form-input"
            :placeholder="$t('politicalProfile.general.locationPlaceholder')"
        />
      </div>

      <!-- Founded Year -->
      <div>
        <label class="form-label flex items-center gap-2">
          <Icon icon="mdi:calendar" />
          {{ $t('politicalProfile.general.foundedYear') }}
        </label>
        <input
            type="number"
            v-model="general.foundedYear"
            class="form-input"
            :placeholder="$t('politicalProfile.general.foundedYearPlaceholder')"
        />
      </div>
    </div>

    <!-- Save Button -->
    <div class="col-span-1 lg:col-span-3 flex justify-end mt-4">
      <button
          type="button"
          @click="handleSave"
          class="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
      >
        <Icon icon="mdi:content-save" class="text-lg" />
        {{ $t('politicalProfile.general.save') }}
      </button>
    </div>
  </div>
</template>


<script setup>
import { ref, computed, watch } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  profile: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['save'])

const general = ref({
  groupName: '',
  tagline: '',
  entityType: '',
  location: '',
  foundedYear: '',
  avatarColor: '',
  logo: null
})

const previewSrc = ref(null)

const previewBgStyle = computed(() => {
  if (previewSrc.value) return {}
  return general.value.avatarColor
      ? { backgroundColor: general.value.avatarColor }
      : { backgroundColor: '#e5e7eb' }
})

const entityTypes = [
  { value: 'individual', icon: 'mdi:account', labelKey: 'politicalProfile.entityTypes.individual' },
  { value: 'party', icon: 'mdi:account-group', labelKey: 'politicalProfile.entityTypes.party' },
  { value: 'collective', icon: 'mdi:account-multiple', labelKey: 'politicalProfile.entityTypes.collective' },
  { value: 'media', icon: 'mdi:television-classic', labelKey: 'politicalProfile.entityTypes.media' }
]

const requiresApproval = computed(() =>
    ['party', 'collective', 'media'].includes(general.value.entityType)
)

watch(
    () => props.profile,
    (profile) => {
      if (profile && Object.keys(profile).length) {
        general.value.groupName = profile.group_name || ''
        general.value.tagline = profile.tagline || ''
        general.value.entityType = profile.entity_type || ''
        general.value.location = profile.location || ''
        general.value.foundedYear = profile.founded_year || ''
        general.value.avatarColor = profile.avatar_color || ''
        previewSrc.value = profile.logo_url || null
      }
    },
    { immediate: true }
)

function handleLogoUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  general.value.logo = file
  previewSrc.value = URL.createObjectURL(file)
}

function triggerFileInput() {
  document.getElementById('logoFileInput').click()
}

function removeLogo() {
  general.value.logo = null
  previewSrc.value = null
}

function handleDrop(e) {
  const file = e.dataTransfer.files[0]
  if (!file) return
  general.value.logo = file
  previewSrc.value = URL.createObjectURL(file)
}

function handleSave() {
  const payload = {
    group_name: general.value.groupName,
    tagline: general.value.tagline,
    entity_type: general.value.entityType,
    location: general.value.location,
    founded_year:
        general.value.foundedYear != null && general.value.foundedYear !== ''
            ? String(general.value.foundedYear)
            : null,
    avatar_color: general.value.avatarColor
  }

  if (general.value.logo instanceof File) {
    payload.logo = general.value.logo
  } else if (general.value.logo === null && previewSrc.value === null) {
    payload.logo = null
  }

  emit('save', payload)
}
</script>

<style scoped>
.form-input {
  @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500;
}
.form-label {
  @apply block mb-1 font-medium text-gray-700 dark:text-gray-300;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

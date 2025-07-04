<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Logo Upload Area -->
        <div class="flex flex-col justify-center items-center gap-4 py-6">
            <div
                class="relative w-full aspect-square border-2 border-dashed rounded-xl cursor-pointer hover:border-red-500 transition flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400 overflow-hidden group"
                @dragover.prevent
                @drop.prevent="handleDrop"
            >
                <!-- Preview -->
                <img
                    v-if="store.general.logoPreview"
                    :src="store.general.logoPreview"
                    alt="Logo preview"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
                <span v-else class="text-center px-4 text-sm sm:text-base">
          {{ $t('politicalProfile.general.logoPlaceholder') }}
        </span>

                <!-- Hidden File Input -->
                <input
                    type="file"
                    id="logoFileInput"
                    class="absolute inset-0 opacity-0 cursor-pointer"
                    @change="handleLogoUpload"
                    accept="image/*"
                />

                <!-- Remove Button -->
                <button
                    v-if="store.general.logoPreview"
                    @click="removeLogo"
                    class="absolute top-2 right-2 bg-black/60 text-white rounded-full p-1 hover:bg-black/80 transition z-10"
                >
                    ✕
                </button>
            </div>

            <button
                type="button"
                @click="triggerFileInput"
                class="px-4 py-2 mt-4 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition"
            >
                {{ $t('politicalProfile.general.uploadButton') }}
            </button>
        </div>

        <!-- Info Fields -->
        <div class="md:col-span-2 space-y-6 px-2 md:px-0">
            <div>
                <label class="form-label">{{ $t('politicalProfile.general.groupName') }}</label>
                <input
                    type="text"
                    v-model="store.general.groupName"
                    class="form-input"
                    :placeholder="$t('politicalProfile.general.groupNamePlaceholder')"
                />
            </div>

            <div>
                <label class="form-label">{{ $t('politicalProfile.general.tagline') }}</label>
                <input
                    type="text"
                    v-model="store.general.tagline"
                    class="form-input"
                    :placeholder="$t('politicalProfile.general.taglinePlaceholder')"
                />
            </div>

            <div>
                <label class="form-label">{{ $t('politicalProfile.general.entityType') }}</label>
                <select v-model="store.general.entityType" class="form-input">
                    <option disabled value="">
                        {{ $t('politicalProfile.general.entityTypePlaceholder') }}
                    </option>
                    <option value="party">{{ $t('politicalProfile.general.types.party') }}</option>
                    <option value="collective">{{ $t('politicalProfile.general.types.collective') }}</option>
                    <option value="media">{{ $t('politicalProfile.general.types.media') }}</option>
                    <option value="individual">{{ $t('politicalProfile.general.types.individual') }}</option>
                </select>
            </div>

            <div>
                <label class="form-label">{{ $t('politicalProfile.general.location') }}</label>
                <input
                    type="text"
                    v-model="store.general.location"
                    class="form-input"
                    :placeholder="$t('politicalProfile.general.locationPlaceholder')"
                />
            </div>

            <div>
                <label class="form-label">{{ $t('politicalProfile.general.foundedYear') }}</label>
                <input
                    type="number"
                    v-model="store.general.foundedYear"
                    class="form-input"
                    :placeholder="$t('politicalProfile.general.foundedYearPlaceholder')"
                />
            </div>
        </div>

        <!-- Save Button -->
        <div class="col-span-1 md:col-span-3 flex justify-end mt-6 px-2 md:px-0">
            <button
                type="button"
                @click="handleSave"
                class="flex items-center gap-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
            >
                {{ $t('politicalProfile.general.save') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { usePoliticalProfileStore } from '@/stores/politicalProfile'
const store = usePoliticalProfileStore()

const triggerFileInput = () => {
    document.getElementById('logoFileInput')?.click()
}

const handleLogoUpload = (e) => {
    const file = e.target.files[0]
    previewAndSaveLogo(file)
}

const handleDrop = (e) => {
    const file = e.dataTransfer.files[0]
    previewAndSaveLogo(file)
}

const previewAndSaveLogo = (file) => {
    if (!file || !file.type.startsWith('image/')) return

    const reader = new FileReader()
    reader.onload = () => {
        store.saveGeneral({
            ...store.general,
            logo: file,
            logoPreview: reader.result
        })
    }
    reader.readAsDataURL(file)
}

const removeLogo = () => {
    store.saveGeneral({
        ...store.general,
        logo: null,
        logoPreview: null
    })
}

const handleSave = () => {
    console.log('✅ Saved General section in Pinia:', store.general)
}
</script>

<style scoped>
.form-input {
    @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500;
}
.form-label {
    @apply block mb-1 font-medium text-gray-700 dark:text-gray-300;
}
</style>

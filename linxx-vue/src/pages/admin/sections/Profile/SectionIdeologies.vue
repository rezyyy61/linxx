<template>
    <div class="space-y-12">
        <!-- Ideologies Section -->
        <div>
            <label class="form-label block text-lg mb-4">
                {{ $t('politicalProfile.ideologies.label') }}
            </label>

            <!-- Predefined Checkboxes -->
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <label
                    v-for="option in predefinedIdeologies"
                    :key="option.key"
                    class="flex items-center gap-2 px-3 py-2 border rounded-lg transition bg-white dark:bg-gray-800"
                    :class="{
            'border-red-500 shadow': selectedKeys.includes(option.key),
            'border-gray-300 dark:border-gray-600': !selectedKeys.includes(option.key)
          }"
                >
                    <input
                        type="checkbox"
                        :value="option.key"
                        v-model="selectedKeys"
                        class="checkbox"
                    />
                    <span class="text-sm font-medium">{{ option.label }}</span>
                </label>
            </div>

            <!-- Custom Ideologies -->
            <div class="flex flex-wrap gap-2 mb-4">
        <span
            v-for="(item, index) in customKeys"
            :key="'custom-' + index"
            class="tag-chip"
        >
          {{ item }}
          <button @click="removeCustomIdeology(item)">✕</button>
        </span>
            </div>

            <form @submit.prevent="addCustomIdeology" class="flex flex-col sm:flex-row gap-3">
                <input
                    v-model="customIdeology"
                    type="text"
                    class="form-input flex-1"
                    :placeholder="$t('politicalProfile.placeholders.ideologies')"
                />
                <button type="submit" class="btn-red">
                    {{ $t('politicalProfile.ideologies.addButton') }}
                </button>
            </form>
        </div>

        <!-- Keywords Section -->
        <div>
            <label class="form-label block text-lg mb-4">
                {{ $t('politicalProfile.ideologies.keywordsLabel') }}
                <span class="text-sm text-gray-500 dark:text-gray-400 font-normal">
          ({{ $t('politicalProfile.ideologies.optional') }})
        </span>
            </label>

            <div class="flex flex-wrap gap-2 mb-4">
        <span
            v-for="(item, index) in store.ideologies.keywords"
            :key="'kw-' + index"
            class="tag-chip"
        >
          {{ item }}
          <button @click="removeKeyword(index)">✕</button>
        </span>
            </div>

            <form @submit.prevent="addKeyword" class="flex flex-col sm:flex-row gap-3">
                <input
                    v-model="newKeyword"
                    type="text"
                    class="form-input flex-1"
                    :placeholder="$t('politicalProfile.ideologies.keywordsPlaceholder')"
                />
                <button type="submit" class="btn-red">
                    {{ $t('politicalProfile.ideologies.addButton') }}
                </button>
            </form>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mt-6">
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
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { usePoliticalProfileStore } from '@/stores/politicalProfile'

const { t } = useI18n()
const store = usePoliticalProfileStore()

// --- Ideologies
const ideologyKeys = [
    'socialism',
    'feminism',
    'anarchism',
    'environmentalism',
    'antiCapitalism',
    'marxism',
    'humanRights'
]

const predefinedIdeologies = computed(() =>
    ideologyKeys.map((key) => ({
        key,
        label: t(`politicalProfile.ideologies.list.${key}`)
    }))
)

const selectedKeys = ref(
    store.ideologies.ideologies.map(i => typeof i === 'string' ? i : i.value)
)

const customKeys = computed(() =>
    selectedKeys.value.filter(key => !ideologyKeys.includes(key))
)

const customIdeology = ref('')
const newKeyword = ref('')

// Add/remove custom ideology
const addCustomIdeology = () => {
    const val = customIdeology.value.trim()
    if (val && !selectedKeys.value.includes(val)) {
        selectedKeys.value.push(val)
        customIdeology.value = ''
    }
}

const removeCustomIdeology = (val) => {
    selectedKeys.value = selectedKeys.value.filter(i => i !== val)
}

// --- Keywords
const addKeyword = () => {
    const val = newKeyword.value.trim()
    if (val && !store.ideologies.keywords.includes(val)) {
        store.ideologies.keywords.push(val)
        newKeyword.value = ''
    }
}

const removeKeyword = (index) => {
    store.ideologies.keywords.splice(index, 1)
}

// --- Save
const handleSave = () => {
    const mapped = selectedKeys.value.map(key => ({
        value: key,
        type: ideologyKeys.includes(key) ? 'predefined' : 'custom'
    }))

    store.saveIdeologies({
        ideologies: mapped,
        keywords: [...store.ideologies.keywords]
    })

    console.log('✅ Ideologies saved to store:', store.ideologies)
}
</script>

<style scoped>
.form-label {
    @apply font-medium text-gray-700 dark:text-gray-300;
}
.form-input {
    @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500;
}
.btn-red {
    @apply px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition;
}
.checkbox {
    @apply accent-red-600 w-4 h-4;
}
.tag-chip {
    @apply flex items-center gap-2 bg-red-100 text-red-700 dark:bg-red-800/40 dark:text-red-300 px-3 py-1 rounded-full;
}
.tag-chip button {
    @apply text-red-500 hover:text-red-700 dark:hover:text-red-200 transition;
}
</style>

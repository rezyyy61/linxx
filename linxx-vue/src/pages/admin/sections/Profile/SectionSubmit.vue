<template>
    <div class="space-y-10">
        <!-- Heading -->
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-2">
                {{ $t('politicalProfile.submit.title') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $t('politicalProfile.submit.subtitle') }}
            </p>
        </div>

        <!-- Review cards -->
        <Disclosure
            v-for="card in reviewCards"
            :key="card.key"
            v-slot="{ open }"
        >
            <DisclosureButton
                class="w-full flex justify-between items-center bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-600 text-left"
            >
                <span class="font-medium">{{ card.title }}</span>
                <ChevronDown :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition" />
            </DisclosureButton>

            <DisclosurePanel class="mt-2 px-4 py-3 border-l border-gray-300 dark:border-gray-600">
                <component :is="card.component" />
            </DisclosurePanel>
        </Disclosure>

        <!-- Confirmation (optional) -->
        <div class="flex items-start gap-2">
            <input
                type="checkbox"
                id="confirm"
                v-model="confirmed"
                class="accent-red-600 w-5 h-5 mt-1"
            />
            <label for="confirm" class="text-sm text-gray-700 dark:text-gray-300">
                {{ $t('politicalProfile.submit.confirmText') }}
            </label>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button
                type="button"
                :disabled="disableSubmit"
                @click="handleSubmit"
                class="flex items-center gap-2 px-6 py-2 rounded-lg font-medium transition
               text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
            >
                <svg
                    v-if="loading"
                    class="animate-spin w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v8H4z"
                    />
                </svg>
                {{ loading ? $t('politicalProfile.submit.sending') : $t('politicalProfile.submit.button') }}
            </button>
        </div>

        <!-- JSON Preview -->
        <details class="mt-6">
            <summary class="cursor-pointer text-sm text-blue-600">
                JSON preview
            </summary>
            <pre class="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs overflow-x-auto">
{{ JSON.stringify(allData, null, 2) }}
      </pre>
        </details>
    </div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent } from 'vue'
import { usePoliticalProfileStore } from '@/stores/politicalProfile'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ChevronDown } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'

/* i18n & Pinia */
const { t } = useI18n()
const store = usePoliticalProfileStore()

/* Async review cards */
const reviewCards = [
    {
        key: 'general',
        title: t('politicalProfile.sections.general'),
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionGeneral.vue')
        )
    },
    {
        key: 'ideologies',
        title: t('politicalProfile.sections.ideologies'),
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionIdeologies.vue')
        )
    },
    {
        key: 'description',
        title: t('politicalProfile.sections.description'),
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionDescription.vue')
        )
    },
    {
        key: 'links',
        title: t('politicalProfile.sections.links'),
        component: defineAsyncComponent(() =>
            import('@/pages/admin/sections/Profile/SectionLinks.vue')
        )
    }
]

/* Whole payload */
const allData = computed(() => ({
    general: store.general,
    ideologies: store.ideologies,
    description: store.description,
    links: store.links
}))

/* UI states */
const confirmed      = ref(false)
const loading        = ref(false)
const disableSubmit  = computed(() => loading.value || !confirmed.value)

/* Mock submit */
async function handleSubmit () {
    loading.value = true
    try {
        localStorage.setItem('politicalProfileDraft', JSON.stringify(allData.value))
        await new Promise(r => setTimeout(r, 1200))
        console.log('✅ saved to LocalStorage', allData.value)
    } catch (e) {
        console.error('❌ submit failed', e)
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
/* Tailwind handles styling */
</style>

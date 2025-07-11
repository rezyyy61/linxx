<template>
    <section
        class="bg-white dark:bg-gray-900 p-6 sm:p-8 space-y-6 border-l border-r border-gray-200 dark:border-gray-700 rounded-2xl"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <!-- عنوان -->
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400 border-b border-red-300 dark:border-red-500 pb-2">
            {{ $t('home.party.publication_list') }}
        </h2>

        <!-- نشریات -->
        <div v-if="publications.length" class="grid gap-6 sm:grid-cols-2">
            <div
                v-for="pub in publications"
                :key="pub.id"
                class="group border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-gray-50 dark:bg-gray-800 hover:shadow-md transition"
            >
                <!-- تصویر جلد -->
                <img
                    :src="pub.cover_url || defaultCover"
                    alt="جلد نشریه"
                    class="w-full h-full object-cover rounded-xl border border-gray-300 dark:border-gray-600 mb-4"
                />

                <!-- اطلاعات -->
                <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ pub.title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    {{ pub.issue }} | {{ formatDate(pub.published_at) }}
                </p>

                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3 mb-3">
                    {{ pub.description || $t('home.party.no_description') }}
                </p>

                <a
                    :href="pub.file_url"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline transition"
                >
                    📄 {{ $t('home.party.view_file') }}
                </a>
            </div>
        </div>

        <!-- بدون نشریه -->
        <div v-else class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">
            {{ $t('home.party.no_publications') }}
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { usePublicationStore } from '@/stores/publications'
import { storeToRefs } from 'pinia'

const route = useRoute()
const publicationStore = usePublicationStore()
const { publications } = storeToRefs(publicationStore)

const defaultCover = '/images/default-cover.jpg'

onMounted(async () => {
    const partyId = route.params.id
    if (partyId) {
        await publicationStore.fetchPublicationsByParty(partyId)
    }
})

function formatDate(date) {
    if (!date) return ''
    const d = new Date(date)
    return d.toLocaleDateString('fa-IR', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>

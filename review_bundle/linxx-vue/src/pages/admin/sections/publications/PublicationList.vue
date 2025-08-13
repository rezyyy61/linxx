<template>
    <section class="space-y-10">
        <!-- Search -->
        <div class="flex justify-center">
            <input
                v-model="search"
                :placeholder="$t('dashboard.publication.search_placeholder')"
                class="w-full max-w-lg px-5 py-3 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition"
            />
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center text-gray-500 dark:text-gray-400 text-lg animate-pulse">
            {{ $t('dashboard.publication.loadingList') }}
        </div>

        <!-- Empty -->
        <div v-else-if="filtered.length === 0" class="text-center text-gray-400 italic py-12">
            {{ $t('dashboard.publication.empty') }}
        </div>

        <!-- Grid -->
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div
                v-for="pub in filtered"
                :key="pub.id"
                class="rounded-3xl border border-gray-200 dark:border-gray-700 shadow-lg bg-white dark:bg-gray-900 p-6 flex flex-col justify-between hover:shadow-xl transition-all duration-300"
            >
                <!-- Header Info -->
                <div class="space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        📰 {{ pub.issue || $t('dashboard.publication.label') }}
                    </p>

                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">
                        {{ pub.title }}
                    </h3>

                    <!-- Description -->
                    <p v-if="pub.description" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ isExpanded(pub.id) ? pub.description : truncate(pub.description, 130) }}
                        <template v-if="pub.description.length > 130">
                            <button
                                @click="toggleExpand(pub.id)"
                                class="ml-1 text-blue-600 dark:text-blue-400 text-xs font-semibold hover:underline"
                            >
                                {{ isExpanded(pub.id) ? $t('dashboard.publication.read_less') : $t('dashboard.publication.read_more') }}
                            </button>
                        </template>
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-6 space-y-2">
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        📅 {{ pub.published_at ? formatDate(pub.published_at) : '---' }}
                    </p>

                    <div class="flex justify-between items-center">
                        <a
                            :href="pub.file_url"
                            target="_blank"
                            class="text-blue-600 dark:text-blue-400 text-sm font-semibold hover:underline"
                        >
                            📄 {{ $t('dashboard.publication.download') }}
                        </a>
                        <button
                            @click="remove(pub.id)"
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-sm font-medium"
                        >
                            🗑 {{ $t('dashboard.publication.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { usePublicationStore } from '@/stores/publications'

const { t } = useI18n()
const toast = useToast()
const search = ref('')
const expandedIds = ref(new Set())

const publicationStore = usePublicationStore()

const loading = computed(() => publicationStore.loading)
const publications = computed(() => publicationStore.publications)

const filtered = computed(() => {
    const keyword = search.value.toLowerCase().trim()
    if (!keyword) return publications.value
    return publications.value.filter(pub =>
        (pub.title + ' ' + (pub.issue || '')).toLowerCase().includes(keyword)
    )
})

function formatDate(dateStr) {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function truncate(text, length) {
    if (!text || typeof text !== 'string') return ''
    return text.length <= length ? text : text.slice(0, length) + '...'
}

function toggleExpand(id) {
    if (expandedIds.value.has(id)) {
        expandedIds.value.delete(id)
    } else {
        expandedIds.value.add(id)
    }
}

function isExpanded(id) {
    return expandedIds.value.has(id)
}

async function remove(id) {
    if (confirm(t('dashboard.publication.confirm_delete'))) {
        try {
            await publicationStore.deletePublication(id)
            toast.success(t('dashboard.publication.delete_success'))
        } catch (e) {
            toast.error(t('dashboard.publication.delete_error'))
        }
    }
}

onMounted(() => {
    publicationStore.fetchPublications()
})
</script>

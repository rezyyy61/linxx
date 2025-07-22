<template>
    <div
        :dir="containerDirection"
        class="relative overflow-hidden border-l-4 border-blue-500 dark:border-blue-400 bg-white dark:bg-gray-900 rounded-xl shadow-xl p-6 space-y-4"
    >
        <!-- Badge -->
        <span
            class="absolute top-3 left-3 bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-100 text-xs font-bold px-2 py-1 rounded"
        >
      {{ $t('dashboard.publication.badge') }}
    </span>

        <!-- Header -->
        <div>
            <h2
                class="text-xl sm:text-2xl font-extrabold text-gray-900 dark:text-white"
                :class="titleFont"
                :dir="titleDirection"
            >
                {{ title }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                <span>{{ $t('dashboard.publication.issue') }}: {{ issue || '-' }}</span>
                <span class="text-xs">•</span>
                <span>{{ formatDate(publishedAt) }}</span>
            </p>
        </div>

        <!-- Description -->
        <div
            class="text-[16px] text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line p-2"
            :dir="descriptionDirection"
        >
            {{ cleanDescription }}
        </div>


        <!-- File Download Block -->
        <div
            class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"
        >
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white">{{ fileName }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $t('dashboard.publication.format') }}: PDF
                    </p>
                </div>
            </div>
            <a
                :href="fileUrl"
                target="_blank"
                class="text-sm font-semibold text-blue-600 hover:underline"
            >
                {{ $t('dashboard.publication.view') }}
            </a>
        </div>
    </div>
</template>


<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import dayjs from 'dayjs'
import 'dayjs/locale/fa'

const props = defineProps({ post: Object })
const { t, locale } = useI18n()

const media = computed(() => props.post.media?.[0] || {})

const title = computed(() => media.value.meta?.title || t('dashboard.publication.defaultTitle'))
const issue = computed(() => media.value.meta?.issue || '')
const publishedAt = computed(() => media.value.meta?.published_at || '')
const rawDescription = computed(() => props.post.text || '')
const fileUrl = computed(() => media.value.full_url || media.value.url || '#')
const fileName = computed(() => media.value.meta?.file_name || t('dashboard.publication.defaultFile'))
const titleFont = computed(() =>
    locale.value === 'fa' ? 'font-iransans' : 'font-inter'
)


function formatDate(date) {
    return dayjs(date).locale(locale.value === 'fa' ? 'fa' : 'en').format('YYYY/MM/DD')
}

function getDirection(text) {
    return /[\u0600-\u06FF]/.test(text) ? 'rtl' : 'ltr'
}

function sanitizeText(text) {
    if (!text) return ''
    return text
        .split('\n')
        .map(line => line.trim())
        .filter(line => line !== '')
        .join('\n')
        .replace(/\s{2,}/g, ' ')
}

const cleanDescription = computed(() => sanitizeText(rawDescription.value))


const titleDirection = computed(() => getDirection(title.value))
const descriptionDirection = computed(() => getDirection(cleanDescription.value))
const containerDirection = computed(() => getDirection(`${title.value} ${cleanDescription.value}`))
</script>


<template>
    <div v-if="validFiles.length" class="space-y-3">
        <div
            v-for="file in validFiles"
            :key="file.url || file.original_name || Math.random()"
            class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"
        >
            <div class="flex items-center gap-3">
                <component
                    :is="getIconComponent(file)"
                    class="w-6 h-6 text-red-500"
                    fill="none"
                />

                <div>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        {{ file.original_name || getFilename(file.url) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $t('dashboard.publication.format') }}: {{ getFileExtension(file).toUpperCase() }}
                    </p>
                </div>
            </div>

            <a
                :href="file.url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-blue-600 hover:underline"
            >
                {{ $t('dashboard.publication.view') }}
            </a>
        </div>
    </div>
</template>

<script setup>
import {
    FileText,
    FileArchive,
    File,
    FileAudio,
    FileCode,
    FileImage,
    FileVideo,
    FileBadge
} from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
    files: {
        type: Array,
        default: () => [],
    },
})

const validFiles = computed(() =>
    props.files.filter(file => file && typeof file.url === 'string' && file.url.trim() !== '')
)

function getFilename(url) {
    if (!url || typeof url !== 'string') return ''
    try {
        return decodeURIComponent(url.split('/').pop() || '')
    } catch (e) {
        return ''
    }
}

function getFileExtension(file) {
    const name = file?.original_name || getFilename(file?.url)
    if (!name || !name.includes('.')) return ''
    return name.split('.').pop().toLowerCase()
}

function getIconComponent(file) {
    const ext = getFileExtension(file)
    if (['pdf', 'doc', 'docx', 'txt'].includes(ext)) return FileText
    if (['zip', 'rar'].includes(ext)) return FileArchive
    if (['mp3', 'wav', 'm4a', 'aac'].includes(ext)) return FileAudio
    if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return FileImage
    if (['mp4', 'webm', 'avi'].includes(ext)) return FileVideo
    if (['js', 'vue', 'html', 'css', 'ts'].includes(ext)) return FileCode
    return FileBadge
}
</script>

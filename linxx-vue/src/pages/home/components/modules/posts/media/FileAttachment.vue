<template>
    <div v-if="files.length" class="space-y-3">
        <div
            v-for="file in files"
            :key="file.url"
            class="flex items-center justify-between px-4 py-2 rounded-xl bg-white dark:bg-gray-900 shadow-sm border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
        >
            <div class="flex items-center gap-3 overflow-hidden">
                <component
                    :is="getIconComponent(file)"
                    class="w-6 h-6 text-white p-1 rounded-md flex-shrink-0"
                    :class="getIconColor(file)"
                />

                <a
                    :href="file.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="truncate text-sm font-medium text-gray-700 dark:text-gray-200 hover:underline max-w-[18rem]"
                >
                    {{ file.original_name || getFilename(file.url) }}
                </a>

            </div>

            <span
                v-if="file.meta?.size"
                class="text-xs text-gray-500 dark:text-gray-400 ml-2 whitespace-nowrap"
            >
        {{ humanFileSize(file.meta.size) }}
      </span>
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

const props = defineProps({
    files: {
        type: Array,
        default: () => [],
    },
})

function getFilename(url) {
    return decodeURIComponent(url.split('/').pop())
}

function getFileExtension(file) {
    const name = file.original_name || getFilename(file.url)
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

function getIconColor(file) {
    const ext = getFileExtension(file)

    if (['pdf', 'doc', 'docx', 'txt'].includes(ext)) return 'bg-red-500'
    if (['zip', 'rar'].includes(ext)) return 'bg-yellow-500'
    if (['mp3', 'wav', 'm4a', 'aac'].includes(ext)) return 'bg-purple-500'
    if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return 'bg-green-500'
    if (['mp4', 'webm', 'avi'].includes(ext)) return 'bg-blue-500'
    if (['js', 'vue', 'html', 'css', 'ts'].includes(ext)) return 'bg-indigo-500'

    return 'bg-gray-400'
}

function humanFileSize(bytes, si = true) {
    const thresh = si ? 1000 : 1024
    if (Math.abs(bytes) < thresh) return bytes + ' B'
    const units = si
        ? ['kB', 'MB', 'GB', 'TB']
        : ['KiB', 'MiB', 'GiB', 'TiB']
    let u = -1
    do {
        bytes /= thresh
        ++u
    } while (Math.abs(bytes) >= thresh && u < units.length - 1)
    return bytes.toFixed(1) + ' ' + units[u]
}
</script>


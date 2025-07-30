<template>
    <div
        v-if="isProcessing || hasVisibleMedia"
        class="relative bg-white dark:bg-gray-800/70 rounded-2xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-600 p-5 transition-all duration-300 overflow-hidden"
        :style="{ minHeight: isProcessing && !hasVisibleMedia ? '200px' : 'auto' }"
    >
        <div
            v-if="isProcessing && !hasVisibleMedia"
            class="absolute inset-0 bg-white/90 dark:bg-gray-900/60 backdrop-blur-md flex flex-col items-center justify-center text-center text-sm text-gray-600 dark:text-gray-300 z-10 p-6 animate-fade-in"
        >
            <svg class="animate-spin h-6 w-6 mb-4 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>
            <p class="text-base font-medium">{{ $t('post.processingMedia') }}</p>
        </div>

        <div v-if="hasVisibleMedia" class="w-full">
            <ImageGallery v-if="normImages.length" :images="normImages" />
            <VideoPlayer v-if="normVideos.length" :videos="normVideos"  fitMode="contain" />
            <AudioPlayer v-if="normAudios.length" :audios="normAudios" />
            <FileAttachment v-if="normFiles.length" :files="normFiles" />
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue'

import ImageGallery from '@/pages/home/components/modules/posts/media/ImageGallery.vue'
import VideoPlayer from '@/pages/home/components/modules/posts/media/VideoPlayer.vue'
import AudioPlayer from '@/pages/home/components/modules/posts/media/AudioPlayer.vue'
import FileAttachment from '@/pages/home/components/modules/posts/media/FileAttachment.vue'

const props = defineProps({
    post: Object,
    baseUrl: { type: String, default: '' }
})

const media = computed(() => props.post?.media || [])

function abs(p) {
    if (!p) return ''
    if (/^https?:\/\//i.test(p)) return p
    const b = props.baseUrl?.replace(/\/+$/, '') || window.location.origin
    const rel = String(p).replace(/^\/+/, '')
    return `${b}/${rel}`
}

const normImages = computed(() =>
    media.value
        .filter(m => m.type === 'image')
        .map(m => ({
            url: abs(m.url || m.path || m.file_path || m.image_path),
            alt: m.alt || '',
            w: m.width || m.w || null,
            h: m.height || m.h || null
        }))
        .filter(x => x.url)
)

const normVideos = computed(() => {
    return (media.value || [])
        .filter(m => m.type === 'video')
        .map(m => {
            const src = m.url || m.hls_path || m.path || m.file_path || ''
            const thumbPath = m.poster || m.thumb_path || m.meta?.thumb_path || m.thumbnail || ''
            const poster = thumbPath ? abs('storage/' + thumbPath.replace(/^\/+/, '')) : ''
            const type = m.format || m.mime || (/\.(m3u8($|\?))/i.test(src) ? 'hls' : 'video')
            const width = m.width || m.meta?.original_width || m.meta?.width || 640
            const height = m.height || m.meta?.original_height || m.meta?.height || 360
            return {
                src: abs(src),
                poster,
                type,
                width,
                height,
                duration: m.duration || null,
                ratio: width / height,
                orientation: width / height < 1 ? 'portrait' : 'landscape'
            }
        })
        .filter(v => v.src)
})

const normAudios = computed(() =>
    media.value
        .filter(m => m.type === 'audio')
        .map(m => ({
            url: abs(m.url || m.path || m.file_path),
            title: m.title || '',
            duration: m.duration || null
        }))
        .filter(x => x.url)
)

const normFiles = computed(() =>
    media.value
        .filter(m => m.type === 'file' && (m.url || m.path || m.file_path))
        .map(m => ({
            url: abs(m.url || m.path || m.file_path),
            original_name: m.name || m.filename || 'file',
            meta: {
                size: m.size || m.meta?.size || null,
            },
        }))
)

const isProcessing = computed(() => {
    const s = (props.post?.status || '').toString().toLowerCase()
    return props.post?._localPending === true ||
        ['queued', 'processing'].includes(s)
})

const hasVisibleMedia = computed(() =>
    normImages.value.length ||
    normVideos.value.length ||
    normAudios.value.length ||
    normFiles.value.length
)
</script>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.97); }
    to   { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fade-in 0.4s ease-out both;
}
</style>

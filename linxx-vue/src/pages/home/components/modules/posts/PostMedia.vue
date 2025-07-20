<template>
    <div
        class="relative bg-white dark:bg-gray-800/70 rounded-2xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-600 p-5 transition-all duration-300 overflow-hidden min-h-[200px] flex items-center justify-center"
    >
        <div
            v-if="isProcessing && !hasMedia"
            class="absolute inset-0 bg-white/90 dark:bg-gray-900/60 backdrop-blur-md flex flex-col items-center justify-center text-center text-sm text-gray-600 dark:text-gray-300 z-10 p-6 animate-fade-in"
        >
            <svg class="animate-spin h-6 w-6 mb-4 text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path
                    class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>
            <p class="text-base font-medium">{{ $t('post.processingMedia') }}</p>
        </div>

        <div v-if="hasMedia" class="w-full space-y-4">
            <ImageGallery v-if="normImages.length" :images="normImages" />
            <VideoPlayer v-if="normVideos.length" :videos="normVideos" :base-url="''" />
            <AudioPlayer v-if="normAudios.length" :audios="normAudios" />
            <FileAttachment v-if="normFiles.length" :files="normFiles" />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import ImageGallery from '@/pages/home/components/modules/posts/media/ImageGallery.vue'
import VideoPlayer from '@/pages/home/components/modules/posts/media/VideoPlayer.vue'
import AudioPlayer from '@/pages/home/components/modules/posts/media/AudioPlayer.vue'
import FileAttachment from '@/pages/home/components/modules/posts/media/FileAttachment.vue'

const props = defineProps({ post: Object, baseUrl: { type: String, default: '' } })

const media = computed(() => props.post?.media || [])
const isProcessing = computed(() => props.post?._localPending === true || props.post?.status === 'processing')
const hasMedia = computed(() => media.value.length > 0)

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

const normVideos = computed(() =>
    media.value
        .filter(m => m.type === 'video')
        .map(m => {
            const src = m.url || m.hls_path || m.path || m.file_path
            const thumbPath = m.poster || m.thumb_path || m.meta?.thumb_path || m.thumbnail
            const poster = thumbPath ? abs('storage/' + thumbPath.replace(/^\/+/, '')) : ''
            const type = m.format || m.mime || (/\.(m3u8($|\?))/i.test(src) ? 'hls' : 'video')
            return {
                src: abs(src),
                poster,
                type,
                width: m.width || m.original_width || null,
                height: m.height || m.original_height || null,
                duration: m.duration || null
            }
        })
        .filter(x => x.src)
)



const normAudios = computed(() =>
    media.value
        .filter(m => m.type === 'audio')
        .map(m => ({
            src: abs(m.url || m.path || m.file_path),
            title: m.title || '',
            duration: m.duration || null
        }))
        .filter(x => x.src)
)

const normFiles = computed(() =>
    media.value
        .filter(m => m.type === 'file')
        .map(m => ({
            src: abs(m.url || m.path || m.file_path),
            name: m.name || m.filename || 'file',
            size: m.size || null
        }))
        .filter(x => x.src)
)
</script>

<style scoped>
@keyframes fade-in {
    from { opacity:0; transform:scale(0.97); }
    to { opacity:1; transform:scale(1); }
}
.animate-fade-in { animation: fade-in 0.4s ease-out both; }
</style>

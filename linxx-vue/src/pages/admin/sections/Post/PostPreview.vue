<template>
    <div class="mt-10 border-t pt-6 space-y-6">
        <ImageGallery
            v-if="images.length"
            :images="images"
            :removable="true"
            @remove="i => $emit('removeImage', i)"
        />

        <div v-if="formattedVideos.length" class="space-y-6">
            <div
                v-for="(video, i) in formattedVideos"
                :key="video.src"
                class="relative border border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden shadow-md mx-auto"
                :style="video.orientation === 'portrait' ? 'max-width: 360px' : 'max-width: 640px'"
            >
                <video
                    :src="video.src"
                    controls
                    class="w-full h-auto object-contain"
                    :style="video.orientation === 'portrait' ? 'aspect-ratio:9/16' : 'aspect-ratio:16/9'"
                ></video>
                <div
                    class="flex justify-between items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-700"
                >
          <span class="text-xs text-gray-600 dark:text-gray-400 truncate">
            {{ video.name }}
          </span>
                    <button
                        @click="$emit('removeVideo', i)"
                        class="text-xs text-red-600 hover:text-red-800 font-medium transition"
                    >
                        {{ $t('post.remove') }}
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="audio"
            class="border border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 p-4 space-y-2"
        >
            <audio :src="audio.url" controls class="w-full" />
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ audio.file.name }}</span>
                <button
                    @click="$emit('removeAudio')"
                    class="text-xs text-red-600 hover:text-red-800 font-medium transition"
                >
                    {{ $t('post.remove') }}
                </button>
            </div>
        </div>

        <ul v-if="files.length" class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <li
                v-for="(f, i) in files"
                :key="i"
                class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2"
            >
                <span>📄 {{ f.name }} — {{ formatSize(f.size) }}</span>
                <button
                    @click="$emit('removeFile', i)"
                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs font-medium"
                >
                    {{ $t('post.remove') }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import ImageGallery from '@/pages/home/components/modules/posts/media/ImageGallery.vue'

const props = defineProps({
    images: Array,
    videos: Array,
    files: Array,
    audio: Object
})

const formattedVideos = ref([])

watch(
    () => props.videos,
    (list) => {
        formattedVideos.value = []
        if (!list?.length) return
        list.forEach((v, idx) => {
            const src = URL.createObjectURL(v.file)
            const videoObj = {
                src,
                name: v.file.name || `video-${idx}`,
                type: v.file.type || 'video/mp4',
                orientation: 'landscape'
            }

            const probe = document.createElement('video')
            probe.preload = 'metadata'
            probe.src = src
            probe.muted = true
            probe.playsInline = true
            probe.addEventListener('loadedmetadata', () => {
                videoObj.orientation =
                    probe.videoHeight > probe.videoWidth ? 'portrait' : 'landscape'
                formattedVideos.value = [...formattedVideos.value]
                probe.remove()
            })

            formattedVideos.value.push(videoObj)
        })
    },
    { immediate: true, deep: true }
)

function formatSize(size) {
    const kb = size / 1024
    return kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb / 1024).toFixed(1)} MB`
}
</script>

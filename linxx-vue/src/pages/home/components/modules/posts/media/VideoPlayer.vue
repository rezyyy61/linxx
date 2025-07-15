<template>
    <div v-if="videos.length" class="space-y-6">
        <div
            v-for="(video, i) in videos"
            :key="video.url"
            :class="[
        'rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700',
        isSmallVideo[i] ? 'w-full' : 'aspect-[4/5]'
      ]"
        >
            <div class="relative w-full" :class="isSmallVideo[i] ? '' : 'aspect-[4/5]'">
                <video
                    :ref="el => videoRefs[i] = el"
                    class="plyr-video"
                    playsinline
                    controls
                    :poster="video.poster || defaultPoster(video.url)"
                    @loadedmetadata="onMetadataLoaded(i)"
                >
                    <source :src="video.url" type="video/mp4" />
                </video>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted } from 'vue'
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

const props = defineProps({
    videos: {
        type: Array,
        default: () => []
    }
})

const videoRefs = ref([])
const isSmallVideo = ref([])

function defaultPoster () {
    return 'https://via.placeholder.com/640x640?text=Preview'
}

function onMetadataLoaded(index) {
    const video = videoRefs.value[index]
    if (!video) return

    const width = video.videoWidth
    const height = video.videoHeight
    isSmallVideo.value[index] = width < 400 || height > width * 1.5
}

onMounted(async () => {
    await nextTick()
    videoRefs.value.forEach((el) => {
        const plyr = new Plyr(el, {
            controls: [
                'play-large',
                'play',
                'progress',
                'current-time',
                'mute',
                'volume',
                'settings',
                'download',
                'fullscreen'
            ]
        })
    })
})
</script>

<style scoped>
.plyr-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    background-color: black;
}
</style>

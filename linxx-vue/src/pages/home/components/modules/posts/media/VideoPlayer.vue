<template>
    <div v-if="videos.length" class="space-y-6">
        <div
            v-for="(video, i) in videos"
            :key="video.url"
            class="rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700"
        >
            <div class="aspect-video">
                <video
                    :ref="el => videoRefs[i] = el"
                    class="plyr-video"
                    playsinline
                    controls
                    :data-poster="video.poster || defaultPoster(video.url)"
                >
                    <source :src="video.url" type="video/mp4" />
                </video>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue'
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

const props = defineProps({
    videos: {
        type: Array,
        default: () => []
    }
})

const videoRefs = ref([])

function defaultPoster(url) {
    return 'https://via.placeholder.com/640x360?text=Preview'
}

onMounted(async () => {
    await nextTick()
    videoRefs.value.forEach((el) => {
        const player = new Plyr(el, {
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
.aspect-video {
    aspect-ratio: 16 / 9;
}
</style>

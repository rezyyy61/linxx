<template>
    <div
        v-if="sources.length"
        class="player-container"
        :style="{ '--ratio': ratio }"
    >
        <video
            ref="videoEl"
            class="video-js vjs-default-skin vjs-big-play-centered vjs-tech vjs-poster"
            playsinline
            controls
            preload="metadata"
            :poster="current.poster || ''"
        />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import videojs from 'video.js'
import 'video.js/dist/video-js.css'

const props = defineProps({
    video: { type: Object, default: null },
    videos: { type: Array, default: () => [] },
    autoplay: { type: Boolean, default: false },
    loop: { type: Boolean, default: false },
    muted: { type: Boolean, default: false }
})

const sources = computed(() =>
    props.videos?.length ? props.videos : props.video ? [props.video] : []
)

const currentIndex = ref(0)
const current = computed(() => sources.value[currentIndex.value] || {})

const ratio = computed(() => {
    const { width, height } = current.value
    return width && height ? height / width : 9 / 16
})

const videoEl = ref(null)
let player

onMounted(async () => {
    await nextTick()
    player = videojs(videoEl.value, {
        autoplay: props.autoplay,
        loop: props.loop,
        muted: props.muted,
        controls: true,
        fluid: true,
        preload: 'metadata'
    })

    loadSource()

    player.on('loadedmetadata', () => {
        autoZoom()
    })

    player.on('ended', () => {
        if (++currentIndex.value >= sources.value.length) {
            if (props.loop) currentIndex.value = 0
        }
    })
})

onBeforeUnmount(() => {
    player?.dispose()
})

watch([sources, currentIndex], loadSource)

function loadSource() {
    if (!player) return
    const s = current.value
    if (!s?.src) return
    const mime = s.type === 'hls' ? 'application/x-mpegURL' : 'video/mp4'
    player.poster(s.poster || '')
    player.src({ src: s.src, type: mime })
}

function autoZoom() {
    const s = current.value
    if (s.orientation !== 'portrait') return

    const tech = player?.el()?.querySelector('.vjs-tech')
    const posterEl = player?.el()?.querySelector('.vjs-poster')
    const scale = 1.8

    if (tech) {
        tech.style.transform = `scale(${scale})`
        tech.style.transformOrigin = 'center'
    }

    if (posterEl) {
        posterEl.style.transform = 'none'
        posterEl.style.transformOrigin = 'center'
        posterEl.style.width = '100%'
        posterEl.style.height = '100%'
        posterEl.style.backgroundSize = 'cover'
        posterEl.style.backgroundPosition = 'center'
        posterEl.style.backgroundRepeat = 'no-repeat'
    }
}

</script>

<style scoped>
.player-container {
    width: 100%;
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    background-color: #000;
    aspect-ratio: calc(1 / var(--ratio));
    max-width: 640px;
    max-height: 40vh;
    margin: 0 auto;
}

.player-container > :deep(.vjs-tech){
    position: absolute;
    bottom: 0;
    left: 0;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain;
    background: #000;
    border: none;
    transition: transform 0.3s ease;
}

:deep(.vjs-poster) {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    background-color: #000 !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
}
</style>

<template>
    <div
        v-if="sources.length"
        class="player-container"
        :style="{
      '--ratio': ratio,
      maxHeight: isPortrait ? '100vh' : '60vh'
    }"
    >
        <video
            ref="videoEl"
            class="video-js vjs-default-skin vjs-big-play-centered inner"
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
    video: Object,
    videos: { type: Array, default: () => [] },
    autoplay: Boolean,
    loop: Boolean,
    muted: Boolean
})

const sources = computed(() =>
    props.videos?.length ? props.videos : props.video ? [props.video] : []
)

const currentIndex = ref(0)
const current = computed(() => sources.value[currentIndex.value] || {})

const ratio = ref(9 / 16)
const isPortrait = computed(() => ratio.value > 1)

const videoEl = ref(null)
let player

function setRatio() {
    const w = player?.videoWidth()
    const h = player?.videoHeight()
    if (w && h) ratio.value = h / w
}

function load() {
    if (!player) return
    const s = current.value
    if (!s?.src) return
    player.src({ src: s.src, type: s.type === 'hls' ? 'application/x-mpegURL' : 'video/mp4' })
    player.poster(s.poster || '')
}

async function initPlayer() {
    await nextTick()
    if (!videoEl.value || !(videoEl.value instanceof HTMLVideoElement)) return
    player = videojs(videoEl.value, {
        autoplay: props.autoplay,
        loop: props.loop,
        muted: props.muted,
        controls: true,
        fluid: true,
        preload: 'metadata'
    })
    load()
    player.on('loadedmetadata', setRatio)
    player.on('ended', () => {
        if (++currentIndex.value >= sources.value.length && props.loop) currentIndex.value = 0
    })
}

onMounted(() => {
    if (sources.value.length) initPlayer()
})

onBeforeUnmount(() => player?.dispose())

watch([sources, currentIndex], () => {
    ratio.value = 9 / 16
    if (player) load()
    else if (sources.value.length) initPlayer()
})
</script>

<style scoped>
.player-container {
    width: 100%;
    position: relative;
    overflow: hidden;
    background: #000;
    aspect-ratio: calc(1 / var(--ratio));
    margin: 0 auto;
}
.inner {
    position: absolute;
    inset: 0;
    height: 100% !important;
    width: auto !important;
    min-width: 100%;
    object-fit: cover;
    left: 50%;
    transform: translateX(-50%);
}
.player-container > :deep(.vjs-poster) {
    position: absolute;
    inset: 0;
    height: 100% !important;
    width: auto !important;
    min-width: 100%;
    object-fit: cover;
    left: 50%;
    transform: translateX(-50%);
}
</style>

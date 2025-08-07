<template>
  <VideoFrame :width="current.width" :height="current.height">
    <video
        ref="videoEl"
        class="video-js vjs-default-skin vjs-big-play-centered"
        playsinline
        controls
        preload="metadata"
        :poster="current.poster || ''"
    />
  </VideoFrame>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import videojs from 'video.js'
import 'video.js/dist/video-js.css'
import VideoFrame from '@/pages/home/components/modules/posts/media/VideoFrame.vue'

const players = window.__GLOBAL_VIDEO_PLAYERS__ = window.__GLOBAL_VIDEO_PLAYERS__ || []

const props = defineProps({
  video: Object,
  videos: { type: Array, default: () => [] },
  autoplay: Boolean,
  loop: Boolean,
  muted: Boolean
})

const sources = computed(() => props.videos?.length ? props.videos : props.video ? [props.video] : [])
const currentIndex = ref(0)
const current = computed(() => sources.value[currentIndex.value] || {})

const videoEl = ref(null)
let player

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
    fluid: false,
    preload: 'metadata'
  })

  players.push(player)

  player.on('play', () => {
    players.forEach(p => {
      if (p !== player && !p.paused()) p.pause()
    })
  })

  load()

  player.on('ended', () => {
    if (++currentIndex.value >= sources.value.length && props.loop) currentIndex.value = 0
  })
}

onMounted(() => {
  if (sources.value.length) initPlayer()
})

onBeforeUnmount(() => {
  const idx = players.indexOf(player)
  if (idx !== -1) players.splice(idx, 1)
  player?.dispose()
})

watch([sources, currentIndex], () => {
  if (player) load()
  else if (sources.value.length) initPlayer()
})
</script>

<style scoped>
:deep(.video-js) {
  width: 100% !important;
  height: 100% !important;
  object-fit: contain !important;
}
</style>

<template>
  <section class="min-h-screen px-4 text-zinc-900 dark:text-zinc-100">
    <div class="container mx-auto">
      <VideoFrame :ratio="aspectRatio">
        <video
            :key="videoPlayerKey"
            ref="videoPlayer"
            class="video-js vjs-default-skin vjs-big-play-centered"
            controls
            preload="auto"
            playsinline
        ></video>
      </VideoFrame>

      <VideoActions v-if="video" :video="video" />
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-2">
        <div class="lg:col-span-8 space-y-4">
          <CommentThread v-if="video" :video="video" :post-id="video.id" />
          <div class="block lg:hidden">
            <RelatedVideos :video="video" horizontal />
          </div>
        </div>
        <div class="hidden lg:block lg:col-span-4">
          <RelatedVideos :video="video"/>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import {ref, computed, onMounted, watch, onUnmounted, nextTick} from 'vue'
import { useRoute } from 'vue-router'
import { useVideoStore } from '@/stores/useVideoStore'
import VideoActions from '@/pages/home/components/modules/VideoCard/VideoActions.vue'
import RelatedVideos from '@/pages/home/components/modules/VideoCard/RelatedVideos.vue'

import videojs from 'video.js'
import 'video.js/dist/video-js.css'

import 'videojs-contrib-quality-levels'
import videoJsHttpSourceSelector from 'videojs-http-source-selector'
import VideoFrame from "@/pages/home/components/modules/VideoCard/VideoFrame.vue";
import CommentThread from "@/pages/home/components/modules/posts/Footer/Comments/CommentThread.vue";

videojs.registerPlugin('httpSourceSelector', videoJsHttpSourceSelector)

const route = useRoute()
const store = useVideoStore()
const video = ref(null)
const videoPlayer = ref(null)
const videoPlayerKey = ref(0)
let playerInstance = null

onMounted(async () => {
  await store.fetchVideoById(route.params.id)
  video.value = store.selectedVideo

  await nextTick()

  const meta = video.value?.media?.[0]?.meta
  if (!meta) return

  playerInstance = videojs(videoPlayer.value, {
    autoplay: false,
    controls: true,
    preload: 'auto',
    fluid: false,
    sources: [{
      src: `http://localhost:8080/storage/${meta.hls_path}`,
      type: 'application/x-mpegURL'
    }],
    poster: `http://localhost:8080/storage/${meta.thumb_large || meta.thumb_path || ''}`,
    html5: {
      vhs: { overrideNative: true },
      nativeAudioTracks: false,
      nativeVideoTracks: false
    }
  }, function () {
    this.httpSourceSelector({ default: 'auto' })
  })
})


watch(() => route.params.id, async (newId) => {
  await store.fetchVideoById(newId)
  video.value = store.selectedVideo
})

const aspectRatio = computed(() => {
  const meta = video.value?.media?.[0]?.meta
  return (meta?.original_width && meta?.original_height)
      ? `${meta.original_width}/${meta.original_height}`
      : '16/9'
})

watch(() => route.params.id, async (newId) => {
  await store.fetchVideoById(newId)
  video.value = store.selectedVideo

  await nextTick()

  const meta = video.value?.media?.[0]?.meta
  if (!meta || !playerInstance) return

  playerInstance.pause()
  playerInstance.poster(`http://localhost:8080/storage/${meta.thumb_large || meta.thumb_path || ''}`)
  playerInstance.src({
    src: `http://localhost:8080/storage/${meta.hls_path}`,
    type: 'application/x-mpegURL'
  })
  playerInstance.load()
})



onUnmounted(() => {
  if (playerInstance) playerInstance.dispose()
})
</script>

<style scoped>
:deep(.video-js),
:deep(.video-js .vjs-tech) {
  width: 100% !important;
  height: 100% !important;
  object-fit: contain !important;
  background: black;
  border-radius: 12px;
}

:deep(.video-js .vjs-control-bar) {
  background: rgba(0, 0, 0, 0.6) !important;
  backdrop-filter: blur(10px) !important;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 4px 8px;
  font-family: inherit !important;
  height: 48px !important;
  border-radius: 0 0 12px 12px;
}

:deep(.video-js .vjs-button) {
  color: white !important;
  transition: all 0.3s ease;
}

:deep(.video-js .vjs-button:hover) {
  color: #ff0040 !important;
  transform: scale(1.2);
}

:deep(.video-js .vjs-progress-control) {
  flex: 1;
  margin: 0 10px;
}

:deep(.video-js .vjs-play-progress) {
  background-color: #ff0040 !important;
}

:deep(.video-js .vjs-load-progress) {
  background: rgba(255, 255, 255, 0.2) !important;
}

:deep(.video-js .vjs-volume-level) {
  background-color: #ff0040 !important;
}

:deep(.video-js .vjs-remaining-time),
:deep(.video-js .vjs-current-time),
:deep(.video-js .vjs-duration) {
  color: white !important;
  font-size: 0.8rem;
}

:deep(.video-js .vjs-http-source-selector) {
  font-size: 1rem !important;
  color: white !important;
  display: flex !important;
  align-items: center;
  justify-content: center;
  padding: 4px 8px !important;
  border-radius: 6px;
  transition: all 0.3s ease;
}

/* Hover effect */
:deep(.video-js .vjs-http-source-selector:hover) {
  background: rgba(255, 0, 64, 0.2);
  color: #ff0040 !important;
  transform: scale(1.1);
}

:deep(.video-js .vjs-big-play-button) {
  position: absolute !important;
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%, -50%) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 160px !important;
  height: 62px !important;
  padding: 0 1.5rem !important;
  font-size: 1.1rem !important;
  font-weight: 600 !important;
  color: white !important;
  background: rgba(255, 255, 255, 0.08) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  border: 2px solid rgba(255, 0, 64, 0.4) !important;
  border-radius: 12px !important;
  z-index: 999 !important;
  cursor: pointer;
  transition: all 0.3s ease-in-out !important;
  box-shadow: 0 0 16px rgba(255, 0, 64, 0.3) !important;
  text-align: center !important;
}

:deep(.video-js .vjs-big-play-button .vjs-icon-placeholder::before) {
  content: '▶ PLAY' !important;
  font-family: inherit !important;
  font-weight: 600 !important;
  font-size: 1.1rem !important;
  color: white !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  height: 100% !important;
  width: 100% !important;
  line-height: 1 !important;
}

:deep(.video-js .vjs-big-play-button:hover) {
  background: rgba(255, 0, 64, 0.15) !important;
  box-shadow: 0 0 30px rgba(255, 0, 64, 0.6) !important;
  border-color: rgba(255, 0, 64, 0.8) !important;
  transform: translate(-50%, -50%) scale(1.05) !important;
}

:deep(.video-js.vjs-playing .vjs-big-play-button) {
  display: none !important;
}
</style>

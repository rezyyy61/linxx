<template>
    <div v-if="normVideos.length" class="space-y-6">
        <div
            v-for="(video,i) in normVideos"
            :key="video.src"
            class="rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700"
        >
            <div class="relative w-full aspect-video bg-black">
                <video
                    :ref="el => setRef(el,i)"
                    class="plyr-video"
                    playsinline
                    preload="none"
                    :poster="video.poster || defaultPoster"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onBeforeUnmount } from 'vue'
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'
import Hls from 'hls.js'

const props = defineProps({
    videos: {
        type: Array,
        default: () => []
    },
    baseUrl: {
        type: String,
        default: ''
    }
})

const defaultPoster = 'https://via.placeholder.com/640x360?text=Preview'

function abs(p) {
    if (!p) return ''
    if (/^https?:\/\//i.test(p)) return p
    const b = props.baseUrl ? props.baseUrl.replace(/\/+$/, '') : ''
    const rel = String(p).replace(/^\/+/, '')
    return b ? `${b}/${rel}` : rel
}

const normVideos = computed(() =>
    (props.videos || []).map(v => {
        const src = v.src || v.url || v.hls_path || v.path || ''
        const poster = v.poster || v.thumb_path || v.meta?.thumb_path || v.thumbnail || ''
        const type = v.type || (/\.m3u8($|\?)/i.test(src) ? 'hls' : 'mp4')
        return {
            src: abs(src),
            poster: abs(poster),
            type,
            duration: v.duration || null
        }
    }).filter(v => !!v.src)
)

const els = reactive([])
const hlsArr = reactive([])
const plyrArr = reactive([])
const loaded = reactive([])

function loadSource(i) {
    const el = els[i]
    if (!el) return
    const v = normVideos.value[i]
    if (!v) return
    if (v.type === 'hls' && Hls.isSupported()) {
        const hls = new Hls()
        hls.loadSource(v.src)
        hls.attachMedia(el)
        hlsArr[i] = hls
    } else if (el.canPlayType('application/vnd.apple.mpegurl') && /\.m3u8($|\?)/i.test(v.src)) {
        el.src = v.src
    } else {
        el.src = v.src
    }
    loaded[i] = true
}

function handlePlay(i) {
    if (!loaded[i]) {
        const plyr = plyrArr[i]
        if (plyr) plyr.pause()
        loadSource(i)
        requestAnimationFrame(() => {
            const plyr = plyrArr[i]
            if (plyr) plyr.play()
            else {
                const el = els[i]
                if (el) el.play()
            }
        })
    } else {
        pauseOthers(i)
    }
}

function pauseOthers(except) {
    plyrArr.forEach((p, idx) => {
        if (idx !== except && p && !p.paused) p.pause()
    })
}

function setRef(el, i) {
    if (!el) return
    els[i] = el
    if (plyrArr[i]) return
    const plyr = new Plyr(el, {
        controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'],
        clickToPlay: true
    })
    plyr.on('play', () => handlePlay(i))
    plyrArr[i] = plyr
}

onBeforeUnmount(() => {
    plyrArr.forEach(p => p && p.destroy && p.destroy())
    hlsArr.forEach(h => h && h.destroy && h.destroy())
})
</script>

<style scoped>
.plyr-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    background-color: black;
}
</style>

<template>
    <div
        v-if="visible"
        class="absolute z-50"
        :style="{
    top: `${position.top - 350}px`,
    left: `${position.left}px`,
    transform: 'translateX(-50%)',
  }"
        @click.self="close"
    >
        <div class="bg-white dark:bg-neutral-800 rounded-2xl px-8 py-6 w-[100%] max-w-3xl shadow-2xl relative">
            <button
                class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl"
                @click="close"
                aria-label="Close"
            >
                ✕
            </button>

            <h3 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Share</h3>

            <div class="flex gap-6 overflow-x-auto pb-3 mb-8 no-scrollbar">
                <button
                    v-for="item in shareItems"
                    :key="item.label"
                    @click="shareTo(item.url)"
                    class="flex flex-col items-center gap-2"
                >
                    <div
                        class="w-16 h-16 rounded-full flex items-center justify-center text-white"
                        :style="{ backgroundColor: item.color }"
                    >
                        <component v-if="!item.svg" :is="item.icon" class="w-8 h-8" />
                        <span v-else class="w-8 h-8" v-html="item.svg" />
                    </div>
                    <span class="text-sm text-gray-700 dark:text-white font-medium text-center">{{ item.label }}</span>
                </button>
            </div>

            <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-5 py-3 rounded-xl">
                <span class="truncate text-base text-gray-800 dark:text-white">{{ shareUrl }}</span>
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white text-base px-4 py-2 rounded-full ml-3"
                    @click="copyLink"
                >
                    {{ copied ? 'Copied!' : 'Copy Link' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
    Facebook,
    Twitter,
    Mail,
    Code,
} from 'lucide-vue-next'

const props = defineProps({
    visible: Boolean,
    url: {
        type: String,
        default: ''
    },
    position: {
        type: Object,
        default: () => ({ top: 0, left: 0 })
    }
})

const emit = defineEmits(['close'])
const copied = ref(false)

const shareUrl = computed(() => props.url)

const telegramSvg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
<path fill="#29b6f6" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
<path fill="#fff" d="M33.95,15l-3.746,19.126c0,0-0.161,0.874-1.245,0.874c-0.576,0-0.873-0.274-0.873-0.274l-8.114-6.733 l-3.97-2.001l-5.095-1.355c0,0-0.907-0.262-0.907-1.012c0-0.625,0.933-0.923,0.933-0.923l21.316-8.468 c-0.001-0.001,0.651-0.235,1.126-0.234C33.667,14,34,14.125,34,14.5C34,14.75,33.95,15,33.95,15z"></path>
<path fill="#b0bec5" d="M23,30.505l-3.426,3.374c0,0-0.149,0.115-0.348,0.12c-0.069,0.002-0.143-0.009-0.219-0.043 l0.964-5.965L23,30.505z"></path>
<path fill="#cfd8dc" d="M29.897,18.196c-0.169-0.22-0.481-0.26-0.701-0.093L16,26c0,0,2.106,5.892,2.427,6.912 c0.322,1.021,0.58,1.045,0.58,1.045l0.964-5.965l9.832-9.096C30.023,18.729,30.064,18.416,29.897,18.196z"></path>
</svg>`

const whatsappSvg = `
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 256 256" xml:space="preserve">
<g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
\t<rect x="19.41" y="20.66" rx="0" ry="0" width="54.02" height="49.95" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: evenodd; opacity: 1;" transform=" matrix(1 0 0 1 0 0) "/>
    \t<path d="M 76.735 13.079 C 68.315 4.649 57.117 0.005 45.187 0 C 20.605 0 0.599 20.005 0.589 44.594 c -0.003 7.86 2.05 15.532 5.953 22.296 L 0.215 90 l 23.642 -6.202 c 6.514 3.553 13.848 5.426 21.312 5.428 h 0.018 c 0.001 0 -0.001 0 0 0 c 24.579 0 44.587 -20.007 44.597 -44.597 C 89.789 32.713 85.155 21.509 76.735 13.079 z M 27.076 46.217 c -0.557 -0.744 -4.55 -6.042 -4.55 -11.527 c 0 -5.485 2.879 -8.181 3.9 -9.296 c 1.021 -1.115 2.229 -1.394 2.972 -1.394 s 1.487 0.007 2.136 0.039 c 0.684 0.035 1.603 -0.26 2.507 1.913 c 0.929 2.231 3.157 7.717 3.436 8.274 c 0.279 0.558 0.464 1.208 0.093 1.952 c -0.371 0.743 -0.557 1.208 -1.114 1.859 c -0.557 0.651 -1.17 1.453 -1.672 1.952 c -0.558 0.556 -1.139 1.159 -0.489 2.274 c 0.65 1.116 2.886 4.765 6.199 7.72 c 4.256 3.797 7.847 4.973 8.961 5.531 c 1.114 0.558 1.764 0.465 2.414 -0.279 c 0.65 -0.744 2.786 -3.254 3.529 -4.369 c 0.743 -1.115 1.486 -0.929 2.507 -0.558 c 1.022 0.372 6.5 3.068 7.614 3.625 c 1.114 0.558 1.857 0.837 2.136 1.302 c 0.279 0.465 0.279 2.696 -0.65 5.299 c -0.929 2.603 -5.381 4.979 -7.522 5.298 c -1.92 0.287 -4.349 0.407 -7.019 -0.442 c -1.618 -0.513 -3.694 -1.199 -6.353 -2.347 C 34.934 58.216 27.634 46.961 27.076 46.217 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(76,175,80); fill-rule: evenodd; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
</g>
</svg>
`

const shareItems = computed(() => {
    const encoded = encodeURIComponent(shareUrl.value)
    return [
        { label: 'Share to Feed', icon: Code, color: '#6B7280', url: '#embed' },
        { label: 'WhatsApp', svg: whatsappSvg, color: '#25D366', url: `https://wa.me/?text=${encoded}` },
        { label: 'Facebook', icon: Facebook, color: '#1877F2', url: `https://www.facebook.com/sharer/sharer.php?u=${encoded}` },
        { label: 'Telegram', svg: telegramSvg, color: '#0088cc', url: `https://t.me/share/url?url=${encoded}` },
        { label: 'X', icon: Twitter, color: '#000000', url: `https://twitter.com/intent/tweet?url=${encoded}` },
        { label: 'Email', icon: Mail, color: '#7b7b7b', url: `mailto:?subject=Interesting link&body=${encoded}` },
    ]
})

function shareTo(url) {
    if (url === '#copy') {
        copyLink()
        return
    }
    if (url === '#embed') {
        emit('embed')
        return
    }
    window.open(url, '_blank')
}

function copyLink() {
    navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
}

function close() {
    emit('close')
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

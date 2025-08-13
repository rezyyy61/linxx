<template>
    <div v-if="playerList.length && showPlayer" class="space-y-6">
        <div
            class="bg-white dark:bg-gray-800/70 border border-gray-200 dark:border-gray-600 rounded-xl p-4 shadow-sm dark:shadow-md transition-all duration-300"
        >
            <audio-player
                :audio-list="playerList"
                class="w-full"
            />
        </div>
    </div>
</template>

<script setup>
import AudioPlayer from '@liripeng/vue-audio-player'
import { computed, ref, onMounted, nextTick } from 'vue'

const props = defineProps({
    audios: {
        type: Array,
        default: () => []
    }
})

// ✅ صبر می‌کنیم تا DOM کامل mount بشه
const showPlayer = ref(false)

onMounted(async () => {
    await nextTick()
    showPlayer.value = true
})

const playerList = computed(() =>
    props.audios.map((a) => ({
        src: a.url,
        title: a.title || getFilename(a.url)
    }))
)

function getFilename(url) {
    return decodeURIComponent(url.split('/').pop())
}
</script>

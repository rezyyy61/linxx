<template>
    <div class="mt-10 border-t pt-6 space-y-6">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white">
            {{ $t('post.preview_title') }}
        </h4>

        <!-- Text Preview -->
        <div
            v-if="postText"
            class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl p-5 text-base text-gray-800 dark:text-white whitespace-pre-line"
        >
            {{ postText }}
        </div>

        <PostImageGallery
            v-if="images.length"
            :images="images"
            @remove="index => $emit('removeImage', index)"
        />

        <!-- Video Preview -->
        <div v-if="videos.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div
                v-for="(video, i) in videos"
                :key="i"
                class="relative group border border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden shadow-md"
            >
                <video
                    :src="video.url"
                    controls
                    class="w-full h-64 object-cover rounded-t-xl"
                ></video>

                <div
                    class="flex justify-between items-center px-3 py-2 bg-gray-100 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-700 rounded-b-xl"
                >
            <span class="text-xs text-gray-600 dark:text-gray-400 truncate">
                {{ video.file?.name || 'Video ' + (i + 1) }}
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

        <!-- Audio Preview -->
        <div v-if="audio" class="relative w-full rounded-xl overflow-hidden border border-gray-300 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-800">
            <audio :src="audio.url" controls class="w-full" />
            <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ audio.file.name }}</span>
                <button
                    @click="$emit('removeAudio')"
                    class="text-xs text-red-600 hover:text-red-800 font-medium transition"
                >
                    {{ $t('post.remove') }}
                </button>
            </div>
        </div>

        <!-- File Preview -->
        <ul
            v-if="files.length"
            class="space-y-2 text-sm text-gray-700 dark:text-gray-300"
        >
            <li
                v-for="(file, i) in files"
                :key="i"
                class="flex justify-between items-center bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2"
            >
                <span>📄 {{ file.name }} — {{ formatSize(file.size) }}</span>
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
import PostImageGallery from './PostImageGallery.vue'

const props = defineProps({
    images: Array,
    postText: String,
    videos: Array,
    files: Array,
    audio: Object,
})

const localImages = ref([...props.images])

watch(() => props.images, (val) => {
    localImages.value = [...val]
})


function formatSize(size) {
    const kb = size / 1024
    return kb < 1024 ? `${kb.toFixed(1)} KB` : `${(kb / 1024).toFixed(1)} MB`
}
</script>



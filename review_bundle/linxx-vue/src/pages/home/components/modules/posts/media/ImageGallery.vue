<template>
    <!-- No images -->
    <div v-if="!validImages.length" />

    <!-- Single image -->
    <div
        v-else-if="layout === 1"
        class="relative cursor-pointer flex justify-center w-full"
        @click="openGallery(0)"
    >
        <img
            :src="validImages[0].url"
            :alt="validImages[0].alt || 'Post image'"
            loading="lazy"
            class="w-full h-auto max-h-[65vh] object-contain transition-transform duration-200 hover:scale-105"
        />
        <button
            v-if="removable"
            @click.stop="$emit('remove', 0)"
            class="absolute top-1 right-1 bg-white dark:bg-gray-800 rounded-full p-1 shadow hover:bg-red-100 dark:hover:bg-red-900"
        >
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Two images -->
    <div v-else-if="layout === 2" class="grid grid-cols-2 gap-2">
        <div
            v-for="(img, i) in previewList"
            :key="img.url"
            class="relative cursor-pointer"
            @click="openGallery(i)"
        >
            <img
                :src="img.url"
                :alt="img.alt || 'Post image'"
                loading="lazy"
                class="w-full  object-cover aspect-square transition-transform duration-200 hover:scale-105"
            />
            <button
                v-if="removable"
                @click.stop="$emit('remove', i)"
                class="absolute top-1 right-1 bg-white dark:bg-gray-800 rounded-full p-1 shadow hover:bg-red-100 dark:hover:bg-red-900"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Three images -->
    <div v-else-if="layout === 3" class="grid grid-cols-3 gap-2 auto-rows-[1fr] grid-rows-2">
        <div class="col-span-2 row-span-2 relative cursor-pointer" @click="openGallery(0)">
            <img
                :src="previewList[0].url"
                :alt="previewList[0].alt || 'Post image 1'"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-200 hover:scale-105"
            />
            <button
                v-if="removable"
                @click.stop="$emit('remove', 0)"
                class="absolute top-1 right-1 bg-white dark:bg-gray-800 rounded-full p-1 shadow hover:bg-red-100 dark:hover:bg-red-900"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div
            v-for="i in [1, 2]"
            :key="previewList[i]?.url"
            class="relative cursor-pointer"
            @click="openGallery(i)"
        >
            <img
                :src="previewList[i].url"
                :alt="previewList[i].alt || `Post image ${i + 1}`"
                loading="lazy"
                class="w-full h-full object-cover transition-transform duration-200 hover:scale-105"
            />
            <button
                v-if="removable"
                @click.stop="$emit('remove', i)"
                class="absolute top-1 right-1 bg-white dark:bg-gray-800 rounded-full p-1 shadow hover:bg-red-100 dark:hover:bg-red-900"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Four or more images -->
    <div v-else class="grid grid-cols-2 gap-2">
        <div
            v-for="(img, i) in previewList"
            :key="img.url"
            class="relative cursor-pointer"
            @click="openGallery(i)"
        >
            <img
                :src="img.url"
                :alt="img.alt || 'Post image'"
                loading="lazy"
                class="w-full object-cover aspect-square transition-transform duration-200 hover:scale-105"
            />
            <button
                v-if="removable"
                @click.stop="$emit('remove', i)"
                class="absolute top-1 right-1 bg-white dark:bg-gray-800 rounded-full p-1 shadow hover:bg-red-100 dark:hover:bg-red-900"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div
                v-if="i === previewList.length - 1 && remainingCount > 0"
                class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-2xl font-bold select-none"
            >
                +{{ remainingCount }}
            </div>
        </div>
    </div>

    <vue-easy-lightbox
        v-if="lightboxVisible && validImages.length"
        :visible="lightboxVisible"
        :imgs="lightboxImgs"
        :index="lightboxIndex"
        @hide="lightboxVisible = false"
    />
</template>

<script setup>
import { ref, computed } from 'vue'
import VueEasyLightbox from 'vue-easy-lightbox'

const props = defineProps({
    images: { type: Array, default: () => [] },
    maxPreview: { type: Number, default: 4 },
    removable: { type: Boolean, default: false }
})

const validImages = computed(() => (props.images || []).filter(i => i?.url))
const count = computed(() => validImages.value.length)

const layout = computed(() => {
    if (count.value <= 1) return 1
    if (count.value === 2) return 2
    if (count.value === 3) return 3
    return 4
})

const previewList = computed(() =>
    count.value <= 4 ? validImages.value : validImages.value.slice(0, props.maxPreview)
)

const remainingCount = computed(() => Math.max(count.value - props.maxPreview, 0))

const lightboxVisible = ref(false)
const lightboxIndex = ref(0)

const lightboxImgs = computed(() => validImages.value.map(i => i.url))

function openGallery(i) {
    if (i < validImages.value.length) {
        lightboxIndex.value = i
        lightboxVisible.value = true
    }
}
</script>

<style scoped>
/* Tailwind handles all styling */
</style>

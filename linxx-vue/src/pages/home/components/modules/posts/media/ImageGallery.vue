<template>
    <div v-if="!validImages.length" />

    <div
        v-else-if="layout === 1"
        class="cursor-pointer flex justify-center"
        @click="openGallery(0)"
    >
        <img
            :src="validImages[0].url"
            :alt="validImages[0].alt || 'Post image'"
            loading="lazy"
            class="w-full rounded-2xl object-cover aspect-square sm:aspect-video transition-transform duration-200 hover:scale-105"
        />
    </div>

    <div
        v-else-if="layout === 2"
        class="grid grid-cols-2 gap-2"
    >
        <img
            v-for="(img,i) in previewList"
            :key="img.url"
            :src="img.url"
            :alt="img.alt || 'Post image'"
            loading="lazy"
            @click="openGallery(i)"
            class="w-full rounded-xl object-cover aspect-square cursor-pointer transition-transform duration-200 hover:scale-105"
        />
    </div>

    <div
        v-else-if="layout === 3"
        class="grid grid-cols-3 gap-2 auto-rows-[1fr] grid-rows-2"
    >
        <div
            class="col-span-2 row-span-2 cursor-pointer relative"
            @click="openGallery(0)"
        >
            <img
                :src="previewList[0].url"
                :alt="previewList[0].alt || 'Post image 1'"
                loading="lazy"
                class="w-full h-full rounded-xl object-cover transition-transform duration-200 hover:scale-105"
            />
        </div>
        <div
            class="col-span-1 row-span-1 cursor-pointer"
            @click="openGallery(1)"
        >
            <img
                :src="previewList[1].url"
                :alt="previewList[1].alt || 'Post image 2'"
                loading="lazy"
                class="w-full h-full rounded-xl object-cover transition-transform duration-200 hover:scale-105"
            />
        </div>
        <div
            class="col-span-1 row-span-1 cursor-pointer"
            @click="openGallery(2)"
        >
            <img
                :src="previewList[2].url"
                :alt="previewList[2].alt || 'Post image 3'"
                loading="lazy"
                class="w-full h-full rounded-xl object-cover transition-transform duration-200 hover:scale-105"
            />
        </div>
    </div>

    <div
        v-else
        class="grid grid-cols-2 gap-2"
    >
        <div
            v-for="(img,i) in previewList"
            :key="img.url"
            class="relative cursor-pointer"
            @click="openGallery(i)"
        >
            <img
                :src="img.url"
                :alt="img.alt || 'Post image'"
                loading="lazy"
                class="w-full rounded-xl object-cover aspect-square transition-transform duration-200 hover:scale-105"
            />
            <div
                v-if="i === previewList.length - 1 && remainingCount > 0"
                class="absolute inset-0 rounded-xl bg-black/60 flex items-center justify-center text-white text-2xl font-bold select-none"
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
    images: {
        type: Array,
        default: () => []
    },
    maxPreview: {
        type: Number,
        default: 2
    }
})

const validImages = computed(() => (props.images || []).filter(i => i?.url))
const count = computed(() => validImages.value.length)
const layout = computed(() => {
    const c = count.value
    if (c <= 1) return 1
    if (c === 2) return 2
    if (c === 3) return 3
    return 4
})
const previewList = computed(() => {
    const c = count.value
    if (c <= 4) return validImages.value
    return validImages.value.slice(0, props.maxPreview)
})
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

<template>
    <div
        v-if="validImages.length === 1"
        @click="openGallery(0)"
        class="cursor-pointer flex justify-center"
    >
        <img
            :src="validImages[0].url"
            alt="Post image"
            loading="lazy"
            class="rounded-2xl object-cover aspect-square w-full max-w-[400px] transition-transform duration-200 hover:scale-105"
        />
    </div>

    <div v-else-if="validImages.length === 2" class="grid grid-cols-2 gap-2">
        <img
            v-for="(img, i) in validImages"
            :key="img.url"
            :src="img.url"
            alt="Post image"
            loading="lazy"
            @click="openGallery(i)"
            class="rounded-xl aspect-square object-cover cursor-pointer w-full transition-transform duration-200 hover:scale-105"
        />
    </div>

    <div v-else-if="validImages.length > 2" class="grid grid-cols-2 gap-2">
        <img
            :src="validImages[0].url"
            alt="Post image"
            loading="lazy"
            @click="openGallery(0)"
            class="rounded-xl aspect-square object-cover cursor-pointer w-full transition-transform duration-200 hover:scale-105"
        />

        <div
            class="relative cursor-pointer transition-transform duration-200 hover:scale-105"
            @click="openGallery(1)"
        >
            <img
                :src="validImages[1].url"
                alt="Post image"
                loading="lazy"
                class="rounded-xl aspect-square object-cover w-full"
            />
            <div
                class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-white text-2xl font-bold rounded-xl"
            >
                +{{ validImages.length - 2 }}
            </div>
        </div>
    </div>

    <vue-easy-lightbox
        v-if="visible && validImages.length > 0"
        :visible="visible"
        :imgs="validImages.map(img => img.url)"
        :index="index"
        @hide="visible = false"
    />
</template>

<script setup>
import { ref, computed } from 'vue'
import VueEasyLightbox from 'vue-easy-lightbox'

const props = defineProps({
    images: {
        type: Array,
        default: () => []
    }
})

const validImages = computed(() => props.images.filter(img => img?.url))

const visible = ref(false)
const index = ref(0)

function openGallery(i) {
    if (i < validImages.value.length) {
        index.value = i
        visible.value = true
    }
}
</script>

<style scoped>
.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>

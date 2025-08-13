<template>
    <div>
        <!-- Grid of thumbnails -->
        <div class="grid grid-cols-3 gap-3">
            <div
                v-for="(img, index) in images"
                :key="index"
                class="relative group rounded-lg border overflow-hidden cursor-pointer"
            >
                <img
                    :src="img.url"
                    @click="open(index)"
                    class="object-cover h-32 w-full group-hover:brightness-90 transition"
                />
                <button
                    @click.stop="remove(index)"
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition"
                >
                    <XIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Modal -->
        <Transition name="fade">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
                @keyup.esc="close"
                tabindex="0"
                ref="modal"
            >
                <div class="relative max-w-4xl w-full max-h-[90vh] flex items-center justify-center">
                    <img
                        :src="images[currentIndex].url"
                        class="rounded-xl max-h-[80vh] object-contain shadow-2xl"
                    />

                    <!-- Arrows -->
                    <button
                        @click="prev"
                        class="absolute left-2 p-2 rounded-full bg-black/50 hover:bg-black/80 text-white"
                    >
                        <ChevronLeftIcon class="w-6 h-6" />
                    </button>

                    <button
                        @click="next"
                        class="absolute right-2 p-2 rounded-full bg-black/50 hover:bg-black/80 text-white"
                    >
                        <ChevronRightIcon class="w-6 h-6" />
                    </button>

                    <!-- Close -->
                    <button
                        @click="close"
                        class="absolute top-2 right-2 p-2 bg-white/90 hover:bg-white text-gray-800 rounded-full shadow"
                    >
                        <XIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
    ChevronLeft as ChevronLeftIcon,
    ChevronRight as ChevronRightIcon,
    X as XIcon
} from 'lucide-vue-next'

const props = defineProps({
    images: Array
})
const emit = defineEmits(['remove'])

const showModal = ref(false)
const currentIndex = ref(0)
const modal = ref(null)

function open(index) {
    currentIndex.value = index
    showModal.value = true
    setTimeout(() => modal.value?.focus(), 50)
}

function close() {
    showModal.value = false
}

function next() {
    currentIndex.value = (currentIndex.value + 1) % props.images.length
}

function prev() {
    currentIndex.value =
        (currentIndex.value - 1 + props.images.length) % props.images.length
}

function remove(index) {
    emit('remove', index)
}

function handleKey(e) {
    if (!showModal.value) return
    if (e.key === 'ArrowRight') next()
    if (e.key === 'ArrowLeft') prev()
}

onMounted(() => window.addEventListener('keydown', handleKey))
onBeforeUnmount(() => window.removeEventListener('keydown', handleKey))
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

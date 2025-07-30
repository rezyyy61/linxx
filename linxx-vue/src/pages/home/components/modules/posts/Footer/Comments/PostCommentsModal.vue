<template>
    <transition name="fade">
        <div
            v-if="visible"
            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex justify-center items-center px-2"
            @click.self="$emit('close')"
            @keydown.esc="$emit('close')"
            tabindex="-1"
            dir="ltr"
        >
            <div
                class="relative w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg flex flex-col overflow-hidden max-h-[80vh] my-6
        max-w-sm sm:max-w-md md:max-w-3xl lg:max-w-5xl xl:max-w-7xl"
            >
                <div class="shrink-0 flex justify-between items-center p-4 border-b dark:border-gray-700">
                    <button @click="$emit('close')" class="text-gray-500 hover:text-red-500 text-xl">✕</button>
                </div>

                <!-- Mobile View -->
                <PostCommentsModalMobile
                    v-if="!isWide"
                    :post="post"
                    @close="$emit('close')"
                />

                <!-- Desktop View -->
                <PostCommentsModalDesktop
                    v-else
                    :post="post"
                />
            </div>
        </div>
    </transition>
</template>


<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import PostCommentsModalDesktop from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentsModalDesktop.vue'
import PostCommentsModalMobile from '@/pages/home/components/modules/posts/Footer/Comments/PostCommentsModalMobile.vue'

const props = defineProps({
    visible: Boolean,
    post: Object
})

defineEmits(['close'])

useI18n()

const isWide = ref(window.innerWidth >= 1024)

function handleResize() {
    isWide.value = window.innerWidth >= 1024
}
onMounted(() => window.addEventListener('resize', handleResize, { passive: true }))
onBeforeUnmount(() => window.removeEventListener('resize', handleResize))

watch(() => props.visible, (v) => {
    document.body.style.overflow = v ? 'hidden' : ''
})
onBeforeUnmount(() => {
    document.body.style.overflow = ''
})
</script>

<template>
    <transition name="fade">
        <div
            v-if="visible"
            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex justify-center items-start lg:items-center pt-16 lg:pt-0 px-2"
            @click.self="$emit('close')"
            @keydown.esc="$emit('close')"
            tabindex="-1"
            dir="ltr"
        >
            <div
                class="relative w-full mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg flex flex-col overflow-hidden h-screen lg:h-auto sm:max-w-lg md:max-w-2xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl"
                :style="isWide ? { maxHeight: '90vh' } : {}"
            >
                <div class="shrink-0 flex justify-between items-center p-4 border-b dark:border-gray-700">
                    <button
                        @click="$emit('close')"
                        class="text-gray-500 hover:text-red-500 text-xl"
                    >
                        ✕
                    </button>
                </div>

                <!-- Mobile View -->
                <PostCommentsModalMobile
                    v-if="!isWide"
                    :visible="visible"
                    :post="post"
                    :comments="comments"
                    v-model="inputVal"
                    @send="send"
                    @like="toggleLike"
                    @delete="remove"
                    :total-count="totalCount"
                />

                <PostCommentsModalDesktop
                    v-else
                    :post="post"
                    :comments="comments"
                    v-model="inputVal"
                    @send="send"
                    @like="toggleLike"
                    @delete="remove"
                    :total-count="totalCount"
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
import { useComments } from '@/stores/comments/useComments'

// Props
const props = defineProps({
    visible: Boolean,
    post: Object
})

// Emits
defineEmits(['close'])

useI18n()

// Layout handling
const isWide = ref(window.innerWidth >= 1024)
function handleResize() {
    isWide.value = window.innerWidth >= 1024
}
onMounted(() => window.addEventListener('resize', handleResize, { passive: true }))
onBeforeUnmount(() => window.removeEventListener('resize', handleResize))

// Comments logic (new version)
const {
    comments,
    inputVal,
    totalCount,
    load,
    send,
    toggleLike,
    remove
} = useComments(props.post.id)

// Control body scroll when modal is open
watch(() => props.visible, (v) => {
    document.body.style.overflow = v ? 'hidden' : ''
    if (v && comments.value.length === 0) {
        load()
    }
})

onBeforeUnmount(() => {
    document.body.style.overflow = ''
})
</script>

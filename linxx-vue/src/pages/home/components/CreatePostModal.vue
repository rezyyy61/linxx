<template>
    <Transition appear>
        <div
            v-if="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 py-8"
            @click.self="$emit('close')"
        >
            <Transition
                enter-active-class="duration-300 ease-out"
                enter-from-class="opacity-0 scale-95 translate-y-6"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="duration-200 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 translate-y-6"
            >
                <div
                    v-if="visible"
                    class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-gray-200 dark:ring-gray-700"
                >
                    <!-- Close Button -->
                    <div class="flex justify-end px-6 pt-5">
                        <button
                            @click="$emit('close')"
                            class="grid w-9 h-9 place-content-center rounded-full text-gray-400 hover:text-red-600 hover:bg-red-100/50 dark:hover:bg-red-500/10 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="px-6 py-6">
                        <NewPost :isModal="true" />
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NewPost from '@/pages/admin/sections/NewPost.vue'

const visible = ref(true)
onMounted(() => {
    visible.value = true
})
</script>

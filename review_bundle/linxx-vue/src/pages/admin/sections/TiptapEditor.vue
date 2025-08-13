<template>
    <div :class="[isFullScreen ? 'fixed inset-0 z-50 overflow-auto flex justify-center items-start pt-10 px-4 sm:px-8 bg-gray-100/95 dark:bg-gray-900/95 backdrop-blur-md' : 'max-w-5xl mx-auto mt-10 space-y-6']">
        <div :class="[
      'space-y-6 bg-white dark:bg-gray-800 shadow-[0_10px_40px_rgba(0,0,0,0.1)] rounded-2xl p-6 sm:p-8 transition-all',
      isFullScreen ? 'w-full' : 'w-full max-w-4xl'
    ]">
            <div v-if="isFullScreen" class="text-center">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create New Post</h1>
            </div>

            <div id="toolbar" class="flex flex-wrap gap-2 border p-3 rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600 items-center">
                <select class="ql-header"></select>
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
                <button class="ql-blockquote"></button>
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-clean"></button>

                <button @click="toggleFullScreen" class="ml-auto flex items-center gap-2 px-3 py-2 text-lg font-bold rounded-md bg-blue-600 text-black hover:bg-blue-700 transition-all shadow-lg ring-1 ring-blue-300 dark:ring-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path v-if="!isFullScreen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V6a2 2 0 012-2h2m8 0h2a2 2 0 012 2v2m0 8v2a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-2" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h-4v-4m10 0h4v4m0 6v4h-4m-6 0h-4v-4" />
                    </svg>
                </button>
            </div>

            <div>
                <div id="editor" class="bg-white dark:bg-gray-900 min-h-[300px] border dark:border-gray-700 rounded-lg p-5 text-base leading-relaxed font-sans text-gray-800 dark:text-gray-200 shadow-inner focus:outline-none"></div>
                <div class="mt-3 text-right text-sm text-gray-500 dark:text-gray-400">Characters: {{ charCount }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import Quill from 'quill'

// v-model support
const props = defineProps({
    modelValue: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue'])

const editor = ref(null)
const charCount = ref(0)
const isFullScreen = ref(false)

const toggleFullScreen = () => { isFullScreen.value = !isFullScreen.value }

const rtlRegex = /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF]/
const updateDirection = (text) => {
    const dir = rtlRegex.test(text) ? 'rtl' : 'ltr'
    const align = dir === 'rtl' ? 'right' : 'left'
    editor.value.root.setAttribute('dir', dir)
    editor.value.root.style.textAlign = align
}

onMounted(() => {
    editor.value = new Quill('#editor', {
        theme: 'snow',
        modules: { toolbar: '#toolbar' },
    })
    // init content
    editor.value.root.innerHTML = props.modelValue || '<p></p>'
    updateDirection(editor.value.getText())

    editor.value.on('text-change', () => {
        const html = editor.value.root.innerHTML
        emit('update:modelValue', html)
        charCount.value = editor.value.getText().trim().length
        updateDirection(editor.value.getText())
    })
})

// watch external model changes
watch(() => props.modelValue, (v) => {
    if (v !== editor.value.root.innerHTML) {
        editor.value.root.innerHTML = v || '<p></p>'
        updateDirection(editor.value.getText())
    }
})
</script>

<style scoped>
@import url('https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css');
.ql-toolbar {@apply rounded-t-md border bg-white dark:bg-gray-800;}
.ql-container {@apply border-t-0 border rounded-b-md;}
</style>

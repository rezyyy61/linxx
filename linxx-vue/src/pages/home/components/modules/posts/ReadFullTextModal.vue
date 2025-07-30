<template>
    <Teleport to="body">
        <div v-if="visible" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#2a2a32] w-full max-w-[80%] max-h-[90vh] overflow-hidden rounded-2xl shadow-2xl flex flex-col">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#2a2a2a]">
                    <div class="flex items-center gap-3">
                        <Icon icon="mdi:book-open-page-variant" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Full Text Viewer</h2>
                    </div>
                    <button @click="close" class="text-gray-500 dark:text-gray-300 hover:text-red-500 transition text-2xl">&times;</button>
                </div>

                <div class="flex flex-wrap items-center gap-6 px-6 py-4 bg-gray-100 dark:bg-[#2d2d2d] border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Font Size</label>
                        <input type="range" min="14" max="24" step="1" v-model="fontSize" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ fontSize }}px</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Line Height</label>
                        <input type="range" min="1" max="3" step="0.1" v-model="lineHeight" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ lineHeight }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Text Color</label>
                        <input type="color" v-model="textColor" class="w-6 h-6 border rounded" />
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Font</label>
                        <select v-model="selectedFont" class="text-sm rounded border border-gray-300 dark:border-gray-600 p-1 bg-white dark:bg-[#1f1f1f] dark:text-white">
                            <option v-for="font in isRtl ? rtlFonts : ltrFonts" :key="font" :value="font">{{ font }}</option>
                        </select>
                    </div>
                </div>

                <div
                    v-html="sanitizedHtml"
                    class="prose prose-blue dark:prose-invert max-w-none p-6 overflow-y-auto flex-1 text-base rounded-b-2xl"
                    :dir="isRtl ? 'rtl' : 'ltr'"
                    :style="{
            color: computedTextColor,
            '--tw-prose-body': computedTextColor,
            '--tw-prose-headings': computedTextColor,
            fontSize: fontSize + 'px',
            fontFamily: appliedFont + ', sans-serif',
            lineHeight: lineHeight,
          }"
                />
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import {defineProps, defineEmits, ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { Icon } from '@iconify/vue'
import DOMPurify from 'dompurify'
import { fontSize, lineHeight, textColor, selectedRtlFont, selectedLtrFont, saveSettings, loadSettings } from '@/stores/viewerSettings'

const props = defineProps({ visible: Boolean, text: String })
const emit = defineEmits(['close'])
const close = () => emit('close')

const isDark = ref(false)
const updateTheme = () => {
    isDark.value = document.documentElement.classList.contains('dark')
}

onMounted(() => {
    updateTheme()
    loadSettings()
    const observer = new MutationObserver(updateTheme)
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
    onBeforeUnmount(() => observer.disconnect())
})

const rtlFonts = ['IRANSans', 'Vazir', 'Tanha']
const ltrFonts = ['Inter', 'Roboto', 'Georgia', 'Times New Roman']

const deepDecodeHtml = str => {
    const txt = document.createElement('textarea')
    let prev = str
    let curr = ''
    do {
        txt.innerHTML = prev
        curr = txt.value
        if (curr === prev) break
        prev = curr
    } while (true)
    return curr
}

const rawHtml = computed(() => deepDecodeHtml(props.text || ''))

const sanitizedHtml = computed(() =>
    DOMPurify.sanitize(rawHtml.value, {
        ALLOWED_TAGS: ['p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'strong', 'em', 'ul', 'ol', 'li', 'img', 'code', 'pre', 'blockquote'],
        ALLOWED_ATTR: ['href', 'src', 'alt', 'target', 'rel']
    })
)

const isRtl = computed(() => /[\u0600-\u06FF]/.test(sanitizedHtml.value))

const selectedFont = computed({
    get: () => (isRtl.value ? selectedRtlFont.value : selectedLtrFont.value),
    set: val => {
        if (isRtl.value) selectedRtlFont.value = val
        else selectedLtrFont.value = val
    }
})

const appliedFont = computed(() => (isRtl.value ? selectedRtlFont.value : selectedLtrFont.value))

const computedTextColor = computed(() => textColor.value || (isDark.value ? '#f5f5f5' : '#000'))

watch([fontSize, lineHeight, textColor, selectedRtlFont, selectedLtrFont], saveSettings)
</script>

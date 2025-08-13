// stores/viewerSettings.js
import { ref } from 'vue'

const STORAGE_KEYS = {
    fontSize: 'viewer-font-size',
    lineHeight: 'viewer-line-height',
    textColor: 'viewer-text-color',
    rtlFont: 'viewer-font-rtl',
    ltrFont: 'viewer-font-ltr'
}

export const fontSize = ref(16)
export const lineHeight = ref(1.8)
export const textColor = ref(null)
export const selectedRtlFont = ref('IRANSans')
export const selectedLtrFont = ref('Inter')

export function loadSettings() {
    fontSize.value = parseInt(localStorage.getItem(STORAGE_KEYS.fontSize)) || 16
    lineHeight.value = parseFloat(localStorage.getItem(STORAGE_KEYS.lineHeight)) || 1.8

    const color = localStorage.getItem(STORAGE_KEYS.textColor)
    textColor.value = color === '' ? null : color

    selectedRtlFont.value = localStorage.getItem(STORAGE_KEYS.rtlFont) || 'IRANSans'
    selectedLtrFont.value = localStorage.getItem(STORAGE_KEYS.ltrFont) || 'Inter'
}

export function saveSettings() {
    localStorage.setItem(STORAGE_KEYS.fontSize, fontSize.value)
    localStorage.setItem(STORAGE_KEYS.lineHeight, lineHeight.value)
    localStorage.setItem(STORAGE_KEYS.textColor, textColor.value ?? '')
    localStorage.setItem(STORAGE_KEYS.rtlFont, selectedRtlFont.value)
    localStorage.setItem(STORAGE_KEYS.ltrFont, selectedLtrFont.value)
}

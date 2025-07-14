import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

export const usePreferencesStore = defineStore('preferences', () => {
    const { locale } = useI18n()

    const isDark = ref(false)

    // === Theme ===
    function loadTheme() {
        const savedTheme = localStorage.getItem('theme')
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

        const shouldBeDark = savedTheme
            ? savedTheme === 'dark'
            : prefersDark

        isDark.value = shouldBeDark
        document.documentElement.classList.toggle('dark', isDark.value)
    }

    function toggleTheme() {
        isDark.value = !isDark.value
        document.documentElement.classList.toggle('dark', isDark.value)
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
    }

    // === Locale ===
    function loadLocale() {
        const savedLocale = localStorage.getItem('locale') || 'fa'
        locale.value = savedLocale
    }

    function toggleLocale() {
        const newLocale = locale.value === 'fa' ? 'en' : 'fa'
        locale.value = newLocale
        localStorage.setItem('locale', newLocale)
    }

    return {
        isDark,
        locale,
        toggleTheme,
        toggleLocale,
        loadTheme,
        loadLocale
    }
})

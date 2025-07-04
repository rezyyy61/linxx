import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
    state: () => ({
        darkMode: false
    }),
    actions: {
        toggleDark() {
            this.darkMode = !this.darkMode

            const html = document.documentElement
            if (this.darkMode) {
                html.classList.add('dark')
            } else {
                html.classList.remove('dark')
            }
        }
    }
})

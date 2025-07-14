<template>
    <router-view />
</template>

<script setup>
import { usePreferencesStore } from '@/stores/preferences'
import { onMounted } from 'vue'

const prefs = usePreferencesStore()

onMounted(() => {
    prefs.loadTheme()
    prefs.loadLocale()
})
</script>

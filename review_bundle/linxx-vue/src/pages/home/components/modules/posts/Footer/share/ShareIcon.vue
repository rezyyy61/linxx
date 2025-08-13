<template>
    <a
        :href="url"
        target="_blank"
        rel="noopener noreferrer"
        class="flex flex-col items-center gap-1 hover:scale-105 transition-transform"
    >
        <div class="text-3xl">{{ icon }}</div>
        <div class="text-xs text-gray-600 dark:text-gray-300">{{ label }}</div>
    </a>
</template>

<script setup>
defineProps({
    icon: String,
    label: String,
    url: String
})
</script>


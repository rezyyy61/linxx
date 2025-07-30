<template>
    <div
        :class="[
      'relative flex items-center justify-center rounded-full font-medium text-white overflow-hidden transition-all duration-200',
      sizeClass,
      hasValidSrc ? 'ring-0' : 'ring-1 ring-gray-300 dark:ring-gray-700 shadow-sm'
    ]"
        :style="avatarStyle"
    >
        <template v-if="src">
            <img
                :src="src"
                :alt="altText"
                class="w-full h-full object-cover"
                draggable="false"
            />
        </template>
        <template v-else>
            <span class="leading-none select-none">{{ fallbackChar }}</span>
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    src: String,
    fallback: String,
    color: {
        type: String,
        default: '#10B981'
    },
    size: {
        type: String,
        default: 'md' // 'sm', 'md', 'lg'
    }
})

const hasValidSrc = computed(() => !!props.src && props.src !== 'null')

const sizeClass = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'w-9 h-9 text-sm'
        case 'lg':
            return 'w-14 h-14 text-xl'
        default:
            return 'w-11 h-11 text-base'
    }
})

const fallbackChar = computed(() => {
    return props.fallback?.charAt(0)?.toUpperCase() || '?'
})

const avatarStyle = computed(() =>
    hasValidSrc.value ? {} : { backgroundColor: props.color }
)

const altText = computed(() => `Avatar of ${props.fallback || 'user'}`)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@500&display=swap');

div {
    font-family: 'Inter', sans-serif;
}
</style>

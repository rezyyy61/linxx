<template>
    <div
        class="media-upload-box"
        :class="{ 'opacity-50 cursor-not-allowed': disabled }"
        :title="disabled ? $t('post.only_one_media_type') : ''"
    >
        <div class="upload-content">
            <component :is="resolvedIcon" class="w-8 h-8" :class="iconColor" />
            <p>{{ $t(labelKey) }}</p>
        </div>

        <input
            type="file"
            class="media-input"
            :accept="accept"
            :multiple="multiple"
            :disabled="disabled"
            @change="$emit('change', $event)"
        />
    </div>
</template>

<script setup>
import { h, defineComponent, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import {
    File as FileIcon,
    Image as ImageIcon,
    Video as VideoIcon
} from 'lucide-vue-next'

/**
 * Props
 */
const props = defineProps({
    labelKey: { type: String, required: true },
    accept: { type: String, default: '*' },
    disabled: Boolean,
    multiple: Boolean,
    icon: {
        type: String,
        default: 'file' // image | video | audio | file
    }
})

const { t } = useI18n()

/**
 * Local SVG component for audio icon
 */
const AudioSvgIcon = defineComponent({
    name: 'AudioSvgIcon',
    render() {
        return h(
            'svg',
            {
                class: 'w-8 h-8 text-orange-500',
                fill: 'none',
                stroke: 'currentColor',
                strokeWidth: '1.5',
                viewBox: '0 0 24 24',
                xmlns: 'http://www.w3.org/2000/svg'
            },
            [
                h('path', {
                    strokeLinecap: 'round',
                    strokeLinejoin: 'round',
                    d: 'M9 19V6l12-2v13M9 10l12-2'
                })
            ]
        )
    }
})

/**
 * Map of icon types to actual sections
 */
const iconMap = {
    image: ImageIcon,
    video: VideoIcon,
    audio: AudioSvgIcon,
    file: FileIcon
}

/**
 * Resolved icon component & color class
 */
const resolvedIcon = computed(() => iconMap[props.icon] || FileIcon)

const iconColor = computed(() => {
    return {
        image: 'text-blue-500',
        video: 'text-purple-500',
        audio: 'text-orange-500',
        file: 'text-green-500'
    }[props.icon] || 'text-gray-500'
})
</script>

<style scoped>
.media-upload-box {
    @apply relative border border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 bg-gray-50 dark:bg-gray-800;
}
.upload-content {
    @apply flex flex-col items-center justify-center text-center space-y-3 text-sm text-gray-700 dark:text-gray-300;
}
.media-input {
    @apply absolute inset-0 opacity-0 cursor-pointer;
}
</style>

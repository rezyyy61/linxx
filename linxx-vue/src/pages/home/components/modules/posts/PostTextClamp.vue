<template>
    <p
        v-if="hasText"
        class="text-base leading-relaxed tracking-normal whitespace-pre-line text-gray-700 dark:text-gray-200"
        :dir="dir"
        :class="dir === 'rtl' ? 'text-right' : 'text-left'"
    >

        <span v-if="isExpanded || !isTruncatable">{{ text }}</span>
        <span v-else>{{ truncatedText }}</span>


        <button
            v-if="isTruncatable"
            type="button"
            @click="toggle"
            class="ml-1 text-sm text-blue-500 dark:text-blue-400 hover:underline inline whitespace-nowrap"
        >
            {{ isExpanded ? $t('post.showLess') : $t('post.showMore') }}
        </button>
    </p>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    text: { type: String, default: '' },
    truncate: { type: Boolean, default: true },
    limit: { type: Number, default: null },
    limits: {
        type: Object,
        default: () => ({
            sm: 100,
            md: 300,
            lg: 400
        })
    },
    expanded: { type: Boolean, default: undefined }
})

const emit = defineEmits(['update:expanded', 'toggle'])


const isControlled = computed(() => props.expanded !== undefined)
const internalExpanded = ref(false)
const isExpanded = computed(() => (isControlled.value ? props.expanded : internalExpanded.value))
function setExpanded(v) {
    if (!isControlled.value) internalExpanded.value = v
    emit('update:expanded', v)
    emit('toggle', v)
}
function toggle() {
    setExpanded(!isExpanded.value)
}

/* جهت متن */
const dir = computed(() => (/[\u0600-\u06FF]/.test(props.text) ? 'rtl' : 'ltr'))


const responsiveLimit = ref(props.limits.lg)

function recomputeLimit() {
    const w = window.innerWidth
    const { sm, md, lg } = props.limits
    responsiveLimit.value = w < 640 ? sm : w < 1024 ? md : lg
}

onMounted(() => {
    recomputeLimit()
    window.addEventListener('resize', recomputeLimit, { passive: true })
})
onBeforeUnmount(() => {
    window.removeEventListener('resize', recomputeLimit)
})

/* limit نهایی */
const currentLimit = computed(() => {
    if (!props.truncate) return Number.MAX_SAFE_INTEGER
    if (props.limit != null) return props.limit
    return responsiveLimit.value
})

/* وضعیت متن */
const hasText = computed(() => !!props.text && props.text.length > 0)
const isTruncatable = computed(() => props.truncate && props.text.length > currentLimit.value)
const truncatedText = computed(() => props.text.slice(0, currentLimit.value) + '...')

/* سینک expanded خارجی */
watch(
    () => props.expanded,
    (v) => {
        if (isControlled.value) {
            // nothing else; display computed above
        }
    }
)
</script>

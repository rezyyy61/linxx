<template>
    <div class="space-y-1">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center gap-2">
            <component v-if="icon" :is="iconComponent" class="w-4 h-4 text-gray-500" />
            {{ label }}
        </label>

        <input
            v-bind="$attrs"
            v-model="model"
            :dir="direction"
            :class="['input transition-all', error ? 'border-red-500 ring-red-400' : '']"
        />

        <transition name="fade">
            <p v-if="error" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400">
                <AlertCircle class="w-4 h-4 flex-shrink-0" />
                {{ error }}
            </p>
        </transition>

    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import * as icons from 'lucide-vue-next'
import { AlertCircle } from 'lucide-vue-next'


const props = defineProps({
    modelValue: [String, Number],
    label: String,
    icon: String,
    error: String,
})

const emit = defineEmits(['update:modelValue'])

const model = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val)
})

const direction = ref('rtl')
watch(model, (val) => {
    const isRTL = /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF]/.test(val)
    direction.value = isRTL ? 'rtl' : 'ltr'
}, { immediate: true })

const iconComponent = computed(() => icons[props.icon] || null)
</script>

<style scoped>
.input {
    @apply w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

</style>

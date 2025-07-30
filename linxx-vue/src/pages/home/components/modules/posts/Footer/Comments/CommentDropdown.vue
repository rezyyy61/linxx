<template>
    <div class="relative dropdown-menu">
        <!-- trigger -->
        <button @click="toggle" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="5"  r="1.5" />
                <circle cx="12" cy="12" r="1.5" />
                <circle cx="12" cy="19" r="1.5" />
            </svg>
        </button>

        <!-- menu -->
        <div
            v-if="open"
            class="absolute top-6 right-0 z-50 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-md shadow-lg w-32 py-1 text-sm"
        >
            <button
                v-if="canEdit"
                class="w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="clickEdit"
            >
                Edit
            </button>

            <button
                v-if="canDelete"
                class="w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="clickDelete"
            >
                Delete
            </button>

            <button
                class="w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="clickReport"
            >
                Report
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    commentUserId: Number,
    postUserId: Number,
    currentUserId: Number,
})

const emit = defineEmits(['edit', 'delete', 'report'])

const open = ref(false)
const toggle = () => (open.value = !open.value)
const close = () => (open.value = false)

const canEdit = computed(() => props.currentUserId === props.commentUserId)
const canDelete = computed(() =>
    !!props.currentUserId &&
    (props.currentUserId === props.commentUserId || props.currentUserId === props.postUserId)
)

function clickEdit () { emit('edit'); close() }
function clickDelete () { emit('delete'); close() }
function clickReport () { emit('report'); close() }

function onClickOutside (e) {
    if (!e.target.closest('.dropdown-menu')) close()
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))
</script>

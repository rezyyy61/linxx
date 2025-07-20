<template>
    <div class="flex items-start gap-3 mt-4">
        <img
            v-if="isLoggedIn"
            :src="avatar"
            class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-300 dark:ring-gray-600"
        />

        <div
            class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-3 transition-all border border-transparent"
            :class="{ 'focus-within:border-blue-400 focus-within:shadow-sm': isLoggedIn }"
        >
            <div v-if="isLoggedIn">
                <div
                    v-if="replyingTo?.name"
                    class="flex items-center justify-between bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-3 py-1.5 rounded-md mb-2"
                >
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3" />
                        </svg>
                        <span>Replying to <strong>@{{ replyingTo.name }}</strong></span>
                    </div>
                    <button
                        @click="clearReply"
                        class="text-lg text-gray-500 hover:text-red-500 transition"
                        type="button"
                        title="Cancel reply"
                    >
                        ✕
                    </button>
                </div>


                <textarea
                    v-model="text"
                    :dir="textDirection"
                    ref="textareaRef"
                    rows="1"
                    placeholder="Write a comment..."
                    class="w-full resize-none bg-transparent outline-none text-lg leading-6 text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                    @input="autoGrow"
                    @keydown.enter.exact.prevent="send"
                />

                <div class="flex justify-between items-center mt-3">
                    <div class="flex gap-3 items-center text-gray-400 dark:text-gray-300 text-lg">
                        <button @click="insertEmoji('🙏')" title="Appreciation" class="hover:text-blue-500 transition">🙏</button>
                        <button @click="insertEmoji('👍')" title="Agree" class="hover:text-blue-500 transition">👍</button>
                        <button @click="insertEmoji('👎')" title="Disagree" class="hover:text-blue-500 transition">👎</button>
                    </div>
                    <button
                        @click="send"
                        :disabled="!text.trim()"
                        class="text-sm font-medium px-4 py-1.5 rounded-lg transition text-white bg-blue-500 hover:bg-blue-600 disabled:opacity-50"
                    >
                        Send
                    </button>
                </div>
            </div>

            <div v-else class="text-center text-gray-500 dark:text-gray-300 text-lg py-1.5">
                {{ $t('post.loginPrompt') }}
                <button
                    class="text-blue-500 hover:underline font-medium"
                    @click="goToLogin"
                >
                    {{ $t('post.loginButton') }}
                </button>
                {{ $t('post.loginEnd') }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const props = defineProps({
    postId: [Number, String],
    modelValue: String,
    replyingTo: Object
})

const emit = defineEmits(['update:modelValue', 'send', 'cancel-reply'])

const auth = useAuthStore()
const router = useRouter()

const isLoggedIn = computed(() => !!auth.user)
const text = ref(props.modelValue || '')
const textareaRef = ref(null)

const avatar = computed(() =>
    auth.user?.avatar || `https://i.pravatar.cc/40?u=${auth.user?.id || 'guest'}`
)

const textDirection = computed(() =>
    /[\u0600-\u06FF]/.test(text.value) ? 'rtl' : 'ltr'
)

watch(() => props.modelValue, v => {
    if (v !== text.value) text.value = v
})

watch(() => props.replyingTo, newVal => {
    if (newVal?.name) {
        text.value = `@${newVal.name} `
    } else {
        text.value = ''
    }
    nextTick(autoGrow)
})


function autoGrow() {
    const el = textareaRef.value
    if (el) {
        el.style.height = 'auto'
        el.style.height = el.scrollHeight + 'px'
    }
}

function insertEmoji(emoji) {
    text.value += (text.value.length ? ' ' : '') + emoji
    emit('update:modelValue', text.value)
    nextTick(autoGrow)
}

function send() {
    const val = text.value.trim()
    if (!val) return

    emit('send', {
        content: val,
        parentId: props.replyingTo?.id || null
    })

    emit('update:modelValue', '')
    text.value = ''
    nextTick(autoGrow)
}

function clearReply() {
    emit('cancel-reply')
}

function goToLogin() {
    router.push({ name: 'login' })
}
</script>


<template>
    <div v-if="isLoggedIn"
        class="relative w-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-2xl px-4 py-3 border border-gray-200 dark:border-gray-600 shadow-md flex gap-3 items-start">

        <!-- Avatar -->
        <UserAvatar
            v-if="isLoggedIn"
            :src="avatar"
            :fallback="avatarName"
            :color="avatarColor"
            size="md"
            class="mt-1 shrink-0"
        />

        <!-- Input + Actions -->
        <div class="flex-1 space-y-2">

            <!-- Reply Banner -->
            <div
                v-if="replyingTo?.name && !isEditing"
                class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/40 text-blue-800 dark:text-blue-200 px-3 py-1.5 rounded-md text-sm"
            >
                <div class="flex items-center gap-2 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3"/>
                    </svg>
                    Replying to <strong>@{{ replyingTo.name }}</strong>
                </div>
                <button @click="cancel" class="text-gray-400 hover:text-red-500 text-base">✕</button>
            </div>

            <!-- Textarea -->
            <textarea
                ref="textareaRef"
                v-model="text"
                :dir="textDirection"
                rows="1"
                placeholder="Write a comment..."
                class="w-full bg-transparent resize-none outline-none text-[15px] leading-6 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500
           max-h-40 overflow-y-auto"
                @input="autoGrow"
                @keydown.enter.exact.prevent="send"
            ></textarea>

            <p v-if="getError && getError('content')" class="text-sm text-red-500 mt-1">
                {{ getError('content') }}
            </p>


            <!-- Actions -->
            <div class="flex justify-between items-center">
                <div class="flex gap-2 text-xl text-gray-400 dark:text-gray-300">
                    <button
                        v-for="emoji in emojis"
                        :key="emoji"
                        @click="insertEmoji(emoji)"
                        class="hover:text-blue-500"
                    >{{ emoji }}</button>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="isEditing || replyingTo"
                        @click="cancel"
                        class="text-sm text-gray-500 hover:text-red-500"
                    >Cancel</button>

                    <button
                        @click="send"
                        :disabled="!text.trim()"
                        class="px-4 py-1.5 text-sm font-semibold rounded-lg text-white transition disabled:opacity-50"
                        :class="isEditing ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-blue-500 hover:bg-blue-600'"
                    >
                        {{ isEditing ? 'Update' : 'Send' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Prompt -->
    <div v-else
         class="w-full bg-white/100 dark:bg-gray-800/70 px-4 py-3 text-center rounded-2xl border border-gray-400 dark:border-gray-700 text-gray-500 dark:text-gray-300 shadow-sm"
    >
        {{ $t('post.loginPrompt') }}
        <button @click="goToLogin" class="text-blue-500 hover:underline font-medium">
            {{ $t('post.loginButton') }}
        </button>
        {{ $t('post.loginEnd') }}
    </div>
</template>


<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import UserAvatar from '@/pages/auth/UserAvatar.vue'

const props = defineProps({
    postId: [Number, String],
    modelValue: String,
    replyingTo: Object,
    isEditing: Boolean,
    errors: Object,
    getError: Function
})

const emit = defineEmits(['update:modelValue', 'send', 'cancel-reply', 'cancel-edit'])

const auth = useAuthStore()
const router = useRouter()

const isLoggedIn = computed(() => !!auth.user)

const text = ref(props.modelValue || '')
const textareaRef = ref(null)

const emojis = ['🙏', '👍', '👎']

const avatar = computed(() => auth.user?.data?.political_profile?.logo_url || null)
const avatarColor = computed(() => auth.user?.data?.political_profile?.avatar_color || '#10B981')
const avatarName = computed(() =>
    auth.user?.data?.political_profile?.group_name || auth.user?.data?.name || 'U'
)

const textDirection = computed(() =>
    /[\u0600-\u06FF]/.test(text.value) ? 'rtl' : 'ltr'
)

watch(() => props.modelValue, (val) => {
    if (val !== text.value) text.value = val
})

watch(() => text.value, (val) => {
    emit('update:modelValue', val)
})

watch(() => props.replyingTo, (val) => {
    if (val?.name && !props.isEditing && !text.value.startsWith(`@${val.name}`)) {
        text.value = `@${val.name} `
        nextTick(autoGrow)
    }
})

function autoGrow () {
    const el = textareaRef.value
    if (el) {
        el.style.height = 'auto'

        // محدودیت به max-height
        const maxHeight = 140
        const newHeight = Math.min(el.scrollHeight, maxHeight)

        el.style.height = `${newHeight}px`
        el.style.overflowY = el.scrollHeight > maxHeight ? 'auto' : 'hidden'
    }
}


function insertEmoji(emoji) {
    text.value += (text.value.length ? ' ' : '') + emoji
    nextTick(autoGrow)
}

async function send() {
    const val = text.value.trim()
    if (!val) return

    emit('send', {
        content: val,
        parentId: props.replyingTo?.id || null
    })

    text.value = ''
    await nextTick()
    autoGrow()
}

function cancel() {
    props.isEditing ? emit('cancel-edit') : emit('cancel-reply')
}

function goToLogin() {
    router.push({ name: 'login' })
}
</script>

<template>
    <div
        class="bg-white/90 dark:bg-gray-800/70 border border-gray-200 dark:border-gray-600 rounded-2xl shadow-sm dark:shadow-md transition-all duration-300 overflow-hidden"
    >

    <PostHeader
            :user="post.user"
            :createdAt="post.created_at"
            :isOwner="currentUserId && post.user.id === currentUserId"
        />

        <div class="p-4 space-y-4">
            <div v-if="post.text">
                <p
                    class="text-base leading-relaxed tracking-normal whitespace-pre-line"
                    :class="[
            textDirection === 'rtl' ? 'text-right' : 'text-left',
            'text-gray-700 dark:text-gray-200'
          ]"
                    :dir="textDirection"
                >
                    <span v-if="!shouldTruncate">{{ post.text }}</span>
                    <span v-else>
            {{ showFull ? post.text : post.text.slice(0, limit) + '...' }}
            <button
                @click="showFull = !showFull"
                class="ml-1 text-sm text-blue-500 dark:text-blue-400 hover:underline inline"
            >
              {{ showFull ? $t('post.showLess') : $t('post.showMore') }}
            </button>
          </span>
                </p>
            </div>

            <PostMedia v-if="hasMedia" :post="post" />
        </div>

        <PostFooter />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import PostHeader from './PostHeader.vue'
import PostMedia from './PostMedia.vue'
import PostFooter from './PostFooter.vue'

const { t: $t } = useI18n()
const authStore = useAuthStore()

const props = defineProps({
    post: Object
})

const currentUserId = computed(() => authStore.user?.id ?? null)
const showFull = ref(false)
const limit = 400

const shouldTruncate = computed(() => props.post.text && props.post.text.length > limit)

const textDirection = computed(() => {
    const rtlChars = /[\u0600-\u06FF]/
    return rtlChars.test(props.post.text) ? 'rtl' : 'ltr'
})

const hasMedia = computed(() => props.post.media && props.post.media.length > 0)
</script>

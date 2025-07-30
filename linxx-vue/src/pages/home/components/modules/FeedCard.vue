<template>
    <div
        class="mx-auto w-full space-y-6"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <div class="space-y-6 max-w-[700px] mx-auto">
            <div v-for="post in posts" :key="post.id">
                <PostCard :post="post" />
            </div>
        </div>
        <div v-if="loading" class="text-center text-sm text-gray-500 dark:text-gray-400 py-6">{{ $t('post.loading') }}...</div>
        <div v-if="!loading && allLoaded" class="text-center text-xs text-gray-400 dark:text-gray-500 py-6">{{ $t('post.noMorePosts') }}</div>
        <div v-if="error" class="text-center text-red-500 dark:text-red-400 py-4">{{ $t('post.somethingWentWrong') }}</div>
        <div ref="sentinel" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'
import PostCard from "@/pages/home/components/modules/posts/PostCard.vue";

const postStore = usePostStore()
const authStore = useAuthStore()

const posts = computed(() => postStore.posts)
const loading = computed(() => postStore.loading)
const allLoaded = computed(() => postStore.allLoaded)
const error = computed(() => postStore.error)

const sentinel = ref(null)
let observer
let unsubRealtime
let unsubUser

function loadMore() {
    if (!loading.value && !allLoaded.value) postStore.fetchMorePosts()
}

onMounted(async () => {
    postStore.resetPagination()
    await postStore.fetchMorePosts()
    observer = new IntersectionObserver(([e]) => {
        if (e.isIntersecting) loadMore()
    }, { rootMargin: '300px' })
    await nextTick()
    if (sentinel.value) observer.observe(sentinel.value)
    unsubRealtime = postStore.subscribeRealtime?.()
    if (authStore.user?.id) unsubUser = postStore.subscribeUserChannel?.(authStore.user.id)
})

onBeforeUnmount(() => {
    observer?.disconnect()
    unsubRealtime?.()
    unsubUser?.()
})
</script>

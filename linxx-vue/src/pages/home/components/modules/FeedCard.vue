<template>
    <div
        class="w-full mx-auto mt-10 space-y-6 px-8"
        :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'"
    >
        <PostCard
            v-for="post in posts"
            :key="post.id"
            :post="post"
        />
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import PostCard from '@/pages/home/components/modules/posts/PostCard.vue'
import { usePostStore } from '@/stores/post'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const postStore = usePostStore()

onMounted(async () => {
    await postStore.fetchPosts()
    postStore.subscribeRealtime()
    if (authStore.user?.id) {
        postStore.subscribeUserChannel(authStore.user.id)
    }
})

const posts = computed(() => postStore.posts)
</script>

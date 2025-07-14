<template>
    <div class="w-full mx-auto mt-10 space-y-6"
         :dir="$i18n.locale === 'fa' ? 'rtl' : 'ltr'">
        <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>
</template>

<script setup>
import {computed, onMounted} from 'vue'
import PostCard from "@/pages/home/components/modules/posts/PostCard.vue";
import {usePostStore} from "@/stores/post";

const postStore = usePostStore()

onMounted(async () => {
    await postStore.fetchPosts()
    console.log('posts after fetch:', postStore.posts)
})

const posts = computed(() => postStore.posts)

</script>

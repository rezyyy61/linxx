<template>
    <div class="p-4 sm:p-6 rounded-2xl shadow-md bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
            {{ $t('politicalProfile.profileMenu.posts') }}
        </h2>

        <div class="space-y-4">
            <PostCard
                v-for="post in store.posts"
                :key="post.id"
                :post="post"
            />
            <div v-if="!store.posts.length" class="text-sm text-gray-500 dark:text-gray-400">
                {{ $t('politicalProfile.profileMenu.noPosts') }}
            </div>
        </div>
    </div>
</template>

<script setup>
import PostCard from '@/pages/home/components/modules/posts/PostCard.vue'
import { usePublicProfileStore } from '@/stores/publicProfile'

const store = usePublicProfileStore()
</script>

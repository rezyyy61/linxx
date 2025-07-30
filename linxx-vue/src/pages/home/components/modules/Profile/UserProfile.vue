<template>
    <div v-if="store.loading" class="text-sm text-gray-400 dark:text-gray-500" dir="ltr">
        {{ $t('politicalProfile.list.loading') }}
    </div>

    <div v-else-if="store.profile" class="grid gap-4 w-full" ref="scrollContainer">
        <UserHeader :user="store.profile" />
        <UserProfileMenu />

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Right/Main Column -->
            <div class="lg:col-span-8">
                <ProfileMainColumn :profile="store.profile" :posts="store.posts" />

                <!-- Loading spinner -->
                <div v-if="store.postsLoading" class="text-center text-gray-400 dark:text-gray-500 py-4">
                    {{ $t('loading') }}...
                </div>
            </div>

            <!-- Left/Sidebar Column -->
            <div class="lg:col-span-4">
                <ProfileSidebar :user="store.profile" />
            </div>
        </div>
    </div>

    <div v-else class="text-red-500">
        {{ $t('politicalProfile.list.error') }}
    </div>
</template>

<script setup>
import { watch, onMounted, onBeforeUnmount, ref } from 'vue'
import { useRoute } from 'vue-router'
import UserHeader from '@/pages/home/components/modules/Profile/UserHeader.vue'
import UserProfileMenu from '@/pages/home/components/modules/Profile/UserProfileMenu.vue'
import ProfileMainColumn from '@/pages/home/components/modules/Profile/ProfileMainColumn.vue'
import ProfileSidebar from '@/pages/home/components/modules/Profile/ProfileSidebar.vue'
import { usePublicProfileStore } from '@/stores/publicProfile'

const route = useRoute()
const slug = route.params.slug
const store = usePublicProfileStore()
const scrollContainer = ref(null)

const handleScroll = async () => {
    const threshold = 300 // px to bottom

    const scrollPos = window.innerHeight + window.scrollY
    const totalHeight = document.body.offsetHeight

    if (scrollPos + threshold >= totalHeight) {
        await store.fetchProfilePosts(slug)
    }
}

onMounted(async () => {
    await store.fetchPublicProfile(slug)
    await store.fetchProfilePosts(slug)
    window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})

watch(
    () => route.params.slug,
    async (newSlug) => {
        if (newSlug) {
            store.resetProfilePosts()
            await store.fetchPublicProfile(newSlug)
            await store.fetchProfilePosts(newSlug)
        }
    }
)
</script>

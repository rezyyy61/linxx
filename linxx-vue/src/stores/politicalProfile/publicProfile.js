import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from '@/lib/axios'
import { htmlToRichJson } from '@/utils/htmlToRichJson'

export const usePublicProfileStore = defineStore('publicProfile', () => {
    // State
    const profile = ref(null)
    const posts = ref([])

    const loading = ref(false)
    const postsLoading = ref(false)
    const error = ref(null)

    const currentPage = ref(1)
    const lastPage = ref(null)
    const allLoaded = ref(false)

    // Normalizer for post object
    const normalize = (post) => ({
        ...post,
        richText: Array.isArray(post.richText)
            ? post.richText
            : htmlToRichJson(post.text || '')
    })

    // Fetch public profile by slug
    async function fetchPublicProfile(slug) {
        loading.value = true
        error.value = null

        try {
            const res = await axios.get(`/api/profile/slug/${slug}`)
            profile.value = res.data.data
        } catch (err) {
            console.error('❌ Failed to fetch public profile:', err)
            error.value = err?.response?.data?.message || 'Failed to fetch public profile'
            profile.value = null
        } finally {
            loading.value = false
        }
    }

    // Fetch posts for the profile by slug
    async function fetchProfilePosts(slug, perPage = 10) {
        if (postsLoading.value || allLoaded.value) return

        postsLoading.value = true
        error.value = null

        try {
            const res = await axios.get(`/api/profile/slug/${slug}/posts?page=${currentPage.value}&per_page=${perPage}`)
            const { data, meta } = res.data

            posts.value.push(...data.map(normalize))
            currentPage.value++
            lastPage.value = meta.last_page

            if (currentPage.value > lastPage.value) {
                allLoaded.value = true
            }
        } catch (err) {
            console.error('❌ Failed to fetch posts:', err)
            error.value = err?.response?.data?.message || 'Failed to fetch posts'
        } finally {
            postsLoading.value = false
        }
    }

    // Reset profile only
    function resetProfile() {
        profile.value = null
        error.value = null
    }

    // Reset posts and pagination
    function resetProfilePosts() {
        posts.value = []
        currentPage.value = 1
        lastPage.value = null
        allLoaded.value = false
    }

    return {
        // State
        profile,
        posts,
        loading,
        postsLoading,
        error,
        currentPage,
        lastPage,
        allLoaded,

        // Actions
        fetchPublicProfile,
        fetchProfilePosts,
        resetProfile,
        resetProfilePosts
    }
})

// src/stores/publicProfile.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import publicAxios from '@/lib/publicAxios'

export const usePublicProfileStore = defineStore('publicProfile', () => {
    const profile = ref(null)
    const posts = ref([])

    const loading = ref(false)
    const error = ref(null)

    const postsLoading = ref(false)
    const currentPage = ref(1)
    const lastPage = ref(null)
    const allLoaded = ref(false)

    async function fetchPublicProfile(slug) {
        loading.value = true
        error.value = null

        try {
            const res = await publicAxios.get(`/api/political-profiles/user/${slug}`)
            profile.value = res.data.data
        } catch (err) {
            error.value = err
            profile.value = null
            console.error('❌ خطا در دریافت پروفایل عمومی:', err)
        } finally {
            loading.value = false
        }
    }

    async function fetchProfilePosts(slug) {
        if (postsLoading.value || allLoaded.value) return

        postsLoading.value = true

        try {
            const res = await publicAxios.get(`/api/political-profiles/user/${slug}/posts?page=${currentPage.value}`)
            const { data, meta } = res.data

            posts.value.push(...data)
            currentPage.value++
            lastPage.value = meta.last_page

            if (currentPage.value > lastPage.value) {
                allLoaded.value = true
            }
        } catch (err) {
            console.error('❌ خطا در دریافت پست‌های پروفایل:', err)
        } finally {
            postsLoading.value = false
        }
    }

    function resetProfilePosts() {
        posts.value = []
        currentPage.value = 1
        lastPage.value = null
        allLoaded.value = false
    }

    return {
        profile,
        posts,
        loading,
        error,
        postsLoading,
        fetchPublicProfile,
        fetchProfilePosts,
        resetProfilePosts,
        currentPage,
        lastPage,
        allLoaded
    }
})

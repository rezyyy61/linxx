import { defineStore } from 'pinia'
import axios from '@/lib/axios'
import { ref } from 'vue'

export const usePostStore = defineStore('post', () => {
    // State
    const postText = ref('')
    const images = ref([])
    const videos = ref([])
    const audio = ref(null)
    const files = ref([])
    async function submitPost() {
        try {
            const formData = new FormData()
            formData.append('text', postText.value)

            images.value.forEach((item, i) => {
                formData.append(`images[${i}]`, item.file)
            })

            videos.value.forEach((item, i) => {
                formData.append(`videos[${i}]`, item.file)
            })

            if (audio.value) {
                formData.append('audio', audio.value.file)
            }

            files.value.forEach((file, i) => {
                formData.append(`files[${i}]`, file)
            })

            const response = await axios.post('/api/posts', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })

            reset()
            return response.data
        } catch (err) {
            console.error('Submit post error:', err)
            throw err
        }
    }

    function reset() {
        postText.value = ''
        images.value = []
        videos.value = []
        audio.value = null
        files.value = []
    }

    return {
        postText,
        images,
        videos,
        audio,
        files,
        submitPost,
        reset
    }
})

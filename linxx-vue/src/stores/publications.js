import { defineStore } from 'pinia'
import axios from '@/lib/axios'

export const usePublicationStore = defineStore('publication', {
    state: () => ({
        publications: [],
        loading: false,
    }),

    getters: {

        filtered: (state) => (keyword) => {
            const lower = keyword.toLowerCase()
            return state.publications.filter(p =>
                (p.title + ' ' + p.issue).toLowerCase().includes(lower)
            )
        }
    },

    actions: {

        async fetchPublications() {
            this.loading = true
            try {
                const { data } = await axios.get('/api/publications')
                this.publications = data.data
            } catch (error) {
                console.error('خطا در دریافت نشریات:', error)
            } finally {
                this.loading = false
            }
        },

        async fetchPublicationsByParty(partyId) {
            this.loading = true
            try {
                const { data } = await axios.get(`/api/parties/${partyId}/publications`)
                this.publications = data
            } catch (error) {
                console.error('خطا در دریافت نشریات حزب:', error)
            } finally {
                this.loading = false
            }
        },

        async addPublication(formData) {
            try {
                await axios.post('/api/publications', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                await this.fetchPublications()
            } catch (error) {
                console.error('خطا در بارگذاری نشریه:', error)
                throw error
            }
        },

        async deletePublication(id) {
            try {
                await axios.delete(`/api/publications/${id}`)
                this.publications = this.publications.filter(p => p.id !== id)
            } catch (error) {
                console.error('خطا در حذف نشریه:', error)
                throw error
            }
        },

        async getSuggestedDescription(file) {
            const formData = new FormData()
            formData.append('file', file)

            try {
                const { data } = await axios.post('/api/publications/suggest-description', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })

                return data.description?.trim() || ''
            } catch (error) {
                console.error('خطا در گرفتن توضیح پیشنهادی:', error)
                throw error
            }
        }
    }
})

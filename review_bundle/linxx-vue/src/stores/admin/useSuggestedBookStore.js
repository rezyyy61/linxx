import { defineStore } from 'pinia'
import axios from '@/lib/axiosAdmin'

export const useSuggestedBookStore = defineStore('suggestedBookStore', {
    state: () => ({
        error: null,
        suggestedBooks: [],
        suggestedCount: 0
    }),

    actions: {
        async fetchSuggestedBooks(status = 'pending') {
            this.error = null
            try {
                const res = await axios.get(`/admin/suggested-books`, { params: { status } })
                this.suggestedBooks = Array.isArray(res.data) ? res.data : res.data.data
                this.suggestedCount = this.suggestedBooks.length
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to fetch suggestions'
                throw err
            }
        },

        async updateSuggestedBook(id, payload) {
            this.error = null
            try {
                const res = await axios.put(`/admin/suggested-books/${id}`, payload)
                this.suggestedBooks = this.suggestedBooks.map(b => (b.id === id ? res.data : b))
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to update suggestion'
                throw err
            }
        },

        async approveSuggestedBook(id, extra = {}) {
            this.error = null
            try {
                await axios.post(`/admin/suggested-books/${id}/approve`, extra)
                this.suggestedBooks = this.suggestedBooks.filter(b => b.id !== id)
                this.suggestedCount = this.suggestedBooks.length
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to approve suggestion'
                throw err
            }
        },

        async deleteSuggestedBook(id) {
            this.error = null
            try {
                await axios.delete(`/admin/suggested-books/${id}`)
                this.suggestedBooks = this.suggestedBooks.filter(b => b.id !== id)
                this.suggestedCount = this.suggestedBooks.length
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to delete suggestion'
                throw err
            }
        }
    }
})

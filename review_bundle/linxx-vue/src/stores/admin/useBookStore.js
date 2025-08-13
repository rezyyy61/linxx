import { defineStore } from 'pinia'
import axios from '@/lib/axiosAdmin'

export const useBookStore = defineStore('bookStore', {
    state: () => ({
        error: null,
    }),

    actions: {
        async addBook(formData) {
            this.error = null
            try {
                const res = await axios.post('/admin/books', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to add book'
                throw err
            }
        },

        async fetchCategories() {
            this.error = null
            try {
                const res = await axios.get('/admin/book-categories')
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to fetch categories'
                throw err
            }
        },

        async fetchBooks() {
            this.error = null
            try {
                const res = await axios.get('/admin/books')
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to fetch books'
                throw err
            }
        },

        async updateBook(id, formData) {
            this.error = null
            try {
                const res = await axios.post(`/admin/books/${id}?_method=PUT`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to update book'
                throw err
            }
        },

        async deleteBook(id) {
            this.error = null
            try {
                await axios.delete(`/admin/books/${id}`)
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to delete book'
                throw err
            }
        },

        async getBook(id) {
            this.error = null
            try {
                const res = await axios.get(`/admin/books/${id}`)
                return res.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to get book'
                throw err
            }
        },
    }
})

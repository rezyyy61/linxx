// src/lib/axiosAdmin.js
import axios from 'axios'

function getCookie (name) {
    const value = `; ${document.cookie}`
    const parts = value.split(`; ${name}=`)
    if (parts.length === 2) return parts.pop().split(';').shift()
}

const instance = axios.create({
    baseURL: 'http://192.168.1.193:8080',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})

// 🛡️ Interceptor for CSRF token (for Laravel Sanctum)
instance.interceptors.request.use(config => {
    const xsrfToken = getCookie('XSRF-TOKEN')
    if (xsrfToken) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken)
    }

    const adminToken = localStorage.getItem('admin_token')
    if (adminToken) {
        config.headers.Authorization = `Bearer ${adminToken}`
    }

    return config
}, error => {
    return Promise.reject(error)
})

// 📦 optional: handle 401 globally
instance.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            console.warn('Unauthorized access — maybe token expired?')
            // you can redirect to login or clear token here
        }
        return Promise.reject(error)
    }
)

export default instance

// src/stores/admin/users/users.api.js
import axios from '@/lib/axiosAdmin'

export function listUsersApi(params = {}) {
    return axios.get('/admin/users', { params }).then(r => r.data)
}

export function showUserApi(id) {
    return axios.get(`/admin/users/${id}`).then(r => r.data)
}

export function updateUserApi(id, payload) {
    return axios.put(`/admin/users/${id}`, payload).then(r => r.data)
}

export function deleteUserApi(id) {
    return axios.delete(`/admin/users/${id}`).then(r => r.data)
}

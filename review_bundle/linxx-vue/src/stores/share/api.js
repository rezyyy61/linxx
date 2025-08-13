// src/stores/shares/api.js
import axios from '@/lib/axios'

export async function createShare(payload) {
    const { data } = await axios.post('api/shares', payload)
    return data
}

export async function getShare(slug) {
    const { data } = await axios.get(`api/shares/${encodeURIComponent(slug)}`)
    return data
}

export async function revokeShare(id) {
    const { data } = await axios.post(`api/shares/${id}/revoke`)
    return data
}

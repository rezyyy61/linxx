import axios from '@/lib/axios'

export async function listPublicEventsApi(params = {}) {
    const p = { ...params }
    if (p.upcoming !== undefined) p.upcoming = p.upcoming ? 1 : 0
    const { data } = await axios.get('/api/public/events', {
        params: p,
        headers: { Accept: 'application/json' }
    })
    return data
}

export async function showPublicEventApi(idOrSlug) {
    const { data } = await axios.get(`/api/public/events/${idOrSlug}`, {
        headers: { Accept: 'application/json' }
    })
    return data.data ?? data
}

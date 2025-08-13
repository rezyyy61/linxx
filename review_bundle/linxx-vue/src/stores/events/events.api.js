import axios from '@/lib/axios'

function toFormData(obj = {}, files = {}) {
    const fd = new FormData()
    Object.entries(obj).forEach(([k, v]) => {
        if (v === undefined || v === null || v === '') return
        if (Array.isArray(v)) v.forEach(x => fd.append(`${k}[]`, x))
        else fd.append(k, v)
    })
    if (files.cover instanceof File) fd.append('cover', files.cover)
    if (files.attachment instanceof File) fd.append('attachment', files.attachment)
    return fd
}

export async function listEventsApi(q = {}) {
    const params = {
        page: q.page ?? 1,
        per_page: q.per_page ?? 15,
        ...(q.creator_id ? { creator_id: q.creator_id } : {}),
        ...(q.public_only !== undefined ? { public_only: q.public_only } : { public_only: true }),
        ...(q.upcoming !== undefined ? { upcoming: q.upcoming } : { upcoming: true }),
    }
    const { data } = await axios.get('api/events', { params, headers: { Accept: 'application/json' } })
    return data
}

export async function showEventApi(id) {
    const { data } = await axios.get(`api/events/${id}`, { headers: { Accept: 'application/json' } })
    return data.data ?? data
}

export async function createEventApi(payload = {}, files = {}) {
    const body = toFormData(payload, files)
    const { data } = await axios.post('api/events', body, { headers: { Accept: 'application/json' } })
    return data.data ?? data
}

export async function updateEventApi(id, payload = {}, files = {}) {
    const body = toFormData(payload, files)
    const { data } = await axios.post(`api/events/${id}?_method=PUT`, body, { headers: { Accept: 'application/json' } })
    return data.data ?? data
}
export async function updateEventStatusApi(id, payload = {}) {
    const { data } = await axios.post(
        `api/events/${id}/status?_method=PATCH`,
        payload,
        { headers: { Accept: 'application/json' } }
    )
    return data.data ?? data
}

export async function deleteEventApi(id) {
    await axios.delete(`api/events/${id}`, { headers: { Accept: 'application/json' } })
}

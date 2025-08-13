// src/features/events/rsvp.api.js
import axios from '@/lib/axios'

function normalizeData(res) {
    return res?.data?.data ?? res?.data ?? res
}

function toErr(e) {
    const r = e?.response
    const msg = r?.data?.message || e?.message || 'Request failed'
    const err = new Error(msg)
    err.status = r?.status
    err.data = r?.data
    return err
}

export async function rsvpApi(eventId, status) {
    try {
        const res = await axios.post(`/api/events/${eventId}/rsvp`, { status }, {
            headers: { Accept: 'application/json' }
        })
        return normalizeData(res) // { user, status, ... }
    } catch (e) {
        throw toErr(e)
    }
}

export async function cancelRsvpApi(eventId) {
    try {
        await axios.delete(`/api/events/${eventId}/rsvp`, { headers: { Accept: 'application/json' } })
        return true
    } catch (e) {
        throw toErr(e)
    }
}

export async function listAttendeesApi(eventId) {
    try {
        const res = await axios.get(`/api/events/${eventId}/attendees`, { headers: { Accept: 'application/json' } })
        return normalizeData(res) // Array<EventRsvpResource>
    } catch (e) {
        throw toErr(e)
    }
}

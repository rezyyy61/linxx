// src/stores/shares/utils.js
export function formatDate(iso) {
    if (!iso) return '-'
    const d = new Date(iso)
    return d.toLocaleDateString('fa-IR', { year: 'numeric', month: 'short', day: 'numeric' })
}

export function canRevoke(share, currentUserId) {
    return share?.revoked_at == null && Number(share?.owner_id || share?.user_id) === Number(currentUserId)
}

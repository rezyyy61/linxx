import axios from '@/lib/axiosAdmin'

export function listTypeRequestsApi(params) {
  return axios.get('/admin/user-type-requests', { params }).then(r => r.data)
}
export function approveTypeRequestApi(userId) {
  return axios.post(`/admin/users/${userId}/type-requests/approve`).then(r => r.data)
}
export function rejectTypeRequestApi(userId) {
  return axios.post(`/admin/users/${userId}/type-requests/reject`).then(r => r.data)
}

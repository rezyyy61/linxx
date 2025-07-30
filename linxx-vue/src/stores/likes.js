import axios from '@/lib/axios'

/**
 * Toggle like on a post
 * @param {Number|String} postId
 * @returns {Promise<{ liked: boolean, likes_count: number }>}
 */
export function toggleLike(postId) {
    return axios.post(`/api/posts/${postId}/like`)
        .then(response => response.data)
}

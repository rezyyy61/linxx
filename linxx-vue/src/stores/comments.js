/* eslint-disable camelcase */
import axios from "@/lib/axios"

const COMMENT_API_BASE = "/api/comments"

export const CommentAPI = {
    list(postId, token = null) {
        return axios.get(COMMENT_API_BASE, {
            params: { post_id: postId },
            headers: token ? { Authorization: `Bearer ${token}` } : {}
        })
    },


    create({ post_id, content, parent_id = null }) {
        return axios.post(COMMENT_API_BASE, {
            post_id,
            content,
            parent_id
        });
    },

    update(commentId, content) {
        return axios.put(`${COMMENT_API_BASE}/${commentId}`, {
            content
        });
    },

    delete(commentId) {
        return axios.delete(`${COMMENT_API_BASE}/${commentId}`);
    },

    toggleLike(commentId) {
        return axios.post(`${COMMENT_API_BASE}/${commentId}/like`);
    },

    report(commentId, reason = "") {
        return axios.post(`${COMMENT_API_BASE}/${commentId}/report`, {
            reason
        });
    }
};

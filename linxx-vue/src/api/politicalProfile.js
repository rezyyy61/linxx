import axios from '@/lib/axios'
import publicAxios from "@/lib/publicAxios";

const BASE_URL = '/api/political-profiles'

export async function createPoliticalProfile (formData) {
  return axios.post(BASE_URL, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

export async function updatePoliticalProfile (id, formData) {
  return axios.post(`${BASE_URL}/${id}?_method=PUT`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

export async function getMyPoliticalProfile () {
  return axios.get(`${BASE_URL}/me`)
}

export async function getPoliticalProfile (id) {
  return axios.get(`${BASE_URL}/${id}`)
}

export async function listPoliticalProfiles () {
  return axios.get(BASE_URL)
}

export async function deletePoliticalProfile (id) {
  return axios.delete(`${BASE_URL}/${id}`)
}

export function getMyProfile() {
    return publicAxios.get('api/user/me')
}

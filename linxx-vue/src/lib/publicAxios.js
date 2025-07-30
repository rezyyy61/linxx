import axios from 'axios'

const publicAxios = axios.create({
    baseURL: 'http://192.168.1.193:8080',
    withCredentials: false,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})

export default publicAxios

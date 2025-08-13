import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.VUE_APP_REVERB_APP_KEY,
    wsHost: process.env.VUE_APP_REVERB_HOST || window.location.hostname,
    wsPort: Number(process.env.VUE_APP_REVERB_PORT) || 6001,
    wssPort: Number(process.env.VUE_APP_REVERB_PORT) || 6001,
    forceTLS: process.env.VUE_APP_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    namespace: null,
    authEndpoint: 'http://localhost:8080/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,

        },
        withCredentials: true,
    },
})

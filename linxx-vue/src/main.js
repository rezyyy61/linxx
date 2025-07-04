import { createApp, watchEffect } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'

import './assets/tailwind.css'
import i18n from './i18n'

const app = createApp(App)
app.use(router)
app.use(createPinia())
app.use(i18n)

watchEffect(() => {
    const html = document.documentElement
    html.setAttribute('dir', i18n.global.locale.value === 'fa' ? 'rtl' : 'ltr')
})

app.mount('#app')

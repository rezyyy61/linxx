import { createApp, watchEffect } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import '@/assets/global.css'


import './assets/tailwind.css'
import i18n from './i18n'

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import clickOutside from './directives/click-outside'
import './echo.js'



const app = createApp(App)

app.directive('click-outside', clickOutside)

app.use(router)
app.use(createPinia())
app.use(i18n)

// 🟢 فعال‌سازی Toast با تنظیمات دلخواه
app.use(Toast, {
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  transition: 'Vue-Toastification__bounce',
  maxToasts: 5,
  newestOnTop: true
})

watchEffect(() => {
  const html = document.documentElement
  html.setAttribute('dir', i18n.global.locale.value === 'fa' ? 'rtl' : 'ltr')
})

app.mount('#app')

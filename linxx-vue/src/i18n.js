import { createI18n } from 'vue-i18n'

function loadLocaleMessages () {
  const locales = {
    en: {},
    fa: {}
  }

  // Load English files
  const enFiles = require.context('./locales/en', true, /\.json$/)
  enFiles.keys().forEach((key) => {
    const fileName = key.replace('./', '').replace('.json', '')
    locales.en[fileName] = enFiles(key)
  })

  // Load Persian files
  const faFiles = require.context('./locales/fa', true, /\.json$/)
  faFiles.keys().forEach((key) => {
    const fileName = key.replace('./', '').replace('.json', '')
    locales.fa[fileName] = faFiles(key)
  })

  return locales
}

const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages: loadLocaleMessages()
})

export default i18n

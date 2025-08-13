<template>
  <section class="space-y-20 animate-fade-in-up">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
      <!-- 🌐 Contact Links -->
      <div class="space-y-12">
        <div>
          <h2 class="section-title">
            <Icon icon="mdi:account-multiple-outline" class="text-red-500 text-4xl" />
            Connect with Us
          </h2>

          <div class="grid grid-cols-3 sm:grid-cols-4 gap-6">
            <div
                v-for="(icon, key) in allLinks"
                :key="key"
                :class="[
                'link-box group transition-all duration-300',
                profileLinkExists(key)
                  ? 'link-box-active'
                  : 'link-box-disabled'
              ]"
            >
              <component
                  :is="profileLinkExists(key) ? 'a' : 'div'"
                  :href="profileLinkExists(key) ? getLinkUrl(key) : undefined"
                  target="_blank"
                  class="flex flex-col items-center gap-2"
              >
                <Icon :icon="icon" class="text-4xl group-hover:scale-110 transition" />
                <span class="capitalize text-sm">{{ key }}</span>
              </component>
            </div>
          </div>
        </div>

        <div v-if="customLinks.length">
          <h3 class="section-subtitle">
            <Icon icon="mdi:link-variant" class="text-red-500" />
            Other Links
          </h3>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
            <a
                v-for="(link, index) in customLinks"
                :key="index"
                :href="link.url"
                target="_blank"
                class="link-box link-box-active hover:scale-105 transition"
            >
              <Icon icon="mdi:link-variant" class="text-3xl" />
              <span class="break-all text-center text-sm">{{ link.title || 'Custom Link' }}</span>
            </a>
          </div>
        </div>
      </div>

      <!-- ✉️ Contact Form -->
      <div class="form-card animate-fade-in">
        <h2 class="section-title">
          <Icon icon="mdi:email-fast-outline" class="text-red-500 text-4xl" />
          Send Us a Message
        </h2>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid md:grid-cols-2 gap-6">
            <div class="input-with-icon">
              <Icon icon="mdi:account" class="input-icon" />
              <input v-model="form.name" type="text" placeholder="👤 Your Name" class="fancy-input" required />
            </div>
            <div class="input-with-icon">
              <Icon icon="mdi:email-outline" class="input-icon" />
              <input v-model="form.email" type="email" placeholder="📧 Your Email" class="fancy-input" required />
            </div>
          </div>

          <div class="input-with-icon">
            <Icon icon="mdi:tag-outline" class="input-icon" />
            <input v-model="form.subject" type="text" placeholder="🔖 Subject (optional)" class="fancy-input" />
          </div>

          <div>
            <textarea v-model="form.message" rows="5" placeholder="📗 Write your message..." class="fancy-textarea"></textarea>
          </div>

          <div class="text-end">
            <button type="submit" class="send-button" :disabled="isSubmitting">
              <Icon icon="mdi:send" class="w-5 h-5" />
              <span v-if="!isSubmitting">Send Message</span>
              <span v-else>Sending...</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  profile: Object
})

const allLinks = {
  website: 'mdi:web',
  email: 'mdi:email-outline',
  twitter: 'mdi:twitter',
  facebook: 'mdi:facebook',
  telegram: 'mdi:telegram',
  instagram: 'mdi:instagram',
  youtube: 'mdi:youtube',
  phone: 'mdi:phone-outline'
}

const profileLinkExists = (key) => {
  return props.profile.links?.some((l) => l.type === key)
}

const getLinkUrl = (key) => {
  return props.profile.links?.find((l) => l.type === key)?.url || '#'
}

const customLinks = computed(() => {
  return props.profile.links?.filter((l) => l.type === 'custom') || []
})

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const isSubmitting = ref(false)

const submitForm = () => {
  isSubmitting.value = true
  setTimeout(() => {
    console.log('Form submitted:', form.value)
    alert('Your message has been sent! ✨')
    form.value = { name: '', email: '', subject: '', message: '' }
    isSubmitting.value = false
  }, 1500)
}
</script>

<style scoped>
.section-title {
  @apply text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3 mb-6;
}
.section-subtitle {
  @apply text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4;
}
.link-box {
  @apply flex flex-col items-center justify-center gap-2 rounded-2xl py-6 px-4 text-sm font-medium shadow-md border transition-all;
}
.link-box-active {
  @apply bg-gradient-to-br from-red-100 to-red-200 text-red-800 dark:from-red-800 dark:to-red-700 dark:text-white border-red-300 dark:border-red-600;
}
.link-box-disabled {
  @apply bg-gray-100 dark:bg-gray-800 text-gray-400 border-gray-200 dark:border-gray-700 opacity-50 pointer-events-none;
}
.form-card {
  @apply bg-white dark:bg-gray-900 rounded-3xl shadow-xl p-10 space-y-8 border border-gray-200 dark:border-gray-700;
}
.input-with-icon {
  @apply relative;
}
.input-icon {
  @apply absolute left-4 top-3.5 text-gray-400;
}
.fancy-input {
  @apply w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 transition-all;
}
.fancy-textarea {
  @apply w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 transition-all;
}
.send-button {
  @apply inline-flex items-center gap-2 px-8 py-3 rounded-full bg-gradient-to-r from-red-500 via-pink-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:pointer-events-none;
}
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out;
}
</style>

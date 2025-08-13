<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0d0d0d] dark:to-[#1a1a1a]">
    <MainNavbar />

    <div class="flex justify-center items-center py-16 px-4">
      <div class="w-full max-w-4xl bg-white/80 dark:bg-gray-900/90 backdrop-blur-md rounded-3xl shadow-2xl p-10 animate-fade-up">

        <!-- Header -->
        <div class="text-center mb-10">
          <h1 class="text-4xl font-extrabold text-red-700 dark:text-red-400 tracking-wide">Linxx</h1>
          <p class="mt-2 text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
            {{ $t('auth.login.subtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10" dir="ltr">

          <!-- Social Login -->
          <div class="flex flex-col justify-center gap-5">
            <p class="text-center text-base font-medium text-gray-600 dark:text-gray-300">
              {{ $t('auth.login.or_continue_with') }}
            </p>

            <button @click="socialLogin('google')" class="flex items-center gap-3 px-6 py-3 border border-gray-300 rounded-xl bg-white hover:bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white dark:border-gray-600 transition shadow-sm">
              <Icon icon="flat-color-icons:google" width="24" height="24" />
              <span class="font-semibold">Google</span>
            </button>

            <button @click="socialLogin('twitter')" class="flex items-center gap-3 px-6 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition shadow-sm">
              <Icon icon="mdi:twitter" width="24" height="24" />
              <span class="font-semibold">Twitter</span>
            </button>

            <button @click="socialLogin('facebook')" class="flex items-center gap-3 px-6 py-3 rounded-xl bg-[#3b5998] hover:bg-[#2d4373] text-white transition shadow-sm">
              <Icon icon="mdi:facebook" width="24" height="24" />
              <span class="font-semibold">Facebook</span>
            </button>
          </div>

          <!-- Login Form -->
          <div class="space-y-6">
            <h2 class="text-2xl font-semibold text-center text-gray-800 dark:text-white">
              {{ $t('auth.login.title') }}
            </h2>

            <form @submit.prevent="login" class="space-y-4">
              <input
                  v-model="form.email"
                  type="email"
                  :placeholder="$t('auth.login.email')"
                  :class="inputClass"
              />
              <input
                  v-model="form.password"
                  type="password"
                  :placeholder="$t('auth.login.password')"
                  :class="inputClass"
              />

              <div :class="[$i18n.locale === 'fa' ? 'text-right' : 'text-left']">
                <router-link to="/forgot-password" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                  {{ $t('auth.login.forgot_password') }}
                </router-link>
              </div>

              <button
                  type="submit"
                  class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow transition"
              >
                {{ $t('auth.login.submit') }}
              </button>
            </form>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
              {{ $t('auth.login.no_account') }}
              <router-link to="/register" class="text-red-600 dark:text-red-400 font-medium hover:underline">
                {{ $t('auth.login.register_link') }}
              </router-link>
            </p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import MainNavbar from "@/pages/home/components/MainNavbar.vue";
import { Icon } from "@iconify/vue";
import { useAuthStore } from "@/stores/auth";
import axios from "@/lib/axios";

export default {
  name: 'LoginPage',
  components: {
    MainNavbar,
    Icon
  },
  data() {
    return {
      form: {
        email: '',
        password: ''
      }
    };
  },
  computed: {
    inputClass() {
      return [
        'form-input',
        this.$i18n.locale === 'fa' ? 'text-right' : 'text-left'
      ];
    }
  },
  methods: {
    async login() {
      const auth = useAuthStore();
      try {
        await auth.login(this.form);
      } catch (error) {
        console.error('Login failed:', error);
      }
    },
    async socialLogin(provider) {
      try {
        window.location.href = `http://localhost:8080/auth/${provider}`;
      } catch (error) {
        console.error(`Social login (${provider}) failed:`, error);
      }
    }
  }
};
</script>

<style scoped>
@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-up {
  animation: fade-up 0.6s ease-out both;
}
.form-input {
  @apply w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500;
}
</style>

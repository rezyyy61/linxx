<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-[#0d0d0d] dark:to-[#1a1a1a]">
    <MainNavbar />

    <!-- Alert Box -->
    <div class="mb-10 mt-10 rounded-xl border border-red-300 dark:border-red-500 bg-red-50 dark:bg-red-900/30 p-6 shadow-sm max-w-4xl mx-auto">
      <div class="flex items-start gap-4">
        <div class="text-red-600 dark:text-red-400 text-2xl">🛡️</div>
        <p class="text-m sm:text-base leading-relaxed text-gray-800 dark:text-gray-200 whitespace-pre-line text-justify">
          {{ $t('auth.register.policy_note') }}
        </p>
      </div>
    </div>

    <!-- Form Box -->
    <div class="flex justify-center items-center py-16 px-4">
      <div class="w-full max-w-6xl bg-white/80 dark:bg-gray-900/90 backdrop-blur-md rounded-3xl shadow-2xl p-10 animate-fade-up">
        <div class="text-center mb-12">
          <h1 class="text-5xl font-extrabold text-red-700 dark:text-red-400 tracking-wide">Linxx</h1>
          <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">{{ $t('auth.register.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12" dir="ltr">
          <!-- Social Login -->
          <div class="flex flex-col justify-center gap-4">
            <p class="text-center text-base font-medium text-gray-600 dark:text-gray-300">
              {{ $t('auth.register.or_continue_with') }}
            </p>

            <button
                @click.prevent="socialLogin('google')"
                class="flex items-center justify-center gap-3 px-6 py-3 border border-gray-300 rounded-xl bg-white hover:bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white dark:border-gray-600 transition shadow-sm"
            >
              <Icon icon="flat-color-icons:google" width="24" height="24" />
              <span class="font-semibold">Sign up with Google</span>
            </button>

            <button
                @click.prevent="socialLogin('facebook')"
                class="flex items-center justify-center gap-3 px-6 py-3 rounded-xl bg-[#3b5998] hover:bg-[#2d4373] text-white transition shadow-sm"
            >
              <Icon icon="logos:facebook" width="24" height="24" />
              <span class="font-semibold">Sign up with Facebook</span>
            </button>

            <button
                @click.prevent="socialLogin('twitter')"
                class="flex items-center justify-center gap-3 px-6 py-3 rounded-xl bg-[#1DA1F2] hover:bg-[#0d8ddb] text-white transition shadow-sm"
            >
              <Icon icon="logos:twitter" width="24" height="24" />
              <span class="font-semibold">Sign up with Twitter</span>
            </button>
          </div>

          <!-- Manual Form -->
          <div class="space-y-6">
            <h2 class="text-2xl font-semibold text-center text-gray-800 dark:text-white">
              {{ $t('auth.register.title') }}
            </h2>

            <form @submit.prevent="handleRegister" class="space-y-4">
              <input v-model="form.name" type="text" :placeholder="$t('auth.register.name')" :class="inputClass" />
              <input v-model="form.email" type="email" :placeholder="$t('auth.register.email')" :class="inputClass" />
              <input v-model="form.password" type="password" :placeholder="$t('auth.register.password')" :class="inputClass" />
              <input v-model="form.password_confirmation" type="password" :placeholder="$t('auth.register.confirm_password')" :class="inputClass" />

              <button
                  type="submit"
                  class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow transition"
              >
                {{ $t('auth.register.submit') }}
              </button>
            </form>

            <p class="text-center text-sm text-gray-600 dark:text-gray-400">
              {{ $t('auth.register.have_account') }}
              <router-link to="/login" class="text-red-600 dark:text-red-400 font-medium hover:underline">
                {{ $t('auth.register.login_link') }}
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
  name: "RegisterPage",
  components: {
    MainNavbar,
    Icon
  },
  data() {
    return {
      form: {
        name: "",
        email: "",
        password: "",
        password_confirmation: ""
      }
    };
  },
  computed: {
    inputClass() {
      return [
        "form-input",
        this.$i18n.locale === "fa" ? "text-right" : "text-left"
      ];
    }
  },
  methods: {
    async handleRegister() {
      const auth = useAuthStore();
      try {
        await auth.register(this.form);
      } catch (error) {
        console.error("Registration failed:", error);
      }
    },
    async socialLogin(provider) {
      try {
        window.location.href = `http://localhost:8080/api/auth/${provider}`;
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

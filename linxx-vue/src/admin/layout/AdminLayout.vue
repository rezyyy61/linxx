<template>
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white p-4">
      <h2 class="text-lg font-bold mb-6">Admin Panel</h2>
      <ul class="space-y-2">
        <li>
          <router-link to="/admin/dashboard" class="hover:underline">Dashboard</router-link>
        </li>
        <li>
          <router-link to="/admin/users" class="hover:underline">Users</router-link>
        </li>
        <li>
          <button @click="logout" class="text-left w-full hover:underline text-red-400 mt-6">Logout</button>
        </li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-6 bg-gray-50">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import axios from '@/lib/axios'

const logout = async () => {
  try {
    await axios.post('/admin/logout', {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('admin_token')}`
      }
    })

    localStorage.removeItem('admin_token')
    localStorage.removeItem('admin_user')
    window.location.href = '/admin/login'
  } catch (err) {
    console.error('Logout failed', err)
  }
}
</script>

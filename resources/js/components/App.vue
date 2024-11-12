<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
      <div class="container mx-auto px-6 py-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <router-link to="/" class="text-xl font-bold text-gray-800">
              Casino Games
            </router-link>
          </div>
          <div class="flex items-center space-x-4">
            <div v-if="user" class="flex items-center space-x-2">
              <span class="text-gray-600">Balance: ${{ formatMoney(user.balance) }}</span>
              <button @click="logout" 
                      class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Logout
              </button>
            </div>
            <div v-else class="flex items-center space-x-2">
              <button @click="showLoginModal = true; isAdminLogin = false"
                      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Login
              </button>
              <button @click="showLoginModal = true; isAdminLogin = true"
                      class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Admin Login
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <router-view></router-view>
    </main>

    <!-- Loading Overlay -->
    <div v-if="loading" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <!-- Error Toast -->
    <div v-if="error" 
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded shadow-lg">
      {{ error }}
    </div>

    <!-- Login Modal -->
    <div v-if="showLoginModal" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">{{ isAdminLogin ? 'Admin Login' : 'User Login' }}</h2>
        <form @submit.prevent="login" class="space-y-4">
          <div>
            <label class="block text-gray-700 mb-2">Email</label>
            <input v-model="loginForm.email" 
                   type="email" 
                   required 
                   class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-gray-700 mb-2">Password</label>
            <input v-model="loginForm.password" 
                   type="password" 
                   required 
                   class="w-full border rounded px-3 py-2">
          </div>
          <button type="submit" 
                  class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Login
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'App',
  
  data() {
    return {
      user: null,
      loading: false,
      error: null,
      showLoginModal: false,
      isAdminLogin: false,
      loginForm: {
        email: '',
        password: ''
      }
    }
  },

  methods: {
    async checkAuth() {
      try {
        const response = await axios.get('/api/user')
        this.user = response.data
      } catch (error) {
        console.error('Auth check failed:', error)
      }
    },

    async login() {
      this.loading = true
      this.error = null

      try {
        const endpoint = this.isAdminLogin ? '/api/admin/login' : '/api/login'
        const response = await axios.post(endpoint, this.loginForm)
        this.user = response.data
        this.showLoginModal = false
        this.loginForm = { email: '', password: '' }
        
        // Redirect to admin dashboard if admin login
        if (this.isAdminLogin && response.data.is_admin) {
          this.$router.push('/admin/dashboard')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
      } finally {
        this.loading = false
      }
    },
    async logout() {
      this.loading = true
      this.error = null

      try {
        await axios.post('/api/logout')
        this.user = null
        this.$router.push('/')
      } catch (error) {
        this.error = 'Logout failed'
      } finally {
        this.loading = false
      }
    },

    formatMoney(amount) {
      return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }
  },

  watch: {
    error(newError) {
      if (newError) {
        setTimeout(() => {
          this.error = null
        }, 5000)
      }
    }
  },

  mounted() {
    this.checkAuth()
  }
}
</script>

<style>
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-lg shadow">
      <div>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          Casino Admin Login
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="login">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="username" class="sr-only">Username</label>
            <input v-model="form.username" 
                   id="username" 
                   name="username" 
                   type="text" 
                   required 
                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                   placeholder="Username">
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input v-model="form.password" 
                   id="password" 
                   name="password" 
                   type="password" 
                   required 
                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                   placeholder="Password">
          </div>
        </div>

        <div>
          <button type="submit" 
                  class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Sign in
          </button>
        </div>
      </form>

      <!-- Error Message -->
      <div v-if="error" class="mt-4 text-center text-sm text-red-600">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminLogin',
  
  data() {
    return {
      form: {
        username: '',
        password: ''
      },
      error: null
    }
  },

  methods: {
    async login() {
      try {
        this.error = null
        const response = await this.$axios.post('/api/admin/login', this.form)
        
        if (response.data.token) {
          localStorage.setItem('admin_token', response.data.token)
          this.$axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
          this.$router.push('/admin/dashboard')
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
      }
    }
  }
}
</script>
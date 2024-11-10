<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white border-b border-gray-200">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex items-center flex-shrink-0">
              <h1 class="text-xl font-bold">Casino Admin</h1>
            </div>
          </div>
          <div class="flex items-center">
            <button @click="logout" class="px-4 py-2 text-sm text-red-600 hover:text-red-900">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <div class="py-6">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 mt-2 sm:grid-cols-2 lg:grid-cols-4">
          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="p-3 bg-blue-500 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Revenue
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      ${{ formatMoney(stats.totalRevenue) }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="p-3 bg-green-500 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Active Users
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.activeUsers }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="p-3 bg-yellow-500 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      RTP
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.rtp }}%
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="overflow-hidden bg-white rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="p-3 bg-red-500 rounded-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
                <div class="flex-1 w-0 ml-5">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Bets
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.totalBets }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Controls -->
        <div class="mt-8">
          <div class="md:grid md:grid-cols-3 md:gap-6">
            <!-- Game Settings -->
            <div class="mt-5 md:mt-0 md:col-span-1">
              <div class="px-4 py-5 bg-white shadow sm:rounded-lg sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Game Settings</h3>
                <div class="mt-6 space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Min Bet</label>
                    <input v-model="settings.minBet" type="number" step="0.01" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Max Bet</label>
                    <input v-model="settings.maxBet" type="number" step="0.01" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">RTP %</label>
                    <input v-model="settings.rtp" type="number" step="0.1" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  </div>
                  <button @click="saveSettings" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Settings
                  </button>
                </div>
              </div>
            </div>

            <!-- Jackpot Management -->
            <div class="mt-5 md:mt-0 md:col-span-1">
              <div class="px-4 py-5 bg-white shadow sm:rounded-lg sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Jackpot Management</h3>
                <div class="mt-6 space-y-4">
                  <div v-for="jackpot in jackpots" :key="jackpot.id" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">{{ jackpot.name }}</label>
                    <div class="flex space-x-2">
                      <input v-model="jackpot.amount" type="number" step="0.01" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                      <button @click="updateJackpot(jackpot)" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Update
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- User Management -->
            <div class="mt-5 md:mt-0 md:col-span-1">
              <div class="px-4 py-5 bg-white shadow sm:rounded-lg sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">User Management</h3>
                <div class="mt-6 space-y-4">
                  <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Search Users</label>
                    <input v-model="userSearch" type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Email or username">
                  </div>
                  <div v-if="selectedUser" class="space-y-2">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Balance</label>
                      <input v-model="selectedUser.balance" type="number" step="0.01" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex space-x-2">
                      <button @click="updateUser" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update User
                      </button>
                      <button @click="banUser" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        {{ selectedUser.banned ? 'Unban' : 'Ban' }} User
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Transaction History -->
        <div class="mt-8">
          <div class="px-4 py-5 bg-white shadow sm:rounded-lg sm:p-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Transactions</h3>
            <div class="mt-6">
              <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                              User
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                              Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                              Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                              Date
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                          <tr v-for="transaction in transactions" :key="transaction.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900">{{ transaction.user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span :class="[
                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                transaction.type === 'win' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                              ]">
                                {{ transaction.type }}
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900">${{ formatMoney(transaction.amount) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900">{{ formatDate(transaction.created_at) }}</div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  
  data() {
    return {
      stats: {
        totalRevenue: 0,
        activeUsers: 0,
        rtp: 0,
        totalBets: 0
      },
      settings: {
        minBet: 1,
        maxBet: 100,
        rtp: 96
      },
      jackpots: [],
      userSearch: '',
      selectedUser: null,
      transactions: []
    }
  },

  methods: {
    async fetchStats() {
      try {
        const response = await this.$axios.get('/api/admin/stats')
        this.stats = response.data
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    },

    async saveSettings() {
      try {
        await this.$axios.post('/api/admin/settings', this.settings)
        this.$toast.success('Settings saved successfully')
      } catch (error) {
        this.$toast.error('Failed to save settings')
      }
    },

    async updateJackpot(jackpot) {
      try {
        await this.$axios.put(`/api/admin/jackpots/${jackpot.id}`, {
          amount: jackpot.amount
        })
        this.$toast.success('Jackpot updated successfully')
      } catch (error) {
        this.$toast.error('Failed to update jackpot')
      }
    },

    async searchUsers() {
      if (!this.userSearch) return

      try {
        const response = await this.$axios.get(`/api/admin/users?search=${this.userSearch}`)
        this.selectedUser = response.data
      } catch (error) {
        this.$toast.error('User not found')
      }
    },

    async updateUser() {
      if (!this.selectedUser) return

      try {
        await this.$axios.put(`/api/admin/users/${this.selectedUser.id}`, {
          balance: this.selectedUser.balance
        })
        this.$toast.success('User updated successfully')
      } catch (error) {
        this.$toast.error('Failed to update user')
      }
    },

    async banUser() {
      if (!this.selectedUser) return

      try {
        await this.$axios.post(`/api/admin/users/${this.selectedUser.id}/ban`)
        this.selectedUser.banned = !this.selectedUser.banned
        this.$toast.success(`User ${this.selectedUser.banned ? 'banned' : 'unbanned'} successfully`)
      } catch (error) {
        this.$toast.error('Failed to update user status')
      }
    },

    async fetchTransactions() {
      try {
        const response = await this.$axios.get('/api/admin/transactions')
        this.transactions = response.data
      } catch (error) {
        console.error('Error fetching transactions:', error)
      }
    },

    async logout() {
      try {
        await this.$axios.post('/api/admin/logout')
        this.$router.push('/admin/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    },

    formatMoney(amount) {
      return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  },

  watch: {
    userSearch(newVal) {
      if (newVal) {
        this.searchUsers()
      } else {
        this.selectedUser = null
      }
    }
  },

  mounted() {
    this.fetchStats()
    this.fetchTransactions()
  }
}
</script>
<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Stats Cards -->
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Users</h3>
        <p class="text-3xl font-bold">{{ stats.totalUsers }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Deposits</h3>
        <p class="text-3xl font-bold">${{ formatMoney(stats.totalDeposits) }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Total Withdrawals</h3>
        <p class="text-3xl font-bold">${{ formatMoney(stats.totalWithdrawals) }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm font-medium">Active Games</h3>
        <p class="text-3xl font-bold">{{ stats.activeGames }}</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-8">
      <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="currentTab = tab.id"
            :class="[
              currentTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow">
      <!-- Users Management -->
      <div v-if="currentTab === 'users'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Users Management</h2>
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Search users..."
            class="px-4 py-2 border rounded"
          >
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in filteredUsers" :key="user.id">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">${{ formatMoney(user.balance) }}</div>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ user.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium">
                  <button 
                    @click="toggleUserStatus(user)"
                    class="text-blue-600 hover:text-blue-900 mr-2"
                  >
                    {{ user.status === 'active' ? 'Suspend' : 'Activate' }}
                  </button>
                  <button 
                    @click="showEditBalanceModal(user)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Edit Balance
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Transactions -->
      <div v-if="currentTab === 'transactions'" class="p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Transactions</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transaction in transactions" :key="transaction.id">
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ transaction.user.name }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ transaction.type }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">${{ formatMoney(transaction.amount) }}</div>
                </td>
                <td class="px-6 py-4">
                  <span :class="[
                    transaction.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                  ]">
                    {{ transaction.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ formatDate(transaction.created_at) }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Games Management -->
      <div v-if="currentTab === 'games'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Games Management</h2>
          <button 
            @click="showAddGameModal = true"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Add New Game
          </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="game in games" :key="game.id" class="bg-white rounded-lg shadow">
            <img :src="game.image" :alt="game.name" class="w-full h-48 object-cover rounded-t-lg">
            <div class="p-4">
              <h3 class="text-lg font-semibold">{{ game.name }}</h3>
              <p class="text-gray-600 text-sm mb-4">{{ game.description }}</p>
              <div class="flex justify-between items-center">
                <button 
                  @click="toggleGameStatus(game)"
                  :class="[
                    game.active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700',
                    'text-white px-4 py-2 rounded'
                  ]"
                >
                  {{ game.active ? 'Disable' : 'Enable' }}
                </button>
                <button 
                  @click="editGame(game)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  Edit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Settings -->
      <div v-if="currentTab === 'settings'" class="p-6">
        <h2 class="text-xl font-semibold mb-6">Casino Settings</h2>
        <div class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Minimum Deposit</label>
              <input 
                type="number" 
                v-model="settings.minDeposit"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Maximum Deposit</label>
              <input 
                type="number" 
                v-model="settings.maxDeposit"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Minimum Withdrawal</label>
              <input 
                type="number" 
                v-model="settings.minWithdrawal"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Maximum Withdrawal</label>
              <input 
                type="number" 
                v-model="settings.maxWithdrawal"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              >
            </div>
          </div>
          <div class="flex justify-end">
            <button 
              @click="saveSettings"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Save Settings
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Balance Modal -->
  <div v-if="showEditBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-96">
      <h3 class="text-lg font-semibold mb-4">Edit User Balance</h3>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">New Balance</label>
        <input 
          type="number" 
          v-model="newBalance"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
      </div>
      <div class="flex justify-end space-x-2">
        <button 
          @click="showEditBalanceModal = false"
          class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300"
        >
          Cancel
        </button>
        <button 
          @click="updateUserBalance"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Update
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminDashboard',
  
  data() {
    return {
      currentTab: 'users',
      searchQuery: '',
      stats: {
        totalUsers: 0,
        totalDeposits: 0,
        totalWithdrawals: 0,
        activeGames: 0
      },
      tabs: [
        { id: 'users', name: 'Users' },
        { id: 'transactions', name: 'Transactions' },
        { id: 'games', name: 'Games' },
        { id: 'settings', name: 'Settings' }
      ],
      users: [],
      transactions: [],
      games: [],
      settings: {
        minDeposit: 10,
        maxDeposit: 10000,
        minWithdrawal: 20,
        maxWithdrawal: 5000
      },
      showEditBalanceModal: false,
      selectedUser: null,
      newBalance: 0
    }
  },

  computed: {
    filteredUsers() {
      if (!this.searchQuery) return this.users
      const query = this.searchQuery.toLowerCase()
      return this.users.filter(user => 
        user.name.toLowerCase().includes(query) || 
        user.email.toLowerCase().includes(query)
      )
    }
  },

  methods: {
    async fetchStats() {
      try {
        const response = await axios.get('/api/admin/stats')
        this.stats = response.data
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    },

    async fetchUsers() {
      try {
        const response = await axios.get('/api/admin/users')
        this.users = response.data
      } catch (error) {
        console.error('Error fetching users:', error)
      }
    },

    async fetchTransactions() {
      try {
        const response = await axios.get('/api/admin/transactions')
        this.transactions = response.data
      } catch (error) {
        console.error('Error fetching transactions:', error)
      }
    },

    async fetchGames() {
      try {
        const response = await axios.get('/api/admin/games')
        this.games = response.data
      } catch (error) {
        console.error('Error fetching games:', error)
      }
    },

    async toggleUserStatus(user) {
      try {
        await axios.post(`/api/admin/users/${user.id}/toggle-status`)
        await this.fetchUsers()
      } catch (error) {
        console.error('Error toggling user status:', error)
      }
    },

    showEditBalanceModal(user) {
      this.selectedUser = user
      this.newBalance = user.balance
      this.showEditBalanceModal = true
    },

    async updateUserBalance() {
      try {
        await axios.post(`/api/admin/users/${this.selectedUser.id}/update-balance`, {
          balance: this.newBalance
        })
        await this.fetchUsers()
        this.showEditBalanceModal = false
      } catch (error) {
        console.error('Error updating user balance:', error)
      }
    },

    async toggleGameStatus(game) {
      try {
        await axios.post(`/api/admin/games/${game.id}/toggle-status`)
        await this.fetchGames()
      } catch (error) {
        console.error('Error toggling game status:', error)
      }
    },

    async saveSettings() {
      try {
        await axios.post('/api/admin/settings', this.settings)
      } catch (error) {
        console.error('Error saving settings:', error)
      }
    },

    formatMoney(amount) {
      return parseFloat(amount).toFixed(2)
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },

  created() {
    this.fetchStats()
    this.fetchUsers()
    this.fetchTransactions()
    this.fetchGames()
  }
}
</script>
<template>
  <div class="p-6">
    <div class="mb-8 flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">User Management</h2>
      <div class="flex space-x-4">
        <button
          @click="showCreateUserModal = true"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
        >
          Create User
        </button>
        <button
          @click="exportUsers"
          class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200"
        >
          Export Users
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Search</label>
          <input
            v-model="filters.search"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Search users..."
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select
            v-model="filters.status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <select
            v-model="filters.role"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option value="">All</option>
            <option value="user">User</option>
            <option value="vip">VIP</option>
            <option value="moderator">Moderator</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Sort By</label>
          <select
            v-model="filters.sortBy"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
            <option value="created_at">Registration Date</option>
            <option value="last_login">Last Login</option>
            <option value="balance">Balance</option>
            <option value="total_bets">Total Bets</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              User
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Role
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Balance
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Last Login
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img class="h-10 w-10 rounded-full" :src="user.avatar" alt="">
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ user.email }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                user.status === 'active' ? 'bg-green-100 text-green-800' :
                user.status === 'inactive' ? 'bg-yellow-100 text-yellow-800' :
                'bg-red-100 text-red-800'
              ]">
                {{ user.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.role }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              ${{ formatMoney(user.balance) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.last_login) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editUser(user)"
                class="text-blue-600 hover:text-blue-900 mr-4"
              >
                Edit
              </button>
              <button
                @click="showUserDetails(user)"
                class="text-green-600 hover:text-green-900"
              >
                Details
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            @click="nextPage"
            :disabled="!hasNextPage"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ paginationStart }}</span>
              to
              <span class="font-medium">{{ paginationEnd }}</span>
              of
              <span class="font-medium">{{ totalUsers }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Previous
              </button>
              <button
                v-for="page in pageNumbers"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium',
                  currentPage === page
                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                    : 'text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="nextPage"
                :disabled="!hasNextPage"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div v-if="showCreateUserModal || editingUser" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <button
              @click="closeUserModal"
              class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ editingUser ? 'Edit User' : 'Create User' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    v-model="userForm.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    v-model="userForm.email"
                    type="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Role</label>
                  <select
                    v-model="userForm.role"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="user">User</option>
                    <option value="vip">VIP</option>
                    <option value="moderator">Moderator</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <select
                    v-model="userForm.status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                  </select>
                </div>
                <div v-if="!editingUser">
                  <label class="block text-sm font-medium text-gray-700">Password</label>
                  <input
                    v-model="userForm.password"
                    type="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              @click="saveUser"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ editingUser ? 'Save Changes' : 'Create User' }}
            </button>
            <button
              @click="closeUserModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- User Details Modal -->
    <div v-if="selectedUser" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <button
              @click="selectedUser = null"
              class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                User Details
              </h3>

              <!-- User Stats -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-gray-500">Total Bets</div>
                  <div class="mt-1 text-lg font-semibold text-gray-900">
                    {{ selectedUser.stats.totalBets }}
                  </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-gray-500">Win Rate</div>
                  <div class="mt-1 text-lg font-semibold text-gray-900">
                    {{ selectedUser.stats.winRate }}%
                  </div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                  <div class="text-sm font-medium text-gray-500">Average Bet</div>
                  <div class="mt-1 text-lg font-semibold text-gray-900">
                    ${{ formatMoney(selectedUser.stats.averageBet) }}
                  </div>
                </div>
              </div>

              <!-- Recent Activity -->
              <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Activity</h4>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                  <ul class="divide-y divide-gray-200">
                    <li v-for="activity in selectedUser.recentActivity" :key="activity.id" class="p-4">
                      <div class="flex items-center justify-between">
                        <div>
                          <p class="text-sm font-medium text-gray-900">{{ activity.type }}</p>
                          <p class="text-xs text-gray-500">{{ formatDate(activity.date) }}</p>
                        </div>
                        <div :class="[
                          'text-sm font-medium',
                          activity.amount > 0 ? 'text-green-600' : 'text-red-600'
                        ]">
                          {{ activity.amount > 0 ? '+' : '' }}${{ formatMoney(activity.amount) }}
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Account Actions -->
              <div class="space-x-4">
                <button
                  @click="resetPassword(selectedUser)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700"
                >
                  Reset Password
                </button>
                <button
                  @click="toggleUserStatus(selectedUser)"
                  :class="[
                    'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md',
                    selectedUser.status === 'active'
                      ? 'text-white bg-red-600 hover:bg-red-700'
                      : 'text-white bg-green-600 hover:bg-green-700'
                  ]"
                >
                  {{ selectedUser.status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                </button>
                <button
                  @click="showTransactionHistory(selectedUser)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200"
                >
                  Transaction History
                </button>
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
  name: 'UserManagement',

  data() {
    return {
      users: [],
      filters: {
        search: '',
        status: '',
        role: '',
        sortBy: 'created_at'
      },
      currentPage: 1,
      totalUsers: 0,
      perPage: 10,
      showCreateUserModal: false,
      editingUser: null,
      selectedUser: null,
      userForm: {
        name: '',
        email: '',
        role: 'user',
        status: 'active',
        password: ''
      }
    }
  },

  computed: {
    paginationStart() {
      return (this.currentPage - 1) * this.perPage + 1
    },

    paginationEnd() {
      return Math.min(this.currentPage * this.perPage, this.totalUsers)
    },

    pageNumbers() {
      const totalPages = Math.ceil(this.totalUsers / this.perPage)
      return Array.from({ length: totalPages }, (_, i) => i + 1)
    },

    hasNextPage() {
      return this.currentPage * this.perPage < this.totalUsers
    }
  },

  methods: {
    async fetchUsers() {
      try {
        const response = await this.$axios.get('/api/admin/users', {
          params: {
            ...this.filters,
            page: this.currentPage,
            per_page: this.perPage
          }
        })
        this.users = response.data.users
        this.totalUsers = response.data.total
      } catch (error) {
        this.$toast.error('Failed to fetch users')
      }
    },

    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--
        this.fetchUsers()
      }
    },

    nextPage() {
      if (this.hasNextPage) {
        this.currentPage++
        this.fetchUsers()
      }
    },

    goToPage(page) {
      this.currentPage = page
      this.fetchUsers()
    },

    editUser(user) {
      this.editingUser = user
      this.userForm = { ...user }
      delete this.userForm.password
    },

    async showUserDetails(user) {
      try {
        const response = await this.$axios.get(`/api/admin/users/${user.id}/details`)
        this.selectedUser = {
          ...user,
          ...response.data
        }
      } catch (error) {
        this.$toast.error('Failed to fetch user details')
      }
    },

    closeUserModal() {
      this.showCreateUserModal = false
      this.editingUser = null
      this.userForm = {
        name: '',
        email: '',
        role: 'user',
        status: 'active',
        password: ''
      }
    },

    async saveUser() {
      try {
        if (this.editingUser) {
          await this.$axios.put(`/api/admin/users/${this.editingUser.id}`, this.userForm)
          this.$toast.success('User updated successfully')
        } else {
          await this.$axios.post('/api/admin/users', this.userForm)
          this.$toast.success('User created successfully')
        }
        this.closeUserModal()
        this.fetchUsers()
      } catch (error) {
        this.$toast.error('Failed to save user')
      }
    },

    async resetPassword(user) {
      if (!confirm(`Are you sure you want to reset the password for ${user.name}?`)) return

      try {
        await this.$axios.post(`/api/admin/users/${user.id}/reset-password`)
        this.$toast.success('Password reset email sent')
      } catch (error) {
        this.$toast.error('Failed to reset password')
      }
    },

    async toggleUserStatus(user) {
      const newStatus = user.status === 'active' ? 'suspended' : 'active'
      const action = newStatus === 'active' ? 'activate' : 'suspend'

      if (!confirm(`Are you sure you want to ${action} ${user.name}'s account?`)) return

      try {
        await this.$axios.patch(`/api/admin/users/${user.id}/status`, { status: newStatus })
        user.status = newStatus
        this.$toast.success(`User ${action}d successfully`)
      } catch (error) {
        this.$toast.error(`Failed to ${action} user`)
      }
    },

    async showTransactionHistory(user) {
      try {
        const response = await this.$axios.get(`/api/admin/users/${user.id}/transactions`)
        // Handle transaction history display
      } catch (error) {
        this.$toast.error('Failed to fetch transaction history')
      }
    },

    async exportUsers() {
      try {
        const response = await this.$axios.get('/api/admin/users/export', {
          params: this.filters,
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'users.csv')
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        this.$toast.error('Failed to export users')
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
    filters: {
      deep: true,
      handler() {
        this.currentPage = 1
        this.fetchUsers()
      }
    }
  },

  mounted() {
    this.fetchUsers()
  }
}
</script>
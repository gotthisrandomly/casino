<template>
  <div class="p-6">
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900">System Maintenance</h2>
    </div>

    <!-- Maintenance Mode Toggle -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Maintenance Mode</h3>
          <p class="text-sm text-gray-500">
            When enabled, the site will be inaccessible to regular users
          </p>
        </div>
        <button
          @click="toggleMaintenanceMode"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-md',
            maintenance.enabled
              ? 'bg-red-100 text-red-600 hover:bg-red-200'
              : 'bg-green-100 text-green-600 hover:bg-green-200'
          ]"
        >
          {{ maintenance.enabled ? 'Disable' : 'Enable' }}
        </button>
      </div>

      <div v-if="maintenance.enabled" class="mt-4">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Maintenance Message</label>
          <textarea
            v-model="maintenance.message"
            rows="3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Enter message to display to users..."
          ></textarea>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Expected Duration</label>
          <div class="mt-1 grid grid-cols-2 gap-4">
            <input
              v-model="maintenance.startTime"
              type="datetime-local"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
            <input
              v-model="maintenance.endTime"
              type="datetime-local"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
          </div>
        </div>

        <div class="mb-4">
          <div class="flex items-center">
            <input
              v-model="maintenance.allowAdminAccess"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label class="ml-2 block text-sm text-gray-700">Allow admin access during maintenance</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Backup Controls -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Database Backup</h3>
      
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Create a backup before maintenance</p>
            <p class="text-xs text-gray-400">Last backup: {{ maintenance.lastBackup || 'Never' }}</p>
          </div>
          <button
            @click="createBackup"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            :disabled="isBackingUp"
          >
            {{ isBackingUp ? 'Creating Backup...' : 'Create Backup' }}
          </button>
        </div>

        <!-- Recent Backups -->
        <div v-if="maintenance.backups.length > 0">
          <h4 class="text-sm font-medium text-gray-700 mb-2">Recent Backups</h4>
          <ul class="divide-y divide-gray-200">
            <li v-for="backup in maintenance.backups" :key="backup.id" class="py-3 flex justify-between items-center">
              <div>
                <p class="text-sm text-gray-900">{{ backup.filename }}</p>
                <p class="text-xs text-gray-500">{{ backup.created_at }}</p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="downloadBackup(backup)"
                  class="text-blue-600 hover:text-blue-800"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                </button>
                <button
                  @click="deleteBackup(backup)"
                  class="text-red-600 hover:text-red-800"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- System Health -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">System Health</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <h4 class="text-sm font-medium text-gray-700 mb-2">Server Resources</h4>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-500">CPU Usage</span>
                <span class="text-sm text-gray-700">{{ systemHealth.cpu }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-blue-600 h-2 rounded-full"
                  :style="{ width: systemHealth.cpu + '%' }"
                ></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-500">Memory Usage</span>
                <span class="text-sm text-gray-700">{{ systemHealth.memory }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-blue-600 h-2 rounded-full"
                  :style="{ width: systemHealth.memory + '%' }"
                ></div>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-500">Disk Usage</span>
                <span class="text-sm text-gray-700">{{ systemHealth.disk }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-blue-600 h-2 rounded-full"
                  :style="{ width: systemHealth.disk + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-medium text-gray-700 mb-2">Active Processes</h4>
          <div class="space-y-2">
            <div
              v-for="process in systemHealth.processes"
              :key="process.id"
              class="flex justify-between items-center p-2 bg-gray-50 rounded"
            >
              <span class="text-sm text-gray-700">{{ process.name }}</span>
              <span :class="[
                'px-2 py-1 text-xs rounded-full',
                process.status === 'running' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ process.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MaintenanceMode',

  data() {
    return {
      maintenance: {
        enabled: false,
        message: '',
        startTime: '',
        endTime: '',
        allowAdminAccess: true,
        lastBackup: null,
        backups: []
      },
      isBackingUp: false,
      systemHealth: {
        cpu: 0,
        memory: 0,
        disk: 0,
        processes: []
      }
    }
  },

  methods: {
    async fetchMaintenanceStatus() {
      try {
        const response = await this.$axios.get('/api/admin/maintenance/status')
        this.maintenance = response.data
      } catch (error) {
        this.$toast.error('Failed to fetch maintenance status')
      }
    },

    async toggleMaintenanceMode() {
      try {
        const endpoint = this.maintenance.enabled ? 'disable' : 'enable'
        await this.$axios.post(`/api/admin/maintenance/${endpoint}`, {
          message: this.maintenance.message,
          startTime: this.maintenance.startTime,
          endTime: this.maintenance.endTime,
          allowAdminAccess: this.maintenance.allowAdminAccess
        })
        this.maintenance.enabled = !this.maintenance.enabled
        this.$toast.success(`Maintenance mode ${this.maintenance.enabled ? 'enabled' : 'disabled'}`)
      } catch (error) {
        this.$toast.error('Failed to toggle maintenance mode')
      }
    },

    async createBackup() {
      this.isBackingUp = true
      try {
        await this.$axios.post('/api/admin/maintenance/backup')
        this.fetchMaintenanceStatus() // Refresh backup list
        this.$toast.success('Backup created successfully')
      } catch (error) {
        this.$toast.error('Failed to create backup')
      } finally {
        this.isBackingUp = false
      }
    },

    async downloadBackup(backup) {
      try {
        const response = await this.$axios.get(`/api/admin/maintenance/backup/${backup.id}/download`, {
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', backup.filename)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        this.$toast.error('Failed to download backup')
      }
    },

    async deleteBackup(backup) {
      if (!confirm('Are you sure you want to delete this backup?')) return

      try {
        await this.$axios.delete(`/api/admin/maintenance/backup/${backup.id}`)
        this.fetchMaintenanceStatus() // Refresh backup list
        this.$toast.success('Backup deleted successfully')
      } catch (error) {
        this.$toast.error('Failed to delete backup')
      }
    },

    async fetchSystemHealth() {
      try {
        const response = await this.$axios.get('/api/admin/maintenance/health')
        this.systemHealth = response.data
      } catch (error) {
        console.error('Failed to fetch system health:', error)
      }
    },

    startHealthMonitoring() {
      this.fetchSystemHealth()
      // Update system health every 30 seconds
      this.healthInterval = setInterval(this.fetchSystemHealth, 30000)
    }
  },

  mounted() {
    this.fetchMaintenanceStatus()
    this.startHealthMonitoring()
  },

  beforeUnmount() {
    if (this.healthInterval) {
      clearInterval(this.healthInterval)
    }
  }
}
</script>
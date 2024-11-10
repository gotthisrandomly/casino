<template>
  <div class="p-6">
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900">Security Settings</h2>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- 2FA Settings -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Two-Factor Authentication</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Enable 2FA for admin access</p>
              <p v-if="security.twoFactorEnabled" class="text-xs text-green-600">Currently enabled</p>
              <p v-else class="text-xs text-gray-400">Currently disabled</p>
            </div>
            <button
              @click="toggleTwoFactor"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-md',
                security.twoFactorEnabled
                  ? 'bg-red-100 text-red-600 hover:bg-red-200'
                  : 'bg-green-100 text-green-600 hover:bg-green-200'
              ]"
            >
              {{ security.twoFactorEnabled ? 'Disable' : 'Enable' }}
            </button>
          </div>
          
          <div v-if="showQRCode" class="mt-4">
            <p class="text-sm text-gray-500 mb-2">Scan this QR code with your authenticator app:</p>
            <img :src="qrCodeUrl" alt="2FA QR Code" class="mb-4">
            <div class="flex space-x-2">
              <input
                v-model="verificationCode"
                type="text"
                placeholder="Enter verification code"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
              <button
                @click="verifyTwoFactor"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
              >
                Verify
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- IP Whitelist -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">IP Whitelist</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Restrict admin access to specific IPs</p>
              <p v-if="security.ipWhitelistEnabled" class="text-xs text-green-600">Whitelist active</p>
              <p v-else class="text-xs text-gray-400">Whitelist inactive</p>
            </div>
            <button
              @click="toggleIpWhitelist"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-md',
                security.ipWhitelistEnabled
                  ? 'bg-red-100 text-red-600 hover:bg-red-200'
                  : 'bg-green-100 text-green-600 hover:bg-green-200'
              ]"
            >
              {{ security.ipWhitelistEnabled ? 'Disable' : 'Enable' }}
            </button>
          </div>
          
          <div v-if="security.ipWhitelistEnabled">
            <div v-for="(ip, index) in security.whitelistedIps" :key="index" class="flex items-center space-x-2 mb-2">
              <input
                v-model="security.whitelistedIps[index]"
                type="text"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
              <button
                @click="removeIp(index)"
                class="p-2 text-red-600 hover:text-red-800"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <button
              @click="addIp"
              class="mt-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200"
            >
              Add IP
            </button>
          </div>
        </div>
      </div>

      <!-- Password Policy -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Password Policy</h3>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Minimum Length</label>
              <input
                v-model="security.passwordPolicy.minLength"
                type="number"
                min="8"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Maximum Age (days)</label>
              <input
                v-model="security.passwordPolicy.maxAge"
                type="number"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
            </div>
          </div>
          
          <div class="space-y-2">
            <div class="flex items-center">
              <input
                v-model="security.passwordPolicy.requireUppercase"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label class="ml-2 block text-sm text-gray-700">Require uppercase letters</label>
            </div>
            <div class="flex items-center">
              <input
                v-model="security.passwordPolicy.requireNumbers"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label class="ml-2 block text-sm text-gray-700">Require numbers</label>
            </div>
            <div class="flex items-center">
              <input
                v-model="security.passwordPolicy.requireSpecialChars"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label class="ml-2 block text-sm text-gray-700">Require special characters</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Login Attempts -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Login Security</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Maximum Failed Attempts</label>
            <input
              v-model="security.maxLoginAttempts"
              type="number"
              min="1"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Lockout Duration (minutes)</label>
            <input
              v-model="security.lockoutDuration"
              type="number"
              min="1"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
          </div>
          <div class="flex items-center">
            <input
              v-model="security.notifyOnFailedLogin"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label class="ml-2 block text-sm text-gray-700">Notify admin on failed login attempts</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="mt-6">
      <button
        @click="saveSecuritySettings"
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Save Security Settings
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SecuritySettings',

  data() {
    return {
      security: {
        twoFactorEnabled: false,
        ipWhitelistEnabled: false,
        whitelistedIps: [],
        maxLoginAttempts: 5,
        lockoutDuration: 30,
        notifyOnFailedLogin: true,
        passwordPolicy: {
          minLength: 8,
          maxAge: 90,
          requireUppercase: true,
          requireNumbers: true,
          requireSpecialChars: true
        }
      },
      showQRCode: false,
      qrCodeUrl: '',
      verificationCode: ''
    }
  },

  methods: {
    async fetchSecuritySettings() {
      try {
        const response = await this.$axios.get('/api/admin/security')
        this.security = response.data
      } catch (error) {
        this.$toast.error('Failed to load security settings')
      }
    },

    async saveSecuritySettings() {
      try {
        await this.$axios.post('/api/admin/security', this.security)
        this.$toast.success('Security settings saved successfully')
      } catch (error) {
        this.$toast.error('Failed to save security settings')
      }
    },

    async toggleTwoFactor() {
      if (!this.security.twoFactorEnabled) {
        try {
          const response = await this.$axios.post('/api/admin/security/2fa/enable')
          this.qrCodeUrl = response.data.qrCode
          this.showQRCode = true
        } catch (error) {
          this.$toast.error('Failed to enable 2FA')
        }
      } else {
        try {
          await this.$axios.post('/api/admin/security/2fa/disable')
          this.security.twoFactorEnabled = false
          this.showQRCode = false
          this.$toast.success('2FA disabled successfully')
        } catch (error) {
          this.$toast.error('Failed to disable 2FA')
        }
      }
    },

    async verifyTwoFactor() {
      try {
        await this.$axios.post('/api/admin/security/2fa/verify', {
          code: this.verificationCode
        })
        this.security.twoFactorEnabled = true
        this.showQRCode = false
        this.verificationCode = ''
        this.$toast.success('2FA enabled successfully')
      } catch (error) {
        this.$toast.error('Invalid verification code')
      }
    },

    toggleIpWhitelist() {
      this.security.ipWhitelistEnabled = !this.security.ipWhitelistEnabled
    },

    addIp() {
      this.security.whitelistedIps.push('')
    },

    removeIp(index) {
      this.security.whitelistedIps.splice(index, 1)
    }
  },

  mounted() {
    this.fetchSecuritySettings()
  }
}
</script>
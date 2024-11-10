<template>
  <div class="p-6">
    <div class="mb-8">
      <h2 class="text-2xl font-bold text-gray-900">Analytics & Reports</h2>
    </div>

    <!-- Date Range Selector -->
    <div class="mb-6 flex space-x-4">
      <div class="relative">
        <input type="date" v-model="dateRange.start" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
      </div>
      <div class="relative">
        <input type="date" v-model="dateRange.end" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
      </div>
      <button @click="generateReport" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Generate Report
      </button>
      <button @click="exportToCSV" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Export to CSV
      </button>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Revenue Stats -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Total Revenue
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    ${{ formatMoney(stats.totalRevenue) }}
                  </div>
                  <div :class="[
                    'ml-2 text-sm',
                    stats.revenueChange >= 0 ? 'text-green-600' : 'text-red-600'
                  ]">
                    {{ stats.revenueChange >= 0 ? '↑' : '↓' }} {{ Math.abs(stats.revenueChange) }}%
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-900">
              View detailed report
            </a>
          </div>
        </div>
      </div>

      <!-- User Activity -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Active Users
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.activeUsers }}
                  </div>
                  <div :class="[
                    'ml-2 text-sm',
                    stats.userChange >= 0 ? 'text-green-600' : 'text-red-600'
                  ]">
                    {{ stats.userChange >= 0 ? '↑' : '↓' }} {{ Math.abs(stats.userChange) }}%
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-900">
              View user details
            </a>
          </div>
        </div>
      </div>

      <!-- Game Performance -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  Average RTP
                </dt>
                <dd class="flex items-baseline">
                  <div class="text-2xl font-semibold text-gray-900">
                    {{ stats.averageRTP }}%
                  </div>
                  <div :class="[
                    'ml-2 text-sm',
                    stats.rtpChange >= 0 ? 'text-green-600' : 'text-red-600'
                  ]">
                    {{ stats.rtpChange >= 0 ? '↑' : '↓' }} {{ Math.abs(stats.rtpChange) }}%
                  </div>
                </dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
          <div class="text-sm">
            <a href="#" class="font-medium text-blue-600 hover:text-blue-900">
              View game stats
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
      <!-- Revenue Chart -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Revenue Over Time</h3>
          <div class="mt-2 h-96">
            <canvas ref="revenueChart"></canvas>
          </div>
        </div>
      </div>

      <!-- User Activity Chart -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <h3 class="text-lg leading-6 font-medium text-gray-900">User Activity</h3>
          <div class="mt-2 h-96">
            <canvas ref="userChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Detailed Reports
        </h3>
        <div class="flex space-x-3">
          <select v-model="reportType" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
      </div>
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="overflow-hidden border-b border-gray-200">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Period
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Revenue
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Users
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Bets
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      RTP
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                      <span class="sr-only">Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="report in reports" :key="report.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ report.period }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      ${{ formatMoney(report.revenue) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ report.users }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ report.bets }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ report.rtp }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <a href="#" class="text-blue-600 hover:text-blue-900">Details</a>
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
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'Reports',

  data() {
    return {
      dateRange: {
        start: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        end: new Date().toISOString().split('T')[0]
      },
      reportType: 'daily',
      stats: {
        totalRevenue: 0,
        revenueChange: 0,
        activeUsers: 0,
        userChange: 0,
        averageRTP: 0,
        rtpChange: 0
      },
      reports: [],
      revenueChart: null,
      userChart: null
    }
  },

  methods: {
    async generateReport() {
      try {
        const response = await this.$axios.get('/api/admin/reports', {
          params: {
            start_date: this.dateRange.start,
            end_date: this.dateRange.end,
            type: this.reportType
          }
        })

        this.stats = response.data.stats
        this.reports = response.data.reports

        this.updateCharts()
      } catch (error) {
        console.error('Error generating report:', error)
        this.$toast.error('Failed to generate report')
      }
    },

    updateCharts() {
      // Destroy existing charts
      if (this.revenueChart) this.revenueChart.destroy()
      if (this.userChart) this.userChart.destroy()

      // Create revenue chart
      const revenueCtx = this.$refs.revenueChart.getContext('2d')
      this.revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: this.reports.map(r => r.period),
          datasets: [{
            label: 'Revenue',
            data: this.reports.map(r => r.revenue),
            borderColor: 'rgb(59, 130, 246)',
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      })

      // Create user activity chart
      const userCtx = this.$refs.userChart.getContext('2d')
      this.userChart = new Chart(userCtx, {
        type: 'bar',
        data: {
          labels: this.reports.map(r => r.period),
          datasets: [{
            label: 'Active Users',
            data: this.reports.map(r => r.users),
            backgroundColor: 'rgba(59, 130, 246, 0.5)'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      })
    },

    async exportToCSV() {
      try {
        const response = await this.$axios.get('/api/admin/reports/export', {
          params: {
            start_date: this.dateRange.start,
            end_date: this.dateRange.end,
            type: this.reportType
          },
          responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `casino-report-${this.dateRange.start}-${this.dateRange.end}.csv`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        console.error('Error exporting report:', error)
        this.$toast.error('Failed to export report')
      }
    },

    formatMoney(amount) {
      return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    }
  },

  mounted() {
    this.generateReport()
  },

  beforeUnmount() {
    if (this.revenueChart) this.revenueChart.destroy()
    if (this.userChart) this.userChart.destroy()
  }
}
</script>
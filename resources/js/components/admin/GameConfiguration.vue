<template>
  <div class="p-6">
    <div class="mb-8 flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">Game Configuration</h2>
      <button
        @click="showAddGameModal = true"
        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
      >
        Add New Game
      </button>
    </div>

    <!-- Game Categories -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Game Categories</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          v-for="category in categories"
          :key="category.id"
          class="relative group"
        >
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="font-medium text-gray-900">{{ category.name }}</p>
              <p class="text-sm text-gray-500">{{ category.gamesCount }} games</p>
            </div>
            <button
              @click="editCategory(category)"
              class="text-gray-400 hover:text-gray-500"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
          </div>
        </div>
        <div
          @click="showAddCategoryModal = true"
          class="flex items-center justify-center p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 hover:border-gray-400 cursor-pointer"
        >
          <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span class="ml-2 text-sm font-medium text-gray-600">Add Category</span>
        </div>
      </div>
    </div>

    <!-- Games List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Category</label>
            <select
              v-model="filters.category"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select
              v-model="filters.status"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
        </div>
        <div class="flex items-center">
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search games..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
          >
        </div>
      </div>

      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Game
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Category
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              RTP
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Min/Max Bet
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="game in games" :key="game.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img class="h-10 w-10 rounded-lg" :src="game.thumbnail" :alt="game.name">
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ game.name }}</div>
                  <div class="text-sm text-gray-500">ID: {{ game.id }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ game.category }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ game.rtp }}%</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">${{ game.minBet }} / ${{ game.maxBet }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                game.status === 'active' ? 'bg-green-100 text-green-800' :
                game.status === 'inactive' ? 'bg-red-100 text-red-800' :
                'bg-yellow-100 text-yellow-800'
              ]">
                {{ game.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="editGame(game)"
                class="text-blue-600 hover:text-blue-900 mr-4"
              >
                Configure
              </button>
              <button
                @click="toggleGameStatus(game)"
                :class="[
                  game.status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'
                ]"
              >
                {{ game.status === 'active' ? 'Disable' : 'Enable' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Game Modal -->
    <div v-if="showAddGameModal || editingGame" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <button
              @click="closeGameModal"
              class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ editingGame ? 'Edit Game' : 'Add New Game' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Game Name</label>
                  <input
                    v-model="gameForm.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Category</label>
                  <select
                    v-model="gameForm.categoryId"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">RTP (%)</label>
                    <input
                      v-model.number="gameForm.rtp"
                      type="number"
                      min="1"
                      max="100"
                      step="0.1"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Volatility</label>
                    <select
                      v-model="gameForm.volatility"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                    </select>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Min Bet ($)</label>
                    <input
                      v-model.number="gameForm.minBet"
                      type="number"
                      min="0"
                      step="0.1"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Max Bet ($)</label>
                    <input
                      v-model.number="gameForm.maxBet"
                      type="number"
                      min="0"
                      step="0.1"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    >
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Game Features</label>
                  <div class="mt-2 space-y-2">
                    <div v-for="feature in availableFeatures" :key="feature.id" class="flex items-center">
                      <input
                        :id="'feature-' + feature.id"
                        v-model="gameForm.features"
                        :value="feature.id"
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      >
                      <label :for="'feature-' + feature.id" class="ml-2 text-sm text-gray-700">
                        {{ feature.name }}
                      </label>
                    </div>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Thumbnail</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label
                          for="file-upload"
                          class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
                        >
                          <span>Upload a file</span>
                          <input id="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">
                        PNG, JPG, GIF up to 2MB
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              @click="saveGame"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ editingGame ? 'Save Changes' : 'Add Game' }}
            </button>
            <button
              @click="closeGameModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div v-if="showAddCategoryModal || editingCategory" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <button
              @click="closeCategoryModal"
              class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ editingCategory ? 'Edit Category' : 'Add Category' }}
              </h3>
              <div class="mt-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Category Name</label>
                  <input
                    v-model="categoryForm.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    v-model="categoryForm.description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Icon</label>
                  <select
                    v-model="categoryForm.icon"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="slots">Slots</option>
                    <option value="cards">Cards</option>
                    <option value="table">Table Games</option>
                    <option value="live">Live Casino</option>
                    <option value="instant">Instant Win</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
            <button
              @click="saveCategory"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ editingCategory ? 'Save Changes' : 'Add Category' }}
            </button>
            <button
              @click="closeCategoryModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GameConfiguration',

  data() {
    return {
      games: [],
      categories: [],
      filters: {
        search: '',
        category: '',
        status: ''
      },
      showAddGameModal: false,
      showAddCategoryModal: false,
      editingGame: null,
      editingCategory: null,
      gameForm: {
        name: '',
        categoryId: '',
        rtp: 96,
        volatility: 'medium',
        minBet: 0.10,
        maxBet: 100,
        features: [],
        thumbnail: null
      },
      categoryForm: {
        name: '',
        description: '',
        icon: 'slots'
      },
      availableFeatures: [
        { id: 'wild', name: 'Wild Symbols' },
        { id: 'scatter', name: 'Scatter Symbols' },
        { id: 'freespins', name: 'Free Spins' },
        { id: 'multiplier', name: 'Multipliers' },
        { id: 'bonus', name: 'Bonus Rounds' },
        { id: 'jackpot', name: 'Progressive Jackpot' }
      ]
    }
  },

  methods: {
    async fetchGames() {
      try {
        const response = await this.$axios.get('/api/admin/games', {
          params: this.filters
        })
        this.games = response.data
      } catch (error) {
        this.$toast.error('Failed to fetch games')
      }
    },

    async fetchCategories() {
      try {
        const response = await this.$axios.get('/api/admin/game-categories')
        this.categories = response.data
      } catch (error) {
        this.$toast.error('Failed to fetch categories')
      }
    },

    editGame(game) {
      this.editingGame = game
      this.gameForm = {
        ...game,
        categoryId: game.categoryId,
        features: [...game.features]
      }
      this.showAddGameModal = true
    },

    editCategory(category) {
      this.editingCategory = category
      this.categoryForm = { ...category }
      this.showAddCategoryModal = true
    },

    async toggleGameStatus(game) {
      try {
        const newStatus = game.status === 'active' ? 'inactive' : 'active'
        await this.$axios.patch(`/api/admin/games/${game.id}/status`, {
          status: newStatus
        })
        game.status = newStatus
        this.$toast.success(`Game ${newStatus === 'active' ? 'activated' : 'deactivated'} successfully`)
      } catch (error) {
        this.$toast.error('Failed to update game status')
      }
    },

    handleFileUpload(event) {
      const file = event.target.files[0]
      if (file) {
        this.gameForm.thumbnail = file
      }
    },

    async saveGame() {
      try {
        const formData = new FormData()
        Object.keys(this.gameForm).forEach(key => {
          if (key === 'features') {
            formData.append(key, JSON.stringify(this.gameForm[key]))
          } else {
            formData.append(key, this.gameForm[key])
          }
        })

        if (this.editingGame) {
          await this.$axios.post(`/api/admin/games/${this.editingGame.id}`, formData)
          this.$toast.success('Game updated successfully')
        } else {
          await this.$axios.post('/api/admin/games', formData)
          this.$toast.success('Game added successfully')
        }

        this.closeGameModal()
        this.fetchGames()
      } catch (error) {
        this.$toast.error('Failed to save game')
      }
    },

    async saveCategory() {
      try {
        if (this.editingCategory) {
          await this.$axios.put(`/api/admin/game-categories/${this.editingCategory.id}`, this.categoryForm)
          this.$toast.success('Category updated successfully')
        } else {
          await this.$axios.post('/api/admin/game-categories', this.categoryForm)
          this.$toast.success('Category added successfully')
        }

        this.closeCategoryModal()
        this.fetchCategories()
      } catch (error) {
        this.$toast.error('Failed to save category')
      }
    },

    closeGameModal() {
      this.showAddGameModal = false
      this.editingGame = null
      this.gameForm = {
        name: '',
        categoryId: '',
        rtp: 96,
        volatility: 'medium',
        minBet: 0.10,
        maxBet: 100,
        features: [],
        thumbnail: null
      }
    },

    closeCategoryModal() {
      this.showAddCategoryModal = false
      this.editingCategory = null
      this.categoryForm = {
        name: '',
        description: '',
        icon: 'slots'
      }
    }
  },

  watch: {
    filters: {
      deep: true,
      handler() {
        this.fetchGames()
      }
    }
  },

  mounted() {
    this.fetchGames()
    this.fetchCategories()
  }
}
</script>
<template>
  <div class="game-lobby">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Game Lobby</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="game in games" :key="game.id" class="game-card">
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img :src="game.thumbnail" :alt="game.name" class="w-full h-48 object-cover">
            <div class="p-4">
              <h3 class="text-xl font-semibold mb-2">{{ game.name }}</h3>
              <p class="text-gray-600 mb-4">{{ game.description }}</p>
              <div class="flex justify-between items-center mb-4">
                <span class="text-sm text-gray-500">
                  Min: ${{ game.min_bet }} | Max: ${{ game.max_bet }}
                </span>
                <span class="text-sm text-gray-500">RTP: {{ game.rtp }}%</span>
              </div>
              <div v-if="game.jackpots && game.jackpots.length" class="mb-4">
                <div v-for="jackpot in game.jackpots" :key="jackpot.id" 
                     class="text-green-600 font-semibold">
                  {{ jackpot.name }}: ${{ formatMoney(jackpot.current_amount) }}
                </div>
              </div>
              <button @click="playGame(game)" 
                      class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                Play Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GameLobby',
  
  data() {
    return {
      games: [],
    }
  },

  methods: {
    async fetchGames() {
      try {
        const response = await axios.get('/api/games')
        this.games = response.data.data
      } catch (error) {
        console.error('Error fetching games:', error)
      }
    },

    formatMoney(amount) {
      return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    },

    playGame(game) {
      this.$router.push({ name: 'game', params: { id: game.id }})
    }
  },

  mounted() {
    this.fetchGames()
  }
}
</script>

<style scoped>
.game-card {
  transition: transform 0.2s;
}

.game-card:hover {
  transform: translateY(-5px);
}
</style>
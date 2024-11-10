<template>
  <div class="game-player">
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ game.name }}</h1>
        <button @click="$router.push({ name: 'lobby' })" 
                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
          Back to Lobby
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Game Display -->
        <div class="lg:col-span-2">
          <div class="bg-gray-900 aspect-video rounded-lg overflow-hidden">
            <div class="game-container h-full w-full relative">
              <!-- Game Canvas -->
              <canvas ref="gameCanvas" class="w-full h-full"></canvas>
              
              <!-- Overlay for Bonus Rounds -->
              <div v-if="activeBonusRound" 
                   class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="text-center text-white">
                  <h3 class="text-2xl font-bold mb-4">Bonus Round!</h3>
                  <p>Free Spins Remaining: {{ activeBonusRound.spins_remaining }}</p>
                  <p>Multiplier: {{ activeBonusRound.multiplier }}x</p>
                  <p class="text-green-400">Total Bonus Win: ${{ formatMoney(activeBonusRound.total_win) }}</p>
                </div>
              </div>

              <!-- Win Celebration -->
              <div v-if="showWinCelebration" 
                   class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                  <h2 class="text-4xl font-bold text-yellow-400 mb-4">Big Win!</h2>
                  <p class="text-2xl text-white">{{ celebrationMessage }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Controls Panel -->
        <div class="bg-white p-6 rounded-lg shadow">
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Balance</h3>
            <p class="text-2xl font-bold">${{ formatMoney(balance) }}</p>
          </div>

          <!-- Jackpots -->
          <div v-if="game.jackpots && game.jackpots.length" class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Jackpots</h3>
            <div v-for="jackpot in game.jackpots" :key="jackpot.id" 
                 class="text-green-600 text-xl font-bold mb-2">
              {{ jackpot.name }}: ${{ formatMoney(jackpot.current_amount) }}
            </div>
          </div>

          <!-- Bet Controls -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Bet Amount</h3>
            <div class="flex items-center space-x-4">
              <button @click="decrementBet" 
                      class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">-</button>
              <input v-model="betAmount" type="number" 
                     class="w-24 text-center border rounded px-2 py-1"
                     :min="game.min_bet" :max="game.max_bet">
              <button @click="incrementBet"
                      class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">+</button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-4">
            <button @click="placeBet" 
                    :disabled="!canPlaceBet"
                    class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold
                           hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
              Spin
            </button>
            
            <button @click="autoPlay" 
                    :disabled="autoPlayActive || !canPlaceBet"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold
                           hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
              {{ autoPlayActive ? 'Auto Play Active' : 'Auto Play' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GamePlayer',
  
  props: {
    gameId: {
      type: [Number, String],
      required: true
    }
  },

  data() {
    return {
      game: null,
      balance: 0,
      betAmount: 0,
      autoPlayActive: false,
      gameSession: null,
      activeBonusRound: null,
      showWinCelebration: false,
      celebrationMessage: '',
      gameEngine: null,
    }
  },

  computed: {
    canPlaceBet() {
      return this.balance >= this.betAmount && 
             this.betAmount >= this.game?.min_bet && 
             this.betAmount <= this.game?.max_bet &&
             !this.showWinCelebration
    }
  },

  methods: {
    async initializeGame() {
      try {
        // Fetch game details
        const gameResponse = await axios.get(`/api/games/${this.gameId}`)
        this.game = gameResponse.data.data
        this.betAmount = this.game.min_bet

        // Initialize game session
        const sessionResponse = await axios.post(`/api/games/${this.gameId}/initialize`)
        this.gameSession = sessionResponse.data.data

        // Initialize game engine based on game type
        this.initGameEngine()

      } catch (error) {
        console.error('Error initializing game:', error)
      }
    },

    initGameEngine() {
      const canvas = this.$refs.gameCanvas
      const ctx = canvas.getContext('2d')
      
      // Initialize game engine based on game type
      switch(this.game.type) {
        case 'slot':
          this.gameEngine = new SlotEngine(canvas, ctx)
          break
        // Add other game types here
      }
    },

    async placeBet() {
      if (!this.canPlaceBet) return

      try {
        const response = await axios.post(`/api/sessions/${this.gameSession.session_id}/play`, {
          action: 'bet',
          amount: this.betAmount
        })

        const result = response.data.data
        this.balance = result.balance
        
        // Handle win
        if (result.win > 0) {
          this.showWin(result.win)
        }

        // Handle bonus round
        if (result.bonus_round) {
          this.activeBonusRound = result.bonus_round
        }

        // Handle jackpot win
        if (result.jackpot_win) {
          this.showJackpotWin(result.jackpot_win)
        }

        // Update game display
        this.gameEngine.animate(result)

      } catch (error) {
        console.error('Error placing bet:', error)
      }
    },

    showWin(amount) {
      this.showWinCelebration = true
      this.celebrationMessage = `You won $${this.formatMoney(amount)}!`
      
      setTimeout(() => {
        this.showWinCelebration = false
        this.celebrationMessage = ''
      }, 3000)
    },

    showJackpotWin(jackpot) {
      this.showWinCelebration = true
      this.celebrationMessage = `Congratulations! You won the ${jackpot.name}!\n$${this.formatMoney(jackpot.amount)}`
      
      setTimeout(() => {
        this.showWinCelebration = false
        this.celebrationMessage = ''
      }, 5000)
    },

    async autoPlay() {
      this.autoPlayActive = true
      
      while (this.autoPlayActive && this.canPlaceBet) {
        await this.placeBet()
        await new Promise(resolve => setTimeout(resolve, 2000))
      }
      
      this.autoPlayActive = false
    },

    stopAutoPlay() {
      this.autoPlayActive = false
    },

    incrementBet() {
      const newBet = this.betAmount + 1
      if (newBet <= this.game.max_bet) {
        this.betAmount = newBet
      }
    },

    decrementBet() {
      const newBet = this.betAmount - 1
      if (newBet >= this.game.min_bet) {
        this.betAmount = newBet
      }
    },

    formatMoney(amount) {
      return Number(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })
    },

    async fetchBalance() {
      try {
        const response = await axios.get('/api/user/balance')
        this.balance = response.data.data.balance
      } catch (error) {
        console.error('Error fetching balance:', error)
      }
    }
  },

  async mounted() {
    await this.initializeGame()
    await this.fetchBalance()

    // Set up event listeners
    window.addEventListener('beforeunload', this.stopAutoPlay)
  },

  beforeUnmount() {
    this.stopAutoPlay()
    window.removeEventListener('beforeunload', this.stopAutoPlay)
  }
}
</script>

<style scoped>
.game-container {
  background-color: #1a1a1a;
}

@keyframes celebrate {
  0% { transform: scale(0.8); opacity: 0; }
  50% { transform: scale(1.2); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}

.win-celebration {
  animation: celebrate 0.5s ease-out;
}
</style>
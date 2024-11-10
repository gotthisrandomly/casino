import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import GamePlayer from '../GamePlayer.vue'
import SoundManager from '../../utils/SoundManager'
import AnimationManager from '../../utils/AnimationManager'

jest.mock('../../utils/SoundManager')
jest.mock('../../utils/AnimationManager')

describe('GamePlayer', () => {
    let wrapper
    const mockGame = {
        id: 1,
        name: 'Test Slot',
        min_bet: 1.00,
        max_bet: 100.00,
        type: 'slot',
        jackpots: [
            {
                id: 1,
                name: 'Grand',
                current_amount: 10000.00
            }
        ]
    }

    beforeEach(() => {
        wrapper = mount(GamePlayer, {
            props: {
                gameId: 1
            },
            global: {
                mocks: {
                    $axios: {
                        get: jest.fn().mockResolvedValue({ data: { data: mockGame } }),
                        post: jest.fn().mockResolvedValue({ data: { data: { balance: 1000 } } })
                    }
                }
            }
        })
    })

    it('renders game name correctly', async () => {
        await nextTick()
        expect(wrapper.text()).toContain('Test Slot')
    })

    it('validates bet amount within limits', async () => {
        await nextTick()
        
        // Test below minimum
        await wrapper.setData({ betAmount: 0.50 })
        expect(wrapper.vm.canPlaceBet).toBe(false)

        // Test above maximum
        await wrapper.setData({ betAmount: 150.00 })
        expect(wrapper.vm.canPlaceBet).toBe(false)

        // Test valid amount
        await wrapper.setData({ betAmount: 50.00 })
        expect(wrapper.vm.canPlaceBet).toBe(true)
    })

    it('handles winning spin correctly', async () => {
        await nextTick()
        
        const mockWin = {
            data: {
                data: {
                    win: 100.00,
                    symbols: ['7', '7', '7'],
                    winningLines: [{ positions: [0, 1, 2] }],
                    balance: 1100.00
                }
            }
        }

        wrapper.vm.$axios.post.mockResolvedValueOnce(mockWin)
        
        await wrapper.vm.placeBet()
        
        expect(SoundManager.play).toHaveBeenCalledWith('win')
        expect(wrapper.emitted('balanceUpdate')).toBeTruthy()
        expect(wrapper.vm.showWinCelebration).toBe(true)
    })

    it('handles bonus round activation', async () => {
        await nextTick()
        
        const mockBonus = {
            data: {
                data: {
                    bonus_round: {
                        type: 'free_spins',
                        spins_remaining: 10,
                        multiplier: 2
                    }
                }
            }
        }

        wrapper.vm.$axios.post.mockResolvedValueOnce(mockBonus)
        
        await wrapper.vm.placeBet()
        
        expect(SoundManager.play).toHaveBeenCalledWith('bonus')
        expect(wrapper.vm.activeBonusRound).toBeTruthy()
        expect(wrapper.text()).toContain('Free Spins')
    })

    it('handles jackpot win', async () => {
        await nextTick()
        
        const mockJackpotWin = {
            data: {
                data: {
                    jackpot_win: {
                        name: 'Grand',
                        amount: 10000.00
                    }
                }
            }
        }

        wrapper.vm.$axios.post.mockResolvedValueOnce(mockJackpotWin)
        
        await wrapper.vm.placeBet()
        
        expect(SoundManager.play).toHaveBeenCalledWith('jackpot')
        expect(AnimationManager.prototype.createJackpotAnimation).toHaveBeenCalled()
        expect(wrapper.text()).toContain('Grand Jackpot')
    })

    it('handles network errors gracefully', async () => {
        await nextTick()
        
        wrapper.vm.$axios.post.mockRejectedValueOnce(new Error('Network Error'))
        
        await wrapper.vm.placeBet()
        
        expect(wrapper.vm.error).toBeTruthy()
        expect(wrapper.text()).toContain('Network Error')
    })

    it('updates bet amount correctly', async () => {
        await nextTick()
        
        await wrapper.find('.increase-bet').trigger('click')
        expect(wrapper.vm.betAmount).toBe(2.00)
        
        await wrapper.find('.decrease-bet').trigger('click')
        expect(wrapper.vm.betAmount).toBe(1.00)
    })

    it('disables autoplay when balance is insufficient', async () => {
        await nextTick()
        
        await wrapper.setData({ balance: 0 })
        await wrapper.find('.auto-play').trigger('click')
        
        expect(wrapper.vm.autoPlayActive).toBe(false)
    })

    it('stops autoplay on page leave', async () => {
        await nextTick()
        
        await wrapper.vm.startAutoPlay()
        wrapper.vm.$options.beforeUnmount[0].call(wrapper.vm)
        
        expect(wrapper.vm.autoPlayActive).toBe(false)
    })
})
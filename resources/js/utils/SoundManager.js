class SoundManager {
    constructor() {
        this.sounds = {};
        this.muted = false;
        this.volume = 1.0;
        
        // Preload sounds
        this.loadSounds();
    }

    loadSounds() {
        const soundFiles = {
            spin: '/sounds/spin.mp3',
            win: '/sounds/win.mp3',
            bigWin: '/sounds/big-win.mp3',
            jackpot: '/sounds/jackpot.mp3',
            click: '/sounds/click.mp3',
            bonus: '/sounds/bonus.mp3',
            coinDrop: '/sounds/coin-drop.mp3'
        };

        Object.entries(soundFiles).forEach(([key, path]) => {
            const audio = new Audio(path);
            audio.preload = 'auto';
            this.sounds[key] = audio;
        });
    }

    play(soundName) {
        if (this.muted || !this.sounds[soundName]) return;

        const sound = this.sounds[soundName];
        sound.volume = this.volume;
        sound.currentTime = 0;
        sound.play().catch(error => console.error('Sound play failed:', error));
    }

    setVolume(volume) {
        this.volume = Math.max(0, Math.min(1, volume));
        Object.values(this.sounds).forEach(sound => {
            sound.volume = this.volume;
        });
    }

    toggleMute() {
        this.muted = !this.muted;
        return this.muted;
    }

    stopAll() {
        Object.values(this.sounds).forEach(sound => {
            sound.pause();
            sound.currentTime = 0;
        });
    }
}

export default new SoundManager();
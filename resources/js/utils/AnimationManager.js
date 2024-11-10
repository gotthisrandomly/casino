class AnimationManager {
    constructor(canvas, ctx) {
        this.canvas = canvas;
        this.ctx = ctx;
        this.animations = new Map();
        this.particleSystem = new ParticleSystem(canvas, ctx);
    }

    addAnimation(name, frames, duration) {
        this.animations.set(name, {
            frames,
            duration,
            currentFrame: 0,
            startTime: null,
            isPlaying: false
        });
    }

    play(name, x, y, onComplete) {
        const animation = this.animations.get(name);
        if (!animation) return;

        animation.isPlaying = true;
        animation.startTime = performance.now();
        animation.currentFrame = 0;

        const animate = (timestamp) => {
            if (!animation.isPlaying) return;

            const progress = (timestamp - animation.startTime) / animation.duration;
            
            if (progress >= 1) {
                animation.isPlaying = false;
                if (onComplete) onComplete();
                return;
            }

            const frameIndex = Math.floor(progress * animation.frames.length);
            if (frameIndex !== animation.currentFrame) {
                animation.currentFrame = frameIndex;
                this.drawFrame(animation.frames[frameIndex], x, y);
            }

            requestAnimationFrame(animate);
        };

        requestAnimationFrame(animate);
    }

    drawFrame(frame, x, y) {
        this.ctx.clearRect(x, y, frame.width, frame.height);
        this.ctx.drawImage(frame, x, y);
    }

    createWinAnimation(amount, x, y) {
        const text = `$${amount.toLocaleString()}`;
        
        this.ctx.font = 'bold 24px Arial';
        this.ctx.fillStyle = '#FFD700';
        this.ctx.strokeStyle = '#000';
        this.ctx.lineWidth = 3;
        
        this.ctx.strokeText(text, x, y);
        this.ctx.fillText(text, x, y);
        
        this.particleSystem.emit({
            x,
            y,
            count: Math.min(amount / 10, 100),
            color: '#FFD700',
            spread: 1,
            speed: 5,
            lifetime: 1000
        });
    }

    createJackpotAnimation() {
        this.particleSystem.emit({
            x: this.canvas.width / 2,
            y: this.canvas.height / 2,
            count: 200,
            color: ['#FFD700', '#FFA500', '#FF4500'],
            spread: 2,
            speed: 8,
            lifetime: 2000
        });
    }
}

class ParticleSystem {
    constructor(canvas, ctx) {
        this.canvas = canvas;
        this.ctx = ctx;
        this.particles = [];
        this.running = false;
    }

    emit(config) {
        const {
            x,
            y,
            count,
            color,
            spread,
            speed,
            lifetime
        } = config;

        for (let i = 0; i < count; i++) {
            const angle = (Math.PI * 2 * i) / count;
            const velocity = {
                x: Math.cos(angle) * speed * (0.5 + Math.random() * spread),
                y: Math.sin(angle) * speed * (0.5 + Math.random() * spread)
            };

            this.particles.push({
                x,
                y,
                velocity,
                color: Array.isArray(color) ? color[Math.floor(Math.random() * color.length)] : color,
                life: lifetime,
                maxLife: lifetime,
                size: 2 + Math.random() * 3
            });
        }

        if (!this.running) {
            this.running = true;
            this.animate();
        }
    }

    animate() {
        if (!this.particles.length) {
            this.running = false;
            return;
        }

        this.ctx.save();
        this.particles.forEach((particle, index) => {
            particle.x += particle.velocity.x;
            particle.y += particle.velocity.y;
            particle.life -= 16.67; // Assuming 60fps

            if (particle.life <= 0) {
                this.particles.splice(index, 1);
                return;
            }

            const alpha = particle.life / particle.maxLife;
            this.ctx.fillStyle = particle.color;
            this.ctx.globalAlpha = alpha;
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fill();
        });
        this.ctx.restore();

        requestAnimationFrame(() => this.animate());
    }
}

export default AnimationManager;
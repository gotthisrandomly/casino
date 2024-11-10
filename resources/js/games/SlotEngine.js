export default class SlotEngine {
  constructor(canvas, ctx) {
    this.canvas = canvas;
    this.ctx = ctx;
    this.symbols = [];
    this.reels = 5;
    this.rows = 3;
    this.spinning = false;
    this.spinDuration = 2000; // 2 seconds
    this.symbolSize = 100;
    
    // Load symbols
    this.loadSymbols();
    
    // Initial render
    this.render();
  }

  async loadSymbols() {
    const symbolNames = ['7', 'bar', 'cherry', 'lemon', 'orange', 'plum', 'watermelon'];
    
    this.symbols = await Promise.all(
      symbolNames.map(async (name) => {
        const img = new Image();
        img.src = `/images/symbols/${name}.png`;
        await new Promise(resolve => img.onload = resolve);
        return { name, img };
      })
    );
  }

  render() {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    
    // Calculate dimensions
    const reelWidth = this.canvas.width / this.reels;
    const symbolSize = Math.min(reelWidth * 0.8, this.canvas.height / this.rows * 0.8);
    
    // Draw symbols
    for (let reel = 0; reel < this.reels; reel++) {
      for (let row = 0; row < this.rows; row++) {
        const x = reel * reelWidth + (reelWidth - symbolSize) / 2;
        const y = row * (this.canvas.height / this.rows) + (this.canvas.height / this.rows - symbolSize) / 2;
        
        if (this.symbols.length > 0) {
          const symbol = this.symbols[Math.floor(Math.random() * this.symbols.length)];
          this.ctx.drawImage(symbol.img, x, y, symbolSize, symbolSize);
        }
      }
    }
  }

  async animate(result) {
    if (this.spinning) return;
    this.spinning = true;

    // Animation frames for spinning effect
    const frames = 30;
    const frameDelay = this.spinDuration / frames;

    for (let frame = 0; frame < frames; frame++) {
      // Clear canvas
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      
      // Draw spinning effect
      const offset = Math.sin(frame / frames * Math.PI) * 20;
      
      // Draw symbols with offset
      this.render(offset);
      
      // Wait for next frame
      await new Promise(resolve => setTimeout(resolve, frameDelay));
    }

    // Show final result
    this.showResult(result);
    this.spinning = false;
  }

  showResult(result) {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    
    // Calculate dimensions
    const reelWidth = this.canvas.width / this.reels;
    const symbolSize = Math.min(reelWidth * 0.8, this.canvas.height / this.rows * 0.8);
    
    // Draw result symbols
    result.symbols.forEach((symbol, index) => {
      const reel = index % this.reels;
      const row = Math.floor(index / this.reels);
      
      const x = reel * reelWidth + (reelWidth - symbolSize) / 2;
      const y = row * (this.canvas.height / this.rows) + (this.canvas.height / this.rows - symbolSize) / 2;
      
      const symbolObj = this.symbols.find(s => s.name === symbol);
      if (symbolObj) {
        this.ctx.drawImage(symbolObj.img, x, y, symbolSize, symbolSize);
      }
    });

    // Highlight winning lines
    if (result.winningLines) {
      this.highlightWinningLines(result.winningLines);
    }
  }

  highlightWinningLines(winningLines) {
    const reelWidth = this.canvas.width / this.reels;
    const rowHeight = this.canvas.height / this.rows;

    this.ctx.strokeStyle = '#ffdd00';
    this.ctx.lineWidth = 3;

    winningLines.forEach(line => {
      this.ctx.beginPath();
      line.positions.forEach((pos, index) => {
        const x = (pos % this.reels) * reelWidth + reelWidth / 2;
        const y = Math.floor(pos / this.reels) * rowHeight + rowHeight / 2;
        
        if (index === 0) {
          this.ctx.moveTo(x, y);
        } else {
          this.ctx.lineTo(x, y);
        }
      });
      this.ctx.stroke();
    });
  }

  resize() {
    // Handle canvas resize
    this.canvas.width = this.canvas.clientWidth;
    this.canvas.height = this.canvas.clientHeight;
    this.render();
  }
}
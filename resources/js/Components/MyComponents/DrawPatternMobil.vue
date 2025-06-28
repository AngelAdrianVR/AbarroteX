<template>
  <div class="pattern-container">
    <p class="text-center text-sm">Patrón</p>
    <canvas ref="canvas" :width="canvasSize" :height="canvasSize"></canvas>
  </div>
</template>

<script>
export default {
  data() {
    return {
      canvasSize: this.size,
      gridSize: 3,
      pointRadius: 10,
    };
  },
  mounted() {
    this.drawPattern();
  },
  props:{
    points: Array,
    size: {
      type: Number,
      default: 85,
    },
  },
  methods: {
    getCoordinates(index) {
      const padding = this.canvasSize * 0.2;
      const size = this.canvasSize - padding * 2;
      const step = size / (this.gridSize - 1);

      const row = Math.floor(index / this.gridSize);
      const col = index % this.gridSize;

      const x = padding + col * step;
      const y = padding + row * step;

      return { x, y };
    },
    drawPattern() {
      const canvas = this.$refs.canvas;
      const ctx = canvas.getContext('2d');

      // Limpiar canvas
      ctx.clearRect(0, 0, this.canvasSize, this.canvasSize);
      ctx.fillStyle = '#fff';
      ctx.fillRect(0, 0, this.canvasSize, this.canvasSize);

      // === 1. Dibujar líneas del patrón primero ===
      ctx.strokeStyle = '#9b59b6';
      ctx.lineWidth = 5;
      ctx.beginPath();

      this.points.forEach((pointIndex, i) => {
        const { x, y } = this.getCoordinates(pointIndex);
        if (i === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      });
      ctx.stroke();

      // === 2. Dibujar todos los puntos ===
      for (let i = 0; i < 9; i++) {
        const { x, y } = this.getCoordinates(i);
        ctx.beginPath();
        ctx.fillStyle = '#444';
        ctx.arc(x, y, this.pointRadius, 0, 2 * Math.PI);
        ctx.fill();
      }

      // === 3. Dibujar los números solo en los puntos del patrón ===
      this.points.forEach((pointIndex, i) => {
        const { x, y } = this.getCoordinates(pointIndex);
        ctx.fillStyle = '#fff';
        ctx.font = '10px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(i + 1, x, y);
      });
    },
  },
};
</script>

<style scoped>
.pattern-container {
  width: 150px;
  margin: auto;
  border: 1px solid #ddd;
  border-radius: 12px;
  padding: 2px;
  background-color: #fff;
}
canvas {
  display: block;
  margin: auto;
}
</style>

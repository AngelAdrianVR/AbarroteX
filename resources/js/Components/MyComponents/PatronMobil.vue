<template>
  <div class="space-y-2 max-w-sm">
    <h3 class="text-lg font-semibold">Método de desbloqueo</h3>

    <el-radio-group @change="resetPattern" v-model="selectedMethod">
      <el-radio-button label="pattern">Patrón</el-radio-button>
      <el-radio-button label="password">Contraseña</el-radio-button>
    </el-radio-group>

    <!-- Password Input -->
    <div v-if="selectedMethod === 'password'">
      <el-input
        @input="syncUnlockPassword"
        v-model="password"
        placeholder="Escribe la contraseña"
        show-password
        autocomplete="off"
        class="mt-2"
      />
    </div>

    <!-- Pattern Lock -->
    <div v-if="selectedMethod === 'pattern'" class="p-4 border rounded-lg">
      <p class="text-sm font-medium mb-3">Dibujar patrón (Selecciona punto por punto)</p>
      <div class="size-28 relative mx-auto">
        <div
          v-for="(point, index) in 9"
          :key="index"
          class="size-6 rounded-full border-2 border-gray-600 mx-auto my-auto relative cursor-pointer z-50"
          :class="{ 'bg-gray-500 !border-gray-700': selectedPoints.includes(index) }"
          @click="selectPoint(index, $event)"
          :style="getPointStyle(index)"
        >
          <span
            v-if="selectedPoints.includes(index)"
            class="absolute text-base text-white font-bold inset-0 flex items-center justify-center"
          >{{ selectedPoints.indexOf(index) + 1 }}</span>
        </div>

        <!-- SVG para las líneas -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 100 100">
          <line
            v-for="(line, i) in lines"
            :key="i"
            :x1="line.x1"
            :y1="line.y1"
            :x2="line.x2"
            :y2="line.y2"
            stroke="#8C4FF5"
            stroke-width="2"
          />
        </svg>
      </div>

      <div class="text-xs text-gray-500 mt-4 text-center">
        <el-button type="danger" size="small" @click="resetPattern">
          Limpiar patrón
        </el-button>
        <!-- Pattern: [{{ selectedPoints.join(', ') }}] -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedMethod: "pattern",
      password: "",
      selectedPoints: [],
      lines: [],
    };
  },
  props:{
    initialData: Object
  },
  emits:['syncPattern', 'syncPassword'],
  methods: {
    getPointCoords(index) {
      const col = index % 3;
      const row = Math.floor(index / 3);
      const step = 33; // espacio entre puntos (en SVG)
      return {
        x: col * step + step / 2,
        y: row * step + step / 2,
      };
    },
    getPointStyle(index) {
      const row = Math.floor(index / 3);
      const col = index % 3;
      return {
        position: 'absolute',
        left: `${col * 33 + 12}px`,
        top: `${row * 33 + 12}px`,
      };
    },
    resetPattern() {
      this.selectedPoints = [];
      this.lines = [];
      this.password = ''
      this.$emit('syncPattern', this.selectedPoints);
      this.$emit('syncPassword', this.password);
    },
    selectPoint(index, event) {
      if (!this.selectedPoints.includes(index)) {
        const coords = this.getPointCoords(index);

        if (this.selectedPoints.length > 0) {
          const lastIndex = this.selectedPoints[this.selectedPoints.length - 1];
          const lastCoords = this.getPointCoords(lastIndex);
          this.lines.push({
            x1: lastCoords.x,
            y1: lastCoords.y,
            x2: coords.x,
            y2: coords.y,
          });
        }

        this.selectedPoints.push(index);
        this.$emit('syncPattern', this.selectedPoints);
      }
    },
    syncUnlockPassword() {
      this.$emit('syncPassword', this.password);
    }
  },
  mounted() {
    const savedPassword = this.initialData?.unlockPassword;
    const savedPattern = this.initialData?.unlockPattern || [];

    if (savedPattern.length) {
      savedPattern.forEach((pointStr) => {
        const point = parseInt(pointStr);
        this.selectPoint(point);
      });
    } else if ( savedPassword ) {
      this.selectedMethod = "password";
      this.password = savedPassword;
    }
  }
};
</script>

<style scoped>
.grid {
  position: relative;
}
</style>

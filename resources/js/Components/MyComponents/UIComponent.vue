<template>
  <div class="w-full max-w-md mx-auto mt-10 bg-gray-800 p-10 rounded-xl">
    <div 
      :class="progress !== 100 ? 'opacity-0 -translate-y-5' : 'opacity-100'" 
      class="flex items-center justify-center text-white font-bold text-lg mb-2">
      <div class="flex items-center justify-center size-10 border-gray-600 border rounded-full transition-all ease-linear duration-500">
        <svg class="w-10 h-10 text-green-400 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
    </div>
    <div class="relative w-full h-6 bg-gray-700/50 text-[#a5fc6a] rounded-full overflow-hidden shadow-lg">
      <div
        class="absolute top-0 left-0 h-full rounded-full transition-all duration-500 ease-in-out shadow-md"
        :style="{
          width: progress + '%',
          backgroundColor: getColor(progress),
          boxShadow: '0 0 10px ' + getColor(progress)
        }"
      ></div>
    </div>
    <div class="text-center mt-2 text-white font-bold relative">
      <span class="tooltip">{{ progress }}%</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const progress = ref(0);

const getColor = (percent) => {
  if (percent > 95) return '#5ee295'; // Verde
  if (percent > 70) return '#a5fc6a'; // Naranja
  if (percent > 50) return '#ffff00'; // Amarillo
  if (percent > 25) return '#f8b15f'; // naranja
  return '#ff0000'; // Rojo
};

onMounted(() => {
  const interval = setInterval(() => {
    if (progress.value < 100) {
      progress.value += 1;
    } else {
      clearInterval(interval);
    }
  }, 100);
});
</script>

<style>
.tooltip {
  position: relative;
  display: inline-block;
  cursor: pointer;
}
.tooltip::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 150%;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s;
}
.tooltip:hover::after {
  opacity: 1;
}
</style>

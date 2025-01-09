<template>
    <Head title="¡Pago exitoso!" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-r from-green-400 to-blue-500 text-white">
    <div class="bg-white rounded-lg shadow-lg p-8 transform transition-all duration-300 hover:scale-105">
      <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">¡Pago Completado!</h1>
      <p class="mb-4 text-center text-gray-700">Gracias por tu compra. Tu pago ha sido procesado exitosamente.</p>
      
      <!-- Icono de Check con animación -->
      <div class="flex items-center justify-center mb-4 animate-fadeIn">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="size-16 text-green-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5 5L20 7" />
        </svg>
      </div>

        <div class="flex justify-center">
            <a href="/dashboard" class="mt-4 inline-block rounded-full px-6 py-3 bg-primary transition duration-300">Regresar al Inicio</a>
        </div>
    </div>
  </div>
</template>

<script>
import confetti from "canvas-confetti";
import { Head } from '@inertiajs/vue3';

export default {
data() {
    return {

    }
},
components:{
Head
},
props:{
    // customer: Object
},
methods: {
    launchConfetti() {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 },
        });
    },
},
mounted() {
    this.launchConfetti();
},
}
</script>

<style scoped>
/* Animación para el icono de check */
@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeIn {
  animation: fadeIn 1s ease forwards;
}
</style>

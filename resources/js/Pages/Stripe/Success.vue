<template>
    <Head title="¡Pago exitoso!" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-r from-green-400 to-blue-500 text-white">
    <div class="bg-white rounded-lg shadow-lg p-8 transform transition-all duration-300">
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

      <!-- Boton de regresar al inicio -->
      <div class="flex justify-center">
        <button
          @click="$inertia.visit('/dashboard')"
          class="bg-gray-100 text-center w-48 rounded-full h-14 relative text-black text-xl font-semibold group"
          type="button"
        >
          <div
            class="bg-primary text-white rounded-full h-12 w-1/4 flex items-center justify-center absolute left-1 top-[4px] group-hover:w-[184px] z-10 duration-500"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
          </div>
          <p class="translate-x-2">Regresar</p>
        </button>
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

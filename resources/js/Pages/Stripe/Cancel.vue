<template>
<Head title="Payment canceled" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <figure class="mx-auto w-1/2 md:w-1/4 lg:w-1/5 xl:w-1/6">
        <img class="" src="@/../../public/images/black_logo.png" alt="logo">
    </figure>
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-4xl text-center">
      <div class="text-primary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9V3h4v6h5l-7 8-7-8h5z" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Pago cancelado</h2>
      <p class="text-gray-600 mb-6">Tu pago ha sido cancelado. Puedes reintentar el pago si así lo deseas.</p>
      <button
        @click="$inertia.get('/dashboard')"
        class="bg-primary text-white px-6 py-3 rounded-lg shadow-md hover:bg-orange-500 transition-colors duration-300"
      >
        Volver al inicio
      </button>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';

export default {
  components:{
      Head
  },
  // name: 'PaymentCancelled',
};
</script>

<style scoped>
/* Tailwind ya maneja los estilos principales */
</style>

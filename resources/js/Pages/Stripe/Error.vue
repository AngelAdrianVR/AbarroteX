<template>
<Head title="Payment error" />
  <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <figure class="mx-auto w-1/2 md:w-1/4 lg:w-1/5 xl:w-1/6">
        <img class="" src="@/../../public/images/black_logo.png" alt="logo">
    </figure>
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-4xl text-center">
      <div class="text-red-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Error al completar el pago</h2>
      <p class="text-gray-600 mb-6">Hubo un error al completar el pago. Por favor intente de nuevo o contacte con soporte si el problema persiste.</p>
      <div class="flex justify-between mx-auto lg:w-1/2">
        <a href="https://api.whatsapp.com/send?phone=523321705650&text=Hola!%20tuve%20un%20problema%20con%20el%20pago:" target="_blank"
            @click="contactSupport"
            class="mt-4 text-red-500 underline hover:text-red-600 transition-colors duration-300"
        >
            Contactar con soporte
        </a>
        <button
            @click="retryPayment"
            class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-red-600 transition-colors duration-300"
        >
            Reintentar pago
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';

export default {
    components:{
        Head
    },

  // name: 'PaymentError',
  methods: {
    retryPayment() {
      this.$inertia.get('user/profile');
    },
    contactSupport() {
      // Lógica para contactar al soporte
      console.log('Contactando soporte...');
    },
  },
};
</script>


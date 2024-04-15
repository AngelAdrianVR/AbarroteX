<template>
  <button v-if="route == null" @click="goBack" class="flex justify-center items-center rounded-full size-6 focus:outline-none hover-3dbuttom bg-[#373737]">
        <i class="fa-solid fa-angle-left text-white text-xs"></i>
  </button>

  <button v-else @click="goToRoute" class="flex justify-center items-center rounded-full size-6 focus:outline-none hover-3dbuttom bg-[#373737]">
        <i class="fa-solid fa-angle-left text-white text-xs"></i>
  </button>
</template>

<script>
export default {
  props:{
    route: {
      type: String,
      default: null
    }
  },
  methods: {
    goBack() {
      // Puedes personalizar esta lógica según tus necesidades
      // En este ejemplo, se utiliza la función history.back() para retroceder una página
      window.history.back();
    },
    goToRoute() {
      this.$inertia.visit(route(this.route));      
    },
  },
};
</script>

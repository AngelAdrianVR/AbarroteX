<template>
  <AppLayout title="Catálogo base">
    <div class="px-2 lg:px-10 py-7">
      <Back />
      <!-- header botones -->
      <div class="flex justify-between items-center mb-4">
        <h1 class="font-bold text-lg">Catálogo base</h1>

        <!-- Tabs -->
        <div class="flex items-center space-x-2"></div>
      </div>

      <p>
        Selecciona todos los productos disponibles en tu tienda, agrégalos a tu inventario
        y personalízalos según tus necesidades. Puedes editarlos en cualquier momento,
        añadir información como el precio de venta y las existencias, ¡y estarán listos
        para vender!
      </p>

      <section class="mt-10">
        <el-transfer
          v-model="products"
          filterable
          filter-placeholder="State Abbreviations"
          :data="states"
        />
      </section>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
  data() {
    return {
      products: [],
      states: [
        {
          key: 1,
          label: 'California',
          initial: 'CA'
        },
        {
          key: 2,
          label: 'Ilinois',
          initial: 'Il'
        },

      ]
    };
  },
  components: {
    AppLayout,
    Back
  },
  props: {},
  methods: {},
};
</script>

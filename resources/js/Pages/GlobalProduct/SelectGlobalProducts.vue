<template>
  <AppLayout title="Catálogo base">
    <div class="px-2 lg:px-20 py-7">
      <!-- tabs -->
      <div class="flex justify-between mb-5">
        <Back />
        <div class="flex items-center justify-center">
            <button @click="$inertia.get(route('products.index'))" class="text-primary bg-primarylight rounded-full px-6 py-1 z-0">Mis productos</button>
            <button class="text-white bg-primary rounded-full px-5 py-1 z-10 -ml-5 cursor-default">Catálogo base</button>
        </div>
        <span></span>
      </div>
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

      <!-- transfer -->
      <section class="mt-10 grid lg:grid-cols-2 gap-3">

        <div class="mx-auto">
          <el-transfer
            v-model="products"
            filterable
            filter-placeholder="Buscar producto"
            :titles="['Catálogo base', 'Mis productos']"
            :data="globalProducts"
            @left-check-change="handleLeftCheckChange"
          >
            <template #left-footer>
                <!-- boton filtro izquierdo -->
                <button @click.stop="showLeftFilter = !showLeftFilter" class="rounded-full border border-[#c4c4c4] size-7 flex items-center justify-center mx-auto my-2">
                  <svg width="15" height="15" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8826_331" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                    <rect width="14" height="14" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8826_331)">
                      <path d="M5.83333 10.5V9.33333H8.16667V10.5H5.83333ZM3.5 7.58333V6.41667H10.5V7.58333H3.5ZM1.75 4.66667V3.5H12.25V4.66667H1.75Z" fill="#999999"/>
                    </g>
                  </svg>
                </button>
            </template>
            <template #right-footer>
                <!-- boton filtro derecho -->
                <!-- <button @click.stop="showRightFilter = !showRightFilter" class="rounded-full border border-[#c4c4c4] size-7 flex items-center justify-center mx-auto my-2">
                  <svg width="15" height="15" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8826_331" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                    <rect width="14" height="14" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8826_331)">
                      <path d="M5.83333 10.5V9.33333H8.16667V10.5H5.83333ZM3.5 7.58333V6.41667H10.5V7.58333H3.5ZM1.75 4.66667V3.5H12.25V4.66667H1.75Z" fill="#999999"/>
                    </g>
                  </svg>
                </button> -->
            </template>
          </el-transfer>
        </div>

        <!-- vista previa de producto -->
        <div v-if="productInfo" class="rounded-lg border border-[#D9D9D9] md:w-[500px] h-[400px] mx-auto">
          <p class="border-b border-[#D9D9D9] font-bold px-5 py-2">Vista previa del producto</p>
          <Loading v-if="loadingProduct" class="mt-28" />
          <div class="py-3 px-7 h-full w-full" v-else>
            <figure class="h-1/2">
              <img class="h-full mx-auto rounded-md" :src="productInfo?.media[0]?.original_url" alt="">
            </figure>

            <div class="mt-7 text-sm flex">
              <div class="space-y-1 w-24">
                <p>Nombre:</p>
                <p>Categoría:</p>
                <p>Marca:</p>
                <p>Precio:</p>
                <p>Código:</p>
              </div>
              <div class="space-y-1 font-bold">
                <p>{{ productInfo?.name ?? '-' }}</p>
                <p>{{ productInfo?.category?.name ?? '-' }}</p>
                <p>{{ productInfo?.brand ?? '-' }}</p>
                <p>${{ productInfo?.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                <p>{{ productInfo?.code ?? 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Boton para transferir los poductos -->
        <div class="col-span-full text-center mt-7">
          <PrimaryButton :disabled="!products.length" @click="searchSales">Transferir productos</PrimaryButton>
        </div>
      </section>
    </div>

    <!-- ventana de filtro izquierdo -->
    <div v-if="showLeftFilter"
        class="absolute bottom-24 left-5 border border[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-50 w-80">
        <div>
            <InputLabel value="Categoría" class="ml-3 mb-1" />
            <el-select v-model="leftFilterCategory" clearable filterable placeholder="Seleccione"
                no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in categories" :key="item" :label="item.name"
                    :value="item.id" />
            </el-select>
        </div>
        <div class="my-3">
            <InputLabel value="Marca" class="ml-3 mb-1" />
            <el-select v-model="leftFilterBrand" clearable filterable placeholder="Seleccione"
                no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in brands" :key="item" :label="item"
                    :value="item" />
            </el-select>
        </div>
        <div class="flex space-x-2">
          <PrimaryButton @click="filterGlobalProducts" class="!py-1">Aplicar</PrimaryButton>
          <CancelButton @click="showLeftFilter = false" class="!py-1">Cancelar</CancelButton>
        </div>
      </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
  data() {
    return {
      products: [],
      globalProducts: [],
      productInfo: null,
      loadingProduct: false,
      showLeftFilter: false, //muestra filtro izquierdo
      showRightFilter: false, //muestra filtro derecho
      leftFilterCategory: null, //información para fltrar por categoría izquierdo
      leftFilterBrand: null, //información para fltrar por marca izquierdo
      brands: ['La costeña', 'Coca-cola', 'Otra'],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    InputLabel,
    Loading,
    Back
  },
  props: {
    global_products: Array,
    my_products: Array,
    categories: Array,
  },
  methods: {
    myProductsFormater() {
      this.products = this.my_products.map(product => ({
        key: product.id,
        label: product.name
      }));
    },
    globalProductsFormater() {
      this.globalProducts = this.global_products.map(product => ({
        key: product.id,
        label: product.name
      }));
    },
    handleLeftCheckChange(checkedProducts) {
      // Verificar si hay al menos un elemento seleccionado
      if (checkedProducts.length > 0) {
        const lastSelection = checkedProducts[checkedProducts.length - 1]; // Obtener el último elemento del arreglo

        this.getLeftProductCheckedInfo(lastSelection);
      }
    },
    // recupera la informacion del producto seleccionado
    async getLeftProductCheckedInfo(productId) {
      try {
        this.loadingProduct = true;
        const response = await axios.get(route('global-products.fetch-info-product', productId));

        if (response.status === 200) {
          this.productInfo = response.data.item;
        }
      } catch (error) {
        console.log(error);
        this.$notify({
            title: "Advertencia",
            message: "No se pudo cargar la información del producto",
            type: "warning",
        });
      } finally {
        this.loadingProduct = false;
      }
    },
  },
  mounted() {
    // this.myProductsFormater();
    this.globalProductsFormater(); //formatea los productos globales para que el transfer los renderice
  }
};
</script>

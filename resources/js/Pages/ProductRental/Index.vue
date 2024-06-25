<template>
  <AppLayout title="Renta de productos">
    <div class="px-2 lg:px-10 py-7">
      <h1 class="font-bold">Renta de productos</h1>

      <div class="md:flex justify-between mt-3">
        <article class="flex items-center space-x-5 lg:w-1/3">
          <div class="lg:w-full relative">
            <input
              v-model="searchQuery"
              @keydown.enter="searchProductRental"
              class="input w-full pl-9"
              placeholder="Buscar por cliente o producto"
              type="search"
            />
            <i
              class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"
            ></i>
          </div>
          <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
            {{ searchedWord }}
          </el-tag>
        </article>
        <div class="my-4 md:my-0 flex items-center justify-end space-x-3">
          <PrimaryButton @click="$inertia.get(route('services.create'))"
            >Registrar renta de producto</PrimaryButton
          >
        </div>
      </div>

      <!-- Tabla de servicios -->
      <div class="mt-9">
        <!-- <p v-if="localProductRentals.length" class="text-gray66 text-[11px]">
          {{ localProductRentals.length }} de {{ total_product_rentals }} elementos
        </p> -->
        <!-- <ProductRentalsTable :products="localProductRentals" /> -->
        <!-- <p v-if="localProductRentals.length" class="text-gray66 text-[11px] mt-3">
          {{ localProductRentals.length }} de {{ total_product_rentals }} elementos
        </p> -->
        <p v-if="loadingItems" class="text-xs my-4 text-center">
          Cargando
          <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </p>
        <button
          v-else-if="
            total_product_rentals > 30 &&
            localProductRentals.length < total_product_rentals &&
            localProductRentals.length
          "
          @click="fetchItemsByPage"
          class="w-full text-primary my-4 text-xs mx-auto underline ml-6"
        >
          Cargar más elementos
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import ProductRentalsTable from "@/Components/MyComponents/ProductRental/ProductRentalsTable.vue";
import axios from "axios";

export default {
  data() {
    return {
      searchQuery: null, //buscador de producto en renta.
      searchedWord: null, //palabra con la que se hizo la última busqueda.
      localProductRentals: this.product_rentals, //arreglo local de productos rentados
      loadingItems: false, //cestado de carga al recuperar mas items en la tabla.
      loading: false, //estado de carga cuando se busca a un producto en renta por medio del buscador
      currentPage: 1, //para paginación
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    ProductRentalsTable,
    ThirthButton,
  },
  props: {
    product_rentals: Array,
    total_product_rentals: Number,
  },
  methods: {
    async searchProductRental() {
      this.loading = true;
      if (this.searchQuery != "") {
        try {
          const response = await axios.get(route("product-rentals.search"), {
            params: { query: this.searchQuery },
          });
          if (response.status == 200) {
            this.localProductRentals = response.data.items;
            this.searchedWord = this.searchQuery;
            this.searchQuery = null;
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
        }
      } else {
        this.localProductRentals = this.product_rentals;
      }
    },
    async fetchItemsByPage() {
      try {
        this.loadingItems = true;
        const response = await axios.get(route("product-rentals.get-by-page", this.currentPage));

        if (response.status === 200) {
          this.localProductRentals = [...this.localProductRentals, ...response.data.items];
          this.currentPage++;

          // Actualiza la URL con la pagina
          if (this.currentPage > 1) {
            const currentURL = new URL(window.location.href);
            currentURL.searchParams.set("page", this.currentPage);
            window.history.replaceState({}, document.title, currentURL.href);
          }
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingItems = false;
      }
    },
    closedTag() {
      this.localProductRentals = this.product_rentals;
      this.searchedWord = null;
    },
  },
};
</script>

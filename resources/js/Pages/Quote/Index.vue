<template>
  <AppLayout title="Cotizaciones">
    <div class="px-2 lg:px-10 py-7">
      <h1>Cotizaciones</h1>

      <div class="md:flex justify-between mt-3">
        <article class="flex items-center flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0 lg:w-1/3">
          <div class="w-full relative">
            <input v-model="searchQuery" @keydown.enter="searchQuotes" class="input pl-9"
              placeholder="Buscar cotización por nombre de contacto o folio" type="search" />
            <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
          </div>
          <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
            {{ searchedWord }}
          </el-tag>
        </article>
        <div v-if="this.$page.props.auth.user.permissions.includes('Crear cotizaciones')" class="my-4 md:my-0 flex items-center justify-end space-x-3">
          <PrimaryButton @click="$inertia.get(route('quotes.create'))">Crear cotización</PrimaryButton>
        </div>
      </div>

      <!-- Tabla de servicios -->
      <div class="mt-9">
        <p v-if="localQuotes.length" class="text-gray66 text-[11px]">
          {{ localQuotes.length }} de {{ total_quotes }} elementos
        </p>
        <QuotesTable :quotes="localQuotes" />
        <p v-if="localQuotes.length" class="text-gray66 text-[11px] mt-3">
          {{ localQuotes.length }} de {{ total_quotes }} elementos
        </p>
        <p v-if="loadingItems" class="text-xs my-4 text-center">
          Cargando
          <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </p>
        <button v-else-if="
          total_quotes > 20 &&
          localQuotes.length < total_quotes &&
          localQuotes.length
        " @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">
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
import QuotesTable from "@/Components/MyComponents/Quote/QuotesTable.vue";
import axios from "axios";

export default {
  data() {
    return {
      searchQuery: null, //buscador de servicio.
      searchedWord: null, //palabra con la que se hizo la última busqueda.
      localQuotes: this.quotes, //arreglo local de cotizaciones
      loadingItems: false, //cestado de carga al recuperar mas items en la tabla.
      loading: false, //estado de carga cuando se busca a un servicio por medio del buscador
      currentPage: 1, //para paginación
    }
  },
  components: {
    AppLayout,
    PrimaryButton,
    QuotesTable,
    ThirthButton,
  },
  props: {
    quotes: Array,
    total_quotes: Number,
  },
  methods: {
    async searchQuotes() {
      this.loading = true;
      if (this.searchQuery != "") {
        try {
          const response = await axios.get(route("quotes.search"), {
            params: { query: this.searchQuery },
          });
          if (response.status == 200) {
            this.localQuotes = response.data.items;
            this.searchedWord = this.searchQuery;
            this.searchQuery = null;
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
        }
      } else {
        this.localQuotes = this.quotes;
      }
    },
    async fetchItemsByPage() {
      try {
        this.loadingItems = true;
        const response = await axios.get(route("quotes.get-by-page", this.currentPage));

        if (response.status === 200) {
          this.localQuotes = [...this.localQuotes, ...response.data.items];
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
      this.localQuotes = this.quotes;
      this.searchedWord = null;
    },
  }
}
</script>

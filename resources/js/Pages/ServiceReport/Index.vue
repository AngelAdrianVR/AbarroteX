<template>
  <AppLayout title="Reportes de Servicio">
    <div class="px-2 lg:px-10 py-7">
      <h1>Reportes de Servicio</h1>
      <div class="md:flex justify-between mt-3">
        <article class="flex items-center flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0 lg:w-1/3">
          <div class="w-full relative">
            <input v-model="searchQuery" @keydown.enter="searchReports" class="input w-full pl-9"
              placeholder="Buscar por solicitante, responsable o quien recibio" type="search" />
            <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
          </div>
          <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
            {{ searchedWord }}
          </el-tag>
        </article>
        <div v-if="canCreate" class="my-4 md:my-0 flex items-center justify-end space-x-3">
          <PrimaryButton @click="$inertia.get(route('service-reports.create'))">
            Crear reporte
          </PrimaryButton>
        </div>
      </div>

      <!-- tabla -->
      <div class="mt-9">
        <p v-if="localReports.length" class="text-gray66 text-[11px]">
          {{ localReports.length }} de {{ total_reports }} elementos
        </p>
        <ServiceReportsTable :services="localReports" />
        <p v-if="localReports.length" class="text-gray66 text-[11px] mt-3">
          {{ localReports.length }} de {{ total_reports }} elementos
        </p>
        <p v-if="loadingItems" class="text-xs my-4 text-center">
          Cargando
          <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
        </p>
        <button v-else-if="
          total_reports > 30 &&
          localReports.length < total_reports &&
          localReports.length
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
import ServiceReportsTable from "@/Components/MyComponents/ServiceReport/ServiceReportsTable.vue";
import axios from "axios";

export default {
  data() {
    return {
      searchQuery: null, //buscador de servicio.
      searchedWord: null, //palabra con la que se hizo la última busqueda.
      localReports: this.service_reports, //arreglo local de servicios
      loadingItems: false, //cestado de carga al recuperar mas items en la tabla.
      loading: false, //estado de carga cuando se busca a un servicio por medio del buscador
      currentPage: 1, //para paginación
      canCreate: true,
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    ServiceReportsTable,
    ThirthButton,
  },
  props: {
    service_reports: Array,
    total_reports: Number,
  },
  methods: {
    async searchReports() {
      this.loading = true;
      if (this.searchQuery != "") {
        try {
          const response = await axios.get(route("service-reports.search"), {
            params: { query: this.searchQuery },
          });
          if (response.status == 200) {
            this.localReports = response.data.items;
            this.searchedWord = this.searchQuery;
            this.searchQuery = null;
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
        }
      } else {
        this.localReports = this.service_reports;
      }
    },
    async fetchItemsByPage() {
      try {
        this.loadingItems = true;
        const response = await axios.get(route("service-reports.get-by-page", this.currentPage));

        if (response.status === 200) {
          this.localReports = [...this.localReports, ...response.data.items];
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
      this.localReports = this.service_reports;
      this.searchedWord = null;
    },
  },
};
</script>

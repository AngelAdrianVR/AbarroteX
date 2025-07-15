<template>
  <AppLayout title="Ordenes de Servicio">
    <div class="px-2 lg:px-10 py-7">
      <h1>Ordenes de Servicio</h1>
      <div class="md:flex justify-between mt-3">
        <article class="flex items-center flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0 lg:w-1/3">
          <div class="w-full relative">
            <input v-model="searchQuery" @keydown.enter="searchReports" class="input w-full pl-9"
              placeholder="Buscar por cliente o N° orden" type="search" ref="searchInput" />
            <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
          </div>
          <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
            {{ searchedWord }}
          </el-tag>
        </article>
        <div v-if="canCreate" class="my-4 md:my-0 flex items-center justify-end space-x-3">
          <PrimaryButton @click="$inertia.get(route('service-reports.create'))">
            Crear orden
          </PrimaryButton>
        </div>
      </div>
      <Loading v-if="loading" class="mt-20" />
      <div v-else-if="data" class="mt-8">
        <!-- Index para dm compresores -->
        <ServiceReportsTable6 v-if="$page.props.auth.user.store.id == 6" :reports="data.reports"
          :pagination="data.pagination" :showPagination="!searchedWord" />
        <!-- Index para apontephone -->
        <ServiceReportsTable v-else @refresh-data="handleRefreshing" :reports="data.reports" :pagination="data.pagination"
          :showPagination="!searchedWord" />
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import ServiceReportsTable from "@/Components/MyComponents/ServiceReport/ServiceReportsTable.vue";
import ServiceReportsTable6 from "@/Components/MyComponents/ServiceReport/ServiceReportsTable6.vue";
import axios from "axios";
import Loading from "@/Components/MyComponents/Loading.vue";

export default {
  data() {
    return {
      searchQuery: null, //buscador de servicio.
      searchedWord: null, //palabra con la que se hizo la última busqueda.
      loading: false, //estado de carga cuando se busca a un servicio por medio del buscador
      canCreate: this.$page.props.auth.user.permissions.includes('Crear ordenes de servicio'),
      data: null,
      //para paginación
      currentPage: null,
      pageSize: null,
    };
  },
  components: {
    AppLayout,
    ThirthButton,
    PrimaryButton,
    ServiceReportsTable, // para apontephone
    ServiceReportsTable6, //para dm compresores
    Loading,
  },
  props: {},
  methods: {
    handleRefreshing(page, pageSize) {
      this.currentPage = page;
      this.pageSize = pageSize;
      this.fetchData();
    },
    getPageFromUrl() {
      const params = new URLSearchParams(window.location.search);
      const page = params.get('page');
      const pageSize = params.get('pageSize');
      if (page) {
        this.currentPage = parseInt(page);
      } else {
        this.currentPage = 1;
      }
      if (pageSize) {
        this.pageSize = parseInt(pageSize);
      } else {
        this.pageSize = 100;
      }
    },
    closedTag() {
      this.searchedWord = null;
      this.fetchData();
    },
    inputFocus() {
      this.$nextTick(() => {
        this.$refs.searchInput.focus();
      });
    },
    async fetchData() {
      try {
        this.loading = true;
        const response = await axios.get(route('service-reports.get-data-for-table', { page: this.currentPage, pageSize: this.pageSize }));

        if (response.status === 200) {
          this.data = response.data.data;
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
    async searchReports() {
      if (this.searchQuery) {
        try {
          this.loading = true;
          const response = await axios.get(route('service-reports.search'), { params: { query: this.searchQuery } });
          if (response.status == 200) {
            this.data.reports = response.data.items;
            this.searchedWord = this.searchQuery;
            this.searchQuery = null;
          }

        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
          this.inputFocus();
        }
      }
    },
  },
  mounted() {
    this.getPageFromUrl(); //obtiene la variable page de la url.
    this.fetchData();
  },
};
</script>

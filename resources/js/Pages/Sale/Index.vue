<template>
    <AppLayout title="Historial de ventas">
        <div class="px-2 lg:px-10 py-7">
            <h1 class="font-bold">Historial de ventas</h1>
            <Loading v-if="loading" class="mt-20" />
            <div v-else-if="data" class="mt-8">
                <RegisteredSalesTable :items="data.items" @refresh-data="handleRefreshing" :pagination="data.pagination" />
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import RegisteredSalesTable from '@/Components/MyComponents/Sale/RegisteredSalesTable.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
    data() {
        return {
            loading: false,
            data: null,
            //para paginación
            currentPage: null,
            pageSize: null,
        }
    },
    components: {
        AppLayout,
        RegisteredSalesTable,
        Loading,
    },
    props: {
    },
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
        async fetchData() {
            try {
                this.loading = true;
                const response = await axios.get(route('sales.get-data-for-table', { page: this.currentPage, pageSize: this.pageSize }));

                if (response.status === 200) {
                    this.data = response.data.data;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.getPageFromUrl(); //obtiene la variable page de la url.
        this.fetchData();
    },
}
</script>

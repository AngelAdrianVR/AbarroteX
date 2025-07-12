<template>
    <AppLayout title="Clientes">
        <div class="px-2 lg:px-10 py-7">
            <h1 class="font-bold">Clientes</h1>
            <div class="md:flex justify-between mt-3">
                <article class="flex items-center flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0 lg:w-1/3">
                    <div class="w-full relative">
                        <input v-model="searchQuery" @keydown.enter="searchClients" class="input w-full pl-9"
                            placeholder="Buscar cliente por nombre, teléfono o rfc" type="search" ref="searchInput">
                        <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                    </div>
                    <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
                        {{ searchedWord }}
                    </el-tag>
                </article>
                <div v-if="canCreate" class="my-4 md:my-0 flex items-center justify-end space-x-3">
                    <!-- <ThirthButton>Registrar abono</ThirthButton> -->
                    <PrimaryButton @click="$inertia.get(route('clients.create'))">Nuevo cliente</PrimaryButton>
                </div>
            </div>
            <Loading v-if="loading" class="mt-20" />
            <div v-else-if="data" class="mt-8">
                <ClientsTable :clients="data.clients" @refresh-data="handleRefreshing"
                    :pagination="data.pagination" :showPagination="!searchedWord" />
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import ClientsTable from '@/Components/MyComponents/Client/ClientsTable.vue';
import axios from 'axios';
import Loading from '@/Components/MyComponents/Loading.vue';

export default {
    data() {
        return {
            searchQuery: null, //buscador de cliente.
            searchedWord: null, //palabra con la que se hizo la última busqueda.
            loading: false, //estado de carga cuando se busca a un cliente por medio del buscador
            canCreate: this.$page.props.auth.user.permissions.includes('Crear clientes'),
            data: null,
            //para paginación
            currentPage: null,
            pageSize: null,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        ClientsTable,
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
                const response = await axios.get(route('clients.get-data-for-table', { page: this.currentPage, pageSize: this.pageSize }));

                if (response.status === 200) {
                    this.data = response.data.data;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async searchClients() {
            if (this.searchQuery) {
                try {
                    this.loading = true;
                    const response = await axios.get(route('clients.search'), { params: { query: this.searchQuery } });
                    if (response.status == 200) {
                        this.data.clients = response.data.items;
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
}
</script>
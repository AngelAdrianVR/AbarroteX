<template>
    <AppLayout title="Clientes">
        <div class="px-2 lg:px-10 py-7">
            <h1>Clientes</h1>

            <div class="md:flex justify-between mt-3">
                <article class="flex items-center flex-col space-y-2 lg:flex-row lg:space-x-2 lg:space-y-0 lg:w-1/3">
                    <div class="w-full relative">
                        <input v-model="searchQuery" @keydown.enter="searchClients" class="input w-full pl-9"
                            placeholder="Buscar cliente por nombre, teléfono o rfc" type="search">
                        <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                    </div>
                    <el-tag @close="closedTag" v-if="searchedWord" closable type="primary">
                        {{ searchedWord }}
                    </el-tag>
                </article>
                <div class="my-4 md:my-0 flex items-center justify-end space-x-3">
                    <!-- <ThirthButton>Registrar abono</ThirthButton> -->
                    <PrimaryButton @click="$inertia.get(route('clients.create'))">Nuevo cliente</PrimaryButton>
                </div>
            </div>

            <!-- Tabla de clientes -->
            <div class="mt-9">
                <p v-if="localClients.length" class="text-gray66 text-[11px]">{{ localClients.length }} de {{
                    total_clients }} elementos
                </p>
                <ClientsTable :clients="localClients" />
                 <p v-if="localClients.length" class="text-gray66 text-[11px] mt-3">{{ localClients.length }} de {{
                    total_clients }} elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-else-if="total_clients > 20 && localClients.length < total_clients && localClients.length"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                    elementos</button>
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

export default {
data() {
    return {
        searchQuery: null, //buscador de cliente.
        searchedWord: null, //palabra con la que se hizo la última busqueda.
        localClients: this.clients, //arreglo local de clientes
        loadingItems: false, //cestado de carga al recuperar mas items en la tabla.
        loading: false, //estado de carga cuando se busca a un cliente por medio del buscador
        currentPage: 1, //para paginación
    }
},
components:{
AppLayout,
PrimaryButton,
ThirthButton,
ClientsTable,
},
props:{
clients: Array,
total_clients: Number
},
methods:{
    async searchClients() {
        this.loading = true;
        if (this.searchQuery != '') {
            try {
                const response = await axios.get(route('clients.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.localClients = response.data.items;
                    this.searchedWord = this.searchQuery;
                    this.searchQuery = null;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        } else {
            this.localClients = this.clients;
        }
    },
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.get(route('clients.get-by-page', this.currentPage));

            if (response.status === 200) {
                this.localClients = [...this.localClients, ...response.data.items];
                this.currentPage++;

                // Actualiza la URL con la pagina
                if (this.currentPage > 1) {
                    const currentURL = new URL(window.location.href);
                    currentURL.searchParams.set('page', this.currentPage);
                    window.history.replaceState({}, document.title, currentURL.href);
                }
            }
        } catch (error) {
            console.log(error)
        } finally {
            this.loadingItems = false;
        }
    },
    closedTag() {
        this.localClients = this.clients;
        this.searchedWord = null;
    }
}
}
</script>
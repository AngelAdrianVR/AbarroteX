<template>
    <AppLayout title="Clientes">
        <div class="px-2 lg:px-10 py-7">
            <h1>Clientes</h1>

            <div class="md:flex justify-between mt-3">
                <div class="lg:w-1/3 relative">
                    <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                        placeholder="Buscar cliente" type="search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                </div>
                <div class="my-4 lg:my-0 flex items-center justify-end space-x-3">
                    <ThirthButton>Registrar abono</ThirthButton>
                    <PrimaryButton>Nuevo cliente</PrimaryButton>
                </div>
            </div>

            <!-- Tabla de clientes -->
            <ClientsTable />
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import ClientsTable from '@/Components/MyComponents/Client/ClientsTable.vue';

export default {
data() {
    return {
        searchQuery: null,
    }
},
components:{
AppLayout,
PrimaryButton,
ThirthButton,
ClientsTable,
},
props:{

},
methods:{
    async searchProducts() {
        this.loading = true;
        if (this.searchQuery != '') {
            try {
                const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.products = this.localProducts;
                    this.localProducts = response.data.items;
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        } else {
            // Aquí podemos simular una operación asíncrona para mostrar el indicador de carga
            try {
                await this.resetLocalProducts();
            } finally {
                this.loading = false;
            }
        }
    },
}
}
</script>
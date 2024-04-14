<template>
    <AppLayout title="Productos globales">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3 mb-3">
                <h1 class="font-bold text-lg">Productos globales</h1>
                <PrimaryButton @click="$inertia.get(route('global-products.create'))" class="!rounded-full">Nuevo producto global</PrimaryButton>
            </div>

            <!-- buscador -->
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                    placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>

            <!-- tabla -->
            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8 lg:w-11/12">
                <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{
                    total_products }} elementos
                </p>
                <GlobalProductTable :products="localProducts" />
                <p v-if="localProducts.length" class="text-gray66 text-[11px]">{{ localProducts.length }} de {{
                    total_products }} elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-else-if="total_products > 15 && localProducts.length < total_products && localProducts.length"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                    elementos</button>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import GlobalProductTable from '@/Components/MyComponents/GlobalProduct/GlobalProductTable.vue';

export default {
data() {
    return {
        loading: false,
        searchQuery: null,
        localProducts: this.global_products,
        // paginacion
        loadingItems: false,
        currentPage: 1,
    }
},
components:{
AppLayout,
GlobalProductTable,
PrimaryButton,
Loading
},
props:{
global_products: Array,
total_products: Number
},
methods:{
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.get(route('global-products.get-by-page', this.currentPage));

            if (response.status === 200) {
                this.localProducts = [...this.localProducts, ...response.data.items];
                this.currentPage++;
            }
        } catch (error) {
            console.log(error)
        } finally {
            this.loadingItems = false;
        }
    },
    async searchProducts() {
            if ( this.searchQuery != '') {
                try {
                    this.loading = true;
                const response = await axios.get(route('global-products.search'), { params: { query: this.searchQuery } });
                if (response.status == 200) {
                    this.localProducts = response.data.items;
                }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            } else {
                this.localProducts = this.global_products;
            }
        },
}
}
</script>
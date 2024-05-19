<template>
    <OnlineStoreLayout :title="store.name">
        <div ref="scrollContainer" style="height: 91vh; overflow-y: scroll;" @scroll="handleScroll">
            <!-- Banners -->
            <section class="my-4 mx-3">
                <figure class="md:w-1/2 mx-auto">
                    <img class="rounded-lg" src="@/../../public/images/black_logo.png" alt="">
                    <div class="flex items-center justify-center space-x-3">
                        <i class="fa-solid fa-circle text-xs text-grayD9"></i>
                    </div>
                </figure>
            </section>
            <!-- Separador de banner y productos -->
            <div class="border-b border-grayD9 my-10"></div>

            <!-- Productos -->
            <section class="pb-16">
                <h1 class="font-bold text-3xl text-center mb-12">Productos</h1>
                
                <div class="md:grid md:grid-cols-2 lg:grid-cols-4 gap-5 mx-7 md:mx-9 space-y-4 md:space-y-0">
                    <OnlineProductCard v-for="product in visibleProducts" :key="product" :product="product" />
                </div>

                <!-- estado de carga -->
                <div v-if="loading" class="flex justify-center items-center py-10">
                    <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
                </div>
            </section>

        </div>
    </OnlineStoreLayout>
  
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import OnlineProductCard from '@/Components/MyComponents/OnlineSale/OnlineProductCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';

export default {
data() {
    return {
        loading: false, // bandera de carga para recuperar mas items con scroll.
        visibleProducts: this.products //variable local de productos visibles
    }
},
components:{
OnlineStoreLayout,
OnlineProductCard,
PrimaryButton,
},
props:{
store: Object,
products: Array,
total_products: Number
},
methods:{
    handleScroll() {
        const container = this.$refs.scrollContainer;
        const scrollHeight = container.scrollHeight;
        const scrollTop = container.scrollTop;
        const clientHeight = container.clientHeight;

        // Determinar si has llegado al final de la vista
        if (scrollHeight - scrollTop === clientHeight) {

            // Ejecutar tu método cuando llegues al final. No se ejecuta si se estan cargando ya products
            if (!this.loading) {
                this.loadMoreProducts();
            }
        }
    },
    async loadMoreProducts() {
        const offset = this.visibleProducts.length; //de donde empieza a contar la carga de products
        let limit = 12; // numero de post que carga por petición

        // regula el numero de products para cargar segun los que queden por cargar y cuando es 0 no se hace la peticion
        if ( ( this.total_products - this.visibleProducts.length) < limit ) {
            limit = this.total_products - this.visibleProducts.length;
            }

            if ( ( this.total_products - this.visibleProducts.length ) !== 0 ) {
                this.loading = true;
                try {
                    const response = await axios.get(route('online-sales.load-more-products', [offset, limit]), { storeId: this.store.id});
                    if (response.status === 200 ) {
                        this.visibleProducts = [...this.visibleProducts, ...response.data.products]; //agrega los products obtenidos al array de products que se muestran 
                    }
                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            } else {
                console.log('No hay mas productos por cargar');
                return ;
            }
    },
},
mounted() {
    this.visibleProducts = this.products;
}
}
</script>

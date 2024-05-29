<template>
    <OnlineStoreLayout :title="store.name">
        <div ref="scrollContainer" style="height: 91vh; overflow-y: scroll;" @scroll="handleScroll">
            <!-- Banners -->
            <section v-if="banners.media?.length" class="my-4">
                <figure class="md:w-1/2 h-96 mx-auto flex flex-col justify-center mt-7 rounded-lg">
                    <img class="!rounded-md h-full object-contain" :src="banners.media[currentBanner].original_url" alt="">
                    <div class="flex items-center justify-center space-x-3 mt-4">
                        <i @click="currentBanner = index" v-for="(dot, index) in banners.media?.length" :key="dot" :class="index == currentBanner ? 'text-primary' : 'cursor-pointer text-xs' "
                            class="fa-solid fa-circle text-grayD9 transition-all duration-300"></i>
                    </div>
                </figure>
            </section>

            <!-- Separador de banner y productos -->
            <div class="border-b border-grayD9 my-10"></div>

            <!-- Productos -->
            <section class="pb-16">
                <h1 class="font-bold text-3xl text-center mb-12">Productos</h1>
                
                <div class="md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mx-7 md:mx-9 space-y-4 md:space-y-0">
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
        visibleProducts: this.products, //variable local de productos visibles
        currentBanner: 0 //index de banners
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
total_products: Number,
store_id: Number, //id de la tienda para guardarla en el localStorage
banners: Object, //banners
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
    startTimer() {
        this.timer = setInterval(() => {
            this.currentBanner = (this.currentBanner + 1) % this.banners.media?.length;
        }, 5000);
    },
},
mounted() {
    this.visibleProducts = this.products;
    // localStorage.clear(); //borrar local storage

    // Guardar store_id en el localStorage
    localStorage.setItem('storeId', this.store_id);

    //iniciar contador para cambiar banners automaticamente.
    this.startTimer()
}
}
</script>

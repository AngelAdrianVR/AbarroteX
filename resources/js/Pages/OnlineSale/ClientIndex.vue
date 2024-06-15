<template>
    <OnlineStoreLayout :title="store.name">
        <div ref="scrollContainer" style="height: 91vh; overflow-y: scroll;" @scroll="handleScroll">
            <!-- Banners -->
            <section v-if="banners?.media?.length" class="my-4">
                <figure class="lg:h-96 mx-auto flex flex-col justify-center mt-7 rounded-lg">
                    <img class="!rounded-md h-full object-contain" :src="banners?.media[currentBanner].original_url" alt="">
                    <div class="flex items-center justify-center space-x-3 mt-4">
                        <i @click="currentBanner = index" v-for="(dot, index) in banners?.media?.length" :key="dot" :class="index == currentBanner ? 'text-primary' : 'cursor-pointer text-xs' "
                            class="fa-solid fa-circle text-grayD9 transition-all duration-300"></i>
                    </div>
                </figure>
            </section>

            <!-- Separador de banner y productos -->
            <div class="border-b border-grayD9 my-10"></div>

            <!-- Productos -->
            <section class="pb-16">
                <h1 class="font-bold text-3xl text-center mb-12">Productos</h1>
                <h1 v-if="store.online_store_properties?.enabled_free_delivery" class="font-bold text-xl text-center text-primary mb-1">
                    Envío gratis en compra mínima de ${{ store.online_store_properties?.min_free_delivery }}</h1>
                
                <div v-if="visibleProducts.length" class="md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mx-7 md:mx-9 space-y-4 md:space-y-0">
                    <OnlineProductCard v-for="product in visibleProducts" :key="product" :product="product" :store="store" />
                </div>
                <el-empty v-else description="No hay productos en la tienda" />

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
        currentBanner: 0, //index de banners
        store: null, //informacion de la tienda
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
                    console.log('offset', this.visibleProducts.length)
                    console.log('limit', limit)
                    const response = await axios.post(route('online-sales.load-more-products'), { 
                        storeId: this.store_id, offset: offset, limit: limit
                    });
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
            this.currentBanner = (this.currentBanner + 1) % this.banners?.media?.length;
        }, 5000);
    },
    async fetchStoreInfo() {
        try {
            const response = await axios.get(route('stores.fetch-store-info', this.store_id));
            if ( response.status === 200 ) {
                this.store = response.data.store;
            }
        } catch (error) {
            console.log(error);
        }
    }
},
created() {
    //obtengo el id del local storage para comparar si entró a otra tienda y borrar el carrito
    const savedStoreId = localStorage.getItem('storeId');
    
    // Guardar store_id en el localStorage
    localStorage.setItem('storeId', this.store_id);

    console.log('viejo id', savedStoreId);
    console.log('nuevo id', this.store_id);
    console.log('son diferentes?', savedStoreId != this.store_id);

    if ( savedStoreId != this.store_id ) {
        localStorage.removeItem('Ezycart'); //borrar una variable del local storage
    }
},
mounted() {
    this.visibleProducts = this.products; // clona los productos para poder manipularlos
    // localStorage.clear(); //borrar local storage
    // localStorage.removeItem('Ezycart'); //borrar una variable del local storage

    // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
    this.fetchStoreInfo();

    //iniciar contador para cambiar banners automaticamente.
    this.startTimer();

}
}
</script>

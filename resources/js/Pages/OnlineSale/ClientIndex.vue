<template>
    <OnlineStoreLayout :title="store.name">
        <div ref="scrollContainer" style="height: 82vh; overflow-y: scroll;" @scroll="handleScroll">
            <!-- Banners -->
            <section v-if="banners?.media?.length" class="my-4">
                <figure class="lg:h-96 mx-auto flex flex-col justify-center mt-7 rounded-lg">
                    <img class="!rounded-md h-full object-contain" :src="banners?.media[currentBanner]?.original_url"
                        alt="">
                    <div class="flex items-center justify-center space-x-3 mt-4">
                        <i @click="currentBanner = index" v-for="(dot, index) in banners?.media?.length" :key="dot"
                            :class="index == currentBanner ? 'text-primary' : 'cursor-pointer text-xs'"
                            class="fa-solid fa-circle text-grayD9 transition-all duration-300"></i>
                    </div>
                </figure>
            </section>

            <!-- Separador de banner y productos -->
            <div class="border-b border-grayD9 my-8"></div>

            <!-- Productos -->
            <section class="pb-16">
                <!-- tabs -->
                <div class="flex justify-center pb-5">
                    <ToggleButton id="start" ref="togglebutton" @update="handleToggle" :labels="['Productos', 'Servicios']"
                        class="w-3/4 md:w-[45%] lg:w-[35%] xl:w-[20%]" />
                </div>

                <section v-if="activeTab === 'Productos'">
                    <Products :store="store" :visibleProducts="visibleProducts" />
                </section>
                <section v-else>
                    <Services :store="store" :visibleServices="visibleServices" />
                </section>

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
import ToggleButton from "@/Components/MyComponents/ToggleButton.vue";
import Products from './Tabs/Products.vue';
import Services from './Tabs/Services.vue';
import axios from 'axios';

export default {
    data() {
        return {
            loading: false, // bandera de carga para recuperar mas items con scroll.
            visibleProducts: this.products, //variable local de productos visibles
            visibleServices: this.services, //variable local de servicios visibles
            currentBanner: 0, //index de banners
            activeTab: 'Productos',
        }
    },
    components: {
        OnlineStoreLayout,
        ToggleButton,
        Products,
        Services,
    },
    props: {
        store: Object,
        products: Array,
        total_products: Number,
        services: Array,
        total_services: Number,
        store_id: Number, //id de la tienda para guardarla en el localStorage
        banners: Object, //banners
    },
    methods: {
        handleScroll() {
            const container = this.$refs.scrollContainer;
            const scrollHeight = container.scrollHeight;
            const scrollTop = container.scrollTop;
            const clientHeight = container.clientHeight;

            // Determinar si has llegado al final de la vista
            if (scrollHeight - scrollTop === clientHeight) {

                // Ejecutar tu método cuando llegues al final. No se ejecuta si se estan cargando ya products
                if (!this.loading && this.activeTab === 'Productos') {
                    this.loadMoreProducts();
                }
            }
        },
        async loadMoreProducts() {
            const offset = this.visibleProducts.length; //de donde empieza a contar la carga de products
            let limit = 12; // numero de post que carga por petición

            // regula el numero de products para cargar segun los que queden por cargar y cuando es 0 no se hace la peticion
            if ((this.total_products - this.visibleProducts.length) < limit) {
                limit = this.total_products - this.visibleProducts.length;
            }

            if ((this.total_products - this.visibleProducts.length) !== 0) {
                this.loading = true;
                try {
                    const response = await axios.post(route('online-sales.load-more-products'), {
                        storeId: this.store_id, offset: offset, limit: limit
                    });
                    if (response.status === 200) {
                        this.visibleProducts = [...this.visibleProducts, ...response.data.products]; //agrega los products obtenidos al array de products que se muestran 
                    }
                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            } else {
                console.log('No hay mas productos por cargar');
                return;
            }
        },
        startTimer() {
            this.timer = setInterval(() => {
                this.currentBanner = (this.currentBanner + 1) % this.banners?.media?.length;
            }, 5000);
        },
        handleToggle(active) {
            this.activeTab = active;

            const tab = active == 'Productos' ? 'products' : 'services';
            this.updateURL(tab);
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
    },
    created() {
        //obtengo el id del local storage para comparar si entró a otra tienda y borrar el carrito
        const savedStoreId = localStorage.getItem('storeId');

        // Guardar store_id en el localStorage
        localStorage.setItem('storeId', this.store_id);

        if (savedStoreId != this.store_id) {
            localStorage.removeItem('Ezycart'); //borrar una variable del local storage
        }
    },
    mounted() {
        this.visibleProducts = this.products; // clona los productos para poder manipularlos
        // localStorage.clear(); //borrar local storage
        // localStorage.removeItem('Ezycart'); //borrar una variable del local storage

        //iniciar contador para cambiar banners automaticamente.
        this.startTimer();

         // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'activeTab' de los parámetros de búsqueda
        const activeTabFromURL = currentURL.searchParams.get('tab');
        if (activeTabFromURL) {
            if (activeTabFromURL == 'services') {
                const tab = 'Servicios';
                this.$refs.togglebutton.toggle();
            }
        }
    }
}
</script>

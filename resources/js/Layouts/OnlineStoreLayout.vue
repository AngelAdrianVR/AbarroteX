<template>
    <div>

        <Head :title="title" />

        <div class="bg-white">
            <!-- resto de pagina -->
            <main class="w-full">
                <nav class="bg-white border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-20 borde items-center">
                            <!-- Logo -->
                            <Link v-if="!loadigLogo" :href="route('online-sales.client-index', encodedIdStore ?? 0)">
                            <img v-if="logo?.media?.length" class="h-12 md:h-16" :src="logo?.media[0]?.original_url"
                                alt="logotipo de la tienda">
                            <img v-else class="h-12 md:h-16" src="@/../../public/images/black_logo.png" alt="">
                            </Link>

                            <!-- buscador de productos -->
                            <div class="relative w-44 md:w-80">
                                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur"
                                    @input="searchProducts" ref="searchInput" class="input w-full pl-9"
                                    placeholder="Buscar producto" type="search">
                                <i
                                    class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                                <!-- Resultados de la búsqueda -->
                                <div v-if="searchFocus && searchQuery"
                                    class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-50 max-h-48 overflow-auto">
                                    <ul v-if="productsFound?.length > 0 && !loading">
                                        <li @click="product.global_product_id ? $inertia.get(route('online-sales.show-global-product', product.id))
                                            : $inertia.get(route('online-sales.show-local-product', product.id))"
                                            v-for="(product, index) in productsFound" :key="index"
                                            class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{
                                                product.global_product_id ?
                                                    product.global_product?.name : product.name }}</li>
                                    </ul>
                                    <p v-else-if="!loading" class="text-center text-sm text-gray-600 px-5 py-2">No se
                                        encontraron
                                        coincidencias
                                    </p>
                                    <!-- estado de carga -->
                                    <div v-if="loading" class="flex justify-center items-center py-10">
                                        <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Carrito -->
                            <Link :href="route('online-sales.cart')" class="relative">
                            <button class="bg-[#232323] rounded-full size-9 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                <span v-if="cartCount > 0"
                                    class="absolute -top-2 -right-2 bg-primary text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">{{
                                        cartCount }}</span>
                            </button>
                            </Link>
                        </div>
                    </div>
                </nav>

                <div class="flex flex-col justify-between bg-white">
                    <slot />
                </div>
            </main>
            <footer v-if="!loadigLogo && !loadigStore"
                class="flex justify-between items-center bg-[#232323] p-3 h-[72px] md:h-20 md:px-7">
                <!-- Logo de la tienda -->
                <figure class="flex items-center space-x-2">
                    <img v-if="logo?.media?.length" class="h-10 md:h-12" :src="logo?.media[0]?.original_url"
                        alt="logotipo de la tienda">
                    <p v-else class="tex-sm text-gray99">{{ store?.name }}</p>
                </figure>
                <!-- whatsapp button computed -->
                <div v-if="store?.online_store_properties?.whatsapp" class="hidden md:flex flex-col items-center">
                    <h2 class="text-white font-bold">Contáctanos</h2>
                    <div class="flex items-center space-x-3 mt-2">
                        <i class="fa-brands fa-whatsapp text-[24px] text-gray99"></i>
                        <p class="text-gray99">{{ store?.online_store_properties?.whatsapp?.replace(/(\d{2})(?=\d)/g,
                            '$1 ') }}
                        </p>
                    </div>
                </div>
                <!-- ubicación de tienda -->
                <div v-if="store?.address" class="hidden md:flex flex-col items-center">
                    <h2 class="text-white font-bold text-sm md:text-base">Encuéntranos en</h2>
                    <div class="flex items-center space-x-3 mt-2">
                        <i class="fa-solid fa-location-dot text-xs md:text-[24px] text-gray99"></i>
                        <p class="text-gray99 text-xs md:text-base">{{ store?.address }}</p>
                    </div>
                </div>
                <!-- whatsapp button floating -->
                <a v-if="store?.online_store_properties?.whatsapp"
                    class="z-50 size-12 rounded-full bg-green-500 shadow-md flex items-center justify-center fixed bottom-8 md:bottom-20 right-1 hover:scale-105"
                    :href="whatsappLink" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-beat fa-whatsapp text-xl text-gray-100"></i>
                </a>
                <!-- Logo de ezy -->
                <figure class="text-center text-xs">
                    <img class="h-10 md:h-12 mx-auto" src="@/../../public/images/white_logo.png" alt="">
                    <p class="text-gray99">Potencia tu negocio, prueba </p>
                    <a :href="'/'" target="_blank" class="text-primary">Punto de venta</a>
                </figure>
            </footer>
        </div>
    </div>
</template>

<script>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import emitter from '@/eventBus.js';

export default {
    data() {
        return {
            searchQuery: null, //buscador. Palabras escritas en el buscador
            searchFocus: false, //buscador. Bandera de enfoque para el buscador
            productsFound: null, //buscador. productos encontrados.
            cart: [], //productos guardados en el carrito (localStorage)
            storeId: null, //se recupera el id de la tienda desde el localstorage
            store: {}, //se recupera la informacion de la tienda
            loading: false, //cargando la busqueda de productos
            loadigLogo: false, //cargando logo
            loadigStore: false, //cargando tienda
            logo: null, //se recupera el logotipo de la tienda con el storeId obtenido del localstorage
            encodedIdStore: null, //url codificada de mi tienda
        }
    },
    components: {
        ApplicationMark,
        Head,
        Link
    },
    props: {
        title: String,
    },
    methods: {
        handleBlur() {
            // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
            setTimeout(() => {
                this.searchFocus = false;
            }, 100);
        },
        async searchProducts() {
            try {
                this.loading = true;
                const response = await axios.get(route('online-sales.search-products', this.storeId), { params: { query: this.searchQuery } });
                if (response.status === 200) {
                    this.productsFound = response.data.items;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        },
        loadCart() {
            const cart = localStorage.getItem('Ezycart');
            if (cart) {
                this.cart = JSON.parse(cart);
            }
        },
        async getLogo() {
            this.loadigLogo = true;
            try {
                const response = await axios.get(route('online-sales.get-logo', this.storeId ?? 1));
                if (response.status === 200) {
                    this.logo = response.data.item;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadigLogo = false;
            }
        },
        encodeStoreId() {
            const encodedId = btoa(this.storeId.toString());
            this.encodedIdStore = encodedId;
        },
        async fetchStoreInfo() {
            this.loadigStore = true;
            try {
                const response = await axios.get(route('stores.fetch-store-info', this.storeId));
                if (response.status === 200) {
                    this.store = response.data.store;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadigStore = false;
            }
        }
    },
    computed: {
        cartCount() {
            return this.cart.length;
        },
        whatsappLink() {
            const text = encodeURIComponent('Hola! quiero hacer un pedido de tu tienda en línea!');
            return `https://api.whatsapp.com/send?phone=${this.store?.online_store_properties?.whatsapp}&text=${text}`;
        }
    },
    created() {
        this.loadCart();

        // recupera el store_id del localStorage
        this.storeId = localStorage.getItem('storeId');
        this.getLogo();

        // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
        this.fetchStoreInfo();

    },
    mounted() {
        //codifica el id de la tienda
        this.encodeStoreId();

        // Escucha el evento personalizado para actualizar carrito
        emitter.on('product-added-to-cart', this.loadCart);
    },
    beforeUnmount() {
        // Elimina el listener del evento cuando se desmonta el componente
        emitter.off('product-added-to-cart', this.loadCart);
    }
}
</script>

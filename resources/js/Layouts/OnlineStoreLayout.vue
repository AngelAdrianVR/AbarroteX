<template>
    <div>
        <Head :title="title" />

        <div class="overflow-hidden h-screen md:flex bg-white">
            <!-- resto de pagina -->
            <main class="w-full">
                <nav class="bg-white border-b border-gray-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-20 borde items-center">
                            <!-- Logo -->
                            <Link :href="route('online-sales.client-index', storeId ?? 0)">
                                <img v-if="logo?.media?.length" class="h-12 md:h-16" :src="logo?.media[0]?.original_url" alt="logotipo de la tienda">
                                <img v-else class="h-12 md:h-16" src="@/../../public/images/black_logo.png" alt="">
                            </Link>

                            <!-- buscador de productos -->
                            <div class="relative w-44 md:w-80">
                                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
                                ref="searchInput" class="input w-full pl-9" placeholder="Buscar producto"
                                type="search">
                                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                                <!-- Resultados de la búsqueda -->
                                <div v-if="searchFocus && searchQuery"
                                class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-50 max-h-48 overflow-auto">
                                <ul v-if="productsFound?.length > 0 && !loading">
                                    <li @click="product.global_product_id ? $inertia.get(route('online-sales.show-global-product', product.global_product_id))
                                        :$inertia.get(route('online-sales.show-local-product', product.id))" v-for="(product, index) in productsFound" :key="index"
                                    class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{ product.global_product_id ?
                                        product.global_product?.name : product.name }}</li>
                                </ul>
                                <p v-else-if="!loading" class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron
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
                                <button class="bg-[#F2F2F2] rounded-full size-9 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray99">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                    <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-primary text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">{{ cartCount }}</span>
                                </button>
                            </Link>
                        </div>
                    </div>
                </nav>

                <div class="overflow-y-auto h-[calc(100vh-3rem)] bg-white">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

export default {
data() {
    return {
        searchQuery: null, //buscador. Palabras escritas en el buscador
        searchFocus: false, //buscador. Bandera de enfoque para el buscador
        productsFound: null, //buscador. productos encontrados.
        loading: false, //cargando la busqueda de productos
        cart: [], //productos guardados en el carrito (localStorage)
        storeId: null, //se recupera el id de la tienda desde el localstorage
        logo: null //se recupera el logotipo de la tienda con el storeId obtenido del localstorage
    }
},
components:{
ApplicationMark,
Head,
Link
},
props:{
title: String,
},
methods:{
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
        try {
            const response = await axios.get(route('online-sales.get-logo', this.storeId ?? 1));
            if ( response.status === 200 ) {
                this.logo = response.data.item;
            }
        } catch (error) {
         console.log(error);   
        }
    }
},
computed: {
    cartCount() {
        return this.cart.length;
    }
},
created() {
    this.loadCart();
    
},
mounted() {
    // recupera el store_id del localStorage
    this.storeId = localStorage.getItem('storeId');
    this.getLogo();
}
}
</script>

<template>
    <div @click="openDetails"
        class="cursor-pointer py-3 px-5 rounded-3xl border-2 border-gray-100 flex flex-col h-[400px] hover:shadow-2xl relative group transition-all ease-linear duration-200">
        <!-- Deatalle de cantidad disponible en stock  -->
        <div v-if="store?.online_store_properties?.inventory"
            class="absolute -top-2 left-0 w-full bg-black rounded-t-lg opacity-0 group-hover:opacity-60 group-hover:translate-y-2 transition-all duration-500 ease-out lg:group-hover:block py-1">
            <p class="text-white text-center opacity-100">{{ product.current_stock ?? '0' }} {{ product.bulk_product ?
                product.measure_unit + '(s)' : 'unidades' }} disponibles</p>
            <p v-if="product.product_on_request" class="text-white text-center opacity-100">Este producto se surte bajo
                pedido ({{ product.days_for_delivery }} días hábiles)</p>
        </div>
        <!-- Imagen -->
        <figure class="h-1/2 text-center rounded-xl bg-[#f9f9f9] flex items-center justify-center">
            <img class="h-full object-contain p-3 select-none" :draggable="false"
                v-if="product.global_product_id ? product.global_product.media?.length : product.media?.length"
                :src="product.global_product_id ? product.global_product.media[0]?.original_url : product.media[0]?.original_url"
                alt="producto">
            <div v-else>
                <i class="fa-regular fa-image text-7xl text-gray-200"></i>
                <p class="text-sm text-gray-300">Imagen no disponible</p>
            </div>
        </figure>
        <!-- Detalles -->
        <div class="text-left ml-3 mt-2 flex flex-col justify-center h-1/2">
            <div class="h-12">
                <h1 class="font-bold text-gray-700">{{ product.global_product_id ? product.global_product.name :
                    product.name }}</h1>
                <span class="text-gray99">{{ product.currency === '$USD' ? 'USD' : 'MXN' }}</span>
            </div>

            <div class="flex items-center space-x-1 my-4">
                <p class="text-2xl font-bold">
                    ${{ product.global_product_id ? product.global_product.public_price
                        : product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    <span class="text-lg">{{ product.bulk_product ? '/ ' + product.measure_unit : '' }}</span>
                </p>
            </div>
            <!-- Si no es bajo pedido -->
            <div @click.stop="" class="flex justify-between items-center cursor-default"
                v-if="!product.product_on_request">
                <el-input-number v-model="quantity" size="small" :min="1" :max="999" :precision="2" />
                <div v-if="alreadyInCart" class="flex items-center space-x-3">
                    <span class="text-green-400 text-sm">Agregado</span>
                    <div
                        class="text-green-400 size-9 flex items-center justify-center rounded-full border border-green-400 bg-[#D9FECF]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <div v-else class="flex items-center space-x-3">
                    <p v-if="store?.online_store_properties?.inventory && product.current_stock < 1"
                        class="text-sm text-gray-700">Agotado</p>
                    <button :disabled="store?.online_store_properties?.inventory && product.current_stock < 1"
                        @click="addToCart"
                        class="size-10 flex items-center justify-center rounded-full border-2 border-gray-300 hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </button>
                </div>
                <!-- <p v-if="alreadyInCart" class="text-green-500"><i class="fa-regular fa-circle-check"></i> Agregado</p>
                <PrimaryButton v-else @click="addToCart" class="!active:scale-75">Agregar al carrito</PrimaryButton> -->
            </div>
            <!-- Si es bajo pedido -->
            <div v-else @click.stop="" class="flex justify-between items-center">
                <!-- Toma en cuenta el stock disponible si está activada la configuración de la tienda -->
                <el-input-number v-if="store?.online_store_properties?.inventory" :disabled="product.current_stock < 1"
                    v-model="quantity" size="small" :min="0" :max="product.current_stock" :precision="2" />

                <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
                <el-input-number v-else v-model="quantity" size="small" :min="1" :max="999" :precision="2" />

                <div v-if="alreadyInCart"
                    class="text-green-400 size-9 flex items-center justify-center rounded-full border border-green-400 bg-[#D9FECF]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>

                <div v-else class="flex items-center space-x-3">
                    <p v-if="store?.online_store_properties?.inventory && product.current_stock < 1"
                        class="text-sm text-gray-700">Agotado</p>
                    <button :disabled="store?.online_store_properties?.inventory && product.current_stock < 1"
                        @click="addToCart"
                        class="size-10 flex items-center justify-center rounded-full border-2 border-gray-300 hover:bg-primary hover:border-transparent hover:text-white transition-all ease-linear duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </button>
                </div>

                <!-- Botones viejos -->
                <!-- <p v-if="alreadyInCart" class="text-green-500"><i class="fa-regular fa-circle-check"></i> Agregado</p> -->

                <!-- <PrimaryButton v-else :disabled="store?.online_store_properties?.inventory && product.current_stock < 1"
                    @click="addToCart" class="!active:scale-75">
                    {{ store?.online_store_properties?.inventory && product.current_stock < 1 ? 'Agotado' : 'Agregar al carrito' }} </PrimaryButton> -->
            </div>
        </div>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import emitter from '@/eventBus.js';

export default {
    data() {
        return {
            quantity: 1, //cantidad seleccionada para guardar al carrito
            alreadyInCart: false, //bandera para saber si ya esta en carrito para evitar error con cantidades
        }
    },
    components: {
        PrimaryButton,
        Link
    },
    props: {
        product: Object,
        store: Object
    },
    methods: {
        openDetails() {
            if (this.product.global_product_id) {
                this.$inertia.visit(route('online-sales.show-global-product', { slug: this.store.slug, global_product_id: this.product.id }));
            } else {
                this.$inertia.visit(route('online-sales.show-local-product', { slug: this.store.slug, product_id: this.product.id }));
            }
        },
        addToCart() {
            // Obtener el carrito actual desde localStorage
            let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

            if (this.product.global_product_id) {

                // Verificar si el producto ya está en el carrito comparando id y si es local o no
                const productInCart = cart.find(item => item.id === this.product.global_product_id && item.isLocal == false);

                // no se ejecuta cuando tenga en el mounted la revisión de si ya está dentro del carrito
                if (productInCart) {
                    // Si el producto ya está en el carrito, actualizar la cantidad
                    // productInCart.quantity += this.quantity; //descomentar si se requiere sumar cantidad al agregar de nuevo a carrito. (problema con current stock)
                } else {
                    // Si el producto no está en el carrito, agregarlo
                    cart.push({
                        product_id: this.product.id,
                        name: this.product.global_product.name,
                        isLocal: false,
                        price: this.product.public_price,
                        quantity: this.quantity,
                        image_url: this.product.global_product.media[0]?.original_url,
                        product_on_request: this.product.product_on_request, //si es o no bajo pedido para que no tome en cuenta el stock
                    });
                    this.alreadyInCart = true; //bandera de que ya está agregado en carrito
                }
            } else {

                // Verificar si el producto ya está en el carrito
                const productInCart = cart.find(item => item.id === this.product.id && item.isLocal == true);

                // no se ejecuta cuando tenga en el mounted la revisión de si ya está dentro del carrito
                if (productInCart) {
                    // Si el producto ya está en el carrito, actualizar la cantidad
                    // productInCart.quantity += this.quantity; //descomentar si se requiere sumar cantidad al agregar de nuevo a carrito. (problema con current stock)
                } else {
                    // Si el producto no está en el carrito, agregarlo
                    cart.push({
                        product_id: this.product.id,
                        name: this.product.name,
                        price: this.product.public_price,
                        isLocal: true,
                        quantity: this.quantity,
                        image_url: this.product.media[0]?.original_url,
                        product_on_request: this.product.product_on_request, //si es o no bajo pedido para que no tome en cuenta el stock
                    });
                    this.alreadyInCart = true; //bandera de que ya está agregado en carrito
                }
            }

            // Guardar el carrito actualizado en localStorage
            localStorage.setItem('Ezycart', JSON.stringify(cart));


            // Mostrar un mensaje o notificación al usuario
            this.$notify({
                title: "Correcto",
                message: "Se ha agregado correctamente al carrito",
                type: "success",
                position: "bottom-right",
            });

            // Emitir el evento personalizado
            emitter.emit('update-cart');
        },
    },
    mounted() {
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
        if (this.product.global_product_id) {
            const globalProductInCart = cart.find(item => item.name == this.product.global_product.name);
            if (globalProductInCart) {
                this.alreadyInCart = true;
            }
        } else {

            const productInCart = cart.find(item => item.name == this.product.name);
            if (productInCart) {
                this.alreadyInCart = true;
            }
        }
    }
}
</script>

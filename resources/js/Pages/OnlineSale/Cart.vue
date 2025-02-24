<template>
    <OnlineStoreLayout title="Mi carrito">
        <main class="md:p-2 lg:p-9 min-h-[calc(100vh-161px)]">
            <div class="pl-4 mt-3">
                <Back />
            </div>

            <section class="xl:w-[60%] mx-auto mt-5 border border-grayD9 rounded-lg mb-9">
                <p class="py-2 px-9 border-b border-grayD9">Mi carrito</p>
                <div class="md:flex">
                    <!-- parte izquierda -->
                    <div v-if="loading" class="h-48 w-full flex flex-col items-center space-y-3 justify-center">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-primary"></i>
                        <span>Cargando carrito...</span>
                    </div>
                    <div v-else
                        class="border-r border-grayD9 p-2 md:py-4 md:px-9 md:w-[70%] h-96 xl:h-[440px] space-y-4 overflow-auto">
                        <div v-if="cart.length">
                            <CartProductCard @productRemoved="removeCartProduct($event)"
                                @updateCart="updateCart($event)" :cartProduct="product" v-for="product in cart"
                                :key="product" :store="store" />
                        </div>
                        <div v-else>
                            <el-empty description="No hay productos en tu carrito" />
                            <p @click="$inertia.get(route('online-sales.client-index', encodedIdStore ?? 0))"
                                class="text-primary text-center cursor-pointer">Seguir comprando</p>
                        </div>
                    </div>
                    <!-- Parte derecha -->
                    <div v-if="cart.length" class="md:w-[30%] md:py-4 md:px-2 text-xs lg:text-sm">
                        <section>
                            <div class="border border-grayD9 grid grid-cols-3 gap-x-1 rounded-lg p-3 mb-5 *:mb-1">
                                <p class="col-span-2">Subtotal:</p>
                                <p><span class="mr-2">$</span>{{ cartTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",") }}</p>
                                <p class="col-span-2">Costo de envío:</p>
                                <p>
                                    <span class="mr-2">$</span>
                                    {{ deliveryPrice.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </p>
                                <p class="font-bold col-span-2">Total:</p>
                                <p class="font-bold">
                                    <span class="mr-2">$</span>
                                    {{ (cartTotal + deliveryPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </p>
                            </div>
                            <p v-if="(cartTotal < store?.online_store_properties?.min_free_delivery) && store?.online_store_properties?.enabled_free_delivery"
                                class="text-xs text-center text-gray99 mb-5">
                                Agregar
                                <span>
                                    ${{ (parseFloat(store?.online_store_properties?.min_free_delivery || 0) -
                                        cartTotal)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </span>
                                más para conseguir envió gratis
                            </p>

                            <div class="text-center pb-5">
                                <PrimaryButton @click="$inertia.get(route('online-sales.create'))" class="!px-8">
                                    Finalizar pedido</PrimaryButton>
                                <p @click="$inertia.get(route('online-sales.client-index', encodedIdStore ?? 0))"
                                    class="text-primary mt-4 cursor-pointer">Seguir comprando</p>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </main>
    </OnlineStoreLayout>
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import CartProductCard from '@/Components/MyComponents/OnlineSale/CartProductCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';

export default {
    data() {
        return {
            cart: [],
            storeId: null, //se recupera el id de la tienda desde el localstorage
            store: {}, //se recupera la información de la tienda para utilizar el costo de envío.
            encodedIdStore: null, //id codificado de la tienda
            // cargas
            loading: true,

        }
    },
    components: {
        OnlineStoreLayout,
        CartProductCard,
        PrimaryButton,
        Back,
    },
    props: {

    },
    methods: {
        updateCart(data) {
            const productIndex = this.cart.findIndex(item => item.product_id === data.id);

            if (productIndex !== -1) {
                this.cart[productIndex].quantity = data.quantity;
            }
        },
        removeCartProduct(cartProductId) {
            const productIndex = this.cart.findIndex(item => item.product_id === cartProductId);

            if (productIndex !== -1) {
                this.cart.splice(productIndex, 1);
            }
        },
        async fetchStoreInfo() {
            this.loading = true;
            try {
                const response = await axios.get(route('stores.fetch-store-info', this.storeId));
                if (response.status === 200) {
                    this.store = response.data.store;

                    //decodifica el id de la tienda
                    const encodedId = btoa(this.storeId.toString());
                    this.encodedIdStore = encodedId;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        }

    },
    computed: {
        cartTotal() {
            return this.cart.reduce((sum, item) => {
                return sum + (item.price * item.quantity);
            }, 0);
        },
        deliveryPrice() {
            const delivery = this.store.online_store_properties.delivery_price ?? 0;
            const minFreeDelivery = this.store.online_store_properties.min_free_delivery ?? 0;

            if (minFreeDelivery === 0 || this.cartTotal < minFreeDelivery) {
                return parseFloat(delivery);
            }

            return 0;
        }
    },
    mounted() {
        // Obtener el carrito actual desde localStorage
        this.cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

        // recupera el store_id del localStorage
        this.storeId = localStorage.getItem('storeId');

        // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
        this.fetchStoreInfo();
    }
}
</script>

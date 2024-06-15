<template>
    <OnlineStoreLayout title="Mi carrito">
        <div class="md:p-2 lg:p-9">
            <div class="pl-4 mt-3">
                <Back />
            </div>

            <section class="xl:w-[60%] mx-auto mt-5 border border-grayD9 rounded-lg mb-9">
                <p class="py-2 px-9 border-b border-grayD9">Mi carrito</p>

                <!-- body -->
                <div class="md:flex">
                    <!-- parte izquierda -->
                    <div class="border-r border-grayD9 p-2 md:py-4 md:px-9 md:w-[70%] h-96 xl:h-[440px] space-y-4 overflow-auto">
                        <div v-if="cart.length">
                            <CartProductCard @productRemoved="removeCartProduct($event)" @updateCart="updateCart($event)"
                            :cartProduct="product" v-for="product in cart" :key="product" :store="store" />
                        </div>
                        
                        <div v-else>
                            <el-empty description="No hay productos en tu carrito" />
                            <p @click="$inertia.get(route('online-sales.client-index', encodedIdStore ?? 0))" class="text-primary text-center cursor-pointer">Seguir comprando</p>
                        </div>
                    </div>

                    <!-- Parte derecha -->
                    <div class="md:w-[30%] md:py-4 md:px-5 text-sm">
                        <section v-if="cart.length">
                            <div class="border border-grayD9 grid grid-cols-3 gap-x-1 rounded-lg p-3 mb-5 *:mb-1">
                                    <p class="col-span-2">Subtotal:</p>
                                    <p><span class="mr-2">$</span>{{ cartTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                                    <p class="col-span-2">Costo de envío:</p>
                                    <!-- Costo de envio si esta activado el minimo para envio gratis -->
                                    <div v-if="store?.online_store_properties?.enabled_free_delivery">
                                        <p v-if="(cartTotal < store?.online_store_properties?.min_free_delivery)"><span class="mr-2">$</span>{{ parseFloat(store?.online_store_properties?.delivery_price || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                                        <p class="text-green-500" v-else><span class="mr-2">$</span>0</p>
                                    </div>
                                    <!-- Costo de envío si esta desactivado el envio gratis -->
                                    <p v-else><span class="mr-2">$</span>{{ parseFloat(store?.online_store_properties?.delivery_price || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                                    <p class="font-bold col-span-2">Total:</p>
                                    <p v-if="cartTotal < store?.online_store_properties?.min_free_delivery" class="font-bold"><span class="mr-2">$</span>{{ (cartTotal + parseFloat(store?.online_store_properties?.delivery_price || 0)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                                    <p v-else class="font-bold"><span class="mr-2">$</span>{{ cartTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            </div>

                            <p v-if="(cartTotal < store?.online_store_properties?.min_free_delivery) && store?.online_store_properties?.enabled_free_delivery" 
                                class="text-xs text-center text-gray99 mb-5">
                                Agregar 
                                <span>
                                    ${{ (parseFloat(store?.online_store_properties?.min_free_delivery || 0) - cartTotal)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </span>
                                más para conseguir envió gratis
                            </p>

                            <div class="text-center pb-5">
                                <PrimaryButton @click="$inertia.get(route('online-sales.create'))" class="!px-8">Finalizar pedido</PrimaryButton>
                                <p @click="$inertia.get(route('online-sales.client-index', encodedIdStore ?? 0))" class="text-primary mt-4 cursor-pointer">Seguir comprando</p>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
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

    }
},
components:{
OnlineStoreLayout,
CartProductCard,
PrimaryButton,
Back
},
props:{

},
methods:{
    updateCart(data) {
        const productIndex = this.cart.findIndex(item => item.id === data.id);

         if (productIndex !== -1) {
            this.cart[productIndex].quantity = data.quantity;
         }
    },
    removeCartProduct(cartProductId) {
        const productIndex = this.cart.findIndex(item => item.id === cartProductId);

         if (productIndex !== -1) {
            this.cart.splice(productIndex, 1);
         }
    },
    async fetchStoreInfo() {
        try {
            const response = await axios.get(route('stores.fetch-store-info', this.storeId));
            if ( response.status === 200 ) {
                this.store = response.data.store;

                //decodifica el id de la tienda
                const encodedId = btoa(this.storeId.toString());
                this.encodedIdStore = encodedId;
            }
        } catch (error) {
            console.log(error);
        }
    }
      
},
computed: {
    cartTotal() {
        return this.cart.reduce((sum, item) => {
            return sum + (item.price * item.quantity);
        }, 0);
    },
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

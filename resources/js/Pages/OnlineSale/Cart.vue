<template>
    <OnlineStoreLayout title="Mi carrito">
        <div class="p-2 md:p-9">
            <Back />

            <section class="xl:w-[60%] mx-auto mt-9 border border-grayD9 rounded-lg">
                <p class="py-2 px-9 border-b border-grayD9">Mi carrito</p>

                <!-- body -->
                <div class="md:flex">
                    <!-- parte izquierda -->
                    <div class="border-r border-grayD9 p-2 md:py-4 md:px-9 md:w-[70%] h-96 space-y-4 overflow-auto">
                        <CartProductCard @productRemoved="removeCartProduct($event)" @updateCart="updateCart($event)" :cartProduct="product" v-for="product in cart" :key="product" />
                    </div>

                    <!-- Parte derecha -->
                    <div class="md:w-[30%] md:py-4 md:px-9">
                        <div class="border border-grayD9 rounded-lg py-7 px-5 flex justify-between font-bold mb-5">
                            <p>Total:</p>
                            <p>${{ cartTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>

                        <div class="text-center">
                            <PrimaryButton @click="$inertia.get(route('online-sales.create'))" class="!px-8">Finalizar pedido</PrimaryButton>
                            <p @click="$inertia.get(route('online-sales.client-index', storeId ?? 0))" class="text-primary mt-4 cursor-pointer">Seguir comprando</p>
                        </div>
                            
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

export default {
data() {
    return {
        cart: [],
        storeId: null //se recupera el id de la tienda desde el localstorage
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
}
}
</script>

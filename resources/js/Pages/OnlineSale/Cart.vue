<template>
    <OnlineStoreLayout title="Mi carrito">
        <div class="p-4 md:p-9">
            <Back />

            <section class="xl:w-[60%] mx-auto mt-9 border border-grayD9 rounded-lg">
                <p class="py-2 px-9 border-b border-grayD9">Mi carrito</p>

                <!-- body -->
                <div class="flex">
                    <!-- parte izquierda -->
                    <div class="border-r border-grayD9 py-4 px-9 w-[70%]">
                        <CartProductCard :productId="product.id" v-for="product in cart" :key="product" />
                    </div>

                    <!-- Parte derecha -->
                    <div class="w-[30%]">

                    </div>
                </div>

            </section>
        </div>
    </OnlineStoreLayout>
  
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';

export default {
data() {
    return {
        cart: null
    }
},
components:{
OnlineStoreLayout,
PrimaryButton,
Back
},
props:{

},
methods:{
},
mounted() {
    // Obtener el carrito actual desde localStorage
        this.cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
}
}
</script>

<template>
    <div class="py-3 px-7 rounded-lg border border-gayD9 flex flex-col h-96">
            <!-- Imagen -->
        <figure class="h-1/2 text-center">
            <Link :href="product.global_product_id ? route('online-sales.show-global-product', product.global_product_id) : route('online-sales.show-local-product', product.id)">
                <img v-if="product.global_product_id ? product.global_product.media?.length : product.media?.length" 
                    :src="product.global_product_id ? product.global_product.media[0]?.original_url : product.media[0]?.original_url" 
                    alt="producto" class="h-full mx-auto">
                <div v-else>
                    <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                    <p class="text-sm text-gray-300">Imagen no disponible</p>
                </div>
            </Link>
        </figure>

        <!-- Detalles -->
        <div class="text-center mt-5 flex flex-col justify-center items-center">
            <h1 class="text-lg">{{ product.global_product_id ? product.global_product.name : product.name }}</h1>
            <p class="text-3xl font-bold my-3">${{ product.global_product_id ? product.global_product.public_price : product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            <el-input-number :disabled="product.global_product?.current_stock < 1 || product.current_stock < 1"
                v-model="quantity" class="mb-5" size="small" :min="0" 
                :max="product.current_stock" :precision="2" />
            <PrimaryButton class="!px-9 !py-1">Agregar al carrito</PrimaryButton>
        </div>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

export default {
data() {
    return {
        quantity: 1, //cantidad seleccionada para guardar al carrito
    }
},
components:{
PrimaryButton,
Link
},
props:{
product: Object
},
methods:{
},
}
</script>

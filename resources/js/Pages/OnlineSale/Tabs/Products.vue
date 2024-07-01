<template>
    <div>
        <h1 class="font-bold text-3xl text-center mb-12">Productos</h1>
        <h1 v-if="store.online_store_properties?.enabled_free_delivery && store.online_store_properties?.min_free_delivery"
            class="font-bold md:text-xl text-center text-primary mb-1">
            Envío gratis en compra mínima de ${{ store.online_store_properties?.min_free_delivery }}</h1>
        <div v-if="visibleProducts.length"
            class="md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mx-2 sm:mx-4 md:mx-9 space-y-4 md:space-y-0">
            <OnlineProductCard v-for="product in visibleProducts" :key="product" :product="product"
                :store="store" />
        </div>
        <el-empty v-else description="No hay productos en la tienda" />

        <!-- estado de carga -->
        <div v-if="loading" class="flex justify-center items-center py-10">
            <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
        </div>
    </div>
</template>

<script>
import OnlineProductCard from '@/Components/MyComponents/OnlineSale/OnlineProductCard.vue';

export default {
data() {
    return {
    };
},
components:{
    OnlineProductCard,
},
props:{
    store: Object,
    visibleProducts: Array
}
};
</script>

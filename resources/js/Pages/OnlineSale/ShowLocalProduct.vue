<template>
    <OnlineStoreLayout :title="product.name">
        <div class="p-4 md:p-9">
            <Back />

            <section class="md:w-1/2 grid grid-cols-2 gap-x-10 border mx-auto mt-9 h-72">
                <!-- Imagen del producto -->
                <figire class="border border-grayD9 rounded-md flex items-center justify-center">
                    <img v-if="product.media.length" :src="product.media[0]?.original_url" alt="producto" class="h-full mx-auto">
                    <div v-else>
                        <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                        <p class="text-sm text-gray-300">Imagen no disponible</p>
                    </div>
                </figire>

                <!-- Detalles del producto -->
                <div class="">
                    <h1 class="font-bold text-2xl mt-4">{{ product.name }}</h1>
                    <p class="text-xl text-primary font-bold mt-4">${{ product.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <div class="flex items-center space-x-3 mt-5">
                        <p class="text-gray99">Cantidad</p>
                        <div class="relative">
                            <el-input-number v-model="quantity" :disables=" product.current_stock < 1" :min="0" :max="product.current_stock" controls-position="right">
                                <template #decrease-icon>
                                <i class="fa-solid fa-angle-down"></i>
                                </template>
                                <template #increase-icon>
                                <i class="fa-solid fa-angle-up"></i>
                                </template>
                            </el-input-number>
                        </div>
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

export default {
data() {
    return {
        quantity: 1
    }
},
components:{
OnlineStoreLayout,
PrimaryButton,
Back
},
props:{
product: Object
},
methods:{}
}
</script>

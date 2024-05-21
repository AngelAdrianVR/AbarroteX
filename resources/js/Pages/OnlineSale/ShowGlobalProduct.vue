<template>
    <OnlineStoreLayout :title="global_product.global_product.name">
        <div class="p-4 md:p-9">
            <Back />

            <section class="xl:w-[60%] md:grid grid-cols-2 gap-x-10 mx-auto mt-9">
                <!-- Imagen del producto -->
                <figire class="border border-grayD9 rounded-md flex items-center justify-center h-96">
                    <img v-if="global_product.global_product?.media?.length" :src="global_product.global_product?.media[0]?.original_url" alt="producto" class="h-68 mx-auto">
                    <div v-else>
                        <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                        <p class="text-sm text-gray-300">Imagen no disponible</p>
                    </div>
                </figire>

                <!-- Detalles del producto -->
                <div class="h-96">
                    <h1 class="font-bold text-2xl mt-4">{{ global_product.global_product.name }}</h1>
                    <p class="text-2xl text-primary font-bold mt-4">${{ global_product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <div class="flex items-center space-x-3 mt-5">
                        <p class="text-gray99">Cantidad</p>
                        <el-input-number v-model="quantity" :disabled=" global_product.current_stock < 1" :min="0" :max="global_product.current_stock" controls-position="right">
                            <template #decrease-icon>
                            <i class="fa-solid fa-angle-down"></i>
                            </template>
                            <template #increase-icon>
                            <i class="fa-solid fa-angle-up"></i>
                            </template>
                        </el-input-number>
                        </div>
                        <!-- Boton -->
                        <div class="text-center mt-7">
                        <PrimaryButton @click="addToCart" :disabled="quantity < 1" class="!px-10">Agregar al carrito</PrimaryButton>
                        </div>
                        <!-- Características del producto -->
                        <div class="mt-7">
                            <h2 class="font-bold mb-3">Acerca del producto</h2>
                            <p>• Característica 1</p>
                            <p>• Característica 2</p>
                            <p>• Característica 3</p>
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
global_product: Object
},
methods:{
    addToCart() {
        // Obtener el carrito actual desde localStorage
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

        // Verificar si el producto ya está en el carrito
        const productInCart = cart.find(item => item.id === this.global_product.global_product_id);

        if (productInCart) {
            // Si el producto ya está en el carrito, actualizar la cantidad
            productInCart.quantity += this.quantity;
        } else {
            // Si el producto no está en el carrito, agregarlo
            cart.push({
                id: this.global_product.global_product_id,
                isLocal: false,
                price: this.product.public_price,
                quantity: this.quantity
            });
        }

        // Guardar el carrito actualizado en localStorage
        localStorage.setItem('Ezycart', JSON.stringify(cart));

        // Mostrar un mensaje o notificación al usuario
        this.$notify({
            title: "Correcto",
            message: "Se ha agregado correctamente al carrito",
            type: "success",
        });
    }
}
}
</script>

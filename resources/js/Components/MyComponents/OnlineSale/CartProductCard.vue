<template>
    <!-- estado de carga -->
    <div v-if="loading" class="flex justify-center items-center py-10">
        <i class="fa-sharp fa-solid fa-circle-notch text-4xl fa-spin ml-2 text-primary"></i>
    </div>
    <section class="text-sm mt-2" v-else>
        <div class="flex space-x-4">
            <!-- Imagen del producto -->
            <figure class="border border-grayD9 rounded-md p-2 w-28">
                <img v-if="product?.global_product_id ? product?.global_product.media?.length : product?.media?.length" 
                        :src="product?.global_product_id ? product?.global_product.media[0]?.original_url : product?.media[0]?.original_url" 
                        alt="producto" class="h-full w-full mx-auto object-contain">
                <div class="flex flex-col items-center justify-center" v-else>
                    <i class="fa-regular fa-image text-3xl text-gray-200"></i>
                    <p class="text-xs text-gray-300 text-center">Imagen no disponible</p>
                </div>
            </figure>

            <!-- Detalles del producto -->
            <div class="mt-3 space-y-2 w-full">
                <h1 class="font-bold">{{ product?.global_product_id ? product?.global_product.name : product?.name }}</h1>
                <p class="font-bold">${{ product?.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <div v-if="actions" class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <!-- Toma en cuenta el stock disponible si está activada la configuración de la tienda -->
                        <el-input-number v-if="store?.online_store_properties?.inventory" :disabled="product?.current_stock < 1"
                            v-model="quantity" size="small" :min="1" :max="product?.current_stock" :precision="2" />

                        <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
                        <el-input-number v-else v-model="quantity" size="small" :min="1" :max="999" :precision="2" />
                            <p v-if="store?.online_store_properties?.inventory" class="text-xs text-primary">disponibles: {{ product?.current_stock }}</p>
                    </div>
                    <!-- Eliminar producto de carrito -->
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                    title="¿Deseas continuar?" @confirm="deleteCartProduct()">
                    <template #reference>
                        <i
                        class="fa-regular fa-trash-can mr-3 text-primary text-sm bg-[#F2F2F2] rounded-full py-1 px-[7px] cursor-pointer"></i>
                    </template>
                    </el-popconfirm>
                </div>
                <p v-else>cantidad: {{ quantity }}</p>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
export default {
data() {
    return {
        loading: false,
        product: null,
        quantity: this.cartProduct.quantity,
    }
},
props:{
    cartProduct: Object,
    store: Object,
    actions: { //acciones de borrar y editar cantidad
        type: Boolean,
        default: true
    }
},
watch: {
    quantity(newQuantity) {
        this.updateCartQuantity(newQuantity);
    }
},
emits: ['updateCart', 'productRemoved'],
methods:{
    async fetchProductInfo() {
        this.loading = true;
        try {
            const response = await axios.get(route('online-sales.fetch-product', [this.cartProduct.id, this.cartProduct.isLocal]));
            if ( response.status === 200 ) {
                this.product = response.data.item;
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
    updateCartQuantity(newQuantity) {
        // Encuentra el producto en el carrito y actualiza su cantidad
        const cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
        const productIndex = cart.findIndex(item => item.id === this.cartProduct.id);

        if (productIndex !== -1) {
            cart[productIndex].quantity = newQuantity;
            localStorage.setItem('Ezycart', JSON.stringify(cart));

            // //recupera el nuvo carrito actualizado para mandarlo en el emit
            // const newCart = JSON.parse(localStorage.getItem('Ezycart')) || [];
            this.$emit('updateCart', { id: this.cartProduct.id, quantity: this.quantity });
        }
    },
    deleteCartProduct() {
        // Eliminar el producto del carrito
        const cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
        const updatedCart = cart.filter(item => item.id !== this.cartProduct.id);
        localStorage.setItem('Ezycart', JSON.stringify(updatedCart));

        this.$emit('productRemoved', this.cartProduct.id); // Emitir un evento para notificar al padre
    }
},
mounted() {
    this.fetchProductInfo(); //recupera la info del producto para mostrarlo
}
}
</script>

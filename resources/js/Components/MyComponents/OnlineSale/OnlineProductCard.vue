<template>
    <div class="py-3 px-5 rounded-lg border-2 border-gayD9 flex flex-col h-96 hover:border-primary relative group">
        <!-- Deatalle de cantidad disponible  -->
        <div v-if="store?.online_store_properties?.inventory" class="absolute top-0 left-0 w-full bg-black opacity-60 rounded-t-lg hidden group-hover:block">
            <p class="text-white text-center py-2">{{  product.current_stock ?? '0' }} Unidades disponibles</p>
        </div>
            <!-- Imagen -->
        <figure class="h-1/2 text-center">
            <Link :href="product.global_product_id ? route('online-sales.show-global-product', product.id) : route('online-sales.show-local-product', product.id)">
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
            <h1>{{ product.global_product_id ? product.global_product.name : product.name }}</h1>
            <p class="text-3xl font-bold my-3">${{ product.global_product_id ? product.global_product.public_price : product.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            <!-- Toma en cuenta el stock disponible si está activada la configuración de la tienda -->
            <el-input-number v-if="store?.online_store_properties?.inventory" :disabled="product.current_stock < 1"
                v-model="quantity" class="mb-5" size="small" :min="0" 
                :max="product.current_stock" :precision="2" />

            <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
            <el-input-number v-else v-model="quantity" class="mb-5" size="small" :min="1" :max="999" :precision="2" />
            <PrimaryButton :disabled="store?.online_store_properties?.inventory && product.current_stock < 1" @click="addToCart" class="!px-9 !py-1">Agregar al carrito</PrimaryButton>
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
        storeId: null, //recupera el id de la tienda almacenada en el local storage
        store: null, //recupera toda la informacióon de la tienda.
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
    addToCart() {
        // Obtener el carrito actual desde localStorage
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

        if ( this.product.global_product_id ) {

            // Verificar si el producto ya está en el carrito comparando id y si es local o no
            const productInCart = cart.find(item => item.id === this.product.global_product_id && item.isLocal == false);

            if (productInCart) {
                // Si el producto ya está en el carrito, actualizar la cantidad
                productInCart.quantity += this.quantity;
            } else {
                // Si el producto no está en el carrito, agregarlo
                cart.push({
                    id: this.product.id,
                    name: this.product.global_product.name,
                    isLocal: false,
                    price: this.product.public_price,
                    quantity: this.quantity,
                    image_url: this.product.global_product.media[0]?.original_url
                });
            }
        } else {

             // Verificar si el producto ya está en el carrito
            const productInCart = cart.find(item => item.id === this.product.id && item.isLocal == true);

            if (productInCart) {
                // Si el producto ya está en el carrito, actualizar la cantidad
                productInCart.quantity += this.quantity;
            } else {
                // Si el producto no está en el carrito, agregarlo
                cart.push({
                    id: this.product.id,
                    name: this.product.name,
                    price: this.product.public_price,
                    isLocal: true,
                    quantity: this.quantity,
                    image_url: this.product.media[0]?.original_url
                });
            }
        }

        // Guardar el carrito actualizado en localStorage
        localStorage.setItem('Ezycart', JSON.stringify(cart));

        // Mostrar un mensaje o notificación al usuario
        this.$notify({
            title: "Correcto",
            message: "Se ha agregado correctamente al carrito",
            type: "success",
        });
    },
    async fetchStoreInfo() {
        try {
            const response = await axios.get(route('stores.fetch-store-info', this.storeId));
            if ( response.status === 200 ) {
                this.store = response.data.store;
            }
        } catch (error) {
            console.log(error);
        }
    }
},
mounted() {
    // recupera el store_id del localStorage
    this.storeId = localStorage.getItem('storeId');

    // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
    this.fetchStoreInfo();
}
}
</script>

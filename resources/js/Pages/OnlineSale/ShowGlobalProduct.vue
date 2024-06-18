<template>
    <OnlineStoreLayout :title="global_product.global_product.name">
        <div class="p-4 md:p-9">
            <Back :to="route('online-sales.client-index', encodedIdStore ?? 0)" />

            <section class="xl:w-[60%] md:grid grid-cols-2 gap-x-10 mx-auto mt-9">
                <!-- Imagen del producto -->
                <figire class="border border-grayD9 rounded-md flex items-center justify-center h-96">
                    <img v-if="global_product.global_product?.media?.length" :src="global_product.global_product?.media[0]?.original_url" alt="producto" 
                    class="h-full mx-auto object-contain">
                    <div v-else>
                        <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                        <p class="text-sm text-gray-300">Imagen no disponible</p>
                    </div>
                </figire>

                <!-- Detalles del producto -->
                <div class="h-96">
                    <h1 class="font-bold text-2xl mt-4">{{ global_product.global_product.name }}</h1>
                    <p class="text-2xl text-primary font-bold mt-4">
                        ${{ integerPart }}
                        <span class="decimal-part">{{ decimalPart }}</span>
                    </p>
                    <div class="flex items-center space-x-3 mt-5">
                        <p class="text-gray99">Cantidad</p>
                        
                        <!-- Toma en cuenta el stock disponible si está activada la configuración de la tienda -->
                        <el-input-number v-if="store?.online_store_properties?.inventory"
                         v-model="quantity" :disabled=" global_product.current_stock < 1" :min="0" :max="global_product.current_stock" controls-position="right">
                            <template #decrease-icon>
                            <i class="fa-solid fa-angle-down"></i>
                            </template>
                            <template #increase-icon>
                            <i class="fa-solid fa-angle-up"></i>
                            </template>
                        </el-input-number>

                        <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
                        <el-input-number v-else
                            v-model="quantity" :min="1" :max="999" controls-position="right">
                            <template #decrease-icon>
                            <i class="fa-solid fa-angle-down"></i>
                            </template>
                            <template #increase-icon>
                            <i class="fa-solid fa-angle-up"></i>
                            </template>
                        </el-input-number>
                        </div>
                        <!-- Se muestran las unidades disponibles si esta activada la configuración de inventario para tienda online -->
                        <p v-if="store?.online_store_properties?.inventory" class="mt-2 text-sm">Unidades disponibles: <span> {{ global_product.current_stock }} </span></p>
                        <!-- Boton -->
                        <div class="text-center mt-7">
                        <PrimaryButton @click="addToCart" :disabled="quantity < 1" class="!px-10">Agregar al carrito</PrimaryButton>
                        </div>
                        <!-- Características del producto -->
                    <div v-if="global_product.description" class="mt-7">
                        <h2 class="font-bold mb-3">Acerca del producto</h2>
                        <div>
                            <p class="whitespace-break-spaces">{{ formattedDescription }}</p>
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
        quantity: 1,
        formattedDescription: null, //descripción del producto formateado con viñetas
        storeId: null, //recupera el id de la tienda almacenada en el local storage
        store: null, //recupera toda la informacióon de la tienda.
        encodedIdStore: null, //id de la tienda codificado
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
        console.log('entró')
        // Obtener el carrito actual desde localStorage
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

        // Verificar si el producto ya está en el carrito
        const productInCart = cart.find(item => item.id === this.global_product.global_product_id && item.isLocal == false);

        if (productInCart) {
            // Si el producto ya está en el carrito, actualizar la cantidad
            productInCart.quantity += this.quantity;
        } else {
            // Si el producto no está en el carrito, agregarlo
            cart.push({
                id: this.global_product.id,
                name: this.global_product.global_product.name,
                isLocal: false,
                price: this.global_product.public_price,
                quantity: this.quantity,
                image_url: this.global_product.global_product.media[0]?.original_url
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
    },
    formatDescription() {
        if ( this.global_product.description != null ) {
            const text = this.global_product.description;
            const lines = text.split('\n');
            const formattedLines = lines.map(line => `• ${line.trim()}`);
            this.formattedDescription = formattedLines.join('\n');
        }
    },
    encodeStoreId() {
      const encodedId = btoa(this.storeId.toString());
      this.encodedIdStore = encodedId;
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
created() {
    this.formatDescription();

    // recupera el store_id del localStorage
    this.storeId = localStorage.getItem('storeId');

    //condifica el id de la tienda
    this.encodeStoreId();
    
    // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
    this.fetchStoreInfo();
},
computed: {
    integerPart() {
      // Obtener la parte entera del precio y formatearla con comas
      return Math.floor(this.global_product.public_price).toLocaleString();
    },
    decimalPart() {
      // Obtener los decimales del precio sin el punto decimal
      const decimalStr = this.global_product.public_price.toFixed(2).split('.')[1];
      return decimalStr;
    }
}
}
</script>

<style scoped>
.decimal-part {
  font-size: 0.65em;
  vertical-align: super;
}
.whitespace-break-spaces {
    white-space: pre-wrap; /* Respect line breaks */
}
</style>


<template>
    <OnlineStoreLayout :title="product.name">
        <div class="p-4 md:p-9 lg:h-[calc(100vh-161px)]">
            <Back :to="route('online-sales.client-index', store?.slug ?? '')" />

            <section class="xl:w-[60%] md:grid grid-cols-2 gap-x-10 mx-auto mt-9">
                <!-- Imagen del producto -->
                <figure class="border border-grayD9 rounded-md flex items-center justify-center h-96">
                    <img v-if="product.media.length" :src="product.media[0]?.original_url" alt="producto"
                        class="h-full mx-auto object-contain">
                    <div v-else>
                        <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                        <p class="text-sm text-gray-300">Imagen no disponible</p>
                    </div>
                </figure>
                <!-- Detalles del producto -->
                <div>
                    <h1 class="font-bold text-2xl mt-4">{{ product.name }}</h1>
                    <p class="text-2xl text-primary font-bold mt-4">
                        <span>{{ product.currency === '$USD' ? 'USD' : 'MXN' }} ${{ integerPart }}</span>
                        <span class="decimal-part">{{ decimalPart }}</span>
                        <span class="text-lg">{{ product.bulk_product ? '/ ' + product.measure_unit : '' }}</span>
                    </p>

                    <!-- Si no es bajo pedido -->
                    <div v-if="product.product_on_request" class="">
                        <div class="flex items-center space-x-3 mt-5">
                            <p class="text-gray99">Cantidad</p>

                            <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
                            <el-input-number v-model="quantity" :min="0" :max="999" controls-position="right">
                                <template #decrease-icon>
                                    <i class="fa-solid fa-angle-down"></i>
                                </template>
                                <template #increase-icon>
                                    <i class="fa-solid fa-angle-up"></i>
                                </template>
                            </el-input-number>
                        </div>
                    </div>

                    <!-- Si no es bajo pedido -->
                    <div v-else>
                        <div class="flex items-center space-x-3 mt-5">
                            <p class="text-gray99">Cantidad</p>
                            
                            <!-- Toma en cuenta el stock disponible si está activada la configuración de la tienda -->
                            <el-input-number v-if="store?.online_store_properties?.inventory" v-model="quantity"
                                :disabled="product.current_stock < 1" :min="0" :max="product.current_stock"
                                controls-position="right">
                                <template #decrease-icon>
                                    <i class="fa-solid fa-angle-down"></i>
                                </template>
                                <template #increase-icon>
                                    <i class="fa-solid fa-angle-up"></i>
                                </template>
                            </el-input-number>

                            <!-- No toma en cuenta el stock disponible si no está activada esa configuración -->
                            <el-input-number v-else v-model="quantity" :min="0" :max="999" controls-position="right">
                                <template #decrease-icon>
                                    <i class="fa-solid fa-angle-down"></i>
                                </template>
                                <template #increase-icon>
                                    <i class="fa-solid fa-angle-up"></i>
                                </template>
                            </el-input-number>
                        </div>
                    </div>

                    <!-- Se muestran las unidades disponibles si esta activada la configuración de inventario para tienda online -->
                    <p v-if="store?.online_store_properties?.inventory" class="mt-2 text-sm">Unidades disponibles:
                        <span> {{
                            product.current_stock ?? 0 }} </span>
                    </p>

                    <p v-if="product.product_on_request" class="font-bold text-sm my-2">*Este producto se surte bajo pedido. Tiempo estimado de entrega: {{ product.days_for_delivery }} días hábiles.</p>

                    <p v-if="alreadyInCart" class="text-green-500 text-lg mt-5 text-center"><i class="fa-regular fa-circle-check"></i> Agregado a carrito</p>

                    <!-- Boton -->
                    <div v-else class="text-center mt-7">
                        <PrimaryButton @click="addToCart" :disabled="quantity < 1" class="!px-10">Agregar al carrito
                        </PrimaryButton>
                    </div>
                    <!-- Características del producto -->
                    <div v-if="product.description" class="mt-7">
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
import emitter from '@/eventBus.js';

export default {
    data() {
        return {
            quantity: 1,
            formattedDescription: null, //descripción del producto formateado con viñetas
            storeId: null, //recupera el id de la tienda almacenada en el local storage
            store: null, //recupera toda la informacióon de la tienda.
            encodedIdStore: null, //id de la tienda codificado
            alreadyInCart: false, //bandera para saber si ya esta en carrito para evitar error con cantidades
        }
    },
    components: {
        OnlineStoreLayout,
        PrimaryButton,
        Back
    },
    props: {
        product: Object
    },
    methods: {
        addToCart() {
            // Obtener el carrito actual desde localStorage
            let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

            // Verificar si el producto ya está en el carrito
            const productInCart = cart.find(item => item.product_id === this.product.id && item.isLocal == true);

            if (productInCart) {
                // Si el producto ya está en el carrito, actualizar la cantidad
                productInCart.quantity += this.quantity;
            } else {
                // Si el producto no está en el carrito, agregarlo
                cart.push({
                    product_id: this.product.id,
                    name: this.product.name,
                    isLocal: true,
                    price: this.product.public_price,
                    quantity: this.quantity,
                    image_url: this.product.media[0]?.original_url
                });
                this.alreadyInCart = true; //bandera de que ya está agregado en carrito
            }

            // Guardar el carrito actualizado en localStorage
            localStorage.setItem('Ezycart', JSON.stringify(cart));

            // Mostrar un mensaje o notificación al usuario
            this.$notify({
                title: "Correcto",
                message: "Se ha agregado correctamente al carrito",
                type: "success",
                position: "bottom-right",
            });

            // Emitir el evento personalizado
            emitter.emit('update-cart');
        },
        formatDescription() {
            if (this.product.description != null) {
                const text = this.product.description;
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
                if (response.status === 200) {
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
    mounted() {
        //revisa si el producto ya ha sido agregado al carrito
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
        const productInCart = cart.find(item => item.name == this.product.name );
        if (productInCart) {
            this.alreadyInCart = true;
        }
    },
    computed: {
        integerPart() {
            // Obtener la parte entera del precio y formatearla con comas
            return Math.floor(this.product.public_price).toLocaleString();
        },
        decimalPart() {
            // Obtener los decimales del precio sin el punto decimal
            const decimalStr = this.product.public_price.toFixed(2).split('.')[1];
            return decimalStr;
        }
    }
}
</script>

<style scoped>
.decimal-part {
    font-size: 0.50em;
    vertical-align: super;
}

.whitespace-break-spaces {
    white-space: pre-wrap;
    /* Respect line breaks */
}
</style>

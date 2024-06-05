<template>
    <OnlineStoreLayout :title="product.name">
        <div class="p-4 md:p-9">
            <Back />

            <section class="xl:w-[60%] md:grid grid-cols-2 gap-x-10 mx-auto mt-9">
                <!-- Imagen del producto -->
                <figire class="border border-grayD9 rounded-md flex items-center justify-center h-96">
                    <img v-if="product.media.length" :src="product.media[0]?.original_url" alt="producto" class="h-full mx-auto">
                    <div v-else>
                        <i class="fa-regular fa-image text-9xl text-gray-200"></i>
                        <p class="text-sm text-gray-300">Imagen no disponible</p>
                    </div>
                </figire>

                <!-- Detalles del producto -->
                <div>
                    <h1 class="font-bold text-2xl mt-4">{{ product.name }}</h1>
                    <p class="text-2xl text-primary font-bold mt-4">
                        ${{ integerPart }}
                        <span class="decimal-part">{{ decimalPart }}</span>
                    </p>
                    <div class="flex items-center space-x-3 mt-5">
                    <p class="text-gray99">Cantidad</p>
                    <el-input-number v-model="quantity" :disabled=" product.current_stock < 1" :min="0" :max="product.current_stock" controls-position="right">
                        <template #decrease-icon>
                        <i class="fa-solid fa-angle-down"></i>
                        </template>
                        <template #increase-icon>
                        <i class="fa-solid fa-angle-up"></i>
                        </template>
                    </el-input-number>
                    </div>
                    <p class="mt-2 text-sm">Unidades disponibles: <span> {{ product.current_stock }} </span></p>
                    <!-- Boton -->
                    <div class="text-center mt-7">
                    <PrimaryButton @click="addToCart" :disabled="quantity < 1" class="!px-10">Agregar al carrito</PrimaryButton>
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

export default {
data() {
    return {
        quantity: 1,
        formattedDescription: null, //descripción del producto formateado con viñetas
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
methods:{
    addToCart() {
        // Obtener el carrito actual desde localStorage
        let cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

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
                isLocal: true,
                price: this.product.public_price,
                quantity: this.quantity,
                image_url: this.product.media[0]?.original_url
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
        if ( this.product.description != null ) {
            const text = this.product.description;
            const lines = text.split('\n');
            const formattedLines = lines.map(line => `• ${line.trim()}`);
            this.formattedDescription = formattedLines.join('\n');
        }
    }
},
mounted() {
    this.formatDescription();
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
  font-size: 0.75em;
  vertical-align: super;
}
.whitespace-break-spaces {
    white-space: pre-wrap; /* Respect line breaks */
}
</style>

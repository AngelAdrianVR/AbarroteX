<template>
    <div class="flex space-x-2 flex-row justify-between items-center common-container text-sm">
        <figure class="border border-l-grayD9 rounded-md size-14 flex items-center justify-center p-1">
            <img class="object-contain h-full" v-if="local_image_url" :src="local_image_url" alt="">
            <div v-else
                class="size-12 bg-white text-gray99 rounded-md text-sm flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
        </figure>
        <div class="!w-56">
            <div class="flex items-center space-x-2">
                <InputLabel value="Producto*" class="mb-1" />
                <p v-if="error_validation" class="text-red-400 text-xs mb-1">Seleccionar un producto</p>
            </div>
            <el-select @change="syncItem" v-model="selection" class="!w-full" filterable required clearable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option :disabled="product.disabled" v-for="product in products" :key="product" :value="product.relative_id" :label="product.name" />
            </el-select>
        </div>
        <div class="w-24">
            <InputLabel value="Precio unitario" class="mb-1 text-sm" />
            <el-input disabled v-model="price" min="1" required type="number"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')"
                placeholder="0.00">
                <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                </template>
            </el-input>
        </div>
        <div class="w-24">
            <InputLabel value="Cantidad" class="mb-1 text-sm" />
            <el-input @change="syncItem" min="1" v-model.number="quantity" type="number" placeholder="Ingresa la cantidad" />
        </div>
        <div>
            <InputLabel value="Total" class="mb-1 text-sm" />
            <p><span class="mr-2">$</span>{{ (quantity * price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
        </div>
        <button type="button" @click="handleDelete">
            <i class="fa-regular fa-trash-can text-sm text-primary cursor-pointer pt-2"></i>
        </button>
    </div>
</template>

<script>
import InputLabel from "@/Components/InputLabel.vue";

export default {
    data() {
        return {
            selection: null, //propiedad requeridas para una venta en linea. (id)
            price: 0, //propiedad requeridas para una venta en linea.
            isLocal: false, //propiedad requeridas para una venta en linea.
            quantity: 1, //propiedad requeridas para una venta en linea.
            local_image_url: null, //guarda el url de la imagen del producto.
            error_validation: false,
            last_product_index_selected: null //guarda el producto seleccionado para habilitarlo de nuevo si se cambia
        };
    },
    components:{
        InputLabel
    },
    emits: ['deleteItem', 'syncItem'],
    props: {
        products: Array,
        id: Number,
        init_state: {
            type: Object,
            default: null,
        },
    },
    mounted() {
        if (this.init_state != null) {
            if ( this.init_state.name ) {
                const productSelectedIndex = this.products.findIndex(item => item.name == this.init_state.name);
                this.selection = this.products[productSelectedIndex].relative_id;
            } else {
                this.selection = null;
            }
            this.price = this.init_state.price;
            this.isLocal = this.init_state.isLocal;
            this.quantity = this.init_state.quantity;
        }
    },
    methods:{
        handleDelete() {
            const productSelectedIndex = this.products.findIndex(item => item.relative_id === this.selection);
            if ( productSelectedIndex != -1 ) {
                this.products[productSelectedIndex].disabled = false;
            }
            this.$emit('deleteItem');
        }
    },
    computed: {
        syncItem() {
            if (this.selection != null) {
                // Encuentra el índice del producto seleccionado en la lista de productos
                const productSelectedIndex = this.products.findIndex(item => item.relative_id === this.selection);

                if (productSelectedIndex != -1) {
                    // Si hay un producto seleccionado anteriormente, habilítalo
                    if (this.last_product_index_selected != null) {
                        this.products[this.last_product_index_selected].disabled = false;
                    }

                    // Guarda el índice del producto seleccionado actualmente
                    this.last_product_index_selected = productSelectedIndex;

                    // Deshabilita el producto seleccionado actualmente para evitar múltiples selecciones
                    this.products[productSelectedIndex].disabled = true;

                    // Emite el evento con la información del producto seleccionado
                    this.price = this.products[productSelectedIndex].price;
                    this.local_image_url = this.products[productSelectedIndex].image_url;
                    this.$emit('syncItem', {
                        id: this.id,
                        name: this.products[productSelectedIndex].name,
                        product_id: this.products[productSelectedIndex].id,
                        price: this.products[productSelectedIndex].price,
                        isLocal: this.products[productSelectedIndex].isLocal,
                        quantity: this.quantity,
                        image_url: this.products[productSelectedIndex].image_url,
                    });
                    this.error_validation = false;
                }
            } else {
                this.error_validation = true;
            }
        }

    },
};
</script>
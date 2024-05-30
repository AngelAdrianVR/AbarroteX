<template>
    <div class="flex space-x-2 space-y-0 flex-row justify-between items-center common-container">
        <div class="w-72">
            <div class="flex items-center space-x-2">
                <InputLabel value="Producto*" class="mb-1" />
                <p v-if="error_validation" class="text-red-400 text-xs mb-1">Seleccionar un producto</p>
            </div>
            <el-select @change="syncItem" v-model="selection" class="!w-full" filterable required clearable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="product in products" :key="product.id" :value="product.id" 
                :label="product.name" />
            </el-select>
        </div>
        <div class="w-24">
            <InputLabel value="Precio unitario" class="mb-1 text-sm" />
            <el-input v-model="price" required type="number"
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
            <el-input @change="syncItem" v-model.number="quantity" type="number" placeholder="Ingresa la cantidad" />
        </div>
        <div>
            <InputLabel value="Total" class="mb-1 text-sm" />
            <p><span class="mr-2">$</span>{{ (quantity * price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
        </div>
        <button type="button" @click="$emit('deleteItem')">
            <i class="
          fa-regular fa-trash-can
          text-sm
          text-primary
          cursor-pointer
        "></i>
        </button>
    </div>
</template>

<script>
import InputLabel from "@/Components/InputLabel.vue";

export default {
    data() {
        return {
            selection: null, //propiedad requeridas para una venta en linea. (id)
            price: 1, //propiedad requeridas para una venta en linea.
            is_local: false, //propiedad requeridas para una venta en linea.
            quantity: 1, //propiedad requeridas para una venta en linea.
            error_validation: false,
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
            this.selection = this.init_state.product_id;
            this.quantity = this.init_state.quantity;
        }
    },
    computed: {
        getTotal() {
            return this.selection != null
                ? (this.quantity * this.products.find(product => { return product.id === this.selection }).price)?.toFixed(2) +
                " " +
                this.products.find(product => { return product.id === this.selection }).currency
                : 0;
        },
        getPrice() {
            return this.selection != null
                ? this.products.find(product => { return product.id === this.selection }).price + ' ' +
                this.products.find(product => { return product.id === this.selection }).currency +
                " / Unidad"
                : 0;
        },
        syncItem() {
            if (this.selection != null && this.quantity) {
                this.$emit('syncItem', {
                    id: this.selection,
                    price: this.id,
                    is_local: this.selection,
                    quantity: this.quantity,
                });
                this.error_validation = false;
            } else {
                this.error_validation = true;
            }
        },
    },
};
</script>
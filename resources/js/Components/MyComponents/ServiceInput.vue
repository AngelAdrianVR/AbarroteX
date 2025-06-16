<template>
    <div class="flex space-x-2 flex-row justify-between items-center common-container text-sm">
        <div class="!w-56">
            <div class="flex items-center space-x-2">
                <InputLabel value="Servicio*" class="mb-1" />
                <p v-if="error_validation" class="text-red-400 text-xs mb-1">Seleccionar un servicio</p>
            </div>
            <el-select ref="serviceSelector" @change="syncItem" v-model="selection" class="!w-full" filterable required
                clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option :disabled="service.disabled" v-for="service in services" :key="service" :value="service.id"
                    :label="service.name" />
            </el-select>
        </div>
        <div class="w-24">
            <InputLabel value="Precio/u" />
            <el-input v-model="price" @change="handlePriceChange" required
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="0.00">
                <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                </template>
            </el-input>
        </div>
        <div class="w-24">
            <InputLabel value="Cantidad" />
            <el-input @change="syncItem" min="1" v-model.number="quantity" type="number"
                placeholder="Ingresa la cantidad" />
        </div>
        <div>
            <InputLabel value="Total" />
            <p><span class="mr-2">$</span>{{ (quantity * price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
        </div>
        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                :title="'¿Continuar?'" @confirm="handleDelete">
                <template #reference>
                    <button type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </template>
            </el-popconfirm>
    </div>
</template>

<script>
import InputLabel from "@/Components/InputLabel.vue";

export default {
    data() {
        return {
            selection: null, //propiedad requeridas para servicio. (id)
            price: 0, //propiedad requeridas para servicio.
            quantity: 1, //propiedad requeridas para servicio.
            error_validation: false,
            last_service_index_selected: null, //guarda el servicio seleccionado para habilitarlo de nuevo si se cambia
            isPriceManuallyChanged: false // Nueva bandera para rastrear cambios manuales en el precio
        };
    },
    components: {
        InputLabel
    },
    emits: ['deleteItem', 'syncItem'],
    props: {
        services: Array,
        id: Number,
        init_state: {
            type: Object,
            default: null,
        },
    },
    computed: {
    },
    methods: {
        handleDelete() {
            const serviceSelectedIndex = this.services.findIndex(item => item.id === this.selection);
            if (serviceSelectedIndex != -1) {
                this.services[serviceSelectedIndex].disabled = false;
            }
            this.$emit('deleteItem');
        },
        handlePriceChange() {
            this.isPriceManuallyChanged = true;
            this.syncItem();
        },
        syncItem() {
            if (this.selection != null) {
                const serviceSelectedIndex = this.services.findIndex(item => item.id === this.selection);

                if (serviceSelectedIndex != -1) {
                    // Solo actualizar precio si es una nueva selección y no se ha modificado manualmente
                    if (this.last_service_index_selected !== serviceSelectedIndex) {
                        if (this.last_service_index_selected != null) {
                            this.services[this.last_service_index_selected].disabled = false;
                            this.isPriceManuallyChanged = false;
                        }

                        this.last_service_index_selected = serviceSelectedIndex;
                        this.services[serviceSelectedIndex].disabled = true;

                        // Solo actualizar precio automáticamente si no ha sido modificado manualmente
                        if (!this.isPriceManuallyChanged) {
                            this.price = this.services[serviceSelectedIndex].price;
                        }
                    }

                    // Emitir siempre los datos actuales
                    this.$emit('syncItem', {
                        id: this.id,
                        name: this.services[serviceSelectedIndex].name,
                        service_id: this.services[serviceSelectedIndex].id,
                        price: this.price,
                        description: this.services[serviceSelectedIndex].description,
                        quantity: this.quantity,
                    });

                    this.error_validation = false;
                }
            } else {
                this.error_validation = true;
            }
        },
        initializeFromState() {
            if (this.init_state.name) {
                const serviceSelectedIndex = this.services.findIndex(
                    item => item.name == this.init_state.name
                );
                
                if (serviceSelectedIndex !== -1) {
                    this.selection = this.services[serviceSelectedIndex].id;
                    // this.updateProductImage(this.services[serviceSelectedIndex]);

                    // Marcar como modificado si el precio inicial es diferente al del producto
                    // if (this.init_state.price !== this.services[serviceSelectedIndex].price) {
                    //     this.isPriceManuallyChanged = true;
                    // }
                }
            }

            this.price = this.init_state.price;
            this.isLocal = this.init_state.isLocal;
            this.quantity = this.init_state.quantity;
        },
    },
    mounted() {
        if (this.init_state != null) {
            this.initializeFromState();
        }
        this.$refs.serviceSelector.focus();
    },
};
</script>
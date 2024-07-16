<template>
    <div class="flex space-x-2 flex-row justify-between items-center common-container text-sm">
        <div class="!w-56">
            <div class="flex items-center space-x-2">
                <InputLabel value="Servicio*" class="mb-1" />
                <p v-if="error_validation" class="text-red-400 text-xs mb-1">Seleccionar un servicio</p>
            </div>
            <el-select @change="syncItem" v-model="selection" class="!w-full" filterable required clearable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option :disabled="service.disabled" v-for="service in services" :key="service" :value="service.id" :label="service.name" />
            </el-select>
        </div>
        <div class="w-24">
            <InputLabel value="Precio/u" class="mb-1 text-sm" />
            <el-input disabled v-model="price" min="1" required type="text"
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
            selection: null, //propiedad requeridas para servicio. (id)
            price: 0, //propiedad requeridas para servicio.
            quantity: 1, //propiedad requeridas para servicio.
            error_validation: false,
            last_service_index_selected: null //guarda el servicio seleccionado para habilitarlo de nuevo si se cambia
        };
    },
    components:{
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
    mounted() {
        if (this.init_state != null) {
            if ( this.init_state.name ) {
                const serviceSelectedIndex = this.services.findIndex(item => item.name === this.init_state.name);
                this.selection = this.services[serviceSelectedIndex].id;
            } else {
                this.selection = null;
            }
            this.price = this.init_state.price;
            this.quantity = this.init_state.quantity;
        }
    },
    methods:{
        handleDelete() {
            const serviceSelectedIndex = this.services.findIndex(item => item.id === this.selection);
            if ( serviceSelectedIndex != -1 ) {
                this.services[serviceSelectedIndex].disabled = false;
            }
            this.$emit('deleteItem');
        }
    },
    computed: {
        syncItem() {
            if (this.selection != null) {
                // Encuentra el índice del servicio seleccionado en la lista de servicios
                const serviceSelectedIndex = this.services.findIndex(item => item.id === this.selection);

                if (serviceSelectedIndex != -1) {
                    // Si hay un servicio seleccionado anteriormente, habilítalo
                    if (this.last_service_index_selected != null) {
                        this.services[this.last_service_index_selected].disabled = false;
                    }

                    // Guarda el índice del servicio seleccionado actualmente
                    this.last_service_index_selected = serviceSelectedIndex;

                    // Deshabilita el servicio seleccionado actualmente para evitar múltiples selecciones
                    this.services[serviceSelectedIndex].disabled = true;

                    // Emite el evento con la información del servicio seleccionado
                    this.price = this.services[serviceSelectedIndex].price;
                    this.local_image_url = this.services[serviceSelectedIndex].image_url;
                    this.$emit('syncItem', {
                        id: this.id,
                        name: this.services[serviceSelectedIndex].name,
                        service_id: this.services[serviceSelectedIndex].id,
                        price: this.services[serviceSelectedIndex].price,
                        description: this.services[serviceSelectedIndex].description,
                        quantity: this.quantity,
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
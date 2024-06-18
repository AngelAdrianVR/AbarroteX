<template>
    <AppLayout title="Crear cotización">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear cotización</h1>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Cliente (en caso de tenerlo registrado)" class="ml-3 mb-1" />
                        <button
                            @click="showClientFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center mb-1">
                            <i class="fa-solid fa-plus text-primary text-[9px] pl-[1px]"></i>
                        </button>
                    </div>
                    <el-select class="w-1/2" filterable v-model="form.client_id" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="client in clients" :key="client" :label="client.name"
                            :value="client.id" />
                    </el-select>
                    <InputError :message="form.errors.client_id" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Nombre del contacto*" class="ml-3 mb-1" />
                    <el-input v-model="form.contact_name" placeholder="Escribe el nombre del contacto" :maxlength="100" clearable />
                    <InputError :message="form.errors.contact_name" />
                </div>

                <div class="mt-3">
                    <InputLabel class="mb-1 ml-2" value="Teléfono*" />
                    <el-input v-model="form.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Correo electrónico (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="form.email" placeholder="Escribe el correo electrónico del contacto" :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>
                
                <div class="mt-3 col-span-full">
                    <InputLabel value="Dirección (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="form.address" placeholder="Escribe la dirección del cliente" :maxlength="100" clearable />
                    <InputError :message="form.errors.address" />
                </div>

                <!-- productos -->
                <!-- ------------------------------------------------------------------- -->
                <h2 class="font-bold ml-3 mt-3 mb-1 col-span-full">Productos</h2>

                <section class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id" :id="item.id"
                        @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.products?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                    productos </p>
                </section>
                <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                        <i class="fa-solid fa-plus"></i>
                        Agregar producto
                    </button>
                </div>
                <!-- -------------------------------------------------------------------- -->


                <!-- Servicios -->
                <!-- ------------------------------------------------------------------- -->
                <h2 class="font-bold ml-3 mt-3 mb-1 col-span-full">Servicios</h2>

                <section class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ServiceInput :services="services" v-for="(item, index) in form.services" :key="item.id" :id="item.id"
                        @deleteItem="deleteItemService(index)" @syncItem="syncItemsService(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.services?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                    servicios </p>
                </section>
                <div class="mt-4 mb-3 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewService">
                        <i class="fa-solid fa-plus"></i>
                        Agregar Servicio
                    </button>
                </div>
                <!-- -------------------------------------------------------------------- -->

                <!-- totales  -->
                <div class="text-sm flex flex-col mr-7 items-end col-span-full">
                    <p class="font-bold">Subtotal: <span class="mx-2">$</span>{{ form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="font-bold ">descuento: <span class="mx-2">$</span>{{ form.discount?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
                    <p class="font-bold">Total: <span class="mx-2">$</span>{{ (form.total - form.discount)?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Notas adicionales (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe aquí tus comentarios que consideres relevantes para el cliente" :maxlength="800" show-word-limit
                        clearable />
                    <InputError :message="form.errors.notes" />
                </div>

                <div class="flex items-center space-x-5 col-span-full">
                    <label for="has_discount" class="text-xs items-center flex mt-2 col-span-full">
                        <el-checkbox @change="resetDiscount()" id="has_discount" class="px-2" name="has_discount" v-model="form.has_discount"></el-checkbox>
                        Aplicar descuento
                    </label>
                    <label v-if="form.has_discount" for="is_percentage_discount" class="text-xs items-center flex mt-2 col-span-full">
                        <el-checkbox @change="resetDiscount()" id="is_percentage_discount" class="px-2" name="is_percentage_discount" v-model="form.is_percentage_discount"></el-checkbox>
                        Descuento en porcentaje
                    </label>
                    <label for="iva" class="text-xs items-center flex mt-2 col-span-full">
                        <el-checkbox id="iva" class="px-2" name="iva" v-model="form.show_iva"></el-checkbox>
                        Mostrar IVA
                    </label>
                </div>

                <!-- Descuento -->
                <div class="mt-3" v-if="form.has_discount">
                    <div v-if="form.is_percentage_discount">
                        <InputLabel value="Porcentaje de descuento*" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.discount" max="100" type="number" placeholder="ingresa el porcentaje del 1 al 100">
                            <template #prefix>
                                <i class="fa-solid fa-percent"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.discount" />
                    </div>
                    <div v-else>
                        <InputLabel value="Cantidad de descuento*" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.discount" type="number" placeholder="ingresa el descuento">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.discount" />
                    </div>
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear cotización
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProductInput from "@/Components/MyComponents/ProductInput.vue";
import ServiceInput from "@/Components/MyComponents/ServiceInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        client_id: null,
        contact_name: null,
        phone: null,
        email: null,
        address: null,
        category: null,
        description: null,
        price: null,
        notes: null,
        show_iva: false,
        has_discount: false, //aplicar descuento
        discount: null, //cantidad de descuento
        is_percentage_discount: false, //tipo de descuento
        total: 0, //cantidad total tomando en cuenta servcios, productos y descuento
        products: [],
        services: [],
        // products: [
        //     {
        //         id: 1,
        //         name: null,
        //         product_id: null,
        //         price: 1,
        //         isLocal: null,
        //         quantity: 1,
        //         image_url: null,
        //     }
        // ],
        // services: [
        //     {
        //         id: 1,
        //         service_id: null,
        //         name: null,
        //         price: 1,
        //         quantity: 1,
        //     }
        // ],
    });

    return {
        form,
        products: null, //se obtienne todos los productos de la tienda
        services: null, //se obtienne todos los servicios de la tienda
        next_item_id: 1, //para el index de productos creados como venta en linea
        next_service_id: 1, //para el index de servicios creados
        totalServicesMoney: null, //total de dinero para los servicios
        totalProductsMoney: null, //total de dinero para los productos
    }
},
components:{
    AppLayout,
    PrimaryButton,
    ProductInput,
    ServiceInput,
    InputLabel,
    InputError,
    Back
},
props:{
    clients: Array,
},
methods:{
    store() {
        this.form.post(route("quotes.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado una cotización",
                    type: "success",
                });
            },
        });
    },
    addNewItem() {
      this.form.products.push({ id: this.next_item_id++, price: null, product_id: null, isLocal: null, quantity: null });
    },
    addNewService() {
      this.form.services.push({ id: this.next_service_id++, price: null, service_id: null, quantity: null });
    },
    deleteItem(index) {
        this.form.products.splice(index, 1);
    },
    deleteItemService(index) {
        this.form.services.splice(index, 1);
    },
    syncItems(index, product_obj) {
        this.form.products[index] = product_obj;
    },
    syncItemsService(index, product_obj) {
        this.form.services[index] = product_obj;
    },
    resetDiscount() {
        this.form.discount = null;
    },
    totalMoneyOrder() {
        //calcula el total de dinero para los productos 
        this.totalProductsMoney = this.form.products.reduce((sum, item) => {
            return sum + (item.price * item.quantity);
        }, 0);

        //calcula el total de dinero para los servicios 
        this.totalServicesMoney = this.form.services.reduce((sum, item) => {
            return sum + (item.price * item.quantity);
        }, 0);
        this.form.total = this.totalProductsMoney + this.totalServicesMoney;
    },
    async fetchAllProducts() {
        try {
          const response = await axios.get(route('online-sales.fetch-all-products'));
          if ( response.status === 200 ) {
            this.products = response.data.products;
          }  
        } catch (error) {
            console.log(error);
        }
    },
    async fetchAllServices() {
        try {
          const response = await axios.get(route('services.fetch-all-services'));
          if ( response.status === 200 ) {
            this.services = response.data.services;
          }  
        } catch (error) {
            console.log(error);
        }
    },
},
watch: {
    'form.products': {
        handler() {
            this.totalMoneyOrder();
        },
        deep: true
    },
    'form.services': {
        handler() {
            this.totalMoneyOrder();
        },
        deep: true
    }
},
mounted() {
    this.fetchAllProducts();
    this.fetchAllServices();
}
}
</script>

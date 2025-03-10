<template>
    <AppLayout title="Crear cotización">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <div class="flex items-center space-x-3 ml-2 col-span-full">
                    <h1 class="font-bold">Crear cotización</h1>
                    <div v-if="loadingClient">
                        <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Cliente (en caso de tenerlo registrado)" class="ml-3 mb-1" />
                        <button
                            @click="showClientFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center mb-1">
                            <i class="fa-solid fa-plus text-primary text-[9px] pl-[1px]"></i>
                        </button>
                    </div>
                    <el-select @change="fillClientInfo()" class="w-1/2" filterable v-model="form.client_id" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="client in clients" :key="client" :label="client.company"
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

                <div class="mt-3">
                    <InputLabel value="Fecha de expiración de cot." class="ml-3 mb-1" />
                    <el-date-picker v-model="form.expired_date" type="date" class="!w-full" placeholder="día/mes/año"
                        :disabled-date="disabledPrevDays" />
                    <InputError :message="form.errors.expired_date" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Condiciones de pago" class="ml-3 mb-1" />
                    <el-select class="w-1/2" filterable v-model="form.payment_conditions" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="payment_condition in payment_conditions" :key="payment_condition" :label="payment_condition"
                            :value="payment_condition" />
                    </el-select>
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
                        @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)" class="mb-1" :showDeleteButton="true" />
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
                <h2 v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')" class="font-bold ml-3 mt-3 mb-1 col-span-full">Servicios</h2>

                <section v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')" class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ServiceInput :services="services" v-for="(item, index) in form.services" :key="item.id" :id="item.id"
                        @deleteItem="deleteItemService(index)" @syncItem="syncItemsService(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.services?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                    servicios </p>
                </section>
                <div v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')" class="mt-4 mb-3 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewService">
                        <i class="fa-solid fa-plus"></i>
                        Agregar Servicio
                    </button>
                </div>
                <!-- -------------------------------------------------------------------- -->

                <!-- totales  -->
                <div class="text-sm flex flex-col mr-7 items-end col-span-full">
                    <p class="font-bold">Subtotal: <span class="mx-2">$</span>{{ (form.total - (form.total * 0.16))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p v-if="form.show_iva" class="font-bold">IVA: <span class="mx-2">$</span>{{ (form.total * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p v-if="form.is_percentage_discount" class="font-bold ">descuento: <span class="mx-2">$</span>{{ (percentageDiscount())?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
                    <p v-else class="font-bold ">descuento: <span class="mx-2">$</span>{{ form.discount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
                    <p v-if="form.is_percentage_discount" class="font-bold">Total: <span class="mx-2">$</span>{{ (form.total - percentageDiscount())?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p v-else class="font-bold">Total: <span class="mx-2">$</span>{{ (form.total - form.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
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
                    <!-- <label for="iva" class="text-xs items-center flex mt-2 col-span-full">
                        <el-checkbox id="iva" class="px-2" name="iva" v-model="form.show_iva"></el-checkbox>
                        Mostrar IVA
                    </label> -->
                </div>

                <!-- Descuento -->
                <div class="mt-3" v-if="form.has_discount">
                    <div v-if="form.is_percentage_discount">
                        <InputLabel value="% Porcentaje de descuento*" class="ml-3 mb-1 text-sm" />
                        <el-input-number v-model="form.discount" :min="0" :max="100" />
                        <InputError :message="form.errors.discount" />
                    </div>
                    <div v-else>
                        <InputLabel value="Cantidad de descuento*" class="ml-3 mb-1 text-sm" />
                        <el-input-number v-model="form.discount" :precision="2" :step="0.1" :min="0" :max="form.total" />
                        <InputError :message="form.errors.discount" />
                    </div>
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="isButtonDisabled">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear cotización
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- client form ---------------------------->
        <DialogModal :show="showClientFormModal" @close="showClientFormModal = false; resetClientForm()">
            <template #title> Agregar cliente </template>
            <template #content>
                <form @submit.prevent="storeClient" class="md:grid grid-cols-2 gap-x-3">
                <div class="mt-3">
                    <InputLabel value="Nombre*" class="ml-3 mb-1" />
                    <el-input v-model="clientForm.name" placeholder="Escribe el nombre del cliente" :maxlength="100" clearable />
                    <InputError :message="clientForm.errors.name" />
                </div>
                <div class="mt-3">
                    <InputLabel class="mb-1 ml-2" value="Teléfono *" />
                    <el-input v-model="clientForm.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="clientForm.errors.phone" />
                </div>
                <div class="mt-3 col-span-full">
                    <InputLabel value="RFC (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="clientForm.rfc" placeholder="Escribe el RFC en caso de tenerlo" :maxlength="100" clearable />
                    <InputError :message="clientForm.errors.rfc" />
                </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                <CancelButton @click="showClientFormModal = false; resetClientForm()" :disabled="clientForm.processing">Cancelar</CancelButton>
                <PrimaryButton @click="storeClient()" :disabled="clientForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>
        <!-- client form ---------------------------->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductInput from "@/Components/MyComponents/ProductInput.vue";
import ServiceInput from "@/Components/MyComponents/ServiceInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {

    const clientForm = useForm({
      name: null,
      rfc: null,
      phone: null,
    });

    const form = useForm({
        client_id: null,
        contact_name: null,
        payment_conditions: null,
        phone: null,
        email: null,
        address: null,
        notes: null,
        expired_date: null,
        show_iva: true,
        has_discount: false, //aplicar descuento
        discount: null, //cantidad de descuento
        is_percentage_discount: false, //tipo de descuento
        total: 0, //cantidad total tomando en cuenta servcios, productos y descuento
        products: [],
        services: [],
    });

    return {
        form,
        clientForm,
        showClientFormModal: false, //modal para registrar un cliente
        loadingClient: false, //carga la informacion del cliente 
        products: null, //se obtienne todos los productos de la tienda
        services: null, //se obtienne todos los servicios de la tienda
        next_item_id: 1, //para el index de productos creados como venta en linea
        next_service_id: 1, //para el index de servicios creados
        totalServicesMoney: null, //total de dinero para los servicios
        totalProductsMoney: null, //total de dinero para los productos
        payment_conditions: [
            'Al contado',
            'Parcialidades',
        ]
    }
},
components:{
    AppLayout,
    PrimaryButton,
    CancelButton,
    ProductInput,
    ServiceInput,
    DialogModal,
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
    storeClient() {
      this.clientForm.post(route('clients.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo cliente",
            type: "success",
          });
          this.showClientFormModal = false;
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
        this.form.discount = 0;
    },
    resetClientForm() {
      this.clientForm.reset();
    },
    disabledPrevDays(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    async fillClientInfo() {
        this.loadingClient = true;
        try {
            const response = await axios.get(route('clients.get-client-info', this.form.client_id));
            if ( response.status === 200 ) {
                const client = response.data.client;
                this.form.contact_name = client.name;
                this.form.phone = client.phone;
                this.form.email = client.email;
                if ( client.street && client.suburb ) {
                    this.form.address = client.street + ' ' + client.ext_number + ', Col. ' + client.suburb + ' ' + client.int_number + '. ' 
                    + client.town + ', ' + client.polity_state;
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loadingClient = false;
        }
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
     percentageDiscount() {
        return this.form.discount * 0.01 * this.form.total;
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
computed: {
    isButtonDisabled() {
        return this.form.processing || 
                (!this.form.products.length && !this.form.services.length) || 
                this.form.products.some(product => !product.product_id) ||
                this.form.services.some(service => !service.service_id);
    }
},
mounted() {
    this.fetchAllProducts();
    this.fetchAllServices();
}
}
</script>

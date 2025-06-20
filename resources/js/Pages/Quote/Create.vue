<template>
    <AppLayout title="Crear cotización">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                <div class="flex items-center space-x-3 ml-2 col-span-full">
                    <h1 class="font-bold">Crear cotización</h1>
                    <div v-if="loadingClient">
                        <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel value="Cliente frecuente (opcional)" />
                        <button @click="showClientFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center mb-1">
                            <i class="fa-solid fa-plus text-primary text-[9px] pl-[1px]"></i>
                        </button>
                    </div>
                    <el-select @change="fillClientInfo" filterable
                        v-model="form.client_id" clearable placeholder="Selecciona el cliente"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="client in clients" :key="client"
                            :label="client.company ? client.company + ' - ' + client.name : client.name"
                            :value="client.id" />
                    </el-select>
                    <InputError :message="form.errors.client_id" />
                </div>
                <div>
                    <InputLabel value="Empresa (opcional)" />
                    <el-input v-model="form.company" placeholder="Escribe el nombre de la empresa" :maxlength="150"
                        clearable />
                    <InputError :message="form.errors.company" />
                </div>
                <div>
                    <InputLabel value="Nombre del contacto*" />
                    <el-input v-model="form.contact_name" placeholder="Escribe el nombre del contacto" :maxlength="150"
                        clearable />
                    <InputError :message="form.errors.contact_name" />
                </div>
                <div>
                    <InputLabel value="Teléfono (opcional)" />
                    <el-input v-model="form.phone"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                        placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>
                <div>
                    <InputLabel value="Correo electrónico (opcional)" />
                    <el-input v-model="form.email" placeholder="Escribe el correo electrónico del contacto"
                        :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel value="Fecha de expiración de cot. (opcional)" />
                        <div class="flex items-center space-x-1">
                            <el-checkbox v-model="form.show_expiration" label="Mostrar" size="small" />
                            <el-tooltip placement="top">
                                <template #content>
                                    <p class="text-center">
                                        Al seleccionar esta opción, se mostrará <br>
                                        en la plantilla de la cotización
                                    </p>
                                </template>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </el-tooltip>
                        </div>
                    </div>
                    <el-date-picker v-model="form.expired_date" type="date" class="!w-full" placeholder="día/mes/año"
                        :disabled-date="disabledPrevDays" />
                    <InputError :message="form.errors.expired_date" />
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel value="Condiciones de pago*" />
                        <div class="flex items-center space-x-1">
                            <el-checkbox v-model="form.show_payment_conditions" label="Mostrar" size="small" />
                            <el-tooltip placement="top">
                                <template #content>
                                    <p class="text-center">
                                        Al seleccionar esta opción, se mostrará <br>
                                        en la plantilla de la cotización
                                    </p>
                                </template>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </el-tooltip>
                        </div>
                    </div>
                    <el-select filterable v-model="form.payment_conditions" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="payment_condition in payment_conditions" :key="payment_condition"
                            :label="payment_condition" :value="payment_condition" />
                    </el-select>
                </div>
                <div class="col-span-full">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Dirección (opcional)" />
                        <div class="flex items-center space-x-1">
                            <el-checkbox v-model="form.show_address" label="Mostrar" size="small" />
                            <el-tooltip placement="top">
                                <template #content>
                                    <p class="text-center">
                                        Al seleccionar esta opción, se mostrará <br>
                                        en la plantilla de la cotización
                                    </p>
                                </template>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </el-tooltip>
                        </div>
                    </div>
                    <el-input v-model="form.address" placeholder="Escribe la dirección del cliente" :maxlength="100"
                        clearable />
                    <InputError :message="form.errors.address" />
                </div>
                <!-- productos -->
                <h2 class="font-bold mt-3 mb-1 col-span-full">Productos</h2>
                <section class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id"
                            :id="item.id" @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)"
                            class="mb-1" :showDeleteButton="true" />
                    </div>
                    <p v-if="!form.products?.length" class="text-sm text-gray-600">
                        Click al botón de "+" para empezar a agregar productos
                    </p>
                </section>
                <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                        <i class="fa-solid fa-plus"></i>
                        Agregar producto
                    </button>
                </div>
                <!-- Servicios -->
                <h2 v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')"
                    class="font-bold mt-3 mb-1 col-span-full">Servicios</h2>
                <section v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')"
                    class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ServiceInput :services="services" v-for="(item, index) in form.services" :key="item.id"
                            :id="item.id" @deleteItem="deleteItemService(index)"
                            @syncItem="syncItemsService(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.services?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a
                        agregar
                        servicios </p>
                </section>
                <div v-if="this.$page.props.auth.user.store.activated_modules?.includes('Servicios')"
                    class="mt-4 mb-3 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewService">
                        <i class="fa-solid fa-plus"></i>
                        Agregar Servicio
                    </button>
                </div>
                <div class="col-span-full">
                    <InputLabel value="Notas adicionales (opcional)" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe aquí tus comentarios que consideres relevantes para el cliente"
                        :maxlength="800" show-word-limit clearable />
                    <InputError :message="form.errors.notes" />
                </div>
                <section class="col-span-full p-2 rounded-md bg-grayF2">
                    <h2 class="font-bold text-gray37">
                        Opciones adicionales
                    </h2>
                    <p class="text-xs text-gray99">
                        Agrega opciones adicionales a cada cotización, si deseas que permanezcan en todas las cotización
                        ve a Configuraciones y luego a la pestaña 
                        <a :href="route('settings.index', {tab: 6})" target="_blank" class="text-primary underline">Cotizaciones</a>
                    </p>
                    <article class="grid xl:grid-cols-2 mx-2 gap-3 mt-2">
                        <div class="border border-grayD9 bg-white rounded-lg">
                            <h2 class="flex items-center justify-between text-gray37 bg-grayF2 rounded-t-lg px-2 py-1">
                                <span class="text-sm font-semibold">IVA</span>
                                <button v-if="form.iva_included != null" @click="form.iva_included = null" type="button"
                                    class="text-primary text-xs">Limpiar</button>
                            </h2>
                            <div class="px-2 py-px">
                                <p class="text-gray99 text-xs">
                                    Solo aplica si manejas precios con IVA
                                </p>
                                <div>
                                    <el-radio-group v-model="form.iva_included">
                                        <el-radio :value="false" size="small">
                                            IVA no incluido (agregar el 16% al total)
                                        </el-radio>
                                        <el-radio :value="true" size="small">
                                            IVA incluido (va dentro del precio)
                                        </el-radio>
                                    </el-radio-group>
                                </div>
                            </div>
                        </div>
                        <div class="border border-grayD9 bg-white rounded-lg">
                            <h2 class="flex items-center justify-between text-gray37 bg-grayF2 rounded-t-lg px-2 py-1">
                                <span class="text-sm font-semibold">Descuento</span>
                                <button v-if="form.is_percentage_discount != null"
                                    @click="form.is_percentage_discount = null; form.discount = null; form.percentage = null"
                                    type="button" class="text-primary text-xs">Limpiar</button>
                            </h2>
                            <div class="px-2 py-px">
                                <p class="text-gray99 text-xs">
                                    Aplica un descuento al total de la cotización
                                </p>
                                <div>
                                    <el-radio-group v-model="form.is_percentage_discount">
                                        <div class="w-full flex items-center justify-between">
                                            <el-radio :value="false" size="small">
                                                <p>Descuento fijo</p>
                                            </el-radio>
                                            <el-input v-model="form.discount" placeholder="" class="!w-[40%]"
                                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                                :parser="(value) => value.replace(/[^\d.]/g, '')" size="small">
                                                <template #prepend>
                                                    $
                                                </template>
                                            </el-input>
                                        </div>
                                        <div class="w-full flex items-center justify-between my-1">
                                            <el-radio :value="true" size="small">
                                                <p>Porcentaje</p>
                                            </el-radio>
                                            <el-input v-model="form.percentage" placeholder="" class="!w-[40%]"
                                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                                :parser="(value) => value.replace(/[^\d.]/g, '')" size="small">
                                                <template #append>
                                                    %
                                                </template>
                                            </el-input>
                                        </div>
                                    </el-radio-group>
                                </div>
                            </div>
                        </div>
                        <div class="border border-grayD9 bg-white rounded-lg">
                            <h2 class="flex items-center justify-between text-gray37 bg-grayF2 rounded-t-lg px-2 py-1">
                                <span class="text-sm font-semibold">Costo de entrega o envío</span>
                                <button v-if="form.delivery_type != null"
                                    @click="form.delivery_type = null; form.delivery1 = null; form.delivery2 = null"
                                    type="button" class="text-primary text-xs">Limpiar</button>
                            </h2>
                            <div class="px-2 py-px">
                                <p class="text-gray99 text-xs">
                                    Incluye envío si aplica
                                </p>
                                <div>
                                    <el-radio-group v-model="form.delivery_type">
                                        <div class="w-full flex items-center justify-between">
                                            <el-radio value="Entrega a domicilio" size="small">
                                                <p>Entrega a domicilio</p>
                                            </el-radio>
                                            <el-input v-model="form.delivery1" placeholder="" class="!w-[40%]"
                                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                                :parser="(value) => value.replace(/[^\d.]/g, '')" size="small">
                                                <template #prepend>$</template>
                                            </el-input>
                                        </div>
                                        <div class="w-full flex items-center justify-between my-1">
                                            <el-radio value="Envío por paquetería" size="small">
                                                <p>Envío por paquetería</p>
                                            </el-radio>
                                            <el-input v-model="form.delivery2" placeholder="" class="!w-[40%]"
                                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                                :parser="(value) => value.replace(/[^\d.]/g, '')" size="small">
                                                <template #prepend>$</template>
                                            </el-input>
                                        </div>
                                    </el-radio-group>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>
                <!-- totales  -->
                <div
                    class="text-sm flex flex-col mr-7 items-end col-span-full *:flex *:items-center *:justify-between *:w-[38%]">
                    <div class="">
                        <span>Subtotal: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>{{ subtotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </div>
                    <div v-if="form.iva_included != null" class="">
                        <span>IVA: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ (form.total * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div v-if="form.delivery_type" class="">
                        <span>{{ form.delivery_type }}: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ deliveryCost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div v-if="form.is_percentage_discount != null" class="">
                        <span>Descuento: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ discounted?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div class="font-bold">
                        <span>Total: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ grandTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
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
                <form @submit.prevent="storeClient" class="md:grid grid-cols-2 gap-3">
                    <div>
                        <InputLabel value="Empresa (opcional)" />
                        <el-input v-model="clientForm.company" placeholder="Escribe el nombre de la empresa"
                            :maxlength="100" clearable />
                        <InputError :message="clientForm.errors.company" />
                    </div>
                    <div>
                        <InputLabel value="Nombre*" />
                        <el-input v-model="clientForm.name" placeholder="Escribe el nombre del cliente" :maxlength="100"
                            clearable />
                        <InputError :message="clientForm.errors.name" />
                    </div>
                    <div>
                        <InputLabel value="Teléfono (opcional)" />
                        <el-input v-model="clientForm.phone"
                            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                            :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                            placeholder="Escribe el número de teléfono" />
                        <InputError :message="clientForm.errors.phone" />
                    </div>
                    <div class="mt-3 col-span-full">
                        <InputLabel value="RFC (opcional)" />
                        <el-input v-model="clientForm.rfc" placeholder="Escribe el RFC en caso de tenerlo"
                            :maxlength="100" clearable />
                        <InputError :message="clientForm.errors.rfc" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showClientFormModal = false; resetClientForm()"
                        :disabled="clientForm.processing">Cancelar</CancelButton>
                    <PrimaryButton @click="storeClient()" :disabled="clientForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>
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
            company: null,
            name: null,
            rfc: null,
            phone: null,
        });

        const form = useForm({
            company: null,
            contact_name: null,
            phone: null,
            payment_conditions: null,
            email: null,
            address: null,
            iva_included: null,
            total: 0, //cantidad total tomando en cuenta servcios, productos y descuento
            products: [],
            services: [],
            expired_date: null,
            notes: null,
            is_percentage_discount: null, //tipo de descuento
            discount: null, //cantidad de descuento
            client_id: null,
            percentage: null, //porcentaje de descuento
            delivery_type: null, //aplicar costo de envio
            delivery1: null,
            delivery2: null,
            show_payment_conditions: false,
            show_address: false,
            show_expiration: false,
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
    components: {
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
    props: {
        clients: Array,
    },
    methods: {
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
            return this.form.percentage * 0.01 * this.subtotal;
        },
        async fillClientInfo() {
             if (!this.form.client_id) {
                return;
            }
            
            this.loadingClient = true;
            try {
                const response = await axios.get(route('clients.get-client-info', this.form.client_id));
                if (response.status === 200) {
                    const client = response.data.client;
                    this.form.company = client.company;
                    this.form.contact_name = client.name;
                    this.form.phone = client.phone;
                    this.form.email = client.email;
                    if (client.street && client.suburb) {
                        this.form.address = client.street + ' ' + client.ext_number + ', Col. ' + client.suburb + ' ' + (client.int_number ?? '') + '. '
                            + client.town + ', ' + client.polity_state;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingClient = false;
            }
        },
        async fetchAllProducts() {
            try {
                const response = await axios.get(route('online-sales.fetch-all-products'));
                if (response.status === 200) {
                    this.products = response.data.products;
                }
            } catch (error) {
                console.log(error);
            }
        },
        async fetchAllServices() {
            try {
                const response = await axios.get(route('services.fetch-all-services'));
                if (response.status === 200) {
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
        },
        subtotal() {
            if (this.form.iva_included) {
                return (this.form.total * 0.84);
            }

            return this.form.total;
        },
        grandTotal() {
            let discounted = 0;
            if (this.form.is_percentage_discount != null) {
                discounted = this.form.is_percentage_discount
                    ? this.percentageDiscount()
                    : this.form.discount;
            }

            if (this.form.iva_included === false) {
                return (this.form.total * 1.16) + this.deliveryCost - discounted;
            }

            return this.form.total + this.deliveryCost - discounted;

        },
        deliveryCost() {
            if (this.form.delivery_type == 'Entrega a domicilio') {
                return this.form.delivery1 ? parseFloat(this.form.delivery1) : 0;
            } else if (this.form.delivery_type == 'Envío por paquetería') {
                return this.form.delivery2 ? parseFloat(this.form.delivery2) : 0;
            } else {
                return 0;
            }
        },
        discounted() {
            if (this.form.is_percentage_discount) {
                return this.percentageDiscount() ?? 0;
            } else {
                return this.form.discount ? parseFloat(this.form.discount) : 0;
            }
        },
    },
    mounted() {
        this.fetchAllProducts();
        this.fetchAllServices();
    }
}
</script>

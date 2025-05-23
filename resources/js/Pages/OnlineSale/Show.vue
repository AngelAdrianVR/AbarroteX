<template>
    <AppLayout :title="'Detalles de pedido ' + online_sale.id">
        <section class="px-2 md:px-10 py-7">
            <Back :to="route('online-sales.index')" />

            <article class="md:flex justify-between mt-4 text-sm">
                <h1 class="font-bold mb-3 md:mb-0">Detalle del pedido</h1>
                <div class="flex items-center space-x-2">
                    <p>Estatus:</p>
                    <span v-html="getStatusIcon(online_sale.status)"></span>
                    <el-select :disabled="online_sale.status == 'Cancelado' || online_sale.status == 'Reembolsado'"
                        @change="handleUpdateStatus" v-model="status" class="!w-40 md:!w-48" filterable required
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="status in getStatuses" :key="status" :value="status" :label="status" />
                    </el-select>
                    <PrimaryButton v-if="canEdit"
                        :disabled="loadingProducts || online_sale.status == 'Entregado' || online_sale.status == 'Cancelado' || online_sale.status == 'Reembolsado'"
                        @click="form.reset(); editOnlineOrderModal = true">
                        Editar
                    </PrimaryButton>
                    <!-- <el-dropdown
                        :disabled="loadingProducts || status == 'Entregado' || status == 'Cancelado' || status == 'Reembolsado'"
                        split-button type="primary" @click="editOnlineOrderModal = true">
                        Editar
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="showDeleteConfirm = true">Eliminar</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
</el-dropdown> -->
                </div>
            </article>

            <article class="my-7">
                <div class="grid grid-cols-2 gap-1 md:w-1/2 text-sm">
                    <p class="text-gray-500">Número de pedido:</p>
                    <p>{{ online_sale.id }}</p>
                    <p class="text-gray-500">Fecha:</p>
                    <p>{{ formatDate(online_sale.created_at) }}</p>
                    <p class="text-gray-500">Cliente:</p>
                    <p>{{ online_sale.name }}</p>
                    <p class="text-gray-500">Teléfono:</p>
                    <p>{{ online_sale.phone }}</p>
                    <p class="text-gray-500">Método de pago:</p>
                    <p>{{ online_sale.payment_method }}</p>
                    <p class="text-gray-500">Dirección:</p>
                    <p>{{ online_sale.suburb }}, {{ online_sale.street }} {{ online_sale.int_number ? '. Int: ' +
                        online_sale.int_number : null }} {{ online_sale.ext_number ? ', Ext: ' + online_sale.ext_number
                            : null }}
                    </p>
                    <p class="text-gray-500">Referencias</p>
                    <p>{{ online_sale.address_references ?? '--' }}</p>
                    <div class="col-span-full border-t mt-3"></div>
                    <p class="font-bold mt-3">Subtotal:</p>
                    <p class="font-bold mt-3">${{ online_sale.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </p>
                    <p class="font-bold">Costo de envío:</p>
                    <p class="font-bold">${{ online_sale.delivery_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") }}</p>
                    <p class="font-bold">Total:</p>
                    <p class="font-bold">${{ (online_sale.total +
                        online_sale.delivery_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </article>

            <article>
                <h2 class="font-bold">Productos ordenados</h2>

                <!-- Tabla de productos ordenados -->
                <div class="overflow-auto">
                    <table class="w-full mt-7">
                        <thead>
                            <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                                <th></th>
                                <th>Nombre de producto</th>
                                <th>Precio unitario</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in online_sale.products" :key="product.id"
                                class="*:text-xs *:py-2 *:px-4">
                                <td>
                                    <figure class="size-16">
                                        <img v-if="product.image_url" :src="product?.image_url" alt="producto"
                                            class="h-full object-contain mx-auto">
                                        <div class="flex flex-col items-center justify-center" v-else>
                                            <i class="fa-regular fa-image text-3xl text-gray-200"></i>
                                            <p class="text-xs text-gray-300 text-center">Imagen no disponible</p>
                                        </div>
                                    </figure>
                                </td>
                                <td @click="handledShowProduct(product)" class="text-primary underline cursor-pointer">
                                    {{ product.name }}
                                </td>
                                <td>${{ product.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>{{ product.quantity }}</td>
                                <td>${{ (product.quantity * product.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",") }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <!-- Confirmación de eliminación -->
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar pedido</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará el pedido seleccionado, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                    <DangerButton @click="deleteOnlineOrder">Eliminar</DangerButton>
                </div>
            </template>
        </ConfirmationModal>
        <!-- Confirmación de eliminación -->

        <!-- Confirmación de Actualización de estatus -->
        <ConfirmationModal :show="showUpdateStatusConfirm" @close="showUpdateStatusConfirm = false">
            <template #title>
                <h1>Cambiar estatus de pedido</h1>
            </template>
            <template #content>
                <p>
                    Se cambiará el estatus del pedido seleccionado, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showUpdateStatusConfirm = false; status = online_sale.status">Cancelar
                    </CancelButton>
                    <PrimaryButton @click="updateStatus">Confirmar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
        <!-- Confirmación de Actualización de estatus -->

        <!-- -------------- Modal edición de orden starts----------------------- -->
        <Modal :show="editOnlineOrderModal" @close="editOnlineOrderModal = false; form.reset">
            <div class="py-4 px-7 relative text-sm">
                <i @click="editOnlineOrderModal = false"
                    class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

                <form class="mt-5 mb-2" @submit.prevent="updateOnlineSale">
                    <h2 class="font-bold mb-4">Editar pedido</h2>
                    <p>Datos del cliente</p>

                    <div class="md:grid grid-cols-2 gap-y-2 gap-x-7 space-y-2 md:space-y-0 mt-3">
                        <div>
                            <InputLabel value="Nombre del cliente*" class="ml-3 mb-1" />
                            <el-input v-model="form.name" placeholder="Escribe el nombre del cliente" :maxlength="100"
                                clearable />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel class="mb-1 ml-2" value="Teléfono*" />
                            <el-input v-model="form.phone"
                                :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                                :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                                placeholder="Escribe el número de teléfono del cliente" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <h1 class="font-bold my-1 ml-3 col-span-full">Dirección</h1>

                        <div>
                            <InputLabel value="Calle*" class="ml-3 mb-1" />
                            <el-input v-model="form.street" placeholder="Escribe tu calle" :maxlength="255" clearable />
                            <InputError :message="form.errors.street" />
                        </div>

                        <div>
                            <InputLabel value="Colonia*" class="ml-3 mb-1" />
                            <el-input v-model="form.suburb" placeholder="Escribe tu colonia" :maxlength="255"
                                clearable />
                            <InputError :message="form.errors.suburb" />
                        </div>

                        <div>
                            <InputLabel class="mb-1 ml-2" value="Código postal" />
                            <el-input v-model="form.postal_code"
                                :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                                :parser="(value) => value.replace(/\D/g, '')" maxlength="6" clearable
                                placeholder="Escribe el código postal" />
                            <InputError :message="form.errors.postal_code" />
                        </div>

                        <div>
                            <InputLabel value="Estado*" class="ml-3 mb-1" />
                            <el-input v-model="form.polity_state" placeholder="Ej. Jalisco, Monterrey, Michoacan"
                                :maxlength="255" clearable />
                            <InputError :message="form.errors.polity_state" />
                        </div>

                        <div>
                            <InputLabel value="Número exterior*" class="ml-3 mb-1" />
                            <el-input v-model="form.ext_number" placeholder="Número de tu casa" :maxlength="255"
                                clearable />
                            <InputError :message="form.errors.ext_number" />
                        </div>

                        <div>
                            <InputLabel value="Número Interior" class="ml-3 mb-1" />
                            <el-input v-model="form.int_number" placeholder="Número de edificio, coto, fraccionamiento"
                                :maxlength="255" clearable />
                            <InputError :message="form.errors.int_number" />
                        </div>

                        <div class="mt-3 col-span-full">
                            <InputLabel value="Referencias (opcional)" class="ml-3 mb-1" />
                            <el-input v-model="form.address_references"
                                placeholder="Escribe referencias que nos ayuden a encontrar tu domicilio. Ej. Oxxo en la esquina"
                                :maxlength="255" clearable />
                            <InputError :message="form.errors.address_references" />
                        </div>
                    </div>

                    <p class="font-bold my-5">Detalles del pedido</p>

                    <section class="max-h-56 overflow-auto">
                        <div class="space-y-3">
                            <InputError :message="form.errors.products" />
                            <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id"
                                :id="item.id" :init_state="item" @deleteItem="deleteItem(index)"
                                @syncItem="syncItems(index, $event)" class="mb-1"
                                :showDeleteButton="form.products.length > 1" />
                        </div>
                        <p v-if="!form.products?.length" class="text-sm text-gray-600"> Click al botón de "+" para
                            empezar a
                            agregar
                            productos </p>
                    </section>
                    <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm">
                        <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                            <i class="fa-solid fa-plus"></i>
                            Agregar producto
                        </button>
                        <div class="flex flex-col mr-7 items-end">
                            <p class="font-bold">Subtotal: <span class="mx-2">$</span>{{
                                form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            <p v-if="form.delivery_price !== null" class="font-bold ">Costo de envío: <span
                                    class="mx-2">$</span>{{
                                        form.delivery_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ",") }}</p>
                            <p class="font-bold">Total: <span class="mx-2">$</span>{{ (form.total +
                                form.delivery_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Método de pago" class="ml-3 mb-1" />
                        <el-select v-model="form.payment_method" class="!w-full" filterable required clearable
                            placeholder="Seleccione" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="payment_method in payment_methods" :key="payment_method"
                                :value="payment_method" :label="payment_method" />
                        </el-select>
                    </div>

                    <div class="flex justify-end space-x-1 pt-5 pb-1 py-3">
                        <CancelButton @click="editOnlineOrderModal = false">Cancelar</CancelButton>
                        <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
        <!-- --------------------------- Modal edición de orden ends ------------------------------------ -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import ProductInput from "@/Components/MyComponents/ProductInput.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';
import { useForm } from "@inertiajs/vue3";
import Back from "@/Components/MyComponents/Back.vue";

export default {
    data() {
        const form = useForm({
            name: this.online_sale.name,
            phone: this.online_sale.phone,
            payment_method: this.online_sale.payment_method,
            suburb: this.online_sale.suburb,
            street: this.online_sale.street,
            postal_code: this.online_sale.postal_code,
            polity_state: this.online_sale.polity_state,
            ext_number: this.online_sale.ext_number,
            int_number: this.online_sale.int_number,
            delivery_price: this.online_sale.delivery_price,
            total: this.online_sale.total,
            store_id: this.$page.props.auth.user.store_id,
            created_from_app: true,
            products: this.online_sale.products,
            address_references: this.online_sale.address_references, //referencias para dar con el lugar
            store_inventory: this.$page.props.auth.user.store?.online_store_properties?.inventory,
        });
        return {
            form,
            loadingProducts: false,
            status: this.online_sale.status,
            statuses: ['Procesando', 'Entregado', 'Cancelado', 'Reembolsado'],
            showUpdateStatusConfirm: false, //modal de confirmación de cambio de estatus (Entregado y cancelado)
            showDeleteConfirm: false, //modal de confirmación de eliminación
            editOnlineOrderModal: false, //modal de ediciónde orden
            products: null, //se obtienne todos los productos de la tienda
            next_item_id: this.online_sale.products.length, //para el index de productos creados como venta en linea
            payment_methods: [
                'Efectivo',
                'Tarjeta de crédito',
                'Tarjeta de débito',
                'Transferencia o depósito',
                'Mercado pago',
            ],
            canEdit: this.$page.props.auth.user.permissions.includes('Editar pedidos'),
        }
    },
    components: {
        AppLayout,
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
        DangerButton,
        ProductInput,
        InputLabel,
        InputError,
        Modal,
        Back
    },
    props: {
        online_sale: Object
    },
    computed: {
        getStatuses() {
            if (this.status == 'Entregado') {
                return ['Entregado', 'Reembolsado'];
            } else {
                return ['Entregado', 'Procesando', 'Cancelado'];
            }
        },
    },
    methods: {
        updateOnlineSale() {
            this.form.put(route("online-sales.update", this.online_sale.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "¡Se ha actualizado el pedido correctamente!",
                        type: "success",
                    });
                    this.editOnlineOrderModal = false;
                },
            });
        },
        handleUpdateStatus() {
            if (this.status == 'Entregado' || this.status == 'Cancelado' || this.status == 'Reembolsado') {
                this.showUpdateStatusConfirm = true;
            } else {
                this.updateStatus();
            }
        },
        addNewItem() {
            this.form.products.push({ id: this.next_item_id++, price: null, product_id: null, is_local: null, quantity: 1 });
        },
        deleteItem(index) {
            if (this.form.products.length > 1) {
                this.form.products.splice(index, 1);
                //actualiza el total y precio de envío cuando se elimina algun producto
                this.syncItems();
                this.totalMoneyOrder();
                this.calculateDeliveryPrice();
            }
        },
        syncItems(index, product_obj) {
            this.form.products[index] = product_obj;
        },
        getStatusIcon(status) {
            switch (status) {
                case 'Pendiente':
                    return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-amber-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            `;
                case 'Procesando':
                    return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-blue-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>`;
                case 'Entregado':
                    return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-green-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>`;
                case 'Cancelado':
                    return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[17px] text-red-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>`;
                case 'Reembolsado':
                    return `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="size-[17px] text-[#8C3DE4]">
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>`;
                default:
                    return '';
            }
        },
        async updateStatus() {
            try {
                //se procesa el estatus para que funcione el metodo del controlador.
                let status = null;
                if (this.status == 'Pendiente') {
                    status = 'pendent';
                } else if (this.status == 'Procesando') {
                    status = 'processing';
                } else if (this.status == 'Entregado') {
                    status = 'delivered';
                } else if (this.status == 'Cancelado') {
                    status = 'cancel';
                } else if (this.status == 'Reembolsado') {
                    status = 'refunded';
                }

                const response = await axios.put(route('online-sales.update-status', this.online_sale.id), { status: status, online_sales_cash_register: this.$page.props.auth.user.store.online_store_properties.online_sales_cash_register });
                if (response.status === 200) {
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha actualizado el estatus",
                        type: "success",
                    });
                    location.reload();
                }
            } catch (error) {
                console.log(error);
            }
        },
        formatDate(dateString) {
            if (!dateString) {
                return '--';  // Retorna '--' si dateString es null o undefined
            }
            try {
                return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
            } catch (error) {
                console.error('Error formatting date:', error);
                return '--';  // Retorna '--' si ocurre un error al formatear la fecha
            }
        },
        async deleteOnlineOrder() {
            try {
                const response = await axios.delete(route('online-sales.destroy', this.online_sale.id));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la orden',
                        type: 'success',
                    });
                    this.showDeleteConfirm = false;
                    this.$inertia.get(route('online-sales.index'));
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Error',
                    message: 'No se pudo eliminar la orden. Intente más tarde',
                    type: 'error',
                });
            }
        },
        async fetchAllProducts() {
            this.loadingProducts = true;
            try {
                const response = await axios.get(route('online-sales.fetch-all-products'));
                if (response.status === 200) {
                    this.products = response.data.products;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loadingProducts = false;
            }
        },
        totalMoneyOrder() {
            //calcula el total de la venta de la orden
            this.form.total = this.form.products.reduce((sum, item) => {
                return sum + (item.price * item.quantity);
            }, 0);
        },
        calculateDeliveryPrice() {
            const storeProperties = this.$page.props.auth.user.store.online_store_properties;
            this.form.delivery_price = storeProperties?.enabled_free_delivery && this.form.total >= storeProperties?.min_free_delivery
                ? 0
                : parseFloat(storeProperties?.delivery_price);
        },
        handledShowProduct(product) {
            const encodedId = btoa(product.product_id.toString());
            console.log(product);
            if (product.isLocal) {
                this.$inertia.get(route('products.show', encodedId));
            } else {
                this.$inertia.get(route('global-product-store.show', encodedId))
            }
        }
    },
    watch: {
        'form.products': {
            handler() {
                this.totalMoneyOrder();
                this.calculateDeliveryPrice();
            },
            deep: true
        }
    },
    mounted() {
        this.fetchAllProducts();
    }
}
</script>

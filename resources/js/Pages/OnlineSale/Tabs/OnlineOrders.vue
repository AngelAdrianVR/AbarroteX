<template>
    <div class="flex justify-between items-center mx-3">
        <h1 class="font-bold">Pedidos en línea</h1>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <!-- filtro -->
                <button @click.stop="showFilter = !showFilter"
                    class="border border-[#D9D9D9] rounded-full py-1 px-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="16" width="16"
                        id="Filter-Sort-Lines-Descending--Streamline-Ultimate">
                        <desc>Filter Sort Lines Descending Streamline Icon: https://streamlinehq.com</desc>
                        <defs></defs>
                        <title>filter</title>
                        <path d="M0.73 4.2791H23.27" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M3.131 9.426H20.869" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M8.7141 19.7209H15.2859" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                        <path d="M5.531 14.573H18.469" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1"></path>
                    </svg>
                    <p class="text-sm ml-2">Filtrar</p>
                </button>
                <div v-if="showFilter"
                    class="absolute top-9 right-0 lg:-left-64 border border-[#D9D9D9] rounded-md p-4 bg-white shadow-lg z-50 w-80">
                    <div class="mb-3">
                        <InputLabel value="Rango de fechas" class="ml-3 mb-1" />
                        <el-date-picker v-model="searchDate" type="daterange" range-separator="A"
                            start-placeholder="Fecha de inicio" end-placeholder="Fecha de fin" class="!w-full" />
                    </div>
                    <div class="flex space-x-3">
                        <PrimaryButton @click="filterOnlineSales" class="!py-1">Aplicar</PrimaryButton>
                        <ThirthButton @click="cleanFilter" class="!py-1">Limpiar</ThirthButton>
                    </div>
                </div>
            </div>
            <PrimaryButton v-if="canCreate" @click="createOnlineOrderModal = true" class="!py-1">Registrar pedido</PrimaryButton>
        </div>
    </div>
    <p class="my-4 mx-3 text-gray99 text-sm">En esta sección solo se muestran los pedidos que se encuentran pendientes,
        procesando y pedidos cancelados.
        Los pedidos entregados, puedes revisarlos en el módulo de <span @click="$inertia.get(route('sales.index'))"
            class="text-primary cursor-pointer underline ml-1">ventas registradas</span>
    </p>

    <Loading v-if="loading" class="mt-20" />
    <div class="mt-8" v-else>
        <p v-if="localOrders?.length" class="text-gray66 text-[11px] mb-3">{{ localOrders?.length }} de {{
            totalOnlineOrders }}
            elementos
        </p>
        <div class="overflow-auto h-[465px] py-3">
            <table v-if="localOrders?.length" class="w-full">
                <thead>
                    <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                        <th># Pedido</th>
                        <th>Fecha de pedido</th>
                        <th>Entregado</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estatus</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr @click="$inertia.visit(route('online-sales.show', online_order.id))"
                        v-for="online_order in localOrders" :key="online_order"
                        class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                        <td class="rounded-s-full">{{ online_order.id }}</td>
                        <td>{{ formatDate(online_order.created_at) }}</td>
                        <td>{{ formatDate(online_order.delivered_at) ?? '--' }}</td>
                        <td>{{ online_order.name }}</td>
                        <td>${{ (online_order.total +
                            online_order.delivery_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <span v-html="getStatusIcon(online_order.status)"></span>
                                <p>{{ online_order.status }}</p>
                            </div>
                        </td>
                        <td class="rounded-e-full text-end">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item
                                            v-if="canChangeStatus && online_order.status != 'Entregado' && online_order.status != 'Cancelado' && online_order.status != 'Procesando'"
                                            :command="'processing|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-[14px] mr-2 text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span class="text-xs">Procesando</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="canChangeStatus && online_order.status != 'Entregado' && online_order.status != 'Cancelado'"
                                            :command="'delivered|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-[14px] mr-2 text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            <span class="text-xs">Entregado</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="canChangeStatus && online_order.status != 'Entregado' && online_order.status != 'Cancelado'"
                                            :command="'cancel|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-[14px] mr-2 text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                            <span class="text-xs">Cancelar pedido</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'see|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <span class="text-xs">Ver</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="online_order.phone && canWhatsapp"
                                            :command="'whatsapp|' + online_order.phone">
                                            <i class="fa-brands fa-whatsapp text-green-500"></i>
                                            <span class="text-xs">Mandar whatapp</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="canDelete" :command="'delete|' + online_order.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="size-[14px] mr-2 text-red-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            <span class="text-xs text-red-600">Eliminar</span>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </td>
                    </tr>
                </tbody>
            </table>
            <el-empty v-else description="No hay ordenes registradas" />
            <p v-if="localOrders?.length" class="text-gray66 text-[11px] mt-1">{{ localOrders?.length }} de {{
                totalOnlineOrders }}
                elementos
            </p>
            <p v-if="loadingItems" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </p>
            <button
                v-else-if="localOrders?.length && totalOnlineOrders > 5 && localOrders?.length < totalOnlineOrders && !filtered"
                @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                elementos</button>
        </div>
    </div>

    <!-- -------------- Modal creación de orden starts----------------------- -->
    <Modal :show="createOnlineOrderModal" @close="createOnlineOrderModal = false">
        <div class="py-4 px-7 relative text-sm">
            <i @click="createOnlineOrderModal = false"
                class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

            <form class="mt-5 mb-2" @submit.prevent="storeOnlineSale">
                <h2 class="font-bold mb-4">Registrar pedido</h2>
                <p>Datos del cliente</p>

                <div class="md:grid grid-cols-2 gap-y-2 gap-x-7 space-y-2 md:space-y-0 mt-3">

                    <div class="col-span-full flex items-center space-x-3">
                        <div class="lg:w-1/2 lg:pr-3">
                            <InputLabel class="mb-1 ml-2" value="Selecciona un cliente (opcional)" />
                            <div class="flex items-center space-x-3">
                                <el-select :disabled="loadingClientInfo" @change="getClientInfo" v-model="client_id"
                                    clearable filterable placeholder="Seleccione"
                                    no-data-text="No hay opciones registradas"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="client in clients" :key="client" :label="client.name"
                                        :value="client.id" />
                                </el-select>
                                <button @click="openClientCreationForm" type="button"
                                    class="rounded-full border-primary size-5 flex items-center justify-center text-primary text-lg">
                                    <i class="fa-solid fa-circle-plus"></i>
                                </button>
                            </div>
                        </div>
                        <p v-if="loadingClientInfo" class="text-xs text-center">
                            Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin text-primary"></i>
                        </p>
                    </div>
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
                        <el-input v-model="form.suburb" placeholder="Escribe tu colonia" :maxlength="255" clearable />
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

                <section class="max-h-72 overflow-auto">
                    <div class="space-y-3">
                        <InputError :message="form.errors.products" />
                        <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id"
                            :id="item.id" :showDeleteButton="form.products.length > 1" @deleteItem="deleteItem(index)"
                            @syncItem="syncItems(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.products?.length" class="text-sm text-gray-600">
                        lick al botón de "+" para empezar a agregar productos </p>
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
                                    form.delivery_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",")
                            }}</p>
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
                    <CancelButton @click="createOnlineOrderModal = false">Cancelar</CancelButton>
                    <PrimaryButton :disabled="form.processing || !form.products[0].name">Confirmar</PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
    <!-- --------------------------- Modal creación de orden ends ------------------------------------ -->

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
                <CancelButton @click="showUpdateStatusConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="updateStatus(tempStatus, orderId);">Confirmar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
    <!-- Confirmación de Actualización de estatus -->
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import ProductInput from "@/Components/MyComponents/ProductInput.vue";
import InputError from "@/Components/InputError.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';
import Modal from "@/Components/Modal.vue";
import { getItemByAttributes, addOrUpdateBatchOfItems } from '@/dbService.js';

export default {
    data() {
        const form = useForm({
            name: null,
            phone: null,
            payment_method: 'Efectivo',
            suburb: null,
            street: null,
            ext_number: null,
            int_number: null,
            postal_code: null,
            polity_state: null,
            address_references: null,
            delivery_price: null, //precio de envío
            total: 0,
            store_id: this.$page.props.auth.user.store_id,
            created_from_app: true,
            store_inventory: this.$page.props.auth.user.store?.online_store_properties?.inventory,
            products: [
                {
                    id: 1,
                    name: null,
                    product_id: null,
                    price: 1,
                    isLocal: null,
                    quantity: 1,
                    image_url: null,
                }
            ],
        });
        return {
            form, //formulario para crear orden en linea
            client_id: null,
            createOnlineOrderModal: false, //modal para crear orden en linea.
            localOrders: this.orders, //ordenes locales 
            loading: false,
            loadingClientInfo: false, //estado de carga al obtener la info del cliente seleccionado en la orden
            showFilter: false, //filtro opciones
            searchDate: null, //filtro fechas
            loadingItems: false, //para paginación
            filtered: false, //bandera para saber si ya se filtró y deshabilitar la carga de elementos ya que hay un error.
            currentPage: 1, //para paginación
            products: null, //se obtienne todos los productos de la tienda
            showDeleteConfirm: false, //modal de eliminación
            showUpdateStatusConfirm: false, //modal de actualizacion de estatus
            next_item_id: 2, //para el index de productos creados como venta en linea
            payment_methods: [
                'Efectivo',
                'Tarjeta de crédito',
                'Tarjeta de débito',
                'Transferencia o depósito',
                'Mercado pago',
            ],
            tempStatus: null, //estatus temporal para usar en modal de confirmacion 
            orderId: null, //id de la orden para usar en modal de confirmacion 
            // inventario de codigos activado
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            canCreate: this.$page.props.auth.user.permissions.includes('Crear pedidos'),
            canDelete: this.$page.props.auth.user.permissions.includes('Eliminar pedidos'),
            canChangeStatus: this.$page.props.auth.user.permissions.includes('Cambiar status de pedidos'),
            canWhatsapp: this.$page.props.auth.user.permissions.includes('Mandar whatsapp'),
        }
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        ProductInput,
        DangerButton,
        InputError,
        InputLabel,
        Loading,
        Modal
    },
    props: {
        orders: Array,
        totalOnlineOrders: Number,
        clients: Array
    },
    methods: {
        addNewItem() {
            this.form.products.push({ id: this.next_item_id++, price: null, product_id: null, isLocal: null, quantity: null });
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
        storeOnlineSale() {
            this.form.post(route("online-sales.store"), {
                onSuccess: () => {
                    // if (this.isInventoryOn) {
                    this.updateCurrentStockInIndexedDB();
                    // }

                    this.$notify({
                        title: "Correcto",
                        message: "¡Se ha creado el pedido correctamente!",
                        type: "success",
                    });
                },
            });
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('online-sales.show', data));
            } else if (commandName == 'processing') {
                this.updateStatus(commandName, data);
            } else if (commandName == 'delivered' || commandName == 'cancel') {
                this.tempStatus = commandName;
                this.orderId = data;
                this.showUpdateStatusConfirm = true;
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            } else if (commandName == 'whatsapp') {
                this.whatsappMessage(data);
            }
        },
        async updateCurrentStockInIndexedDB() {
            // Obtener la pestaña actual y sus productos
            const saleProducts = this.form.products;

            // Asegurarse de que existan productos en la pestaña
            if (!saleProducts) {
                console.log('No hay prpoductos en la venta para actualizar su stock');
                return;
            }

            // Mapear los productos para actualizar su stock
            const products = await Promise.all(saleProducts.map(async (item) => {
                // Obtener productos por código
                let foundProducts = await getItemByAttributes('products', { name: item.name });

                // Verificar si se encontró el producto
                if (foundProducts.length > 0) {
                    const IDBItemStock = foundProducts[0].current_stock;
                    // Actualizar el stock
                    foundProducts[0].current_stock = IDBItemStock <= item.quantity ? 0 : IDBItemStock - item.quantity;
                    return foundProducts[0];
                }

                // Manejar el caso donde no se encuentre el producto
                return null;
            }));

            // Filtrar productos que no fueron encontrados
            const validProducts = products.filter(product => product !== null);

            // Actualizar los productos en IndexedDB
            await addOrUpdateBatchOfItems('products', validProducts);
        },
        async updateStatus(status, orderId) {
            try {
                const response = await axios.put(route('online-sales.update-status', orderId), {
                    status: status,
                    online_sales_cash_register: this.$page.props.auth.user.store.online_store_properties.online_sales_cash_register,
                    store_inventory: this.$page.props.auth.user.store?.online_store_properties?.inventory
                });
                if (response.status === 200) {
                    //buscar la orden seleccionada para actualizar estatus
                    const orderIndex = this.localOrders.findIndex(item => item.id == orderId);

                    //si existe, actualizar el estatus
                    if (orderIndex != -1) {
                        this.localOrders[orderIndex].status = response.data.status;
                        this.localOrders[orderIndex].delivered_at = response.data.delivered_at;

                        this.$notify({
                            title: "Correcto",
                            message: "Se ha actualizado el estatus",
                            type: "success",
                        });
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.showUpdateStatusConfirm = false;
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.post(route('online-sales.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localOrders = [...this.localOrders, ...response.data.items];
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
        async getClientInfo() {
            try {
                this.loadingClientInfo = true;
                const response = await axios.get(route('clients.get-client-info', this.client_id));

                if (response.status === 200) {
                    const client = response.data.client;
                    this.form.name = client.name;
                    this.form.phone = client.phone;
                    this.form.street = client.street;
                    this.form.suburb = client.suburb;
                    this.form.postal_code = client.postal_code;
                    this.form.polity_state = client.polity_state;
                    this.form.ext_number = client.ext_number;
                    this.form.int_number = client.int_number;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se completó la petición',
                    message: 'No se pudo recuperar la nformación del cliente',
                    type: 'success',
                });
            } finally {
                this.loadingClientInfo = false;
            }
        },
        async deleteOnlineOrder() {
            try {
                const response = await axios.delete(route('online-sales.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la orden',
                        type: 'success',
                    });

                    const onlineSaleIndex = this.localOrders.findIndex(item => item.id == this.itemIdToDelete);

                    if (onlineSaleIndex != -1) {
                        this.localOrders.splice(onlineSaleIndex, 1);
                    }

                    this.showDeleteConfirm = false;
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
        async filterOnlineSales() {
            if (this.searchDate != null) {
                this.loading = true;
                try {
                    const response = await axios.get(route('online-sales.filter'), { params: { queryDate: this.searchDate } });
                    if (response.status == 200) {
                        this.localOrders = response.data.items;
                        this.filtered = true;
                    }

                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                    this.showFilter = false;
                }
            } else {
                this.localOrders = this.orders;
                this.showFilter = false;
                this.filtered = false;
                this.currentPage = 1;
            }
        },
        cleanFilter() {
            this.localOrders = this.orders;
            this.showFilter = false;
            this.filtered = false;
            this.currentPage = 1;
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
                default:
                    return '';
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
        totalMoneyOrder() {
            //calcula el total de la venta de la orden
            this.form.total = this.form.products.reduce((sum, item) => {
                return sum + (item.price * item.quantity);
            }, 0);
        },
        calculateDeliveryPrice() {
            const storeProperties = this.$page.props.auth.user.store.online_store_properties;

            if (storeProperties.delivery_price) {
                this.form.delivery_price = storeProperties?.enabled_free_delivery && this.form.total >= storeProperties?.min_free_delivery
                    ? 0
                    : parseFloat(storeProperties?.delivery_price);
            } else {
                this.form.delivery_price = 0;
            }
        },
        whatsappMessage(phone) {
            const text = encodeURIComponent('Hola! hemos recibido tu pedido en línea');
            const url = `https://api.whatsapp.com/send?phone=${phone}&text=${text}`;
            window.open(url, '_blank');
        },
        openClientCreationForm() {
            window.open(route('clients.create'), '_blank');
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

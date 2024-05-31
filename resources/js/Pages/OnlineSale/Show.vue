<template>
    <AppLayout :title="'Detalles de pedido ' + online_sale.id">
        <section class="px-2 md:px-10 py-7">
            <Back :to="route('online-sales.index')" />

            <article class="md:flex justify-between mt-4 text-sm">
                <h1 class="font-bold mb-3 md:mb-0">Detalle del pedido</h1>
                <div class="flex items-center space-x-2">
                    <p>Estatus:</p>
                    <span v-html="getStatusIcon(online_sale.status)"></span>
                    <el-select @change="updateStatus" v-model="status" class="!w-40 md:!w-48" filterable required clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="status in statuses" :key="status" :value="status" 
                        :label="status" />
                    </el-select>
                    <el-dropdown split-button type="primary" @click="editOnlineOrderModal = true">
                        Editar
                        <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="showDeleteConfirm = true">Eliminar</el-dropdown-item>
                        </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </article>

            <article class="my-7">
                <div class="grid grid-cols-2 gap-1 md:w-1/2 text-sm">
                    <p>Número de pedido:</p>
                    <p>{{ online_sale.id }}</p>
                    <p>Fecha:</p>
                    <p>{{ formatDate(online_sale.created_at) }}</p>
                    <p>Cliente:</p>
                    <p>{{ online_sale.name }}</p>
                    <p>Teléfono:</p>
                    <p>{{ online_sale.phone }}</p>
                    <p>Método de pago:</p>
                    <p>{{ online_sale.payment_method }}</p>
                    <p>Referencias</p>
                    <p>{{ online_sale.address_references ?? '--' }}</p>
                    <p class="font-bold mt-3">Subtotal:</p>
                    <p class="font-bold mt-3">${{ online_sale.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="font-bold">Costo de envío:</p>
                    <p class="font-bold">${{ online_sale.delivery_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="font-bold">Total:</p>
                    <p class="font-bold">${{ (online_sale.total + online_sale.delivery_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
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
                                    <figure class="w-16">
                                        <img v-if="product.image_url" 
                                                :src="product?.image_url" 
                                                alt="producto" class="h-full mx-auto">
                                        <div class="flex flex-col items-center justify-center" v-else>
                                            <i class="fa-regular fa-image text-3xl text-gray-200"></i>
                                            <p class="text-xs text-gray-300 text-center">Imagen no disponible</p>
                                        </div>
                                    </figure>
                                </td>
                                <td @click="product.isLocal ? $inertia.get(route('products.show', product.id)) : $inertia.get(route('global-products.show', product.id))" 
                                    class="text-primary underline cursor-pointer">{{ product.name }}</td>
                                <td >${{ product.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>{{ product.quantity }}</td>
                                <td>${{ (product.quantity * product.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

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

    <!-- -------------- Modal creación de orden starts----------------------- -->
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
                    <el-input v-model="form.name" placeholder="Escribe el nombre del cliente" :maxlength="100" clearable />
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
                    <InputLabel value="Número exterior*" class="ml-3 mb-1" />
                    <el-input v-model="form.ext_number" placeholder="Número de tu casa" :maxlength="255" clearable />
                    <InputError :message="form.errors.ext_number" />
                </div>

                <div>
                    <InputLabel value="Número Interior" class="ml-3 mb-1" />
                    <el-input v-model="form.int_number" placeholder="Número de edificio, coto, fraccionamiento" :maxlength="255" clearable />
                    <InputError :message="form.errors.int_number" />
                </div>
            </div>

            <p class="font-bold my-5">Detalles del pedido</p>

            <section class="max-h-72 overflow-auto">
                <div class="space-y-3">
                    <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id" :id="item.id" :init_state="item"
                    @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)" class="mb-1" />
                </div>
                <p v-if="!form.products?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                productos </p>
            </section>
            <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm">
                <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                    <i class="fa-solid fa-plus"></i>
                    Agregar producto
                </button>
                <div class="flex flex-col mr-7 items-end">
                    <p class="font-bold">Subtotal: <span class="mx-2">$</span>{{ form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p v-if="form.delivery_price !== null" class="font-bold ">Costo de envío: <span class="mx-2">$</span>{{ form.delivery_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    <p class="font-bold">Total: <span class="mx-2">$</span>{{ (form.total + form.delivery_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                </div>
            </div>

            <div class="mt-3">
                <InputLabel value="Método de pago" class="ml-3 mb-1" />
                <el-select v-model="form.payment_method" class="!w-full" filterable required clearable placeholder="Seleccione"
                    no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="payment_method in payment_methods" :key="payment_method" :value="payment_method" 
                    :label="payment_method" />
                </el-select>
            </div>

            <div class="flex justify-end space-x-1 pt-5 pb-1 py-3">
            <CancelButton @click="editOnlineOrderModal = false">Cancelar</CancelButton>
            <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
            </div>
        </form>
        </div>
    </Modal>
    <!-- --------------------------- Modal creación de orden ends ------------------------------------ -->
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
        ext_number: this.online_sale.ext_number,
        int_number: this.online_sale.int_number,
        delivery_price: this.online_sale.delivery_price,
        total: this.online_sale.total,
        store_id: this.$page.props.auth.user.store_id,
        created_from_app: true,
        products: this.online_sale.products
    });
    return {
        form,
        status: this.online_sale.status,
        statuses: ['Pendiente', 'Procesando', 'Entregado', 'Cancelado'],
        showDeleteConfirm: false, //modal de confirmación de eliminación
        editOnlineOrderModal: false, //modal de ediciónde orden
        products: null, //se obtienne todos los productos de la tienda
        next_item_id: this.online_sale.products.length, //para el index de productos creados como venta en linea
        payment_methods: [
            'Efectivo',
            'Tarjeta de crédito',
            'Tarjeta de débito',
            'Trandferencia o depósito',
            'Mercado pago',
        ]
    }
},
components:{
AppLayout,
ConfirmationModal,
PrimaryButton,
CancelButton,
DangerButton,
ProductInput,
InputLabel,
Modal,
Back
},
props:{
online_sale: Object
},
methods:{
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
    addNewItem() {
      this.form.products.push({ id: this.next_item_id++, price: null, product_id: null, is_local:null, quantity: null });
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
        default:
          return '';
      }
    },
    async updateStatus() {
        try {
            //se procesa el estatus para que funcione el metodo del controlador.
            let status = null;
            if ( this.status == 'Pendiente' ) {
                status = 'pendent';
            } else if ( this.status == 'Procesando' ) {
                status = 'processing';
            } else if ( this.status == 'Entregado' ) {
                status = 'delivered';
            } else if ( this.status == 'Cancelado' ) {
                status = 'cancel';
            }

            const response = await axios.put(route('online-sales.update-status', this.online_sale.id), { status: status });
            if ( response.status === 200 ) {
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
        try {
          const response = await axios.get(route('online-sales.fetch-all-products'));
          if ( response.status === 200 ) {
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
        this.form.delivery_price = storeProperties?.enabled_free_delivery && this.form.total >= storeProperties?.min_free_delivery 
        ? 0 
        : parseFloat(storeProperties?.delivery_price);
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

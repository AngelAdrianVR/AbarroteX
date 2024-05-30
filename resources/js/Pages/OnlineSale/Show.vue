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
                    <el-dropdown split-button type="primary" @click="editOnlineSale">
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
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';
import Back from "@/Components/MyComponents/Back.vue";

export default {
data() {
    return {
        status: this.online_sale.status,
        statuses: ['Pendiente', 'Procesando', 'Entregado', 'Cancelado'],
        showDeleteConfirm: false //modal de confirmación de eliminación
    }
},
components:{
AppLayout,
PrimaryButton,
CancelButton,
ConfirmationModal,
DangerButton,
Back
},
props:{
online_sale: Object
},
methods:{
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
}
}
</script>

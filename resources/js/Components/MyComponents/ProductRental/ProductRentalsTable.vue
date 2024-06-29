<template>
    <div class="overflow-auto">
        <table v-if="rentals?.length" class="w-full">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                    <th>Folio</th>
                    <th>Nombre del cliente</th>
                    <th>Producto rentado</th>
                    <th>Costo</th>
                    <th>Días transcurridos</th>
                    <th>Fecha de devolución</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="handleShow(encodeId(rent.id))" v-for="(rent, index) in rentals" :key="index"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">
                        {{ 'R-' + rent.id }}
                    </td>
                    <td>{{ rent.client.name }}</td>
                    <td>{{ rent.product.name }}</td>
                    <td>${{ rent.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} / {{ rent.period.name }}</td>
                    <td>{{ getCalculateDaysElapsed(rent) }}</td>
                    <td>
                        <el-tooltip placement="top">
                            <template #content>
                                <p v-html="getTooltipContent(rent)" class="text-center"></p>
                            </template>
                            <span v-html="getReturnDate(rent)"></span>
                        </el-tooltip>
                    </td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'see|' + encodeId(rent.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'edit|' + encodeId(rent.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'pay|' + rent.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>
                                        <span class="text-xs">Registrar pago</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'delete|' + rent.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-[14px] mr-2 text-red-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span class="text-xs text-red-600">Eliminar</span>
                                    </el-dropdown-item>
                                    <p class="text-gray99 px-4">Estado</p>
                                    <el-dropdown-item :command="'complete|' + rent.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span class="text-xs">Completar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'cancel|' + rent.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        <span class="text-xs">Cancelar renta</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay rentas registradas" />

        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar registro de renta</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará la renta seleccionada, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </div>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format, parseISO, differenceInDays } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';

export default {
    data() {
        return {
            showDeleteConfirm: false,
            itemIdToDelete: null,
        }
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        rentals: Array
    },
    methods: {
        getTooltipContent(rent) {
            if (rent.status == 'En uso') {
                return 'EN USO.<br>El producto ha sido entregado<br>y esta en uso por el cliente';
            } else if (rent.status == 'Completado') {
                return 'COMPLETADO.<br>El equipo se ha devuelto y<br>todos los pagos se ha recibido';
            } else if (rent.status == 'Cancelado') {
                return 'CANCELADO.<br>La renta ha sido cancelada';
            }
        },
        getReturnDate(rent) {
            if (rent.status == 'En uso') {
                return '<i class="fa-solid fa-rotate text-xs text-[#09EE05] mr-2"></i>-';
            } else if (rent.status == 'Completado') {
                return '<i class="fa-solid fa-check text-xs text-[#06B918] mr-2"></i>' + this.formatDate(rent.completed_at);
            } else if (rent.status == 'Cancelado') {
                return '<i class="fa-solid fa-xmark text-xs text-[#D70808] mr-2"></i>' + this.formatDate(rent.cancelled_at);
            }
        },
        getCalculateDaysElapsed(rent) {
            if (rent.status == 'En uso') {
                return this.calculateDaysElapsed(rent.rented_at) + ' hasta la fecha';
            } else if (rent.status == 'Completado') {
                return this.calculateDaysElapsed(rent.rented_at, rent.completed_at) + ' hasta que se completó';
            } else if (rent.status == 'Cancelado') {
                return this.calculateDaysElapsed(rent.rented_at, rent.cancelled_at) + ' hasta que se canceló';
            }
        },
        calculateDaysElapsed(startDate, endDate = '') {
            const past = new Date(startDate);
            const end = endDate ? new Date(endDate) : new Date();
            return differenceInDays(end, past);
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('product-rentals.show', data));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('product-rentals.edit', data));
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            } else if (commandName == 'pay') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            } else if (commandName == 'complete') {
                this.updateStatus(data, 'Completado');
            } else if (commandName == 'cancel') {
                this.updateStatus(data, 'Cancelado');
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleShow(encodedId) {
            this.$inertia.visit(route('product-rentals.show', encodedId));
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('product-rentals.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado el registro de renta',
                        type: 'success',
                    });
                    //se busca el index del cliente eliminado para removerlo del arreglo
                    const indexRentDeleted = this.rentals.findIndex(item => item.id == this.itemIdToDelete);
                    if (indexRentDeleted != -1) {
                        this.rentals.splice(indexRentDeleted, 1);
                    }
                    this.showDeleteConfirm = false;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar el registro de renta. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        },
        async updateStatus(rentId, status) {
            try {
                const response = await axios.put(route('product-rentals.update-status', rentId), { status });
                if (response.status == 200) {
                    // Buscar el índice del elemento en this.rentals
                    const index = this.rentals.findIndex(item => item.id == rentId);
                    if (index !== -1) {
                        // Actualizar el elemento en this.rentals
                        this.rentals[index] = response.data.item;
                    }
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo cambiar el status de la renta. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        },
    },
}
</script>
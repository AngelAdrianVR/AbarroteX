<template>
    <div class="overflow-auto">
        <table v-if="quotes?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                    <th class="w-24">Folio</th>
                    <th class="w-32">Creado el</th>
                    <th class="w-32">Nombre del contacto</th>
                    <th class="w-32">Monto</th>
                    <th class="w-32"></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="handleShow(encodeId(quote.id))"
                    v-for="(quote, index) in quotes" :key="index"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">
                        <el-tooltip :content="quote.status" placement="right">
                            <i class="mr-1"
                            :class="getStatusIcont(quote.status)"></i>
                        </el-tooltip>
                        {{ 'C-' + quote.folio }}
                    </td>
                    <td>{{ formatDate(quote.created_at) }}</td>
                    <td>{{ quote.contact_name }}</td>
                    <td>
                        <div v-if="quote.has_discount">
                            <p v-if="quote.is_percentage_discount">${{ (quote.total - (quote.discount * quote.total * 0.01))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                            <p v-else>${{ (quote.total - quote.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                        </div>
                        <p v-else>${{ quote.total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                    </td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item v-if="quote.status === 'Esperando respuesta' || quote.status === 'Rechazada'" :command="'status|' + quote.id + '|Autorizada'">
                                        <i class="fa-solid fa-check text-xs size-[14px] mr-2"></i>
                                        <span class="text-xs">Autorizada</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="quote.status === 'Esperando respuesta'" :command="'status|' + quote.id + '|Rechazada'">
                                        <i class="fa-solid fa-x text-xs size-[14px] mr-2"></i>
                                        <span class="text-xs">Rechazada</span>
                                    </el-dropdown-item>
                                    <!-- <el-dropdown-item v-if="quote.status !== 'Pagado' && quote.status !== 'Pago parcial' && quote.status !== 'Rechazada'" :command="'status|' + quote.id + '|Pago parcial'">
                                        <i class="fa-solid fa-circle-dollar-to-slot text-indigo-500 text-xs"></i>
                                        <span class="text-xs">Pago parcial</span>
                                    </el-dropdown-item> -->
                                    <el-dropdown-item v-if="quote.status === 'Autorizada' || quote.status === 'Pago parcial' || quote.status === 'Esperando respuesta'" :command="'status|' + quote.id + '|Pagado'">
                                        <i class="fa-solid fa-dollar-sign size-[14px] mr-2"></i>
                                        <span class="text-xs">Pagado</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'see|' + encodeId(quote.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'edit|' + encodeId(quote.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'delete|' + quote.id">
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
        <el-empty v-else description="No hay cotizaciones registradas" />

        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar cotización</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará al cotización seleccionado, esto es un proceso irreversible. ¿Continuar
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
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';

export default {
data() {
    return {
        showDeleteConfirm: false,
        itemIdToDelete: null,
    }
},
components:{
ConfirmationModal,
PrimaryButton,
CancelButton,
},
props:{
quotes: Array
},
methods:{
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const data = command.split('|')[1];

        if (commandName == 'see') {
            this.$inertia.get(route('quotes.show', data));
        } else if (commandName == 'edit') {
            this.$inertia.get(route('quotes.edit', data));
        } else if (commandName == 'status') {
            this.updateStatus(data, command.split('|')[2]); 
        }
        else if (commandName == 'delete') {
            this.showDeleteConfirm = true;
            this.itemIdToDelete = data;
        }
    },
    async updateStatus(quoteId, status) {
        try {
            const response = await axios.post(route('quotes.update-status', quoteId), {status: status });
            if ( response.status == 200 ) {
                const quoteIndex = this.quotes.findIndex(item => item.id == quoteId);
                if ( quoteIndex != -1 ) {
                    this.quotes[quoteIndex].status = response.data.status;
                }
                this.$notify({
                    title: 'Correcto',
                    message: 'Se ha actualizazo el estatus de la cotización',
                    type: 'success',
                });
            }
        } catch (error) {
            this.$notify({
                title: 'No se pudo completar la petición',
                message: 'Hubo un problema al cambiar el estatus. Actualiza la página e inténtalo de nuevo',
                type: 'success',
            });
        }
    },
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    handleShow(encodedId) {
        window.open(route('quotes.show', encodedId, '_blank'));
    },
    getStatusIcont(status) {
        if ( status === 'Esperando respuesta' ) {
            return 'fa-regular fa-clock text-amber-500';
        } else if ( status === 'Rechazada' ) {
            return 'fa-solid fa-x text-red-500';
        } else if ( status === 'Autorizada' ) {
            return 'fa-solid fa-check text-blue-500';
        } else if ( status === 'Pagado' ) {
            return 'fa-solid fa-dollar-sign text-green-500';
        } else if ( status === 'Pago parcial' ) {
            return 'fa-solid fa-circle-dollar-to-slot text-indigo-500';
        }
    },
    async deleteItem() {
        try {
            const response = await axios.delete(route('quotes.destroy', this.itemIdToDelete));
            if (response.status == 200) {
                this.$notify({
                    title: 'Correcto',
                    message: 'Se ha eliminado la cotización',
                    type: 'success',
                });
                //se busca el index del cliente eliminado para removerlo del arreglo
                const indexQuoteDeleted = this.services.findIndex(item => item.id == this.itemIdToDelete);
                if ( indexQuoteDeleted != -1 ) {
                    this.services.splice(indexQuoteDeleted, 1);
                }
                this.showDeleteConfirm = false;
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: 'El servidor no pudo procesar la petición',
                message: 'No se pudo eliminar la cotización. Intente más tarde o si el problema persiste, contacte a soporte',
                type: 'error',
            });
        }
    },
    encodeId(id) {
        const encodedId = btoa(id.toString());
        return encodedId;
    },
},
}
</script>
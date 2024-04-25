<template>
    <section v-if="items.length" class="mt-4 text-sm w-full overflow-auto">
        <table class="w-full">
            <tr class="font-bold *:pb-3 *:px-4">
                <td>Fecha</td>
                <td>Efectivo al iniciar</td>
                <td>Efectivo esperado</td>
                <td>Recuento manual</td>
                <td>Diferencia</td>
                <td></td>
            </tr>
            <tr @click="$inertia.get(route('cash-cuts.show', item.id))" class="*:py-2 *:px-4 cursor-pointer hover:bg-primarylight" v-for="item in items" :key="item">
                <td class="rounded-l-full">{{ formatDate(item.created_at) }}</td>
                <td>${{ item.started_cash }}</td>
                <td>${{ item.expected_cash }}</td>
                <td>${{ item.counted_cash }}</td>
                <td :class="differenceStyles(item.difference)">${{ item.difference }}</td>
                <td class="rounded-e-full text-end">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button @click.stop
                            class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'see|' + item.id">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="text-xs">Ver</span>
                                </el-dropdown-item>
                                <el-dropdown-item :command="'delete|' + item.id">
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
        </table>
    </section>
    <p class="text-sm text-center text-gray-400 mt-5" v-else>No hay cortes registrados</p>

    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar corte de caja</h1>
        </template>
        <template #content>
            <p>
                Se eliminará el corte de caja seleccionado, esto es un proceso irreversible. ¿Continuar
                de todas formas?
            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="deleteItem">Eliminar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
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
DangerButton,
CancelButton
},
props:{
items: Array    
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy h:mm a', { locale: es });
    },
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const data = command.split('|')[1];

        if (commandName == 'see') {
            this.$inertia.get(route('cash-cuts.show', data));
        } else if (commandName == 'print') {
            this.print(data);
        } else if (commandName == 'delete') {
            this.showDeleteConfirm = true;
            this.itemIdToDelete = data;
        }
    },
    async deleteItem() {
        try {
            const response = await axios.delete(route('cash-cuts.destroy', this.itemIdToDelete));
            if (response.status == 200) {
                this.$notify({
                    title: 'Correcto',
                    message: 'Se han eliminado el corte de caja',
                    type: 'success',
                });
                location.reload();
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: 'Error',
                message: 'No se pudo eliminar. Inténte más tarde',
                type: 'error',
            });
        }
    },
    differenceStyles(difference) {
        if ( difference < 0 ) {
            return 'text-red-600';
        } else if ( difference === 0 ) {
            return 'text-green-500';
        } else if ( difference > 0 ) {
            return 'text-blue-600';
        }
    }
}
}
</script>

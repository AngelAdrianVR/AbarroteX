<template>
    <div class="overflow-auto">
        <table v-if="reports?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                    <th class="w-20 md:w-[15%]">Folio</th>
                    <th class="w-32 md:w-[15%]">Fecha del servicio</th>
                    <th class="w-32 md:w-[15%]">Responsable</th>
                    <th class="w-32 md:w-[15%]">Solicitante</th>
                    <th class="w-12 md:w-[5%]"></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="openTemplate(report.id)"
                    v-for="(report, index) in reports" :key="index"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">{{ 'RS-' + String(report.folio).padStart(4, '0') }}</td>
                    <td>{{ formatDate(report.service_date) }}</td>
                    <td>{{ report.technician_name ?? 'No especificado' }}</td>
                    <td>{{ report.client_name ?? 'No especificado' }}</td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'see|' + encodeId(report.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="canEdit" :command="'edit|' + encodeId(report.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="canDelete" :command="'delete|' + report.id">
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
        <el-empty v-else description="No hay reportes de servicios registrados" />

        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar servicio</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará el servicio seleccionado, esto es un proceso irreversible. ¿Continuar
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {
            showDeleteConfirm: false,
            itemIdToDelete: null,
            canEdit: this.$page.props.auth.user.permissions.includes('Editar ordenes de servicio'),
            canDelete: this.$page.props.auth.user.permissions.includes('Eliminar ordenes de servicio'),
        }
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        reports: Array
    },
    methods: {
        openTemplate(reportId) {
            const url = route('service-reports.show', this.encodeId(reportId));
            window.open(url, '_blank');
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMM yyyy', { locale: es });
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('service-reports.show', data));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('service-reports.edit', data));
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('service-reports.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado el reporte',
                        type: 'success',
                    });
                    //se busca el index del cliente eliminado para removerlo del arreglo
                    const indexServiceDeleted = this.reports.findIndex(item => item.id == this.itemIdToDelete);
                    if (indexServiceDeleted != -1) {
                        this.reports.splice(indexServiceDeleted, 1);
                    }
                    this.showDeleteConfirm = false;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar el reporte. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        },
    },
}
</script>
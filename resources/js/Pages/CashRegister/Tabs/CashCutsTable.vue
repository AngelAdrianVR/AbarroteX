<template>
    <section v-if="Object.keys(items)?.length" class="mt-4 text-sm w-full overflow-auto">
        <table class="w-full">
            <tr class="font-bold *:pb-3 *:px-4 border-b border-primary">
                <td>Fecha</td>
                <td>Total de cortes</td>
                <td>Total de ventas</td>
                <td>Total de diferencias</td>
                <td></td>
            </tr>
            <tr @click="$inertia.visit(route('cash-cuts.show', index))"
                class="*:py-2 *:px-4 text-xs cursor-pointer hover:bg-primarylight" v-for="(item, index) in items"
                :key="item">
                <td class="rounded-l-full">{{ formatDate(index) }}</td>
                <td>{{ Object.values(item.cuts).length }}</td>
                <td>${{ item.total_sales?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                <td :class="differenceStyles(item.total_difference)">${{
                    item.total_difference?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                <td class="rounded-e-full text-end">
                </td>
            </tr>
        </table>
    </section>
    <el-empty v-else description="No hay cortes registrados" />

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
                <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
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
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        items: Array
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];
            console.log(data)
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
            if (difference < 0) {
                return 'text-red-600';
            } else if (difference === 0) {
                return 'text-green-500';
            } else if (difference > 0) {
                return 'text-blue-600';
            }
        }
    }
}
</script>

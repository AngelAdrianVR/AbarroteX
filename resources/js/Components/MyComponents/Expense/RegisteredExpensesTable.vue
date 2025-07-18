<template>
    <div class="overflow-auto">
        <div class="lg:flex items-center lg:space-x-2">
            <el-pagination v-if="showPagination" v-model:current-page="currentPage" v-model:page-size="pageSize"
                @size-change="handleSizeChange" @current-change="handlePagination"
                layout="total, sizes, prev, pager, next" :page-sizes="[100, 200, 400, 800]" :total="pagination.total"
                size="small" />
            <p class="text-xs text-gray37 mt-1 lg:mt-0">
                {{ items.length }} {{ items.length == 1 ? 'elemento' : 'elementos' }} en la tabla
            </p>
        </div>
        <el-table ref="tableRef" :data="items" @row-click="handleRowClick" max-height="500"
            :row-class-name="tableRowClassName" class="!w-full mx-auto"
            :default-sort="{ prop: 'date', order: 'descending' }">
            <el-table-column fixed sortable prop="date" label="Fecha">
                <template #default="scope">
                    {{ formatDate(scope.row.date) }}
                </template>
            </el-table-column>
            <el-table-column label="Conceptos">
                <template #default="scope">
                    {{ scope.row.expenses.length }}
                </template>
            </el-table-column>
            <el-table-column label="Total">
                <template #default="scope">
                    ${{ scope.row.total_expense?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                </template>
            </el-table-column>
        </el-table>
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar gastos</h1>
            </template>
            <template #content>
                <p>
                    Se eliminarán los gastos del dia seleccionado, esto es un proceso irreversible. ¿Continuar
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
            // Permisos de rol actual
            canDelete: this.$page.props.auth.user.rol == 'Administrador',
            //paginacion
            currentPage: parseInt(this.pagination.current_page),
            pageSize: parseInt(this.pagination.per_page),
        };
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    emits: ['refresh-data'],
    props: {
        items: Object,
        pagination: Object,
        showPagination: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        handleSizeChange() {
            // reiniciar la pagina a 1
            this.currentPage = 1;
            this.$emit('refresh-data', this.currentPage, this.pageSize);
        },
        handlePagination(val) {
            this.$emit('refresh-data', val, this.pageSize);
        },
        handleRowClick(row) {
            const date = format(parseISO(row.date), 'yyyy-MM-dd', { locale: es });
            this.$inertia.visit(route('expenses.show', { date }));
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-[11px] lg:text-xs';
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.handleShow(data);
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('expenses.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se han eliminado los gastos del día',
                        type: 'success',
                    });
                    location.reload();
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar el producto. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        },
    },
}
</script>

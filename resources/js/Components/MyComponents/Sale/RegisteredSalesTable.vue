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
            :row-class-name="tableRowClassName" class="!w-full mx-auto" :default-sort="{ prop: 'date', order: 'descending' }">
            <el-table-column fixed sortable prop="date" label="Fecha" width="125">
                <template #default="scope">
                    {{ formatDate(scope.row.date) }}
                </template>
            </el-table-column>
            <el-table-column label="Ventas en tienda" width="140">
                <template #default="scope">
                    {{ scope.row.normal_folios != 1 ? scope.row.normal_folios + ' ventas' : scope.row.normal_folios +
                        'venta'
                    }}
                    ({{ scope.row.total_normal_quantity }} productos en total)
                </template>
            </el-table-column>
            <el-table-column v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')"
                label="Ventas en línea" width="140">
                <template #default="scope">
                    {{ scope.row.online_folios != 1 ? scope.row.online_folios + ' ventas' : scope.row.online_folios
                        + 'venta'
                    }}
                    ({{ scope.row.total_online_quantity }} productos en total)
                </template>
            </el-table-column>
            <el-table-column v-if="$page.props.auth.user.store.activated_modules.includes('Cotizaciones')"
                label="Cotizaciones" width="140">
                <template #default="scope">
                    {{ scope.row.quote_folios != 1 ? scope.row.quote_folios + ' ventas' : scope.row.quote_folios
                        + 'venta'
                    }}
                    ({{ scope.row.total_quote_quantity }} productos en total)
                </template>
            </el-table-column>
            <el-table-column v-if="$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio')"
                label="Servicios" width="140">
                <template #default="scope">
                    {{ scope.row.service_folios != 1 ? scope.row.service_folios + ' ventas' : scope.row.service_folios
                        + 'venta'
                    }}
                </template>
            </el-table-column>
            <el-table-column label="Total" width="100">
                <template #default="scope">
                    ${{ scope.row.total_day_sale?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template #default="scope">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button @click.stop
                            class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'see|' + scope.row.date">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="text-xs">Ver</span>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {
            loading: false,
            sales: {},
            //paginacion
            currentPage: parseInt(this.pagination.current_page),
            pageSize: parseInt(this.pagination.per_page),
        };
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        DangerButton,
        CancelButton,
        ThirthButton,
        InputLabel,
        Loading
    },
    emits: ['refresh-data'],
    props: {
        // cashRegister: Object,
        items: Object,
        pagination: Object,
        showPagination: {
            type: Boolean,
            default: true,
        },
    },
    computed: {
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
            this.$inertia.visit(route('sales.show', { date }))
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-[11px] lg:text-xs';
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];
            
            if (commandName == 'see') {
                const date = format(parseISO(data), 'yyyy-MM-dd', { locale: es });
                this.$inertia.get(route('sales.show', { date }));
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
    },
}
</script>

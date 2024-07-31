<template>
    <table v-if="internal_invoices?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-grayD0">
                    <th class="w-24 md:w-[10%]">Folio</th>
                    <th class="w-32 md:w-[15%]">Tipo de suscripción</th>
                    <th class="w-32 md:w-[15%]">Periodo</th>
                    <th class="w-32 md:w-[15%]">Monto pagado</th>
                    <th class="w-32 md:w-[15%]">Fecha de pago</th>
                    <th class="w-32 md:w-[15%]">Factura</th>
                    <th class="w-10 md:w-[5%]"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(invoice, index) in internal_invoices" :key="index"
                    class="*:text-xs *:py-2 *:px-4 border-b border-grayD9">
                    <td class="rounded-s-full">{{ invoice.id }}</td>
                    <td>{{ invoice.suscription_type }}</td>
                    <td>{{ 'Del ' + formatDate(invoice.paid_at) + ' al ' + formatDate(invoice.end_of_period) }}</td>
                    <td>${{ invoice.total_paid?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    <td>{{ formatDate(invoice.paid_at) }}</td>
                    <td>
                        <span v-if="invoice.status === 'Sin factura'" class="text-primary underline cursor-pointer">Solicitar factura</span>
                        
                        <el-tooltip v-else-if="invoice.status === 'Solicitado'" content="La factura será enviada en un plazo no mayor a 48 horas." placement="top">
                            <span class="bg-green-100 text-green-500 py-1 px-2 rounded-full cursor-default"><i class="fa-regular fa-clock mr-2"></i>{{ invoice.status }}</span>
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
                                    <el-dropdown-item :command="'support|' + invoice.id">
                                        <span class="text-xs">Contactar con soporte</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay periodos disponibles para facturar" />
</template>

<script>
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {

    }
},
props:{
    internal_invoices: Array
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
    },
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const data = command.split('|')[1];

        if (commandName == 'support') {
            this.$inertia.get(route('support-reports.create'));
        }
    },
}
}
</script>
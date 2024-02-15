<template>
    <AppLayout title="Inicio">
        <h1 class="font-bold mx-4 lg:mx-32 mt-4">Inicio</h1>
        <el-radio-group v-model="period" class="!flex justify-center my-8 mx-2 lg:mx-14">
            <el-radio label="Hoy">Hoy</el-radio>
            <el-radio label="Semanal">Semanal</el-radio>
            <el-radio label="Mensual">Mensual</el-radio>
        </el-radio-group>
        <section class="mx-2 lg:mx-14 mt-6 grid grid-cols-1 lg:grid-cols-4 md:grid-cols-3 gap-1 lg:gap-5">
            <SimpleKPI v-for="(item, index) in simpleKpis" :key="index" :title="item.title" :icon="item.icon"
                class="self-start" :value="item.value" />
            <Kpi :options="profitKpiOptions" :title="getKPITitle()" />
        </section>
        <section class="mx-2 lg:mx-14 mt-6 grid-cols-1 grid lg:grid-cols-3 gap-1 lg:gap-8">
            <PieChart :options="ticketsStatusChartOptions" title="Estado de los tickets"
                icon='<i class="fa-solid fa-circle-nodes ml-2"></i>' />
            <BarChart :options="yearSalesComparisonChartOptions" :title="getBarChartTitle('Ingresos (ventas)')" />
            <BarChart :options="yearSalesComparisonChartOptions" :title="getBarChartTitle('Egresos (compras)')" />
        </section>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleKPI from '@/Components/MyComponents/Dashboard/SimpleKPI.vue';
import PieChart from '@/Components/MyComponents/Charts/PieChart.vue';
import Kpi from '@/Components/MyComponents/Dashboard/Kpi.vue';
import BarChart from "@/Components/MyComponents/Charts/BarChart.vue";

export default {
    data() {
        return {
            period: 'Hoy',
            ticketsStatusChartOptions: {
                colors: ['#0355B5', '#FD8827', '#B927FD', '#3F9C30', '#FD4646', '#3D3D3D'],
                labels: ["Abierto", "En espera", "En espera de 3ro", "Completado", "Re-abierto", "En proceso"],
                series: [1, 2, 3, 4, 5, 6],
            },
            yearSalesComparisonChartOptions: {
                colors: ['#BEBFC1', '#F07209'],
                categories: ['1', '2'],
                series: [{
                    name: 'Año pasado',
                    data: [5, 9]
                },
                {
                    name: 'Año en curso',
                    data: [6, 2]
                }],
            },

            // kpis
            profitKpiOptions: {
                currentVal: 10600,
                refVal: 8966,
                tooltipCurrentVal: 'Ganancias periodo actual',
                tooltipRefVal: 'Ganancias periodo anterior',
                unit: '$',
            },

            // kpis simples
            simpleKpis: [
                {
                    title: "Venta",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalSale().toLocaleString('en-US', { minimumFractionDigits: 2 }),
                },
                {
                    title: "Productos vendidos",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsSold().toLocaleString('en-US', { minimumFractionDigits: 2 }),
                },

            ]
        }
    },
    props: {
        sales: Array,
    },
    components: {
        AppLayout,
        PieChart,
        SimpleKPI,
        Kpi,
        BarChart,
    },
    methods: {
        calculateTotalSale() {
            return this.sales.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);
        },
        calculateTotalProductsSold() {
            return this.sales.reduce((acumulador, current) => {
                return acumulador + current.quantity;
            }, 0);
        },
        getKPITitle() {
            if (this.period == 'Hoy') return "Ganancias de hoy vs ayer";
            if (this.period == 'Semanal') return "Ganancias de esta semana vs semana pasada";
            if (this.period == 'Mensual') return "Ganancias de este mes vs mes pasado";
        },
        getBarChartTitle(concept) {
            if (this.period == 'Hoy') return concept + " de hoy vs ayer";
            if (this.period == 'Semanal') return concept + " de esta semana vs semana pasada";
            if (this.period == 'Mensual') return concept + " de este mes vs mes pasado";
        },
        ticketsByStatus() {
            // Inicializar un objeto para almacenar el recuento de tickets por estado
            const statuses = ["Abierto", "En espera", "En espera de 3ro", "Completado", "Re-abierto", "En proceso"];
            const byStatus = {};
            statuses.forEach(status => {
                byStatus[status] = 0;
            });

            // Contar los tickets por estado
            this.tickets.forEach(ticket => {
                if (byStatus.hasOwnProperty(ticket.status)) {
                    byStatus[ticket.status]++;
                }
            });

            // Convertir el objeto a un arreglo de recuento
            const result = statuses.map(status => byStatus[status]);

            return result;
        },
        ticketsByPriority() {
            // Inicializar un objeto para almacenar el recuento de tickets por estado
            const priorities = ["Baja", "Media", "Alta"];
            const byStatus = {};
            priorities.forEach(priority => {
                byStatus[priority] = 0;
            });

            // Contar los tickets por estado
            this.tickets.forEach(ticket => {
                if (byStatus.hasOwnProperty(ticket.priority)) {
                    byStatus[ticket.priority]++;
                }
            });

            // Convertir el objeto a un arreglo de recuento
            const result = priorities.map(priority => byStatus[priority]);

            return result;
        },
        calculateAverageResponseTimeByPriority(priority) {
            const responseTimes = this.tickets
                .filter(ticket => ticket.priority === priority)
                .map(ticket => {
                    // Check if there are solutions for the ticket
                    if (ticket.ticket_solutions && ticket.ticket_solutions.length > 0) {
                        // Calculate response time in milliseconds
                        const responseTimeMillis = new Date(ticket.ticket_solutions[0].created_at) - new Date(ticket.created_at);

                        // Convert to hours
                        const responseTimeHours = responseTimeMillis / (1000 * 60 * 60);

                        return responseTimeHours;
                    } else {
                        // If there is no solution, return null or a value indicating no response time
                        return null;
                    }
                });

            // Filter out non-null response times
            const validResponseTimes = responseTimes.filter(time => time !== null);

            // Calculate the average response time
            if (validResponseTimes.length > 0) {
                const averageResponseTime = validResponseTimes.reduce((total, time) => total + time, 0) / validResponseTimes.length;
                return averageResponseTime.toFixed(2) + ' horas';
            } else {
                return 'Sin información'; // Another way to handle the case when there are no valid response times
            }
        },
        calculatePercentageResolvedBeforeExpiration() {
            const resolvedBeforeExpiration = this.tickets.filter(ticket => {
                // Check if there are solutions for the ticket and an expiration date
                if (ticket.ticket_solutions && ticket.ticket_solutions.length > 0 && ticket.expired_date) {
                    // Compare the solution date with the expiration date
                    const solutionDate = new Date(ticket.ticket_solutions[0].created_at);
                    const expirationDate = new Date(ticket.expired_date);

                    return solutionDate < expirationDate;
                } else {
                    return false;
                }
            });

            // Calculate the percentage
            const percentage = (resolvedBeforeExpiration.length / this.tickets.filter(item => item.ticket_solutions.length).length) * 100;

            if (percentage)
                return percentage + '%'; // Return the percentage or 0 if there are no tickets
            else
                return 'Sin información';
        },
    }
}
</script>

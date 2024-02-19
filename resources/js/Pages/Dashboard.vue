<template>
    <AppLayout title="Inicio">
        <h1 class="font-bold mx-4 lg:mx-32 mt-4">Inicio</h1>
        <el-radio-group v-model="period" @change="handleChangePeriod" class="!flex justify-center my-8 mx-2 lg:mx-14">
            <el-radio label="Hoy">Hoy</el-radio>
            <el-radio label="Semanal">Semanal</el-radio>
            <el-radio label="Mensual">Mensual</el-radio>
        </el-radio-group>
        <Loading v-if="loading" class="my-16" />
        <main v-else class="mx-2 lg:mx-14 mt-6">
            <section class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-3 gap-1 lg:gap-5">
                <SimpleKPI v-for="(item, index) in getSimpleKpisOptions" :key="index" :title="item.title" :icon="item.icon"
                    class="self-start" :value="item.value" />
                <Kpi v-for="(item, index) in getKpiOptions" :key="index" :options="item" :title="getKPITitle()" />
            </section>
            <section class="grid-cols-1 grid lg:grid-cols-2 gap-1 lg:gap-8 mt-2">
                <BarChart v-for="(item, index) in getBarChartOptions" :key="index" :options="item"
                    :title="getBarChartTitle(item.title)" />
                <PieChart v-for="(item, index) in getPieChartOptions" :key="index" :options="item"
                    title="Top 5 productos más vendidos" icon='<i class="fa-solid fa-trophy ml-2"></i>' />
            </section>
        </main>
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import SimpleKPI from '@/Components/MyComponents/Dashboard/SimpleKPI.vue';
import PieChart from '@/Components/MyComponents/Charts/PieChart.vue';
import Kpi from '@/Components/MyComponents/Dashboard/Kpi.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import BarChart from "@/Components/MyComponents/Charts/BarChart.vue";
import { format, subHours } from 'date-fns';
import axios from 'axios';

export default {
    data() {
        return {
            period: 'Hoy',

            // sales
            salesCurrentPeriod: [],
            salesLastPeriod: [],

            // expenses
            expensesCurrentPeriod: [],
            expensesLastPeriod: [],

            // top products
            topProductsCurrentPeriod: [],

            loading: false,
        }
    },
    props: {

    },
    components: {
        AppLayout,
        PieChart,
        SimpleKPI,
        Kpi,
        BarChart,
        Loading,
    },
    computed: {
        calculateTotalSale() {
            return this.salesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);
        },
        calculateTotalExpense() {
            return this.expensesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);
        },
        calculateTotalProductsSold() {
            return this.salesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.quantity;
            }, 0);
        },
        calculateTotalProductsExpense() {
            return this.expensesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.quantity;
            }, 0);
        },
        getSimpleKpisOptions() {
            return [
                {
                    title: "Venta (ingresos)",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalSale?.toLocaleString('en-US', { minimumFractionDigits: 2 }) //,
                },
                {
                    title: "Productos vendidos",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsSold?.toLocaleString('en-US', { minimumFractionDigits: 2 }) //,
                },
                {
                    title: "Compras (Egresos)",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalExpense?.toLocaleString('en-US', { minimumFractionDigits: 2 }) //,
                },
                {
                    title: "Productos comprados",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsExpense?.toLocaleString('en-US', { minimumFractionDigits: 2 }) //,
                },
            ]
        },
        getKpiOptions() {
            let last = "ayer";
            let current = "hoy";
            if (this.period == 'Semanal') {
                current = "esta semana";
                last = "semana pasada";
            } else if (this.period == 'Mensual') {
                current = "este mes";
                last = "mes pasado";
            }

            return [
                {
                    currentVal: this.calculateProfit('current'),
                    refVal: this.calculateProfit('last'),
                    tooltipCurrentVal: 'Ganancias de ' + current,
                    tooltipRefVal: 'Ganancias de ' + last,
                    unit: '$',
                },
            ]
        },
        getTimeLine() {
            let timeLine = ['6AM', '7AM', '8AM', '9PM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM'];
            let last = { name: "Ayer", data: { sales: this.calculateHourlySales(this.salesLastPeriod), expenses: this.calculateHourlySales(this.expensesLastPeriod) } };
            let current = { name: "Hoy", data: { sales: this.calculateHourlySales(this.salesCurrentPeriod), expenses: this.calculateHourlySales(this.expensesCurrentPeriod) } };

            if (this.period == 'Semanal') {
                timeLine = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];
                last = { name: "Semana pasada", data: { sales: this.calculateDailySales(this.salesLastPeriod), expenses: this.calculateDailySales(this.expensesLastPeriod) } };
                current = { name: "Esta semana", data: { sales: this.calculateDailySales(this.salesCurrentPeriod), expenses: this.calculateDailySales(this.expensesCurrentPeriod) } };
            } else if (this.period == 'Mensual') {
                timeLine = ['Del 1 al 7', 'Del 8 al 14', 'Del 15 al 21', 'Del 22 al 28', 'Del 29 al 31'];
                last = { name: "Mes pasado", data: { sales: this.calculateWeeklySales(this.salesLastPeriod), expenses: this.calculateWeeklySales(this.expensesLastPeriod) } };
                current = { name: "Este mes", data: { sales: this.calculateWeeklySales(this.salesCurrentPeriod), expenses: this.calculateWeeklySales(this.expensesCurrentPeriod) } };
            }

            return { timeline: timeLine, last: last, current: current };
        },
        getBarChartOptions() {
            const timeline = this.getTimeLine;
            return [
                {
                    title: 'Ingresos (ventas)',
                    colors: ['#C30303', '#F07209'],
                    categories: timeline.timeline,
                    series: [{
                        name: timeline.last.name,
                        data: timeline.last.data.sales,
                    },
                    {
                        name: timeline.current.name,
                        data: timeline.current.data.sales,
                    }],
                },
                {
                    title: 'Egresos (compras)',
                    colors: ['#C30303', '#F07209'],
                    categories: timeline.timeline,
                    series: [{
                        name: timeline.last.name,
                        data: timeline.last.data.expenses,
                    },
                    {
                        name: timeline.current.name,
                        data: timeline.current.data.expenses,
                    }],
                },
            ]
        },
        getPieChartOptions() {
            return [
                {
                    colors: ['#C30303', '#373737', '#999999', '#5FCB1F', '#2387FC'],
                    labels: this.topProductsCurrentPeriod.map((item) => item.product.name),
                    series: this.topProductsCurrentPeriod.map((item) => item.total_quantity),
                },
            ]
        },
    },
    methods: {
        setElementsWithNumberFormat(set) {
            return set.map(item => item.toFixed(2));
        },
        calculateHourlySales(data) {
            // Inicializa el array hourlyData con ceros para cada hora del día
            let hourlyData = Array(18).fill(0);

            // Define la zona horaria local de la máquina
            const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            // Recorre las ventas y suma el total por hora
            data.forEach((sale) => {
                const saleDateTime = new Date(sale.created_at);

                // Ajusta la fecha y hora a la zona horaria local
                const localSaleDateTime = subHours(saleDateTime, new Date().getTimezoneOffset() / 60);

                // Convierte la fecha y hora a la hora del día en la zona horaria local
                const saleHour = format(localSaleDateTime, 'H', { timeZone });

                hourlyData[saleHour] += sale.quantity * sale.current_price;
            });
            hourlyData = this.setElementsWithNumberFormat(hourlyData);

            return hourlyData;
        },
        calculateDailySales(data) {
            let dailyData = Array(7).fill(0);

            data.forEach((sale) => {
                const saleDayOfWeek = new Date(sale.created_at).getDay();

                // Incrementa el total por día de la semana correspondiente
                dailyData[saleDayOfWeek] += sale.quantity * sale.current_price;
            });

            dailyData = this.setElementsWithNumberFormat(dailyData);

            return dailyData;
        },
        calculateWeeklySales(data) {
            // Inicializa el array weeklyData con ceros para cada rango semanal
            let weeklyData = Array(5).fill(0);

            // Recorre las ventas y suma el total por rango semanal
            data.forEach((sale) => {
                const saleDay = new Date(sale.created_at).getDate();

                if (saleDay >= 1 && saleDay <= 7) {
                    weeklyData[0] += sale.quantity * sale.current_price;
                } else if (saleDay >= 8 && saleDay <= 14) {
                    weeklyData[1] += sale.quantity * sale.current_price;
                } else if (saleDay >= 15 && saleDay <= 21) {
                    weeklyData[2] += sale.quantity * sale.current_price;
                } else if (saleDay >= 22 && saleDay <= 28) {
                    weeklyData[3] += sale.quantity * sale.current_price;
                } else if (saleDay >= 29 && saleDay <= 31) {
                    weeklyData[4] += sale.quantity * sale.current_price;
                }
            });

            weeklyData = this.setElementsWithNumberFormat(weeklyData);

            return weeklyData;
        },
        calculateProfit(period) {
            let sales = this.salesLastPeriod.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);

            let expenses = this.expensesLastPeriod.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);
            if (period == 'current') {
                sales = this.salesCurrentPeriod.reduce((acumulador, current) => {
                    return acumulador + current.quantity * current.current_price;
                }, 0);

                expenses = this.expensesCurrentPeriod.reduce((acumulador, current) => {
                    return acumulador + current.quantity * current.current_price;
                }, 0);
            }

            return sales - expenses;
        },
        handleChangePeriod() {
            if (this.period == 'Semanal') {
                this.fetchWeekData();
            } else if (this.period == 'Mensual') {
                this.fetchMonthData();
            } else {
                this.fetchDailyData();
            }
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
        async fetchDailyData() {
            this.loading = true;
            try {
                const response = await axios.get(route('dashboard.get-day-data'));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.expensesCurrentPeriod = response.data.expenses;
                    this.expensesLastPeriod = response.data.last_period_expenses;
                    this.topProductsCurrentPeriod = response.data.top_products;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchWeekData() {
            this.loading = true;
            try {
                const response = await axios.get(route('dashboard.get-week-data'));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.expensesCurrentPeriod = response.data.expenses;
                    this.expensesLastPeriod = response.data.last_period_expenses;
                    this.topProductsCurrentPeriod = response.data.top_products;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchMonthData() {
            this.loading = true;
            try {
                const response = await axios.get(route('dashboard.get-month-data'));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.expensesCurrentPeriod = response.data.expenses;
                    this.expensesLastPeriod = response.data.last_period_expenses;
                    this.topProductsCurrentPeriod = response.data.top_products;
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        }
    },
    mounted() {
        this.fetchDailyData();
    }
}
</script>

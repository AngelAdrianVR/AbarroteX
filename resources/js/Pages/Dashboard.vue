<template>
    <AppLayout title="Reportes">
        <h1 class="font-bold mx-4 lg:mx-32 mt-4">Reportes</h1>
        <section v-if="$page.props.auth.user.permissions.includes('Filtrar por periodo')"
            class="flex items-center justify-center">
            <el-radio-group v-model="period" @change="handleChangePeriod"
                class="flex flex-col md:flex-row !items-start my-5 lg:mx-14">
                <el-radio value="Hoy">
                    <span v-if="period != 'Hoy'">Hoy</span>
                    <el-date-picker v-else @change="handleChangePeriodRange" v-model="periodRange" type="date"
                        placeholder="Elige un día" format="DD/MM/YYYY" value-format="YYYY-MM-DD" size="small" />
                </el-radio>
                <el-radio value="Semanal">
                    <span v-if="period != 'Semanal'">Semanal</span>
                    <el-date-picker v-else @change="handleChangePeriodRange" v-model="periodRange" type="week"
                        format="[Semana] ww" value-format="YYYY-MM-DD" placeholder="Elige una semana" size="small" />
                </el-radio>
                <el-radio value="Mensual">
                    <span v-if="period != 'Mensual'">Mensual</span>
                    <el-date-picker v-else @change="handleChangePeriodRange" v-model="periodRange" type="month"
                        format="MM/YYYY" value-format="YYYY-MM-DD" placeholder="Elige un mes" size="small" />
                </el-radio>
            </el-radio-group>
        </section>
        <section v-else>
            <p class="text-center">Reporte de HOY</p>
        </section>
        <Loading v-if="loading" class="my-16" />
        <main v-else class="mx-2 lg:mx-14 my-6">
            <section class="grid grid-cols-1 lg:grid-cols-4 md:grid-cols-3 gap-1 lg:gap-5">
                <template v-for="(item, index) in getSimpleKpisOptions" :key="index">
                    <SimpleKPI v-if="item.show" :title="item.title" :icon="item.icon" class="self-start"
                        :value="item.value" />
                </template>
                <Kpi v-for="(item, index) in getKpiOptions" :key="index" :options="item" :title="getKPITitle()" />
            </section>
            <section class="grid-cols-1 grid lg:grid-cols-2 gap-1 lg:gap-8 mt-2">
                <template v-for="(item, index) in getBarChartOptions" :key="index">
                    <ComparisonBarChart v-if="item.show" :options="item" :title="getBarChartTitle(item.title)" />
                </template>
                <PieChart v-for="(item, index) in getPieChartOptions" :key="index" :options="item"
                    title="Top 5 productos más vendidos" icon='<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" /></svg>' />
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
import ComparisonBarChart from "@/Components/MyComponents/Charts/ComparisonBarChart.vue";
import { format, subHours, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';

export default {
    data() {
        return {
            period: 'Hoy',
            periodRange: null,

            // sales
            salesCurrentPeriod: [],
            salesLastPeriod: [],
            onlineSalesCurrentPeriod: [],
            onlineSalesLastPeriod: [],

            // expenses
            expensesCurrentPeriod: [],
            expensesLastPeriod: [],

            // top products
            topProductsCurrentPeriod: [],
            // Permisos de rol
            canSeeDashboard: this.$page.props.auth.user.store.activated_modules?.includes('Reportes'),

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
        ComparisonBarChart,
        Loading,
    },
    computed: {
        calculateTotalSale() {
            return this.salesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);
        },
        calculateTotalOnlineSale() {
            return this.onlineSalesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.total;
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
        calculateTotalProductsSoldOnline() {
            return this.onlineSalesCurrentPeriod?.reduce((acumulador, current) => {
                return acumulador + current.products.reduce((acc, product) => {
                    return acc + product.quantity;
                }, 0);
            }, 0);
        },
        calculateTotalProductsExpense() {
            return this.expensesCurrentPeriod?.length;
            // return this.expensesCurrentPeriod?.reduce((acumulador, current) => {
            //     return acumulador + current.quantity;
            // }, 0);
        },
        getSimpleKpisOptions() {
            return [
                {
                    title: "Ventas en tienda (ingresos)",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalSale?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: true,
                },
                {
                    title: "Unidades vendidas en tienda",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsSold?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: true,
                },
                {
                    title: "Ventas en linea (ingresos)",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalOnlineSale?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: this.$page.props.auth.user.store.activated_modules.includes('Tienda en línea'),
                },
                {
                    title: "Unidades vendidas en linea",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsSoldOnline?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: this.$page.props.auth.user.store.activated_modules.includes('Tienda en línea'),
                },
                {
                    title: "Compras (Gastos)",
                    icon: "fa-solid fa-dollar-sign",
                    value: "$" + this.calculateTotalExpense?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: true,
                },
                {
                    title: "Gastos registrados",
                    icon: "fa-solid fa-clipboard-list",
                    value: this.calculateTotalProductsExpense?.toLocaleString('en-US', { minimumFractionDigits: 2 }),
                    show: true,
                },
            ]
        },
        getKpiOptions() {
            let last = "dia anterior";
            let current = "dia seleccionado";
            if (this.period == 'Semanal') {
                current = "semana seleccionada";
                last = "semana anterior";
            } else if (this.period == 'Mensual') {
                current = "mes seleccionado";
                last = "mes anterior";
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
            let last = { name: "Día anterior", data: { sales: this.calculateHourlySales(this.salesLastPeriod), onlineSales: this.calculateHourlySales(this.onlineSalesLastPeriod, true), expenses: this.calculateHourlySales(this.expensesLastPeriod) } };
            let current = { name: "Día seleccionado", data: { sales: this.calculateHourlySales(this.salesCurrentPeriod), onlineSales: this.calculateHourlySales(this.onlineSalesCurrentPeriod, true), expenses: this.calculateHourlySales(this.expensesCurrentPeriod) } };

            if (this.period == 'Semanal') {
                timeLine = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'];
                last = { name: "Semana anterior", data: { sales: this.calculateDailySales(this.salesLastPeriod), onlineSales: this.calculateDailySales(this.onlineSalesLastPeriod, true), expenses: this.calculateDailySales(this.expensesLastPeriod) } };
                current = { name: "Semana seleccionada", data: { sales: this.calculateDailySales(this.salesCurrentPeriod), onlineSales: this.calculateDailySales(this.onlineSalesCurrentPeriod, true), expenses: this.calculateDailySales(this.expensesCurrentPeriod) } };
            } else if (this.period == 'Mensual') {
                timeLine = ['Del 1 al 7', 'Del 8 al 14', 'Del 15 al 21', 'Del 22 al 28', 'Del 29 al 31'];
                last = { name: "Mes anterior", data: { sales: this.calculateWeeklySales(this.salesLastPeriod), onlineSales: this.calculateWeeklySales(this.onlineSalesLastPeriod, true), expenses: this.calculateWeeklySales(this.expensesLastPeriod) } };
                current = { name: "Mes seleccionado", data: { sales: this.calculateWeeklySales(this.salesCurrentPeriod), onlineSales: this.calculateWeeklySales(this.onlineSalesCurrentPeriod, true), expenses: this.calculateWeeklySales(this.expensesCurrentPeriod) } };
            }

            return { timeline: timeLine, last: last, current: current };
        },
        getBarChartOptions() {
            const timeline = this.getTimeLine;
            return [
                {
                    title: 'Ingresos (ventas en tienda)',
                    colors: ['#9a9a9a', '#209209'],
                    categories: timeline.timeline,
                    series: [{
                        name: timeline.last.name,
                        data: timeline.last.data.sales,
                    },
                    {
                        name: timeline.current.name,
                        data: timeline.current.data.sales,
                    }],
                    show: true,
                },
                {
                    title: 'Ingresos (ventas en linea)',
                    colors: ['#C30303', '#F07209'],
                    categories: timeline.timeline,
                    series: [{
                        name: timeline.last.name,
                        data: timeline.last.data.onlineSales,
                    },
                    {
                        name: timeline.current.name,
                        data: timeline.current.data.onlineSales,
                    }],
                    show: this.$page.props.auth.user.store.activated_modules.includes('Tienda en línea'),
                },
                {
                    title: 'Gastos (compras)',
                    colors: ['#9a9a9a', '#C30303'],
                    categories: timeline.timeline,
                    series: [{
                        name: timeline.last.name,
                        data: timeline.last.data.expenses,
                    },
                    {
                        name: timeline.current.name,
                        data: timeline.current.data.expenses,
                    }],
                    show: true,
                },
            ]
        },
        getPieChartOptions() {
            return [
                {
                    colors: ['#C30303', '#373737', '#999999', '#5FCB1F', '#2387FC'],
                    labels: this.topProductsCurrentPeriod.map((item) => {
                        return item.product_name;
                    }),
                    series: this.topProductsCurrentPeriod.map((item) => item.total_quantity),
                },
            ]
        },
    },
    methods: {
        getCurrentDate() {
            const today = new Date();
            return format(today, 'yyyy-MM-dd');
        },
        setElementsWithNumberFormat(set) {
            return set.map(item => item.toFixed(2));
        },
        calculateHourlySales(data, isOnline = false) {
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

                if (isOnline) {
                    hourlyData[saleHour] += sale.total;
                } else {
                    hourlyData[saleHour] += sale.quantity * sale.current_price;
                }
            });
            hourlyData = this.setElementsWithNumberFormat(hourlyData);

            return hourlyData;
        },
        calculateDailySales(data, isOnline = false) {
            let dailyData = Array(7).fill(0);

            data.forEach((sale) => {
                const saleDayOfWeek = new Date(sale.created_at).getDay();

                // Incrementa el total por día de la semana correspondiente
                if (isOnline) {
                    dailyData[saleDayOfWeek] += sale.total;
                } else {
                    dailyData[saleDayOfWeek] += sale.quantity * sale.current_price;
                }
            });

            dailyData = this.setElementsWithNumberFormat(dailyData);

            return dailyData;
        },
        calculateWeeklySales(data, isOnline = false) {
            // Inicializa el array weeklyData con ceros para cada rango semanal
            let weeklyData = Array(5).fill(0);

            // Recorre las ventas y suma el total por rango semanal
            data.forEach((sale) => {
                const saleDay = new Date(sale.created_at).getDate();

                const intervals = [
                    { start: 1, end: 7 },
                    { start: 8, end: 14 },
                    { start: 15, end: 21 },
                    { start: 22, end: 28 },
                    { start: 29, end: 31 }
                ];

                intervals.forEach((interval, index) => {
                    if (saleDay >= interval.start && saleDay <= interval.end) {
                        if (isOnline) {
                            weeklyData[index] += sale.total;
                        } else {
                            weeklyData[index] += sale.quantity * sale.current_price;
                        }
                    }
                });
            });

            weeklyData = this.setElementsWithNumberFormat(weeklyData);

            return weeklyData;
        },
        calculateProfit(period) {
            let sales = this.salesLastPeriod.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);

            let onlineSales = this.onlineSalesLastPeriod.reduce((acumulador, current) => {
                return acumulador + current.total;
            }, 0);

            let expenses = this.expensesLastPeriod.reduce((acumulador, current) => {
                return acumulador + current.quantity * current.current_price;
            }, 0);

            if (period == 'current') {
                sales = this.salesCurrentPeriod.reduce((acumulador, current) => {
                    return acumulador + current.quantity * current.current_price;
                }, 0);

                onlineSales = this.onlineSalesCurrentPeriod.reduce((acumulador, current) => {
                    return acumulador + current.total;
                }, 0);

                expenses = this.expensesCurrentPeriod.reduce((acumulador, current) => {
                    return acumulador + current.quantity * current.current_price;
                }, 0);
            }

            return sales + onlineSales - expenses;
        },
        handleChangePeriodRange() {
            if (this.period == 'Semanal') {
                this.fetchWeekData();
            } else if (this.period == 'Mensual') {
                this.fetchMonthData();
            } else {
                this.fetchDailyData();
            }
        },
        handleChangePeriod() {
            if (this.period == 'Semanal') {
                this.periodRange = this.getCurrentDate();
                this.fetchWeekData();
            } else if (this.period == 'Mensual') {
                this.periodRange = this.getCurrentDate();
                this.fetchMonthData();
            } else {
                this.periodRange = this.getCurrentDate();
                this.fetchDailyData();
            }
        },
        getKPITitle() {
            if (this.period == 'Hoy') return "Ganancias de día seleccionado vs anterior";
            if (this.period == 'Semanal') return "Ganancias de semana seleccionada vs anterior";
            if (this.period == 'Mensual') return "Ganancias de mes seleccionado vs anterior";
        },
        getBarChartTitle(concept) {
            if (this.period == 'Hoy') return concept + " de día seleccionado vs anterior";
            if (this.period == 'Semanal') return concept + " de semana seleccionada vs anterior";
            if (this.period == 'Mensual') return concept + " de mes seleccionado vs anterior";
        },
        async fetchDailyData() {
            this.loading = true;
            try {
                const response = await axios.get(route('dashboard.get-day-data', { date: this.periodRange }));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.onlineSalesCurrentPeriod = response.data.online_sales;
                    this.onlineSalesLastPeriod = response.data.last_period_online_sales;
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
                const response = await axios.get(route('dashboard.get-week-data', { date: this.periodRange }));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.onlineSalesCurrentPeriod = response.data.online_sales;
                    this.onlineSalesLastPeriod = response.data.last_period_online_sales;
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
                const response = await axios.get(route('dashboard.get-month-data', { date: this.periodRange }));

                if (response.status === 200) {
                    this.salesCurrentPeriod = response.data.sales;
                    this.salesLastPeriod = response.data.last_period_sales;
                    this.onlineSalesCurrentPeriod = response.data.online_sales;
                    this.onlineSalesLastPeriod = response.data.last_period_online_sales;
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
        // redirigir a punto de venta si no tiene permiso para ver dashboard y acaba de iniciar sesion
        if (!this.canSeeDashboard) {
            this.$inertia.visit(route('sales.point'));
        }
        // redirigir a los tutoriales si no los ha finalizado
        if (!this.$page.props.auth.user.tutorials_seen) {
            this.$inertia.visit(route('tutorials.index'));
        }
        this.periodRange = this.getCurrentDate();
        this.fetchDailyData();
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>

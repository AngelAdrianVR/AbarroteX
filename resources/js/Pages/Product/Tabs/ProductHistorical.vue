<template>
    <div>
        <!-- estado de carga -->
        <Loading2 v-if="loading" class="mt-10" />
        <div v-else>
            <div class="flex items-center justify-center space-x-3">
                <button @click="loadPreviousMonth" class="bg-grayD9 size-7 rounded-full"><i
                        class="fa-solid fa-chevron-left text-[9px] text-black pb-3"></i></button>
                <p class="w-32 text-center text-sm">{{ castDate(currentMonth, currentYear) }}</p>
                <button @click="loadNextMonth" class="bg-grayD9 size-7 rounded-full"><i
                        class="fa-solid fa-chevron-right text-[9px] text-black pb-3"></i></button>
            </div>
            <div v-if="Object.keys(productHistory)?.length">
                <div v-for="(history, index) in productHistory" :key="history">
                    <h2 class="rounded-full text-sm bg-grayD9 font-bold px-3 py-1 my-4 w-36">{{
                        translateMonth(index) }}</h2>
                    <p class="mt-1 ml-4 text-sm flex items-center space-x-2" v-for="activity in history"
                        :key="activity">
                        <span v-html="getIcon(activity.type)"></span>
                        <span>{{ activity.description + ' • ' + activity.created_at }}</span>
                    </p>
                </div>
            </div>
            <p v-else class="text-xs text-gray-500 mt-5 text-center">No hay actividad en esta fecha</p>
        </div>
    </div>
</template>

<script>
import Loading2 from '@/Components/MyComponents/Loading2.vue';

export default {
    name: 'ProductHistorical',
    data() {
        return {
            productHistory: [],
            currentMonth: new Date().getMonth() + 1, // El mes actual
            currentYear: new Date().getFullYear(), // El año actual
            // carga
            loading: false,
        };
    },
    components: {
        Loading2,
    },
    props: {
        product: Object,
    },
    computed: {

    },
    methods: {
        getIcon(type) {
            if (type === 'Precio') {
                return '<i class="fa-solid fa-dollar-sign"></i>';
            } else if (type === 'Entrada') {
                return '<i class="fa-regular fa-square-plus"></i>';
            } else if (type === 'Venta') {
                return '<i class="fa-solid fa-hand-holding-dollar"></i>';
            } else if (type === 'Cancelación') {
                return '<i class="fa-solid fa-hand-holding-dollar"></i>';
            } else if (type === 'Reembolso') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>';
            } else if (type === 'Cancelación') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
            } else if (type === 'Edición') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>';
            }
        },
        castDate(month, year) {
            const monthsCast = {
                1: 'Enero',
                2: 'Febrero',
                3: 'Marzo',
                4: 'Abril',
                5: 'Mayo',
                6: 'Junio',
                7: 'Julio',
                8: 'Agosto',
                9: 'Septiembre',
                10: 'Octubre',
                11: 'Noviembre',
                12: 'Diciembre',
            };

            const translatedMonth = monthsCast[month] || month;

            return `${translatedMonth} ${year}`;
        },
        translateMonth(dateString) {
            const [month, year] = dateString.split(' ');

            const monthsTranslation = {
                'January': 'Enero',
                'February': 'Febrero',
                'March': 'Marzo',
                'April': 'Abril',
                'May': 'Mayo',
                'June': 'Junio',
                'July': 'Julio',
                'August': 'Agosto',
                'September': 'Septiembre',
                'October': 'Octubre',
                'November': 'Noviembre',
                'December': 'Diciembre',
            };

            const translatedMonth = monthsTranslation[month] || month;

            return `${translatedMonth} ${year}`;
        },
        async loadPreviousMonth() {
            if (this.currentMonth === 1) {
                this.currentMonth = 12;
                this.currentYear -= 1;
            } else {
                this.currentMonth -= 1;
            }
            await this.fetchHistory();
        },
        async loadNextMonth() {
            if (this.currentMonth === 12) {
                this.currentMonth = 1;
                this.currentYear += 1;
            } else {
                this.currentMonth += 1;
            }
            await this.fetchHistory();
        },
        async fetchHistory() {
            this.loading = true;
            try {
                const response = await axios.get(route("products.fetch-history", {
                    product_id: this.product.id,
                    month: this.currentMonth,
                    year: this.currentYear,
                }));
                if (response.status === 200) {
                    this.productHistory = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchHistory();
    }
};
</script>

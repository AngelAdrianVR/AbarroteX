<template>
    <div>
        <!-- estado de carga -->
        <SmallLoading v-if="loading" class="mt-10 mx-auto" />
        <div v-else>
            <div class="flex items-center justify-center space-x-3">
                <button @click="loadPreviousMonth" class="bg-grayD9 size-7 rounded-full"><i
                        class="fa-solid fa-chevron-left text-[9px] text-black pb-3"></i></button>
                <p class="w-32 text-center text-sm">{{ castDate(currentMonth, currentYear) }}</p>
                <button @click="loadNextMonth" class="bg-grayD9 size-7 rounded-full"><i
                        class="fa-solid fa-chevron-right text-[9px] text-black pb-3"></i></button>
            </div>
            <div v-if="Object.keys(productHistory)?.length">
                <div v-for="(history, index) in productHistory" :key="history" class="space-y-4">
                    <h2 class="rounded-full text-sm bg-grayD9 font-bold px-3 py-1 my-4 w-36">{{
                        translateMonth(index) }}</h2>
                    <div class="mt-1 ml-4" v-for="activity in history" :key="activity">
                        <p class="flex space-x-2 text-sm">
                            <span v-html="getIcon(activity.type)"></span>
                            <span>{{ activity.description }}</span>
                        </p>
                        <p class="text-xs text-gray99 flex ml-4">
                            <span>{{ activity.created_at }}</span>
                            <span v-if="activity.user">{{ ' • ' + activity.user?.name }}</span>
                        </p>
                    </div>
                </div>
            </div>
            <p v-else class="text-xs text-gray-500 mt-5 text-center">No hay actividad en esta fecha</p>
        </div>
    </div>
</template>

<script>
import SmallLoading from '@/Components/MyComponents/SmallLoading.vue';

export default {
    name: 'GlobalProductHistorical',
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
        SmallLoading,
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
            } else if (type === 'Salida') {
                return '<i class="fa-regular fa-square-minus"></i>';
            } else if (type === 'Ajuste') {
                return '<i class="fa-solid fa-sliders"></i>';
            } else if (type === 'Venta') {
                return '<i class="fa-solid fa-hand-holding-dollar"></i>';
            } else if (type === 'Reembolso') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>';
            } else if (type === 'Cancelación') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
            } else if (type === 'Edición') {
                return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>';
            } else if (type === 'Promoción') {
                return '<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.28963 0C6.61877 2.72132 7.62955 4.40871 8.10018 8.09863C8.68001 7.68802 8.88776 7.19533 8.93416 5.7168C11.7333 11.4332 8.45841 15.0059 5.54061 15.1846C5.18333 15.2064 4.52925 15.2601 4.1119 15.125C-0.115441 13.7554 -1.60456 10.0636 2.14607 5.24023C4.5787 2.25072 4.74355 1.28427 4.28963 0ZM4.82479 7.44531C2.7427 10.1811 2.08598 12.5064 5.0035 14.0547C6.92256 13.584 7.62367 12.4473 7.80232 10.4824C7.42197 11.0129 7.17027 11.2542 6.49178 11.375C6.67028 9.76748 6.13696 8.64466 4.82479 7.44531Z" fill="currentColor"/></svg>';
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
                const response = await axios.get(route("global-product-store.fetch-history", {
                    global_product_store_id: this.product.id,
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

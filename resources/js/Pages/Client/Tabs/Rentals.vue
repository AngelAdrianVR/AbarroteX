<template>
    <Loading v-if="loading" class="mt-10" />
    <section v-else-if="rentals.length" class="overflow-auto">
        <table class="table-fixed w-full">
            <thead>
                <tr class="*:text-start *:pb-2 *:px-2 *:text-sm">
                    <th class="w-28">Folio</th>
                    <th class="w-48">Producto rentado</th>
                    <th class="w-40">Costo</th>
                    <th class="w-40">Días transcurridos</th>
                    <th class="w-40">Fecha de devolución</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item) in rentals" :key="item.id" class="*:text-xs *:py-2 *:px-2">
                    <td>
                        <button @click="handleShow(encodeId(item.id))" type="button" class="text-primary underline">
                            R-{{ item.folio }}
                        </button>
                    </td>
                    <td>{{ item.product.name }}</td>
                    <td>${{ item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} / {{ item.period.name }}</td>
                    <td>{{ getCalculateDaysElapsed(item) }}</td>
                    <td>
                        <el-tooltip placement="right">
                            <template #content>
                                <p v-html="getTooltipContent(item)" class="text-center"></p>
                            </template>
                            <span v-html="getReturnDate(item)"></span>
                        </el-tooltip>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
    <el-empty v-else description="No hay rentas para mostrar" />
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';
import { format, parseISO, differenceInDays } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'Rentals',
    data() {
        return {
            rentals: [],
            // carga
            loading: false,
        }
    },
    components: {
        Loading,
    },
    props: {
        clientId: Number
    },
    computed: {
    },
    methods: {
        getCalculateDaysElapsed(rent) {
            if (rent.status == 'En uso') {
                return this.calculateDaysElapsed(rent.rented_at) + ' hasta la fecha';
            } else if (rent.status == 'Completado') {
                return this.calculateDaysElapsed(rent.rented_at, rent.completed_at) + ' hasta que se completó';
            } else if (rent.status == 'Cancelado') {
                return this.calculateDaysElapsed(rent.rented_at, rent.cancelled_at) + ' hasta que se canceló';
            }
        },
        calculateDaysElapsed(startDate, endDate = '') {
            const past = new Date(startDate);
            const end = endDate ? new Date(endDate) : new Date();
            return differenceInDays(end, past);
        },
        getReturnDate(rent) {
            if (rent.status == 'En uso') {
                return '<i class="fa-solid fa-rotate text-xs text-[#0355B5] mr-2"></i>-';
            } else if (rent.status == 'Completado') {
                return '<i class="fa-solid fa-check text-xs text-[#06B918] mr-2"></i>' + this.formatDate(rent.completed_at);
            } else if (rent.status == 'Cancelado') {
                return '<i class="fa-solid fa-xmark text-xs text-[#D70808] mr-2"></i>' + this.formatDate(rent.cancelled_at);
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMM, yyyy', { locale: es });
        },
        async fetchRentals() {
            this.loading = true;
            try {
                const response = await axios.get(route('clients.get-client-rentals', this.clientId));

                if (response.status === 200) {
                    this.rentals = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    async mounted() {
        await this.fetchRentals();
    }
}
</script>

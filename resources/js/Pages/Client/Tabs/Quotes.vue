<template>
    <Loading v-if="loading" class="mt-10" />
    <section v-else-if="quotes.length">
        <table class="table-fixed">
            <thead>
                <tr class="*:text-start *:pb-2 *:px-2 *:text-sm">
                    <th class="w-32">Folio</th>
                    <th class="w-48">Fecha de creación</th>
                    <th class="w-40">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item) in quotes" :key="item.id" class="*:text-xs *:py-2 *:px-2">
                    <td>
                        <el-tooltip :content="item.status" placement="right">
                            <i class="mr-2" :class="getStatusIcont(item.status)"></i>
                        </el-tooltip>
                        <button @click="handleShow(encodeId(item.id))" type="button" class="text-primary underline">
                            C-{{ item.folio }}
                        </button>
                    </td>
                    <td>{{ formatDate(item.created_at) }}</td>
                    <td>
                        <div v-if="item.has_discount">
                            <p v-if="item.is_percentage_discount">${{ (item.total - (item.discount * item.total *
                                0.01))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                            <p v-else>${{ (item.total - item.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                ",") ?? '-' }}</p>
                        </div>
                        <p v-else>${{ item.total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
    <el-empty v-else description="No hay cotizaciones para mostrar" />
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'Quotes',
    data() {
        return {
            quotes: [],
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
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        handleShow(encodedId) {
            window.open(route('quotes.show', encodedId, '_blank'));
        },
        getStatusIcont(status) {
            if (status === 'Esperando respuesta') {
                return 'fa-regular fa-clock text-amber-500';
            } else if (status === 'Rechazada') {
                return 'fa-solid fa-x text-red-500';
            } else if (status === 'Autorizada') {
                return 'fa-solid fa-check text-blue-500';
            } else if (status === 'Pagado') {
                return 'fa-solid fa-dollar-sign text-green-500';
            } else if (status === 'Pago parcial') {
                return 'fa-solid fa-circle-dollar-to-slot text-indigo-500';
            }
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMM, yyyy', { locale: es });
        },
        async fetchQuotes() {
            this.loading = true;
            try {
                const response = await axios.get(route('clients.get-client-quotes', this.clientId));

                if (response.status === 200) {
                    this.quotes = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    async mounted() {
        await this.fetchQuotes();
    }
}
</script>

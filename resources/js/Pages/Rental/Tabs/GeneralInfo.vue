<template>
    <section class="grid grid-cols-6 gap-x-3 gap-y-2">
        <span class="text-gray37">Folio:</span>
        <span class="col-span-5">{{ 'R-' + rent.id }}</span>
        <span class="text-gray37">Cliente:</span>
        <span class="col-span-5">{{ rent.client.name }}</span>
        <span class="text-gray37">Producto rentado:</span>
        <span @click="viewProduct(rent.product)" class="col-span-5 text-primary underline cursor-pointer">{{
            rent.product.name }}</span>
        <span class="text-gray37">Fecha de entrega a cliente:</span>
        <span class="col-span-5">{{ formatDate(rent.rented_at) }}</span>
        <span class="text-gray37">Fecha de devolución:</span>
        <p class="col-span-5">
            <span>{{ rent.completed_at ? formatDate(rent.completed_at) : 'Sigue con el cliente' }}</span>
            <span class="text-gray99 ml-3">(Días transcurridos: {{ getCalculateDaysElapsed(rent).days }})</span>
        </p>
        <span class="text-gray37">Estado:</span>
        <p class="col-span-5 relative">
            <i v-if="rent.status == 'Completado'"
                class="absolute -left-6 top-1 fa-solid fa-check text-xs text-[#06B918]"></i>
            <i v-if="rent.status == 'En uso'"
                class="absolute -left-6 top-1 fa-solid fa-rotate text-xs text-[#09EE05]"></i>
            <i v-if="rent.status == 'Cancelado'"
                class="absolute -left-6 top-1 fa-solid fa-xmark text-xs text-[#D70808]"></i>
            <span>{{ rent.status }}</span>
        </p>
        <span class="text-gray37">Costo:</span>
        <span class="col-span-5">
            ${{ rent.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} / {{ rent.period.name }}
        </span>
        <span class="text-gray37">Monto total:</span>
        <span class="col-span-5 font-bold">
            ${{ (rent.cost * getCalculateDaysElapsed(rent).periods).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
        </span>
        <span class="text-gray37">Comentarios:</span>
        <p class="col-span-5" style="white-space: pre-line;">
            {{ rent.notes ?? '-' }}
        </p>
    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductTable from '@/Components/MyComponents/Product/ProductTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { format, parseISO, differenceInDays } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {

        };
    },
    components: {
        PrimaryButton,
        CancelButton,
        ProductTable,
        ThirthButton,
        InputError,
        InputLabel,
        Loading,
        Modal,
        DialogModal,
        FileUploader,
    },
    props: {
        rent: Object,
    },
    methods: {
        viewProduct(product) {
            const encodedId = btoa(product.id.toString());
            window.open(route('products.show', encodedId), '_blank');
        },
        formatDate(date) {
            return format(parseISO(date), 'dd MMMM yyyy', { locale: es });
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        getCalculateDaysElapsed(rent) {
            let days = 0;
            if (rent.status == 'En uso') {
                days = this.calculateDaysElapsed(rent.rented_at);
            } else if (rent.status == 'Completado') {
                days = this.calculateDaysElapsed(rent.rented_at, rent.completed_at);
            } else if (rent.status == 'Cancelado') {
                days = this.calculateDaysElapsed(rent.rented_at, rent.cancelled_at);
            }
            return { days, periods: Math.ceil(days / rent.period.days) };
        },
        calculateDaysElapsed(startDate, endDate = '') {
            const past = new Date(startDate);
            const end = endDate ? new Date(endDate) : new Date();
            return differenceInDays(end, past);
        },
    },
    mounted() {
    }
}
</script>
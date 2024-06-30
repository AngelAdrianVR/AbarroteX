<template>
    <div class="border border-grayD9 rounded-[5px] px-3 py-2">
        <header class="flex items-center justify-between border-b border-grayD9 pb-2">
            <h1 class="font-bold text-base">Pago {{ index + 1 }}</h1>
            <div class="flex items-center space-x-1">
                <button @click="$inertia.get(route('rental-payments.edit', encodeId(payment.id)))" type="button"
                    class="flex items-center justify-center size-6 rounded-full bg-grayF2 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </button>
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#232323"
                    title="¿Deseas continuar?" @confirm="deleteItem()">
                    <template #reference>
                        <button type="button"
                            class="flex items-center justify-center size-6 rounded-full bg-grayF2 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button> </template>
                </el-popconfirm>
            </div>
        </header>
        <main class="mt-2 text-sm grid grid-cols-2 gap-2">
            <span class="text-gray37">Fecha de pago:</span>
            <span>{{ formatDate(payment.paid_date) }}</span>
            <span class="text-gray37">Monto pagado:</span>
            <span>${{ payment.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray37">Concepto de pago:</span>
            <span>{{ payment.concept ?? '-' }}</span>
            <span class="text-gray37">Comentarios:</span>
            <span>{{ payment.notes ?? '-' }}</span>
        </main>
    </div>
</template>

<script>
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    name: 'RentalPaymentCard',
    data() {
        return {

        }
    },
    components: {
    },
    props: {
        index: Number,
        payment: Object
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        deleteItem() {
            this.$inertia.delete(route('rental-payments.destroy', this.payment.id));
        },
    },
}
</script>
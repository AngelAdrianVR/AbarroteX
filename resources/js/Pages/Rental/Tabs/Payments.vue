<template>
    <section>
        <header class="mt-3">
            <p class="text-gray37">
                Monto hasta el momento
                <span class="text-black ml-3">${{ getTotalPaid().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
        </header>
        <main class="mt-5 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
            <RentalPaymentCard v-for="(item, index) in payments" :key="item.id" :payment="item" :index="index" />
        </main>
        <el-empty v-if="!payments.length" description="No hay pagos registrados" />
    </section>
</template>

<script>
import RentalPaymentCard from '@/Components/MyComponents/Rental/RentalPaymentCard.vue';

export default {
    data() {
        return {
        };
    },
    components: {
        RentalPaymentCard,
    },
    props: {
        payments: Array,
    },
    methods: {
        getTotalPaid() {
            return this.payments.reduce((accum, item) => {
                return accum += item.amount;
            }, 0);
        },
    },
    mounted() {

    }
}
</script>
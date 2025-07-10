<template>
    <div v-if="sales.length" class="text-sm mt-5 space-y-4">
        <QuoteSaleDetails v-for="(item, index) in sales" :key="index" :groupedSales="item"
                    @show-modal="showModal" :isOutOfCashCut="false" />
    </div>
    <el-empty v-else description="No hay ventas para mostrar" />
</template>

<script>
import QuoteSaleDetails from "@/Components/MyComponents/Sale/QuoteSaleDetails.vue";

export default {
    name: 'QuoteSales',
    data() {
        return {

        };
    },
    emits: ['show-modal'],
    components: {
        QuoteSaleDetails,
    },
    props: {
        sales: Array,
    },
    computed: {

    },
    methods: {
        showModal(modal, saleFolio) {
            this.$emit('show-modal', modal, saleFolio, 'quote');
        },
    },
    mounted() {

    }
};
</script>
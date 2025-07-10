<template>
    <div v-if="sales.length" class="text-sm mt-5 space-y-4">
        <SaleDetails v-for="(item, index) in sales" :key="index" :groupedSales="item"
                    @show-modal="showModal" :isOutOfCashCut="false" />
    </div>
    <el-empty v-else description="No hay ventas para mostrar" />
</template>

<script>
import SaleDetails from "@/Components/MyComponents/Sale/SaleDetails.vue";

export default {
    name: 'StoreSales',
    data() {
        return {


        };
    },
    emits: ['show-modal'],
    components: {
        SaleDetails,
    },
    props: {
        sales: Array,
    },
    computed: {

    },
    methods: {
        showModal(modal, saleFolio) {
            this.$emit('show-modal', modal, saleFolio);
        },
    },
    mounted() {

    }
};
</script>
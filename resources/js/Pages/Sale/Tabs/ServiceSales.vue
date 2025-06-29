<template>
    <div v-if="sales.length" class="text-sm mt-5 space-y-4">
        <ServiceSaleDetails v-for="(item, index) in sales" :key="index" :groupedSales="item"
                    @show-modal="showModal" :isOutOfCashCut="false" />
    </div>
    <el-empty v-else description="No hay ventas para mostrar" />
</template>

<script>
import ServiceSaleDetails from "@/Components/MyComponents/Sale/ServiceSaleDetails.vue";

export default {
    name: 'ServiceSales',
    data() {
        return {

        };
    },
    emits: ['show-modal'],
    components: {
        ServiceSaleDetails,
    },
    props: {
        sales: Array,
    },
    computed: {

    },
    methods: {
        showModal(modal, saleFolio) {
            this.$emit('show-modal', modal, saleFolio, 'service');
        },
    },
    mounted() {

    }
};
</script>
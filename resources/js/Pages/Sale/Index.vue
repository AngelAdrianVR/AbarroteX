<template>
    <AppLayout title="Ventas">
        <div class="px-2 lg:px-10 py-7">
            <el-tabs class="mx-3" v-model="activeTab">
                <el-tab-pane v-for="(item, index) in cash_registers" :key="item" :label="item.name" :name="index">
                        <RegisteredSalesTable :cashRegister="item" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import RegisteredSalesTable from '@/Components/MyComponents/Sale/RegisteredSalesTable.vue';  

export default {
    data() {
        return {
            activeTab: 0,
        }
    },
    components: {
        AppLayout,
        RegisteredSalesTable   
    },
    props: {
        cash_registers: Array,
    },
    methods: {}
}
</script>

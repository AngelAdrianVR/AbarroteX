<template>
    <AppLayout title="Caja">
        <section class="mt-5 mx-2 lg:mx-8">
            <el-tabs class="mx-3" v-model="activeTab">
                <el-tab-pane label="Caja principal" name="1">
                    <CashRegister />
                </el-tab-pane>
                <el-tab-pane label="Historial" name="2">
                    <CashCutsTable :items="cash_cuts" />
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CashCutsTable from '@/Pages/CashCut/Tabs/CashCutsTable.vue';
import CashRegister from '@/Pages/CashCut/Tabs/CashRegister.vue';

export default {
    data() {
        return {
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        CashCutsTable,
        CashRegister,
        PrimaryButton,
    },
    props: {
        cash_cuts: Array
    },
    methods: {

    }
}
</script>
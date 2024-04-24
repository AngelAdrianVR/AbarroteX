<template>
    <AppLayout title="Caja">
        <section class="mt-16 mx-auto xl:w-2/3">
            <el-tabs class="mx-3" v-model="activeTab">
                <el-tab-pane label="Caja principal" name="1">
                    <MainCashRegister />
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
import MainCashRegister from '@/Pages/CashCut/Tabs/MainCashRegister.vue';

export default {
data() {
    return {
        activeTab: '1',
    }
},
components:{
AppLayout,
CashCutsTable,
MainCashRegister,
PrimaryButton, 
},
props:{
cash_cuts: Array
},
methods:{

}
}
</script>
<template>
    <AppLayout title="Caja">
        <section class="mt-16 mx-auto xl:w-2/3">
            <el-tabs class="mx-3" v-model="activeTab">
                <el-tab-pane label="Caja principal" name="1">
                    <!-- <GeneralUserInfo :user="user" /> -->
                </el-tab-pane>
                <el-tab-pane label="Historial" name="2">
                    <!-- <CashRegisterMovements :movements="cash_register_movements" /> -->
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CashRegisterMovements from '@/Pages/CashRegister/Tabs/CashRegisterMovements.vue';

export default {
data() {
    return {
        activeTab: '1',
    }
},
components:{
AppLayout,
CashRegisterMovements,
PrimaryButton, 
},
props:{
cash_register_movements: Array
},
methods:{

}
}
</script>
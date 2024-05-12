<template>
    <AppLayout title="Caja">
        <section class="mt-5 mx-2 lg:mx-8">
        <div class="text-right">
            <ThirthButton @click="$inertia.get(route('cash-registers.create'))">Crear caja</ThirthButton>
        </div>
            <el-tabs class="mx-3" v-model="activeTab">
                <el-tab-pane v-for="(item, index) in cash_registers" :key="item" :label="item.name" :name="index">
                    <CashRegister :cash_register="item" />
                </el-tab-pane>
                <!-- cambiar name al historial de cortes al numero dinamico correspondiente -->
                <el-tab-pane label="Historial de cortes" name="2">
                    <CashCutsTable :items="cash_cuts" />
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CashCutsTable from '@/Pages/CashRegister/Tabs/CashCutsTable.vue';
import CashRegister from '@/Pages/CashRegister/Tabs/CashRegister.vue';

export default {
    data() {
        return {
            activeTab: 0,
        }
    },
    components: {
        AppLayout,
        CashCutsTable,
        PrimaryButton,
        ThirthButton,
        CashRegister
    },
    props: {
        cash_registers: Array,
        cash_cuts: Array,
    },
    methods: {

    }
}
</script>
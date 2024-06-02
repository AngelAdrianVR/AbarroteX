<template>
    <AppLayout title="Ventas">
        <div class="px-2 lg:px-10 py-7">
            <!-- <el-tabs class="mx-3" v-model="activeTab" @tab-click="updateURL">
                <el-tab-pane v-for="(item, index) in cash_registers" :key="item" :label="item.name" :name="String(index + 1)"> -->
                        <RegisteredSalesTable :cashRegister="cash_registers[0]" />
                <!-- </el-tab-pane>
            </el-tabs> -->
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import RegisteredSalesTable from '@/Components/MyComponents/Sale/RegisteredSalesTable.vue';  

export default {
    data() {
        return {
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        RegisteredSalesTable   
    },
    props: {
        cash_registers: Array,
    },
    methods: {
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name );
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        }
    },
    mounted() {
        this.setActiveTabFromURL();
    }
}
</script>

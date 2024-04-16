<template>
    <AppLayout title="Configuraciones">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="font-bold text-lg">Configuraciones</h1>
            </div>

            <!-- tabs options -->
            <el-tabs v-model="activeTab">
                <el-tab-pane label="Punto de venta" name="1">
                    <Point />
                </el-tab-pane>
                <!-- <el-tab-pane label="Cuentas" name="2">
                    cuentas
                </el-tab-pane>
                <el-tab-pane label="Ventas registradas" name="3">
                    Ventas registradas
                </el-tab-pane>
                <el-tab-pane label="Productos" name="4">
                    Productos
                </el-tab-pane>
                <el-tab-pane label="Notificaciones" name="5">
                    Notificaciones
                </el-tab-pane> -->
            </el-tabs>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Point from '@/Pages/Setting/Tabs/Point.vue';

export default {
data() {
    return {
        activeTab: '1',
    }
},
components:{
AppLayout,
PrimaryButton,
Point
},
props:{

},
methods:{

}
}
</script>
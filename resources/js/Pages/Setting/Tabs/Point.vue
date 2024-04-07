<template>
    <Loading v-if="loading" class="my-12" />
    <div v-else class="text-sm">
        <p>Puedes activar la configuración para mostrar la acción en el punto de venta, y si no lo necesitas, también
            tienes la opción de desactivarla</p>

        <section class="mt-5 *:flex *:items-center">
            <div v-for="(item, index) in settings" :key="item.id">
                <p class="w-[15%]">{{ item.key }}</p>
                <el-switch v-model="value1" :loading="settingLoading" size="small" :before-change="beforeChange1" class="w-[6%]" />
                <p class="text-gray99 text-[11px]">{{ item.description }}</p>
            </div>
        </section>
    </div>
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
    data() {
        return {
            loading: false,
            settingLoading: false,
            settings: [],
            value1: false,
        }
    },
    components: {
        Loading,
    },
    methods: {
        beforeChange1() {
            this.settingLoading = true
            return new Promise((resolve) => {
                setTimeout(() => {
                    this.settingLoading = false
                    return resolve(true)
                }, 1000)
            });
        },
        async fetchSettings() {
            try {
                this.loading = true;
                const response = await axios.get(route('settings.get-by-module', 'Punto de venta'));

                if (response.status === 200) {
                    this.settings = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchSettings();
    }
}
</script>
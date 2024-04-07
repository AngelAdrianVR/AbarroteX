<template>
    <Loading v-if="loading" class="my-12" />
    <div v-else class="text-sm">
        <p>Puedes activar la configuración para mostrar la acción en el punto de venta, y si no lo necesitas, también
            tienes la opción de desactivarla</p>

        <section class="mt-5 *:flex *:items-center">
            <div v-for="(item, index) in settings" :key="item.id">
                <p class="w-[15%]">{{ item.key }}</p>
                <el-switch @change="updateSettingValue(index)" v-model="values[index]" :loading="settingLoading[index]" size="small"
                    class="w-[6%]" />
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
            settingLoading: [],
            settings: [],
            values: [],
        }
    },
    components: {
        Loading,
    },
    methods: {
        // beforeSettingChange(index) {
        //     // return this.updateSettingValue(index);
        // },
        async updateSettingValue(index) {
            try {
                this.settingLoading[index] = true
                const response = await axios.put(route('stores.toggle-setting-value', {
                    store: this.$page.props.auth.user.store_id,
                    setting_id: this.settings[index].id
                }), {value: this.values[index]});

                if (response.status === 200) {
                    // por lo pronto no se requiere hacer nada
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.settingLoading[index] = false;
                return true;
            }
        },
        async fetchModuleSettings() {
            try {
                this.loading = true;
                const response = await axios.get(route('stores.get-settings-by-module', {
                    store: this.$page.props.auth.user.store_id, module: 'Punto de venta'
                }));

                if (response.status === 200) {
                    this.settings = response.data.items;
                    this.settingLoading = new Array(response.data.items.length).fill(false);
                    this.values = response.data.items.map(item => {
                        return item.type == 'Bool'
                            ? Boolean(item.pivot.value)
                            : item.pivot.value;
                    });
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchModuleSettings();
    }
}
</script>
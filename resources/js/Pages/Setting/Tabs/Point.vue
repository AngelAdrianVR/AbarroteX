<template>
    <Loading v-if="loading" class="my-12" />
    <div v-else class="text-xs md:text-sm">
        <p>
            En esta sección, puedes personalizar diferentes aspectos de la aplicación activando o desactivando diversas
            configuraciones. Cada configuración tiene un nombre y una breve descripción que explica su
            efecto en la aplicación.
        </p>

        <section class="mt-5 *:flex *:items-start">
            <div v-for="(item, index) in settings" :key="item.id" class="border border-[#D9D9D9] rounded-sm p-4 flex items-center justify-between space-x-2">
                <figure class="w-20 h-auto flex justify-center items-center">
                    <!-- Busca la imagen que coincida con el key de la configracion -->
                    <img class="object-contain" :src="configImages.find(img => img.key === item.key)?.path" alt="">
                </figure>
                <div class="w-full">
                    <p>{{ item.key }}</p>
                    <p v-html="item.description" class="text-[#575757] text-[9px] md:text-[13px] w-[79%]"></p>
                    <p v-if="item.key === 'Impresión automática de tickets'" class="text-[#575757]">
                        Para configurar tu impresora, ve a la pestaña de Impresora o da clic 
                        <Link href="/settings?tab=3" class="text-primary underline">aquí.</Link>
                    </p>
                </div>
                <el-switch @change="updateSettingValue(index)" v-model="values[index]" :loading="settingLoading[index]"
                    size="small" class="w-[12%] md:w-[6%] justify-center" />
            </div>
        </section>
    </div>
</template>

<script>
import Loading from '@/Components/MyComponents/Loading.vue';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
//images
import scanner from '@/../../public/images/config_scanner.png';
import discount from '@/../../public/images/config_discount.png';
import inventory from '@/../../public/images/config_inventory.png';
import currentMoney from '@/../../public/images/config_currentMoney.png';
import max_money from '@/../../public/images/config_max_money.png';
import noCodeProduct from '@/../../public/images/config_noCodeProduct.png';
import email from '@/../../public/images/config_email.png';
import printer from '@/../../public/images/config_printer.png';

export default {
    data() {
        return {
            loading: false,
            settingLoading: [],
            settings: [],
            values: [], 

            // config images
            configImages:[
                {
                    path: scanner,
                    key:'Escanear productos'
                },

                {
                    path: discount,
                    key: 'Hacer descuentos'
                },
                {
                    path: inventory,
                    key: 'Control de inventario'
                },
                {
                    path: currentMoney,
                    key: 'Mostrar dinero en caja'
                },
                {
                    path: max_money,
                    key: 'Aviso de monto máximo en caja'
                },
                {
                    path: noCodeProduct,
                    key: 'Selección rápida de productos sin código'
                },
                {
                    path: email,
                    key: 'Notificaciones por correo'
                },
                {
                    path: printer,
                    key: 'Impresión automática de tickets'
                },
            ]


        }
    },
    components: {
        Loading,
        Link
    },
    methods: {
        async updateSettingValue(index) {
            try {
                this.settingLoading[index] = true
                const response = await axios.put(route('stores.toggle-setting-value', {
                    store: this.$page.props.auth.user.store_id,
                    setting_id: this.settings[index].id
                }), { value: this.values[index] });

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
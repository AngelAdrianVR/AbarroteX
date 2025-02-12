<template>

    <Head title="Ezy Ventas tutorial" />
    <div>
        <!-- Videos del tutorial  -->
        <Modal :maxWidth="'2xl'" :show="tutorialModal">
            <div class="py-7 px-7 relative">
                <!-- <button v-if="step !== 1" @click="prevStep" class="hover:bg-grayD9 cursor-pointer rounded-full size-5 text-xs"><i class="fa-solid fa-chevron-left"></i></button> -->
                <figure class="mx-auto w-32">
                    <img class="" src="@/../../public/images/black_logo.png" alt="logo">
                </figure>
                <h1 class="font-bold text-2xl text-center mb-2">Bienvenido(a) {{ $page.props.auth.user.store.name }}
                </h1>
                <p class="mb-5 text-center">Completa el siguiente tutorial para poder comenzar a utilizar Ezy Ventas</p>

                <!-- Barra de progreso -->
                <div class="grid grid-cols-2 gap-x-3 mb-4 mx-12">
                    <template v-for="index in 2" :key="index">
                        <button @click="step = index"
                            :class="{ 'border-2 border-primary w-full': index <= step, 'border-2 w-full': index > step }"></button>
                    </template>
                </div>

                <p class="font-bold mb-2 text-center" v-text="tutorialContent[step - 1].title"></p>

                <section class="flex justify-center h-72 *:h-full *:rounded-[5px]">
                    <video v-if="step === 1" controls>
                        <source src="@/../../public/Videos/Tutorial_point_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>

                    <!-- <video v-if="step === 2" controls>
                        <source src="@/../../public/Videos/Tutorial_productos_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video> -->

                    <video v-if="step === 2" controls>
                        <source src="@/../../public/Videos/Tutorial_graficas_EzyV.mp4" type="video/mp4">
                        Tu navegador no soporta la reproducción de video.
                    </video>
                </section>
                <div class="flex justify-end items-center space-x-5 pt-2 pb-1 py-3 mt-9">
                    <button type="button" v-if="step !== 1" @click="prevStep" class="text-primary text-sm">
                        <i class="fa-solid fa-chevron-left mr-2"></i>
                        Regresar
                    </button>
                    <PrimaryButton @click="nextStep">
                        Continuar
                        <i class="fa-solid fa-chevron-right ml-2"></i>
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Configuraciones de la tienda -->
        <Modal :maxWidth="'2xl'" :show="configModal">
            <div class="py-8 px-7 relative">
                <button @click="tutorialModal = true; configModal = false"
                    class="hover:bg-grayD9 cursor-pointer rounded-full size-5 text-xs"><i
                        class="fa-solid fa-chevron-left"></i></button>
                <figure class="mx-auto w-32">
                    <img class="" src="@/../../public/images/black_logo.png" alt="logo">
                </figure>
                <h1 class="font-bold text-2xl text-center mb-5">Bienvenido(a) {{ $page.props.auth.user.store.name }}
                </h1>

                <p class="font-bold">Configura tu tienda.</p>
                <p class="text-sm">La configuración inicial te permite personalizar tu experiencia de venta según tus
                    necesidades. Puedes omitir este paso y ajustarlo más tarde en el módulo de configuraciones</p>

                <section class="mt-5 overflow-auto h-72">
                    <div v-for="(item, index) in settings" :key="item.id" class="mb-3 mx-2">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">{{ item.key }}</p>
                            <el-switch @change="updateSettingValue(index)" inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado " inactive-text=" Deshabilitado " v-model="values[index]"
                                :loading="settingLoading[index]" size="small" class="ml-2" />
                        </div>
                        <p class="text-gray99 text-sm w-5/6 text-justify">{{ item.description }}</p>
                    </div>
                </section>

                <div class="flex justify-end space-x-5 pt-2 pb-1 py-3 mt-9">
                    <button class="text-primary" @click="configModal = false; finishModal = true">Omitir</button>
                    <PrimaryButton @click="configModal = false; finishModal = true">
                        Continuar
                        <i class="fa-solid fa-chevron-right ml-2"></i>
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- modal de finalización -->
        <Modal :show="finishModal">
            <div class="py-4 px-7 relative">
                <h1 class="font-bold text-center mb-5">¡Haz terminado la configuración tu tienda!</h1>
                <p class="text-sm text-center">Te invitamos a seguir explorando sobre las diversas funcionalidades y
                    herramientas del sistema.</p>

                <div class="my-7">
                    <svg class="mx-auto" width="63" height="58" viewBox="0 0 43 38" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M23.6278 8.2372C20.6701 14.8922 18.7593 17.6716 14 20.541L14.3106 34.4226H15.5529C16.2798 35.2468 16.8557 35.6125 18.348 36H36.6718C39.467 35.369 39.1564 31.5833 37.2929 30.9523C40.0881 30.9523 40.7092 26.851 38.2247 26.2201C41.3304 25.9046 41.3304 21.8033 38.8458 21.1724C42.5727 21.4878 43.5044 16.1246 38.8458 16.1246H27.6652C29.0744 8.34365 29.5091 4.57308 27.6652 1.92757C25.7806 0.590255 24.856 0.795623 23.3172 1.92757C23.2921 4.34376 23.3653 5.72468 23.6278 8.2372Z"
                            stroke="#F68C0F" />
                        <path d="M12 20H1V37H12V20Z" stroke="#F68C0F" />
                    </svg>
                </div>

                <div class="text-center">
                    <PrimaryButton class="!px-12" @click="$inertia.put(route('users.tutorials-completed'))">¡Comenzar
                        ahora!
                    </PrimaryButton>
                </div>

                <p class="text-xs text-center mt-8">¡Descubre lo que Ezy Ventas puede hacer por ti y tu negocio!</p>
            </div>
        </Modal>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from "@/Components/Modal.vue";
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    data() {
        return {
            tutorialModal: true,
            step: 1,
            tutorialContent: [
                { title: 'Video tutorial para realizar tus ventas' },
                { title: 'Video tutorial de análisis de ventas y gastos' }
            ],
            configModal: false,
            finishModal: false,
            loading: false,
            settingLoading: [],
            settings: [],
            values: [],
        }
    },
    components: {
        PrimaryButton,
        Modal,
        Head
    },
    props: {

    },
    methods: {
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
        nextStep() {
            if (this.step < 2) {
                this.step++;
            } else if (!this.$page.props.auth.user.tutorials_seen) {
                this.tutorialModal = false;
                this.configModal = true;
            } else {
                this.$inertia.visit(route('sales.point'));
            }
        },
        prevStep() {
            this.step--;
        },
    },
    mounted() {
        this.fetchModuleSettings();
    }
}
</script>

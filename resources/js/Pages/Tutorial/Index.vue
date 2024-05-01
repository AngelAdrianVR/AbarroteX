<template>  
<Head title="Ezy Ventas tutorial" />
    <div class="">
        <Modal :maxWidth="'3xl'" :show="tutorialModal">
            <div class="py-4 px-7 relative">
                <h1 class="font-bold text-center mb-5">Bienvenido (a) Abarrotes Doña Juanita</h1>
                <p class="font-bold">Configura tu tienda.</p>
                <p class="text-sm">La configuración inicial te permite personalizar tu experiencia de venta según tus necesidades. Puedes omitir este paso y ajustarlo más tarde en el módulo de configuraciones</p>

                <!-- Barra de progreso -->
                <!-- <div class="rounded-full border border-[#D9D9D9] h-5"></div>   -->

                <form class="mt-5 mb-2 space-y-3" @submit.prevent="storeCashCut">
                    <!-- escaner -->
                    <section>
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">Escanear productos</p>
                            <el-switch
                                v-model="form.scanner"
                                class="ml-2"
                                inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado "
                                inactive-text=" Deshabilitado "
                            />
                        </div>
                        <p class="text-sm">Si cuentas con un lector de códigos de barras puedes habilitar esta opción.</p>
                    </section>

                    <!-- Descuentos -->
                    <section>
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">Descuentos</p>
                            <el-switch
                                v-model="form.discounts"
                                class="ml-2"
                                inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado "
                                inactive-text=" Deshabilitado "
                            />
                        </div>
                        <p class="text-sm">Al habilitar esta opción puedes aplicar descuentos a tus ventas.</p>
                    </section>

                    <!-- Inventario -->
                    <section>
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">Inventario</p>
                            <el-switch
                                v-model="form.stock"
                                class="ml-2"
                                inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado "
                                inactive-text=" Deshabilitado "
                            />
                        </div>
                        <p class="text-sm">Al habilita esta opción podrás mantener un control de las existencias de tus productos.</p>
                    </section>

                    <!-- Monto máximo en caja -->
                    <section>
                        <div class="flex items-center justify-between">
                            <p class="font-semibold">Monto máximo en caja</p>
                            <el-switch
                                v-model="form.max_cash"
                                class="ml-2"
                                inline-prompt
                                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #CCCCCC"
                                active-text=" Habilitado "
                                inactive-text=" Deshabilitado "
                            />
                        </div>
                        <p class="text-sm">Tienes la posibilidad de agregar una cantidad máxima que se deba tener en caja, el sistema te notificará cuando hayas pasado este máximo y te pedirá hacer corte de caja.</p>
                    </section>
                    <div class="flex justify-end space-x-5 pt-2 pb-1 py-3 col-span-full">
                        <button class="text-primary" @click="tutorialModal = false">Omitir</button>
                        <PrimaryButton :disabled="form.processing">Continuar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- modal de finalización -->
        <Modal :show="finishModal">
            <div class="py-4 px-7 relative">
                <h1 class="font-bold text-center mb-5">¡Haz terminado la configuración tu tienda!</h1>
                <p class="text-sm text-center">Te invitamos a seguir explorando sobre las diversas funcionalidades y herramientas del sistema.</p>

                <div class="my-7">
                    <svg class="mx-auto" width="63" height="58" viewBox="0 0 43 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23.6278 8.2372C20.6701 14.8922 18.7593 17.6716 14 20.541L14.3106 34.4226H15.5529C16.2798 35.2468 16.8557 35.6125 18.348 36H36.6718C39.467 35.369 39.1564 31.5833 37.2929 30.9523C40.0881 30.9523 40.7092 26.851 38.2247 26.2201C41.3304 25.9046 41.3304 21.8033 38.8458 21.1724C42.5727 21.4878 43.5044 16.1246 38.8458 16.1246H27.6652C29.0744 8.34365 29.5091 4.57308 27.6652 1.92757C25.7806 0.590255 24.856 0.795623 23.3172 1.92757C23.2921 4.34376 23.3653 5.72468 23.6278 8.2372Z" stroke="#F68C0F"/>
                        <path d="M12 20H1V37H12V20Z" stroke="#F68C0F"/>
                    </svg>
                </div>
                
                <div class="text-center">
                    <PrimaryButton class="!px-12" @click="$inertia.get(route('dashbard'))">Seguir explorando</PrimaryButton>
                </div>

                <p class="text-xs text-center mt-8">¡Descubre lo que Ezy Ventas puede hacer por ti y tu negocio!</p>
            </div>
        </Modal>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import { Head, Link } from '@inertiajs/vue3';

export default {
data() {
    const form = useForm({
        scanner: false,
        discounts: false,
        stock: false,
        max_cash: false,
    });

    return {
        form,
        tutorialModal: false,
        finishModal: true
    }
},
components:{
PrimaryButton,
Modal,
Head,
Link 
},
props:{

},
methods:{

}
}
</script>

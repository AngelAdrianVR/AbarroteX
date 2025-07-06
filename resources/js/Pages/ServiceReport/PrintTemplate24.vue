<template>
    <Head :title="'Orden-' + String(report.folio).padStart(3, '0')" />
    <main :class="printing ? 'w-full' : 'w-[900px] mx-auto'" class="py-1 px-4 formato">
        <section class="absolute top-2 right-2">
            <PrintButton v-if="!printing" @click="print" />
        </section>

        <div v-for="(i, index) in 1" :key="i">
            <div class="flex justify-between items-center">
                <figure class="w-40">
                    <img class="w-full" :src="$page.props.auth.user.store?.media[0]?.original_url" alt="">
                </figure>
                <p class="font-bold">SERVICIO AVANZADO DE REPARACIÓN DE CELULARES</p>
                <p class="text-[#373737] text-base">Orden No. {{ String(report.folio).padStart(4, '0') }}</p>
            </div>

            <body :class="index === 0 ? 'border-dashed border-b border-gray-500 pb-2' : ''">
                <section class="grid grid-cols-2 gap-x-2 text-[#373737] text-[12px] rounded-2xl border border-[#D9D9D9] py-2 px-4">
                    <div class="flex items-center space-x-2">
                        <p class="w-[140px]">Fecha de recepción:</p>
                        <span class="font-semibold">{{ formatDate(report.service_date) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="w-20">Marca:</p>
                        <span class="font-semibold">{{ report.product_details.brand ?? '-' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="w-[140px]">Nombre del cliente:</p>
                        <span class="font-semibold">{{ report.client_name }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="w-20">Modelo:</p>
                        <span class="font-semibold">{{ report.product_details.model ?? '-' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="w-[140px]">Teléfono:</p>
                        <span class="font-semibold">{{ formatPhoneNumber(report.client_phone_number) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <p class="w-20">IMEI:</p>
                        <span class="font-semibold">{{ report.product_details.imei ?? '-' }}</span>
                    </div>
                </section>

                <section class="grid grid-cols-3 gap-x-1 text-xs">
                    <article class="col-span-2 pt-2 space-y-1">
                        <div class="border border-[#D9D9D9] rounded-xl py-1 px-4">
                            <p>Problema reportado:</p>
                            <span class="font-semibold">{{ report.observations ?? '-' }}</span>
                        </div>
                        <div class="border border-[#D9D9D9] rounded-xl py-1 px-4">
                            <p>Servicios a realizar:</p>
                            <span class="font-semibold">{{ report.service_description ?? '-' }}</span>
                        </div>
                    </article>

                    <article class="mt-2">
                        <div class="flex space-x-2">
                            <div class="border border-[#D9D9D9] rounded-xl py-1 px-4">
                                <p>Accesorios:</p>
                                <div v-for="item in allAccessories" :key="item" class="flex items-center gap-2 pl-2">
                                    <i v-if="report.aditionals?.accessories?.includes(item)" class="fa-solid fa-check text-green-500"></i>
                                    <i v-else class="fa-solid fa-xmark text-red-500"></i>
                                    <p>{{ item }}</p>
                                </div>
                            </div>
                            <DrawPatternMobil v-if="report.aditionals?.unlockPattern?.length" 
                                :points="report.aditionals.unlockPattern" />
                            <div class="border border-[#D9D9D9] rounded-xl py-1 px-4" v-else>
                                <p class="text-[#373737]">Contraseña:</p>
                                <p class="mt-2 text-center">{{ report.aditionals?.unlockPassword }}</p>
                            </div>
                        </div>
                    </article>
                </section>

                <section class="flex space-x-1 mt-1 text-[13px] border border-[#D9D9D9] rounded-lg py-1 px-3">
                    <p class="flex border-r border-[#D9D9D9] pr-2">
                        <span>Costo del servicio:</span><span class="ml-2">$</span><span class=" text-right">{{ report.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</span>
                    </p>
                    <p class="flex border-r border-[#D9D9D9] px-2">
                        <span>Anticipo:</span><span class="ml-2"> - $</span><span class="text-right">{{ report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</span>
                    </p>
                    <p class="flex border-r border-[#D9D9D9] px-2">
                        <span>Refacciones:</span><span class="ml-3">$</span><span class="text-right">{{ totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                    <p class="flex font-bold px-2">
                        <span>Total restante:</span><span class="ml-3">$</span><span class="text-right">{{ report.total_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                </section>

                <div class="col-span-full mx-auto mt-2">
                    <p class="text-[14px] font-bold">Condiciones del servicio</p>
                    <p class="text-[12px]">1. Si su equipo no prende, no se responde por otros daños del celular debido a que no se pudo revisar. <br>
                        2. Pantalla y táctil no tiene garantía después de retirarlo el equipo del taller. <br>
                        3. Rompimiento de sellos pierde garantía. <br>
                        4. No se responde garantía por daños ocasionados por alto voltaje, humedad, golpe o mal manejo del equipo. <br>
                        5. Garantía solo por hardware excepto display y táctil, tampoco por software ni desconfinguración. <br>
                        6. Departamento técnico no se hace responsable por pérdida de información, el cliente debe de tener backup. <br>
                        7. Si pasados 20 días no se recoge el equipo nos acogemos a la ley de abandono. <br>
                        8. Toda revisión y diagnostico tiene un costo de $300</p>

                    <div class="flex justify-between items-center mt-6 px-10">
                    <!-- Firma del cliente -->
                    <div class="w-1/3 text-center">
                        <div class="border-t border-gray-400 mb-1"></div>
                        <p class="text-xs text-gray-700">Firma del cliente aceptando condiciones</p>
                    </div>

                    <!-- Firma del negocio -->
                    <div class="w-1/3 text-center">
                        <div class="border-t border-gray-400 mb-1"></div>
                        <p class="text-xs text-gray-700">APONTEPHONE</p>
                    </div>
                    </div>

                </div>
            </body>
        </div>
    </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import PrintButton from '@/Components/MyComponents/PrintButton.vue';
import DrawPatternMobil from '@/Components/MyComponents/DrawPatternMobil.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {
        printing: false,
        allAccessories: ['SIM', 'Cargador', 'Memoria', 'Bateria'],
    }
},
components:{
    DrawPatternMobil,
    PrintButton,
    Head,
},
props:{
    report: Array
},
computed:{
    totalSpareParts() {
        return this.report.spare_parts.reduce((total, sp) => {
            return total + (Number(sp.quantity) * Number(sp.unitPrice));
        }, 0);
    }
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    formatPhoneNumber(number) {
        if (!number) return '-';

        // Elimina espacios previos y todo lo que no sea dígito
        const cleaned = number.toString().replace(/\D/g, '');

        // Divide en grupos de 2 dígitos
        return cleaned.match(/.{1,2}/g).join(' ');
    },
    print() {
        this.printing = true;
        this.$nextTick(() => {
            window.print();
        });
    },
    handleAfterPrint() {
        this.printing = false;
    },
},
mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
},
beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
}
}
</script>

<style scoped>
@media print {
  .formato {
    page-break-inside: avoid;
    height: 50vh; /* media hoja */
  }
}
</style>
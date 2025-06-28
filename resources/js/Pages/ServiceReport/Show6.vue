<template>

    <Head :title="'RS-' + String(report.folio).padStart(4, '0')" />
    <header class="mt-3 mx-8">
        <div class="flex justify-center">
            <PrimaryButton v-if="isInNormalView" @click="print">
                Imprimir o guardar PDF
            </PrimaryButton>
        </div>
        <div class="flex justify-between">
            <figure>
                <img src="@/../../public/images/DMCompresoresLogo.png" alt="Logo de DM compresores">
            </figure>
            <div class="self-center text-sm">
                <p class="flex items-center justify-between w-72">
                    <span>Orden de servicio:</span>
                    <span>No. {{ String(report.folio).padStart(4, '0') }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span>Fecha del servicio:</span>
                    <span>{{ formatDate(report.service_date) }}</span>
                </p>
            </div>
        </div>
    </header>
    <main class="mt-3 mx-14 text-sm">
        <section class="grid grid-cols-4 gap-x-3 gap-y-px">
            <p class="text-[#373737]">Persona que solicito el servicio</p>
            <p class="col-span-3">{{ report.client_name }}</p>
            <p class="text-[#373737]">Departamento</p>
            <p class="col-span-3">{{ report.client_department }}</p>
            <h1 class="text-[#373737] font-bold col-span-full mt-2">Datos del equipo</h1>
            <p class="text-[#373737]">Marca</p>
            <p>{{ report.product_details.brand ?? '-' }}</p>
            <p class="text-[#373737]">Tipo de aceite</p>
            <p>{{ report.product_details.oil_type ?? '-' }}</p>
            <p class="text-[#373737]">Modelo</p>
            <p>{{ report.product_details.model ?? '-' }}</p>
            <p class="text-[#373737]">Sistema de enfriamiento</p>
            <p>{{ report.product_details.chiller_type ?? '-' }}</p>
            <p class="text-[#373737]">Serie</p>
            <p>{{ report.product_details.serie ?? '-' }}</p>
            <p class="text-[#373737]">Voltaje</p>
            <p>{{ report.product_details.voltage ?? '-' }} {{ report.product_details.voltage ? 'V' : '' }}</p>
            <p class="text-[#373737]">Capacidad</p>
            <p>{{ report.product_details.capacity ?? '-' }}</p>
            <p class="text-[#373737]">Marca motor principal</p>
            <p>{{ report.product_details.motor_brand ?? '-' }}</p>
            <p class="text-[#373737]">Horas de trabajo</p>
            <p>{{ report.product_details.worked_hours ?? '-' }} {{ report.product_details.worked_hours ? 'Hrs' : '' }}
            </p>
            <p class="text-[#373737]">Marca motor ventilador</p>
            <p>{{ report.product_details.fan_brand ?? '-' }}</p>
            <p class="text-[#373737]">Tipo de mantenimiento</p>
            <p>{{ report.product_details.maintenance_type ?? '-' }}</p>
            <p class="text-[#373737]">HP motor ventilador</p>
            <p>{{ report.product_details.fan_hp ?? '-' }} {{ report.product_details.fan_hp ? 'HP' : '' }}</p>
            <p class="text-[#373737]">Horas de carga</p>
            <p>{{ report.product_details.charge_hours ?? '-' }} {{ report.product_details.charge_hours ? 'Hrs' : '' }}
            </p>
            <h1 class="text-[#373737] font-bold col-span-full mt-2">Servicio</h1>
            <p class="text-[#373737]">Servicio realizado</p>
            <p class="col-span-3">{{ report.description ?? '-' }}</p>
        </section>
        <h1 class="text-[#373737] font-bold col-span-full mt-6">Rreferencias de refacciones</h1>
        <section>
            <table class="w-full table-fixed mt-2 text-xs">
                <thead>
                    <tr
                        class="*:px-1 *:py-px *:text-start *:text-gray37 *:font-normal *:border *:border-grayD9 rounded-t-[10px] *:bg-grayF2">
                        <th class="w-[30%]">No. De Parte</th>
                        <th class="w-[50%]">Descripción</th>
                        <th class="w-[10%]">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in report.spare_parts" :key="index"
                        class="*:px-1 *:py-px *:text-start *:border *:border-grayD9">
                        <td class="w-[30%]">{{ item.name }} ({{ item.code ?? 'Sin No. de parte' }})</td>
                        <td class="w-[50%]">{{ item.description }}</td>
                        <td class="w-[10%]">{{ item.quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
        <h1 class="text-[#373737] font-bold col-span-full mt-6">Observaciones</h1>
        <section class="grid grid-cols-4 gap-x-3 gap-y-px mb-5">
            <p class="text-[#373737]">Voltaje de placa</p>
            <p>{{ report.observations.plate_voltage }} {{ report.observations.plate_voltage ? 'V' : '' }}</p>
            <p class="text-[#373737]">Temperatura</p>
            <p>{{ report.observations.temperature }} {{ report.observations.plate_voltage ? '°C' : '' }}</p>
            <p class="text-[#373737]">Amperaje de placa</p>
            <p>{{ report.observations.plate_current }} {{ report.observations.plate_current ? 'A' : '' }}</p>
            <p class="text-[#373737]">Presión de carga</p>
            <p>{{ report.observations.charge_pressure ?? '-' }} {{ report.observations.charge_pressure ? 'psi' : '' }}</p>
            <p class="text-[#373737]">Voltaje medido</p>
            <p>{{ report.observations.measured_voltage }} {{ report.observations.measured_voltage ? 'V' : '' }}</p>
            <p class="text-[#373737]">Presión de corte</p>
            <p>{{ report.observations.cut_pressure ?? '-' }} {{ report.observations.cut_pressure ? 'psi' : '' }}</p>
            <p class="text-[#373737]">Amperaje medido</p>
            <p>{{ report.observations.measured_current }} {{ report.observations.measured_current ? 'A' : '' }}</p>
            <h1 class="text-[#373737] font-bold col-span-full mt-2">Registro del servicio</h1>
            <p class="text-[#373737]">Responsable del servicio</p>
            <p class="col-span-3">{{ report.technician_name ?? '-' }}</p>
            <p class="text-[#373737]">Servicio recibio por</p>
            <p class="col-span-3">{{ report.receiver_name ?? '-' }}</p>
        </section>
    </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Head, Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            indexEdit: null,
            isInNormalView: true,
        }
    },
    components: {
        PrimaryButton,
        Head,
        Link
    },
    props: {
        report: Object
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleEditDescription() {
            this.indexEdit = null;
        },
        print() {
            this.isInNormalView = false;
            this.$nextTick(() => {
                window.print();
            });
        },
        handleAfterPrint() {
            this.isInNormalView = true;
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

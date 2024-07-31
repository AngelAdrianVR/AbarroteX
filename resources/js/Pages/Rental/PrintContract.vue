<template>
  <Head :title="'Contrato de renta. R-' + rental.folio" />

  <main class="h-screen overflow-hidden lg:mx-48 mx-4">
      <h1 class="my-5 text-center">CONTRATO DE RENTA DE EQUIPOS</h1>

      <div class="absolute right-2 top-2">
        <PrimaryButton v-if="showEditIcon" @click="print">
            Imprimir o guardar PDF
        </PrimaryButton>
      </div>

    <body class="text-sm">
        <p>
            Este contrato de renta de equipos se celebra en la cuidad de Zapopan, Jalisco, el
            13 de marzo de 2024 entre DM Compresores y Esteren S.A de C.V. con domicilio
            Palomar 32, Colonia Valle del Sol, Guadalajara, Jalisco, CP. 45230.
        </p>

        <!-- Arrendador --------------- -->
        <section class="mt-7">
            <h2 class="font-bold mb-3">Arrendador</h2>
            <div class="flex">
                <div class="w-44 ml-4">
                    <p>Nombre:</p>
                    <p>Dirección:</p>
                    <p>Teléfono:</p>
                    <p>Correo electrónico:</p>
                    <p>Contacto:</p>
                </div>

                <div>
                    <p>{{ $page.props.auth.user.store.name ?? '-' }}</p>
                    <p>{{ $page.props.auth.user.store.address ?? '-' }}</p>
                    <p>{{ $page.props.auth.user.store.contact_phone ?? '-' }}</p>
                    <p>{{ $page.props.auth.user.email ?? '-' }}</p>
                    <p>{{ $page.props.auth.user.name ?? '-' }}</p>
                </div>
            </div>
        </section>

        <!-- Arrendatario --------------- -->
        <section class="mt-7">
            <h2 class="font-bold mb-3">Arrendatario</h2>
            <div class="flex">
                <div class="w-44 ml-4">
                    <p>Empresa:</p>
                    <p>Dirección:</p>
                    <p>Teléfono:</p>
                    <p>Correo electrónico:</p>
                    <p>Contacto:</p>
                </div>

                <div>
                    <p>{{ rental.client?.company ?? '-' }}</p>
                    <p>{{ rental.client.street ? rental.client.street + ' ' + rental.client.ext_number + ', Col. ' + rental.client.suburb + ' ' +
                                rental.client.int_number + '. ' + rental.client.town + ', ' + rental.client.polity_state : '-' }}</p>
                    <p>{{ rental.client?.phone ?? '-' }}</p>
                    <p>{{ rental.client?.email ?? '-' }}</p>
                    <p>{{ rental.client?.name ?? '-' }}</p>
                </div>
            </div>
        </section>

        <!-- Descripción del equipo --------------- -->
        <section class="mt-7">
            <h2 class="font-bold mb-3">Descripción del equipo</h2>
            <div class="flex">
                <div class="w-44 ml-4">
                    <p>Nombre:</p>
                    <p>Marca:</p>
                    <p>Modelo:</p>
                    <p>Número de serie:</p>
                    <p>Valor del equipo:</p>
                </div>

                <div>
                    <p>{{ rental.product?.name ?? '-' }}</p>
                    <p>{{ rental.product?.name ?? '-' }}</p>
                    <p>{{ rental.product?.name ?? '-' }}</p>
                    <p>{{ rental.product?.name ?? '-' }}</p>
                    <p>${{ rental.product?.public_price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                </div>
            </div>
        </section>

        <!-- Términos de renta --------------- -->
        <section class="mt-7">
        <h2 class="font-bold mb-3">Términos de renta</h2>
        <div class="flex">
            <div class="w-44 ml-4">
                <p>Duración del contrato:</p>
                <p>Monto de renta:</p>
                <p>Calendario de pagos:</p>
                <p>Depósito de garantía:</p>
            </div>

            <div>
                <!-- Se suma el dia de entrega -->
                <p>{{ differenceInDays + 1 }} días</p> 
                <p>${{ rental.cost }} / {{ rental.period.name }}</p>
                <p>Cada {{ rental.period.name }}</p>
                <p>${{ '0.00' }}</p>
            </div>
        </div>
        </section>

        <!-- Firmas -->
        <section class="my-9 mx-auto w-2/3 flex justify-between space-x-5
        7">
            <!-- Arrendador -->
            <div class="text-center w-56">
                <p class="font-bold mb-9">Firma del arrendador</p>
                <div class="border-t border-black">
                    <p>{{ $page.props.auth.user.name ?? 'Arrendador' }}</p>
                    <p>{{ $page.props.auth.user.store.name ?? 'Empresa de Arrendador' }}</p>
                </div>
            </div>

            <!-- Arrendatario -->
            <div class="text-center w-56">
                <p class="font-bold mb-9">Firma del arrendatario</p>
                <div class="border-t border-black">
                    <p>{{ rental.client?.name ?? 'Arrendatario' }}</p>
                    <p v-if="rental.client?.company">{{ rental.client?.company }}</p>
                </div>
            </div>
        </section>
    </body>
  </main>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { parseISO, differenceInDays as diffInDays } from 'date-fns';
import { Head, Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      showEditIcon: true,
      differenceInDays: 0,
    };
  },
  components: {
    PrimaryButton,
    Head,
    Link,
  },
  props: {
    rental: Object,
  },
  methods: {
    print() {
      this.showEditIcon = false;
      setTimeout(() => {
        window.print();
      }, 100);
    },
    handleAfterPrint() {
      this.showEditIcon = true;
    },
    calculateDifferenceInDays() {
      const rentedAt = parseISO(this.rental.rented_at);
      const estimatedEndDate = parseISO(this.rental.estimated_end_date);
      this.differenceInDays = diffInDays(estimatedEndDate, rentedAt);
    }
  },
  mounted() {
    window.addEventListener("afterprint", this.handleAfterPrint);
    this.calculateDifferenceInDays(); // calcula cuantos dias dura el contrato
  },
  beforeDestroy() {
    window.removeEventListener("afterprint", this.handleAfterPrint);
  },
};
</script>

<template>

    <Head title="Historial de ventas a credito" />
    <header class="mx-10 my-3">
        <section class="flex items-center justify-between text-gray37 text-sm">
            <ApplicationMark class="block h-11 w-auto" />
            <div class="border-2 border-primary px-3 py-2 rounded-[5px] text-center font-bold">
                Presiona Crl+P para imprimir o<br>
                guardar en PDF
            </div>
            <p class="flex items-center space-x-2 self-end">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                <span>{{ $page.props.auth.user.store.name }}</span>
            </p>
        </section>
        <section class="flex items-start justify-between mt-5 text-sm">
            <div class="grid grid-cols-3 gap-x-3 gap-y-1">
                <p class="font-bold">Cliente</p>
                <p class="col-span-2">{{ 'nombre de cliente' }}</p>
                <p class="font-bold">Teléfono</p>
                <p class="col-span-2">{{ '1234567890' }}</p>
                <p class="font-bold">Dirección</p>
                <p class="col-span-2">{{ 'Direccion del cliente' }}</p>
            </div>
            <div class="grid grid-cols-3 gap-x-3 gap-y-1">
                <p class="font-bold">Fecha</p>
                <p class="col-span-2">{{ '03 junio 2024' }}</p>
            </div>
        </section>
        <section class="flex flex-col items-center justify-center space-y-1 mt-3 text-sm">
            <p>Saldo total pendiente  de pago</p>
            <p class="font-bold">${{ '1,325.00' }}</p>
        </section>
    </header>
    <main>

    </main>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';

export default {
    name: 'creditSaleHistorical',
    data() {
        return {

        }
    },
    components: {
        Head,
        ApplicationMark,
    },
    props: {

    },
    methods: {
        print() {
            window.print();
        }
    },
    mounted() {
        // this.print();
    }
};
</script>
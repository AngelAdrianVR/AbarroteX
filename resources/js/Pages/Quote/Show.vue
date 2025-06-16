<template>
    <Head :title="'COT-' + quote.folio" />

    <main class="w-full mx-auto h-screen flex flex-col justify-between p-4">
        <div>
            <!-- Header --------------------------- -->
            <section class="flex justify-center items-center mt-5">
                <PrintButton v-if="showEditIcon" @click="print" />
            </section>

            <!-- body ---------------------------- -->

            <body class="my-4 md:w-[95%] lg:w-[80%] mx-auto text-[15px]">
                <section class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-left text-xl mb-2">Cotización</h2>
                        <p>Folio. {{ quote.folio }}</p>
                        <p>fecha: <span class="ml-1">{{ formatDate(quote.created_at) }}</span></p>
                        <p>Expiración: <span class="ml-1">{{ formatDate(quote.expired_date) ?? 'N/A' }}</span></p>
                    </div>
                    <figure class="border rounded-lg p-2">
                        <img class="w-[150px] h-[50px] object-contain"
                            :src="quote.store?.logo ? '/storage/' + quote.store.logo : '/images/logo.png'"
                            alt="Logo de la empresa" />
                    </figure>
                </section>

                <el-divider></el-divider>

                <p>{{ quote.store.name ?? '' }} - <span class="ml-1">{{ quote.contact_name }}</span></p>
                <p v-if="quote.store?.address">{{ quote.store?.address }}</p>
                <p class="font-semibold">Condiciones de pago: <span class="ml-1 font-normal">{{ quote.payment_conditions }}</span></p>

                <el-divider></el-divider>

                <!-- tabla de productos y servicios -->
                <table class="w-full my-5">
                    <thead>
                        <tr class="*:text-left *:py-2 *:px-4 *:text-sm font-bold border-b border-[#D9D9D9]">
                            <!-- <th class="rounded-s-full">Folio</th> -->
                            <th>Producto</th>
                            <!-- <th>Descripción</th> -->
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th class="rounded-e-full">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- productos -->
                        <tr v-for="(product, index) in quote.products" :key="index"
                            class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]">
                            <!-- <td>{{ 'P-' + product.id }}</td> -->
                            <td>{{ product.name }}</td>
                            <!-- <td>-</td> -->
                            <!-- <td>${{ (product.price / 1.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }} con iva descontado-->
                            <td>${{ product.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}
                            </td>
                            <td>{{ product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                            <td>${{ (product.price  * product.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        </tr>
                        <!-- servicios -->
                        <tr v-for="(service, index) in quote.services" :key="index"
                            class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]"
                            :class="{ 'bg-[#EDEDED]': (index % 2 != 0 && quote.products.length % 2 == 0) || (index % 2 == 0 && quote.products.length % 2 != 0) }">
                            <!-- <td>{{ 'S-' + service.id }}</td> -->
                            <td>{{ service.name }}</td>
                            <!-- <td class="w-[500px] relative">
                                <i v-if="indexEdit != index && showEditIcon" @click="indexEdit = index"
                                    class="fa-solid fa-pencil text-[10px] absolute top-1 right-0 text-gray99 border border-gray99 rounded-full p-[4px] px-[4px] cursor-pointer"></i>
                                <i v-else-if="indexEdit == index" @click="handleEditDescription()"
                                    class="fa-solid fa-check text-[10px] absolute top-1 right-0 text-white bg-primary rounded-full p-[4px] px-[5px] cursor-pointer"></i>
                                <p style="white-space: pre-line;" class="mr-2" v-if="indexEdit != index">{{
                                    service.description ?? '-' }}</p>
                                <el-input v-else v-model="service.description" :autosize="{ minRows: 2, maxRows: 5 }"
                                    class="!w-[95%]" type="textarea" placeholder="Escribe una descripción"
                                    :maxlength="500" show-word-limit clearable />
                            </td> -->
                            <td>${{ service.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                            <td>{{ service.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                            <td>${{ (service.quantity * service.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- desgloce de total -->
                <div class="text-right text-base">
                    <p v-if="quote.has_discount || quote.show_iva">
                        Subtotal: <span class="ml-4">$</span>
                        <span class="inline-block w-20" v-if="quote.show_iva">{{ (quote.total - (quote.total *
                            0.16))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        <span class="inline-block w-20" v-else>{{
                            quote.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                    <p v-if="quote.show_iva">IVA: <span class="ml-4">$</span><span class="inline-block w-20">{{
                        (quote.total * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                    <p v-if="quote.has_discount">
                        {{ quote.is_percentage_discount ? 'Descuento (' + quote.discount + '%)' : 'Descuento:' }} <span
                            class="ml-4">$</span>
                        <span class="inline-block w-20" v-if="quote.is_percentage_discount">{{ (quote.discount *
                            quote.total * 0.01)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        <span class="inline-block w-20" v-else>{{
                            quote.discount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                    <p class="font-bold">
                        Total: <span class="ml-4">$</span>
                        <span class="inline-block w-20" v-if="quote.has_discount && quote.is_percentage_discount">{{
                            (quote.total - (quote.discount * quote.total *
                                0.01))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        <span class="inline-block w-20" v-else-if="quote.has_discount">{{ (quote.total -
                            quote.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        <span class="inline-block w-20" v-else>{{
                            quote.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                </div>
                <section v-if="quote.notes" class="mt-14 flex items-center justify-center">
                    <div class="border border-[#D9D9D9] bg-[#FAFAFA] rounded-lg p-3 md:w-1/2 mx-4">
                        <p class="font-bold">Notas adicionales:</p>
                        <p class="text-sm">{{ quote.notes }}</p>
                    </div>
                </section>
            </body>
        </div>
    </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PrintButton from '@/Components/MyComponents/PrintButton.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Head, Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            indexEdit: null,
            showEditIcon: true,
        }
    },
    components: {
        PrimaryButton,
        PrintButton,
        Head,
        Link
    },
    props: {
        quote: Object
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleEditDescription() {
            this.indexEdit = null;
        },
        print() {
            this.showEditIcon = false;
            this.$nextTick(() => {
                window.print();
            });
        },
        handleAfterPrint() {
            this.showEditIcon = true;
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

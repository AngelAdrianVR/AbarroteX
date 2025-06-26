<template>

    <Head :title="'C-' + String(quote.folio).padStart(4, '0')" />

    <main class="w-full mx-auto h-screen flex flex-col justify-between p-4 text-xs">
        <div>
            <section class="flex justify-center items-center mt-5">
                <PrintButton v-if="showEditIcon" @click="print" />
            </section>

            <body class="my-4 md:w-[95%] lg:w-[80%] mx-auto">
                <section class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-left text-base mb-2">Cotización</h2>
                        <p>C-{{ String(quote.folio).padStart(4, '0') }}</p>
                        <p>
                            <span class="font-semibold">Fecha: </span>
                            <span class="ml-1">{{ formatDate(quote.created_at) }}</span>
                        </p>
                        <p v-if="quote.show_expiration && quote.expired_date">
                            <span class="font-semibold">Expiración: </span>
                            <span class="ml-1">{{ formatDate(quote.expired_date) }}</span>
                        </p>
                    </div>
                    <figure v-if="storeLogoUrl">
                        <img class="w-[150px] h-[50px] object-contain" :src="storeLogoUrl" alt="Logo de la empresa" />
                    </figure>
                </section>
                <el-divider class="!my-2" />
                <p>
                    <span v-if="quote.company">{{ quote.company }} - </span>
                    <span>{{ quote.contact_name }}</span>
                </p>
                <p v-if="quote.show_address">{{ quote.address }}</p>
                <p v-if="quote.show_payment_conditions">
                    <span class="font-semibold">Condiciones de pago: </span>
                    <span> {{ quote.payment_conditions }}</span>
                </p>
                <el-divider class="!my-2" />
                <!-- tabla de productos y servicios -->
                <table class="w-full">
                    <thead>
                        <tr class="*:text-left *:py-2 *:px-4 font-bold border-b border-[#D9D9D9]">
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
                            class="*:py-2 *:px-4 border-b border-[#EDEDED]">
                            <!-- <td>{{ 'P-' + product.id }}</td> -->
                            <td>{{ product.name }}</td>
                            <!-- <td>-</td> -->
                            <!-- <td>${{ (product.price / 1.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }} con iva descontado-->
                            <td>${{ parseFloat(product.price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-'
                            }}
                            </td>
                            <td>{{ product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                            <td>${{ (product.price * product.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                ?? '-' }}</td>
                        </tr>
                        <!-- servicios -->
                        <tr v-for="(service, index) in quote.services" :key="index"
                            class="*:py-2 *:px-4 border-b border-[#EDEDED]"
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
                <section
                    class="flex flex-col mx-16 items-end col-span-full *:flex *:items-center *:justify-between *:w-[45%] mt-3">
                    <div class="">
                        <span>Subtotal: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>{{ subtotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </div>
                    <div v-if="quote.iva_included != null" class="">
                        <span>IVA: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ (subtotal * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div v-if="quote.delivery_type" class="">
                        <span>{{ quote.delivery_type }}: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ quote.delivery_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div v-if="quote.is_percentage_discount != null" class="">
                        <span>Descuento: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ discounted?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                    <div class="font-bold">
                        <span>Total: </span>
                        <p class="flex items-center justify-between w-[40%]">
                            <span class="mx-2">$</span>
                            <span>
                                {{ grandTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                </section>
                <section v-if="quote.notes" class="w-full mt-4">
                    <div class="border border-[#D9D9D9] bg-[#FAFAFA] rounded-lg px-3 py-1 mx-4">
                        <p class="font-bold">Notas adicionales:</p>
                        <p>{{ quote.notes }}</p>
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
    computed: {
        storeLogoUrl() {
            return this.$page.props.auth.user.store.media?.find(media => media.collection_name === 'logo')?.original_url;
        },
        subtotal() {
            if (this.quote.iva_included) {
                return (this.quote.total / 1.16);
            }

            return this.quote.total;
        },
        grandTotal() {
            if (this.quote.iva_included === false) {
                return (this.quote.total * 1.16) + this.quote.delivery_cost - this.discounted;
            }

            return this.quote.total + this.quote.delivery_cost - this.discounted;
        },
        discounted() {
            let discounted = 0;
            if (this.quote.is_percentage_discount != null) {
                discounted = this.quote.is_percentage_discount
                    ? this.percentageDiscount
                    : this.quote.discount;
            }

            return discounted;
        },
        percentageDiscount() {
            return this.quote.percentage * 0.01 * this.subtotal;
        },
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

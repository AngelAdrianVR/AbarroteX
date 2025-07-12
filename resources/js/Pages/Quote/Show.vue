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
        <section v-if="$page.props.auth.user.quote_config" class="p-2 text-xs space-y-[2px]">
            <h2 class="text-gray37 font-bold text-base">{{ $page.props.auth.user.quote_config?.contact?.name }}</h2>
            <p v-if="$page.props.auth.user.quote_config?.contact?.email" class="flex items-center space-x-2">
                <span class="flex items-center justify-center rounded-full size-6 text-gray37 bg-[#EDEDED]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </span>
                <span>{{ $page.props.auth.user.quote_config?.contact?.email }}</span>
            </p>
            <p v-if="$page.props.auth.user.quote_config?.contact?.whatsapp" class="flex items-center space-x-2">
                <span class="flex items-center justify-center rounded-full size-6 text-gray37 bg-[#EDEDED]">
                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" class="size-4"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.20274 6.73244C8.16987 6.67801 8.08261 6.64449 7.95152 6.57942C7.82043 6.51434 7.17478 6.19607 7.05504 6.15269C6.9353 6.10931 6.84687 6.08683 6.75961 6.21816C6.67235 6.34949 6.42074 6.64449 6.34404 6.73086C6.26735 6.81723 6.19104 6.82946 6.05996 6.76399C5.67307 6.61017 5.31559 6.38986 5.00344 6.11286C4.7169 5.84874 4.47106 5.54301 4.27404 5.20576C4.19578 5.07483 4.26583 5.00384 4.33157 4.93837C4.46913 4.81199 4.58056 4.65946 4.65948 4.48956C4.67701 4.4534 4.68519 4.41337 4.68328 4.37318C4.68136 4.33298 4.66941 4.29393 4.64852 4.25963C4.61683 4.19456 4.35426 3.54855 4.24509 3.28747C4.12144 2.98931 3.57009 2.97905 3.34822 3.22121C2.64817 3.98671 2.80978 4.85397 3.42648 5.67232C3.49222 5.75988 4.3343 7.12801 5.66787 7.65333C7.00144 8.17866 7.00143 8.00315 7.24209 7.98107C7.93352 7.91836 8.40152 7.06491 8.2043 6.73362M5.5 1.00002C4.31164 0.996557 3.17044 1.46836 2.32681 2.31191C1.48318 3.15547 1.00601 4.30187 1 5.49958C0.999368 6.44882 1.29976 7.37342 1.85735 8.13843L1.29544 9.80984L3.02461 9.25573C3.70111 9.70316 4.4847 9.95867 5.29303 9.99539C6.10136 10.0321 6.90459 9.84871 7.61828 9.46443C8.33198 9.08016 8.92979 8.50921 9.34889 7.8116C9.768 7.11399 9.99291 6.31547 10 5.49998C9.99399 4.30223 9.51684 3.15578 8.67322 2.31217C7.82959 1.46855 6.6884 0.996661 5.5 1.00002Z"
                            stroke="currentColor" stroke-width="0.572727" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>

                </span>
                <span>{{ $page.props.auth.user.quote_config?.contact?.whatsapp }}</span>
            </p>
            <p v-if="$page.props.auth.user.quote_config?.footer" class="text-center">
                {{ $page.props.auth.user.quote_config?.footer }}
            </p>
        </section>
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

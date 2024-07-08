<template>
    <Head :title="'COT-' + quote.id" />
    <main class="w-full mx-auto h-screen flex flex-col justify-between">
        <div>   
            <!-- Header --------------------------- -->
            <section class="flex justify-between items-center">
                <div class="relative">
                    <svg width="500" height="109" class="text-white" viewBox="0 0 577 125" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M392.167 125L577 0L0 0V125L392.167 125Z" fill="#373B46"/>
                    </svg>
                    <p class="text-white font-bold text-3xl absolute top-9 left-14 font-sans">{{ $page.props.auth.user.store.name }}</p>
                </div>
                <PrimaryButton v-if="showEditIcon" @click="print">
                    Imprimir o guardar PDF 
                </PrimaryButton>                
                <div class="flex flex-col space-y-1 mr-16 text-right">
                    <p>{{ 'COT-' + (quote.id < 10 ? '0' + quote.id : quote.id) }}</p>
                    <p class="font-bold">Fecha: <span class="font-light">{{ formatDate(quote.created_at) }}</span></p>
                </div>
            </section>

            <!-- body ---------------------------- -->
            <body class="my-4 md:w-[95%] lg:w-[80%] mx-auto">
                <h2 class="font-bold text-center">Cotización</h2>
                <!-- cliente -->
                <p class="font-bold" v-if="quote.client?.name">Cliente: <span class="font-thin">{{ quote.client?.name }}</span></p>
                <p class="font-bold">Contacto: <span class="font-thin">{{ quote.contact_name }}</span></p>
                <!-- dirección -->
                <p class="font-bold" v-if="quote.address">Dirección: <span class="font-thin">{{ quote.address }}</span></p>

                <!-- tabla de productos y servicios -->
                <table class="w-full my-5">
                <thead>
                    <tr class="*:text-left *:py-2 *:px-4 *:text-sm bg-[#373B46] text-white">
                        <th class="rounded-s-full">Folio</th>
                        <th>Producto o servicio</th>
                        <th>Descripción</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th class="rounded-e-full">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- productos -->
                    <tr v-for="(product, index) in quote.products" :key="index"
                        class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]"
                        :class="{ 'bg-[#EDEDED]': index % 2 != 0 }">
                        <td>{{ 'P-' + product.id }}</td>
                        <td>{{ product.name }}</td>
                        <td>-</td>
                        <td>${{ product.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>{{ product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>${{ (product.quantity * product.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                    </tr>
                    <!-- servicios -->
                    <tr v-for="(service, index) in quote.services" :key="index"
                        class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]"
                        :class="{'bg-[#EDEDED]': (index % 2 != 0 && quote.products.length % 2 == 0) || (index % 2 == 0 && quote.products.length % 2 != 0)}">
                        <td>{{ 'S-' + service.id }}</td>
                        <td>{{ service.name }}</td>
                        <td class="w-[500px] relative">
                            <i v-if="indexEdit != index && showEditIcon" @click="indexEdit = index" class="fa-solid fa-pencil text-[10px] absolute top-1 right-0 text-gray99 border border-gray99 rounded-full p-[4px] px-[4px] cursor-pointer"></i>
                            <i v-else-if="indexEdit == index" @click="handleEditDescription()" class="fa-solid fa-check text-[10px] absolute top-1 right-0 text-white bg-primary rounded-full p-[4px] px-[5px] cursor-pointer"></i>
                            <p style="white-space: pre-line;" class="mr-2" v-if="indexEdit != index">{{ service.description ?? '-' }}</p>
                            <el-input v-else v-model="service.description" :autosize="{ minRows: 2, maxRows: 5 }" class="!w-[95%]" type="textarea"
                            placeholder="Escribe una descripción" :maxlength="500" show-word-limit
                            clearable />
                        </td>
                        <td>${{ service.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>{{ service.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>${{ (service.quantity * service.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- desgloce de total -->
            <div class="text-right text-base">
                <p v-if="quote.has_discount || quote.show_iva">
                    Subtotal: <span class="ml-4">$</span> 
                    <span class="inline-block w-20" v-if="quote.show_iva">{{ (quote.total - (quote.total * 0.16))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    <span class="inline-block w-20" v-else>{{ quote.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                </p>
                <p v-if="quote.show_iva">IVA: <span class="ml-4">$</span><span class="inline-block w-20">{{ (quote.total * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                <p v-if="quote.has_discount">
                    {{quote.is_percentage_discount ? 'Descuento (' + quote.discount + '%)' : 'Descuento:' }} <span class="ml-4">$</span>
                    <span class="inline-block w-20" v-if="quote.is_percentage_discount">{{ (quote.discount * quote.total * 0.01)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    <span class="inline-block w-20" v-else>{{ quote.discount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                </p>
                <p class="font-bold">
                    Total: <span class="ml-4">$</span>
                    <span class="inline-block w-20" v-if="quote.has_discount && quote.is_percentage_discount">{{ (quote.total - (quote.discount * quote.total * 0.01))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    <span class="inline-block w-20" v-else-if="quote.has_discount">{{ (quote.total - quote.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    <span class="inline-block w-20" v-else>{{ quote.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                </p>
            </div>

            <div v-if="quote.notes">
                <p class="font-bold">Notas adicionales:</p>
                <p style="white-space: pre-line;" class="mt-1">{{ quote.notes }}</p>
            </div>
            </body>
        </div>

        <!-- footer -------------------------- -->
        <footer>
            <section class="flex flex-col space-y-1 text-base -mb-20">
                <!-- telefono -->
                <div class="flex items-center space-x-2">
                    <div class="border border-[#F68C0F] rounded-full">
                        <svg class="m-1" width="22" height="22" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11240_150" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12" height="12">
                                <rect width="12" height="12" fill="#F68C0F"/>
                            </mask>
                            <g mask="url(#mask0_11240_150)">
                                <path d="M9.975 10.5C8.93333 10.5 7.90417 10.2729 6.8875 9.81875C5.87083 9.36458 4.94583 8.72083 4.1125 7.8875C3.27917 7.05417 2.63542 6.12917 2.18125 5.1125C1.72708 4.09583 1.5 3.06667 1.5 2.025C1.5 1.875 1.55 1.75 1.65 1.65C1.75 1.55 1.875 1.5 2.025 1.5H4.05C4.16667 1.5 4.27083 1.53958 4.3625 1.61875C4.45417 1.69792 4.50833 1.79167 4.525 1.9L4.85 3.65C4.86667 3.78333 4.8625 3.89583 4.8375 3.9875C4.8125 4.07917 4.76667 4.15833 4.7 4.225L3.4875 5.45C3.65417 5.75833 3.85208 6.05625 4.08125 6.34375C4.31042 6.63125 4.5625 6.90833 4.8375 7.175C5.09583 7.43333 5.36667 7.67292 5.65 7.89375C5.93333 8.11458 6.23333 8.31667 6.55 8.5L7.725 7.325C7.8 7.25 7.89792 7.19375 8.01875 7.15625C8.13958 7.11875 8.25833 7.10833 8.375 7.125L10.1 7.475C10.2167 7.50833 10.3125 7.56875 10.3875 7.65625C10.4625 7.74375 10.5 7.84167 10.5 7.95V9.975C10.5 10.125 10.45 10.25 10.35 10.35C10.25 10.45 10.125 10.5 9.975 10.5ZM3.0125 4.5L3.8375 3.675L3.625 2.5H2.5125C2.55417 2.84167 2.6125 3.17917 2.6875 3.5125C2.7625 3.84583 2.87083 4.175 3.0125 4.5ZM7.4875 8.975C7.8125 9.11667 8.14375 9.22917 8.48125 9.3125C8.81875 9.39583 9.15833 9.45 9.5 9.475V8.375L8.325 8.1375L7.4875 8.975Z" fill="#F68C0F"/>
                            </g>
                        </svg>
                    </div>
                    <p>{{ $page.props.auth.user.store.contact_phone?.replace(/(\d{2})(?=\d)/g, '$1 ') }}</p>
                </div>

                <!-- Ubicación -->
                <div class="flex items-center space-x-2">
                    <div class="border border-[#F68C0F] rounded-full">
                        <svg class="m-1" width="22" height="22" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11240_170" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12" height="12">
                                <rect width="12" height="12" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11240_170)">
                                <path d="M6 6C6.275 6 6.51042 5.90208 6.70625 5.70625C6.90208 5.51042 7 5.275 7 5C7 4.725 6.90208 4.48958 6.70625 4.29375C6.51042 4.09792 6.275 4 6 4C5.725 4 5.48958 4.09792 5.29375 4.29375C5.09792 4.48958 5 4.725 5 5C5 5.275 5.09792 5.51042 5.29375 5.70625C5.48958 5.90208 5.725 6 6 6ZM6 9.675C7.01667 8.74167 7.77083 7.89375 8.2625 7.13125C8.75417 6.36875 9 5.69167 9 5.1C9 4.19167 8.71042 3.44792 8.13125 2.86875C7.55208 2.28958 6.84167 2 6 2C5.15833 2 4.44792 2.28958 3.86875 2.86875C3.28958 3.44792 3 4.19167 3 5.1C3 5.69167 3.24583 6.36875 3.7375 7.13125C4.22917 7.89375 4.98333 8.74167 6 9.675ZM6 11C4.65833 9.85833 3.65625 8.79792 2.99375 7.81875C2.33125 6.83958 2 5.93333 2 5.1C2 3.85 2.40208 2.85417 3.20625 2.1125C4.01042 1.37083 4.94167 1 6 1C7.05833 1 7.98958 1.37083 8.79375 2.1125C9.59792 2.85417 10 3.85 10 5.1C10 5.93333 9.66875 6.83958 9.00625 7.81875C8.34375 8.79792 7.34167 9.85833 6 11Z" fill="#F68C0F"/>
                            </g>
                        </svg>

                    </div>
                    <p>{{ $page.props.auth.user.store.address }}</p>
                </div>

                <!-- Correo electrónico -->
                <div class="flex items-center space-x-2">
                    <div class="border border-[#F68C0F] rounded-full">
                        <svg class="m-1" width="22" height="22" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11240_159" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="12" height="12">
                                <rect width="12" height="12" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11240_159)">
                                <path d="M2 10C1.725 10 1.48958 9.90208 1.29375 9.70625C1.09792 9.51042 1 9.275 1 9V3C1 2.725 1.09792 2.48958 1.29375 2.29375C1.48958 2.09792 1.725 2 2 2H10C10.275 2 10.5104 2.09792 10.7063 2.29375C10.9021 2.48958 11 2.725 11 3V9C11 9.275 10.9021 9.51042 10.7063 9.70625C10.5104 9.90208 10.275 10 10 10H2ZM6 6.5L2 4V9H10V4L6 6.5ZM6 5.5L10 3H2L6 5.5ZM2 4V3V9V4Z" fill="#F68C0F"/>
                            </g>
                        </svg>
                    </div>
                    <p>{{ $page.props.auth.user.email }}</p>
                </div>
            </section>


            <!------------- Decoraciones juntas en png ------------->
            <img class="" src="@/../../public/images/quote_bottom_decoration.png" alt="">
            
            
            <!------------- Decoraciones juntas en svg ------------->
            <!-- <svg width="1438" height="149" viewBox="0 0 1438 149" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1072.66 18.3945L900 148.999H1439V18.3945H1072.66Z" fill="#373B46"/>
                <path d="M937.417 96.5741H837L964.394 0H1439V8.29933H1053.82L937.417 96.5741Z" fill="#F68C0F"/>
                <path d="M792.432 112.209H0V102.001H702.883L818.446 16.5547H917L792.432 112.209Z" fill="#373B46"/>
            </svg> -->


            <!------------- Decoraciones por separado para cambio de color ------------->
            <!-- <svg class="hidden xl:block absolute bottom-7 left-0" width="917" height="127" viewBox="0 0 917 127" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M792 126.5H0V113H702.5L818 0H916.5L792 126.5Z" fill="#373B46"/>
            </svg>

            <svg class="hidden xl:block absolute bottom-12 right-0" width="602" height="128" viewBox="0 0 602 128" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M101 128H0.5L128 0H603V11H217.5L101 128Z" fill="#F68C0F"/>
            </svg>

            <svg class="hidden xl:block absolute bottom-0 right-0" width="500" height="160" viewBox="0 0 538 173" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M172.5 0.5L0 173H538.5V0.5H172.5Z" fill="#373B46"/>
            </svg> -->
        </footer>
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
        showEditIcon: true,
    }
},
components:{
PrimaryButton,
Head,
Link
},
props:{
    quote: Object
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    handleEditDescription() {
        this.indexEdit = null;
    },
    print() {
      this.showEditIcon = false;
      setTimeout(() => {
        window.print();
      }, 100);
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

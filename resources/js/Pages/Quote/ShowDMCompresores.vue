<template>
    <Head :title="'COT-' + quote.folio" />
    
    <div :class="printing ? 'w-full' : 'w-[900px]'" class="mx-auto h-screen overflow-hidden p-2 relative">

        <!-- Decoraciones -->
        <section class="*:z-0">
            <img class="absolute top-3 right-3" src="@/../../public/images/decoration_DM_quote_top.png" alt="">
            <img class="absolute bottom-3 left-3" src="@/../../public/images/decoration_DM_quote_bottom.png" alt="">
        </section>

        <!-- Documento -->
        <main class="mx-auto h-screen overflow-hidden p-2 z-10 absolute">
            <figure class="border-b border-grayD9 pb-2 flex justify-between">
                <img class="w-64 self-start" src="@/../../public/images/DMcompresores_logo.png" alt="">
                <PrimaryButton class="self-start !rounded-md" v-if="showEditIcon" @click="print">
                        Imprimir o guardar PDF 
                </PrimaryButton>
            </figure>

            <p class="text-lg font-bold text-right mr-4 py-2">{{ 'FOLIO: DM.' + quote.folio.toString().padStart(4, '0') }}</p>

            <div class="bg-[#EDEDED] h-[30px]"></div>

            <div class="font-bold flex items-center justify-between mx-4">
                <p class="text-[#8F8E8E]">Vendedor:</p>
                <p>JOSE LUIS GARCIA</p>
                <p class="text-[#8F8E8E]">Fecha:</p>
                <p>{{ formatLongDate(quote.created_at) }}</p>
            </div>

            <div class="bg-[#EDEDED] px-2 py-1">
                <p class="text-[#8F8E8E] font-bold">Redes:</p>
            </div>

            <p class="text-[#8F8E8E] font-bold mx-4">Dirección de correo electrónico: <span class="ml-5 underline text-blue-600">dm.compresores@gmail.com</span></p>

            <div class="bg-[#EDEDED] px-2 py-1 font-bold flex items-center justify-between">
                <p class="text-[#8F8E8E]">Teléfono de contacto:</p>
                <p class="text-[#8F8E8E]">3337052368 Y 3319075562</p>
                <p class="text-[#8F8E8E]">WhatsApp:</p>
                <p class="text-[#8F8E8E]">3330581886</p>
            </div>

            <p class="text-[#8F8E8E] font-bold mx-4">Dirección:</p>

            <!-- Informacion del cliente --------------------------------------------------------------- -->
            <div class="bg-[#AEAAAA] text-center">
                <p class="font-bold">Información Del Cliente</p>
            </div>

            <p class="font-bold mx-4">Nombre: {{ quote.client?.company ?? quote.contact_name }}</p>

            <div class="bg-[#EDEDED] px-2 py-1 font-bold flex items-center justify-end space-x-7 md:pr-24">
                <p>Contacto:</p>
                <p>{{quote.contact_name}}</p>
            </div>

            <p class="text-[#8F8E8E] font-bold mx-4">Dirección: <span class="ml-4">{{ quote.client?.street ? quote.client?.street + ' ' + quote.client?.ext_number + ', Col. ' + quote.client?.suburb + ' ' +
                                quote.client?.int_number + '. ' + quote.client?.town + ', ' + quote.client?.polity_state : '-' }}</span></p>

            <div class="bg-[#EDEDED] px-2 py-1">
                <p class="text-[#8F8E8E] font-bold">Correo electrónico: <span class="ml-4">{{ quote.client.email ?? '-' }}</span></p>
            </div>

            <!-- Tabla de productos -->
            <table class="w-full my-5">
                <thead>
                    <tr class="*:text-left *:py-2 *:px-4 *:text-sm bg-[#FFD966]">
                        <th class="">Código</th>
                        <th>Detalle</th>
                        <!-- <th>Descripción</th> -->
                        <th>Cantidad</th>
                        <th>Precio/Unidad</th>
                        <th class="">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- productos -->
                    <tr v-for="(product, index) in quote.products" :key="index"
                        class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]"
                        :class="{ 'bg-[#EDEDED]': index % 2 != 0 }">
                        <td>{{ 'P-' + product.id }}</td>
                        <td>
                            <!-- <i v-if="product.product_on_request" class="fa-regular fa-circle-dot text-red-500 text-xs mr-1"></i> -->
                            {{ product.name }} 
                            <span v-if="product.product_on_request">(entrega en {{ product.days_for_delivery }} días)</span>
                        </td>
                        <!-- <td>-</td> -->
                        <td>{{ product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>${{ (product.price / 1.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>${{ ((product.price / 1.16) * product.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }} + IVA</td>
                    </tr>
                    <!-- servicios -->
                    <tr v-for="(service, index) in quote.services" :key="index"
                        class="*:text-sm *:py-2 *:px-4 border-b border-[#EDEDED]"
                        :class="{'bg-[#EDEDED]': (index % 2 != 0 && quote.products.length % 2 == 0) || (index % 2 == 0 && quote.products.length % 2 != 0)}">
                        <td>{{ 'S-' + service.id }}</td>
                        <td>{{ service.name }}</td>
                        <!-- <td class="w-[500px] relative">
                            <i v-if="indexEdit != index && showEditIcon" @click="indexEdit = index" class="fa-solid fa-pencil text-[10px] absolute top-1 right-0 text-gray99 border border-gray99 rounded-full p-[4px] px-[4px] cursor-pointer"></i>
                            <i v-else-if="indexEdit == index" @click="handleEditDescription()" class="fa-solid fa-check text-[10px] absolute top-1 right-0 text-white bg-primary rounded-full p-[4px] px-[5px] cursor-pointer"></i>
                            <p style="white-space: pre-line;" class="mr-2" v-if="indexEdit != index">{{ service.description ?? '-' }}</p>
                            <el-input v-else v-model="service.description" :autosize="{ minRows: 2, maxRows: 5 }" class="!w-[95%]" type="textarea"
                            placeholder="Escribe una descripción" :maxlength="500" show-word-limit
                            clearable />
                        </td> -->
                        <td>${{ service.price?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>{{ service.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                        <td>${{ (service.quantity * service.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- SUBTOTAL -->
            <div class="*:px-4">
                <p class="font-bold bg-[#AEAAAA] text-[#52433F]">Términos y condiciones</p>
                <p class="font-bold bg-[#EDEDED] text-[#8C8489]">Fecha de expiración de cotización: <span class="ml-4">{{ formatShortDate(quote.expired_date) }}</span></p>
                <div class="font-bold  text-[#8C8489] flex">
                    <p class="flex w-[65%]">
                        <span>Condiciones de pago:</span> 
                        <span class="ml-4">{{ quote.payment_conditions }}</span>
                    </p>
                    <p class="flex space-x-10 w-[35%]">
                        <span class="ml-4 w-20">SUBTOTAL</span>
                        <span class="text-black">${{ (quote.total - (quote.total * 0.16))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} + IVA</span>
                    </p>
                </div>

                <!-- IVA -->
                <div class="font-bold bg-[#EDEDED] text-[#8C8489] flex">
                    <div class="w-[65%]">
                        <p class="text-black">Tiempos de entrega: INMEDIATO. El tiempo de entrega de los productos bajo pedido se describe en tabla.</p> 
                        <!-- <span class="text-black">Productos bajo pedido con el ícono:<i class="fa-regular fa-circle-dot text-red-500 text-xs ml-1"></i></span>  -->
                    </div>
                    <p class="flex space-x-10 w-[35%]">
                        <span class="ml-4 w-20">IVA 16%</span>
                        <span class="text-black font-bold">${{ (quote.total * 0.16)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                    </p>
                </div>

                <!-- total  -->
                <div class="font-bold flex">
                    <p class="flex w-[65%]">
                        <span v-if="quote.notes" class="text-black">Nota: {{ quote.notes }}</span> 
                    </p>
                    <div class="w-[35%] py-2 bg-black">
                        <p class="flex space-x-8 *:text-[#FFC000] ml-4" v-if="quote.has_discount">
                            <span class="w-20">{{quote.is_percentage_discount ? 'Desc. (' + quote.discount + '%)' : 'Desc.' }}</span>
                            <span class="inline-block w-20" v-if="quote.is_percentage_discount">-${{ (quote.discount * quote.total * 0.01)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            <span class="inline-block w-20" v-else>-${{ quote.discount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                        <p class="flex space-x-10 *:text-[#FFC000]">
                            <span class="ml-4 w-20">TOTAL</span>
                            <span class="inline-block w-20" v-if="quote.has_discount && quote.is_percentage_discount">${{ (quote.total - (quote.discount * quote.total * 0.01))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            <span class="inline-block w-20" v-else-if="quote.has_discount">${{ (quote.total - quote.discount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            <span class="inline-block w-20" v-else>${{ quote.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cantidad con letra -->
            <p v-if="quote.has_discount && quote.is_percentage_discount" class="text-lg text-center font-bold mt-3">{{ numberToWords((quote.total - (quote.discount * quote.total * 0.01))) }}</p>
            <p v-else-if="quote.has_discount" class="text-lg text-center font-bold mt-3">{{ numberToWords((quote.total - quote.discount)) }}</p>
            <p v-else class="text-lg text-center font-bold mt-3">{{ numberToWords(quote.total) }}</p>
            <p class="text-lg text-center font-bold mt-3 text-[#ebbe38]">SI USTED TIENE ALGUNA DUDA SOBRE ESTA COTIZACIÓN, POR FAVOR PÓNGASE EN CONTACTO CON NOSOTROS, GRACIAS POR SU PREFERENCIA.</p>

        </main> 
    </div>

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
        printing: false, //cabia el tamaño de la pantalla para ajustar el texto
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
    formatShortDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    formatLongDate(dateString) {
        const date = parseISO(dateString);

        const day = format(date, 'd', { locale: es });
        const month = format(date, 'MMMM', { locale: es });
        const year = format(date, 'yyyy', { locale: es });

        return `Día ${day} de ${month} del año ${year}`;
    },
    handleEditDescription() {
        this.indexEdit = null;
    },
    print() {
      this.showEditIcon = false;
      this.printing = true;
      setTimeout(() => {
        window.print();
      }, 100);
    },
    handleAfterPrint() {
      this.showEditIcon = true;
      this.printing = false;
    },
    numberToWords(amount) {
        const unidades = [
            '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
        ];
        const decenas = [
            '', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
        ];
        const centenas = [
            '', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
        ];

        function convertGroup(n) {
            if (n === '100') return 'cien';
            let output = '';
            if (n[0] !== '0') output += centenas[parseInt(n[0])] + ' ';
            if (n.substring(1) < 10) output += unidades[parseInt(n.substring(1))];
            else if (n[1] === '0') output += unidades[parseInt(n[2])];
            else output += decenas[parseInt(n[1])] + ' y ' + unidades[parseInt(n[2])];
            return output.trim();
        }

        function numberToSpanish(n) {
            const number = parseFloat(n).toFixed(2).split('.');
            const integerPart = number[0];
            const decimalPart = number[1];

            let output = '';
            if (integerPart === '0') {
                output = 'cero';
            } else {
                const groups = [
                    '', 'mil', 'millón', 'mil millones', 'billón', 'mil billones'
                ];
                const groupCount = Math.ceil(integerPart.length / 3);

                for (let i = 0; i < groupCount; i++) {
                    const start = integerPart.length - (i + 1) * 3;
                    const end = integerPart.length - i * 3;
                    const group = integerPart.substring(start < 0 ? 0 : start, end);
                    const groupInWords = convertGroup(group.padStart(3, '0'));
                    if (groupInWords !== '') output = groupInWords + ' ' + groups[i] + ' ' + output;
                }
            }

            output = output.trim();
            return output.charAt(0).toUpperCase() + output.slice(1);
        }

        const [integerPart, decimalPart] = parseFloat(amount).toFixed(2).split('.');
        const integerPartInWords = numberToSpanish(integerPart);
        const centavos = `${decimalPart}/100`;

        return `${integerPartInWords} pesos ${centavos} M.N.`;
    }
},
mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
},
beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
}
}
</script>

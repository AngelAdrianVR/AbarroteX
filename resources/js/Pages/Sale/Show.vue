<template>
    <AppLayout :title="'Venta del día'">
        <div class="md:px-10 px-2 py-7 text-sm md:text-base">
            <div class="flex justify-between items-center">
                <Back />
                <div class="flex items-center space-x-2">
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                        title="¿Continuar?" @confirm="print(sale)">
                        <template #reference>
                            <i @click.stop
                                class="fa-solid fa-print text-primary hover:bg-gray-200 cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                        </template>
                    </el-popconfirm>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                        title="¿Continuar?" @confirm="deleteItem(Object.values(day_sales)[0].sales[0]?.id)">
                        <template #reference>
                            <i @click.stop
                                class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                </div>
            </div>

            <!-- Información de la venta -->
            <div class="mt-7 lg:mx-16 text-gray99 text-sm">
                <p>Total de productos vendidos: <span class="font-thin ml-2 text-gray37">{{
                    Object.values(day_sales)[0].total_quantity }}</span></p>
                <p>Fecha de venta: <span class="font-thin ml-2 text-gray37">{{
                    formatDate(Object.keys(day_sales)[0]) }}</span></p>
                <p>Total de venta: <span class="font-black ml-2 text-gray37">${{
                    Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }}</span></p>
            </div>

            <!-- Productos -->
            <section class="flex flex-col space-y-5 lg:mx-16 text-sm mt-10">
                <article v-for="(group, index) in getGroupedSlaes" :key="index"
                    class="border border-grayD9 *:px-5 *:py-1">
                    <div class="border-b border-grayD9 text-end">
                        <p class="text-gray99">Hora de la venta: <span class="text-gray37">{{ index }}</span></p>
                    </div>
                    <div class="border-b border-grayD9">
                        <table class="w-full">
                            <thead>
                                <tr class="*:px-2">
                                    <th class="w-[30%] text-start">Producto</th>
                                    <th class="w-[30%] text-start">Precio</th>
                                    <th class="w-[30%] text-start">Cantidad</th>
                                    <th class="w-[10%] text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(sale, index2) in group" :key="index2" class="*:px-2">
                                    <td>
                                        <button v-if="sale.product_id" @click="viewProduct(sale)"
                                            class="text-primary underline">
                                            {{ sale.product_name }}
                                        </button>
                                        <el-tooltip v-else content="El producto fue eliminado" placement="right">
                                            <span class="text-red-700">{{ sale.product_name }}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>${{ sale.current_price }}</td>
                                    <td>{{ sale.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                    <td class="text-end">${{ (sale.current_price *
                                        sale.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <!--*** descomentar cuado se guarden los descuentos sobre la venta total ***-->
                        <!-- <div class="text-gray99 flex items-center justify-end space-x-2 *:w-12 px-3">
                            <span>Subtotal:</span>
                            <span class="text-gray37">$</span>
                            <span class="text-gray37">{{ Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</span>
                        </div>
                        <div class="text-gray99 flex items-center justify-end space-x-2 *:w-12 px-3">
                            <span>Descuento:</span>
                            <span class="text-gray37">$</span>
                            <span class="text-gray37">21.50</span>
                        </div> -->
                        <div class="text-gray37 font-black flex items-center justify-end space-x-2 *:w-12 px-2">
                            <span class="text-start">Total:</span>
                            <span>$</span>
                            <span>{{ calcTotal(group).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            sale_id: this.day_sales.id,
            amount: null,
            notes: null,
            date: null,
        });

        return {
            form,
            paymentModal: false,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        Back
    },
    props: {
        day_sales: Object,
    },
    computed: {
        getGroupedSlaes() {
            const salesGroupedByHour = Object.values(this.day_sales)[0].sales.reduce((groupedSales, sale) => {
                // Obtener la hora de creación del elemento
                const createdAt = new Date(sale.created_at);
                const hour = createdAt.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

                // Verificar si ya existe un array para esta hora, de lo contrario, crear uno nuevo
                if (!groupedSales[hour]) {
                    groupedSales[hour] = [];
                }

                // Agregar el elemento al array correspondiente a la hora de creación
                groupedSales[hour].push(sale);

                return groupedSales;
            }, {});

            // Resultado: objeto donde las claves son las horas y los valores son arrays de ventas
            return salesGroupedByHour;
        },
    },
    methods: {
        calcTotal(sales) {
            return sales.reduce((accumulator, currentValue) => {
                const subtotal = currentValue.quantity * currentValue.current_price;
                return accumulator + subtotal;
            }, 0);
        },
        async deleteItem(saleId) {
            try {
                const response = await axios.delete(route('sales.destroy', saleId));
                if (response.status == 200) {

                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la venta del día',
                        type: 'success',
                        position: 'bottom-right',
                    });

                    this.$inertia.get(route('sales.index'));
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Error',
                    message: 'No se pudo eliminar la venta. Inténte más tarde',
                    type: 'error',
                    position: 'bottom-right',
                });
            }
        },
        formatDate(dateString) {
            const months = {
                'January': 'Enero',
                'February': 'Febrero',
                'March': 'Marzo',
                'April': 'Abril',
                'May': 'Mayo',
                'June': 'Junio',
                'July': 'Julio',
                'August': 'Agosto',
                'September': 'Septiembre',
                'October': 'Octubre',
                'November': 'Noviembre',
                'December': 'Diciembre'
            };

            const [day, month, year] = dateString.split('-');
            return `${day} ${months[month]}, ${year}`;
        },
        viewProduct(product) {
            if (product.is_global_product) {
                window.open(route('global-product-store.show', product.product_id), '_blank');
            } else {
                window.open(route('products.show', product.product_id), '_blank');
            }
        },
        print(sale) {
            window.open(route('sales.print-ticket', sale.id), '_blank');
        }
    },
}
</script>
<template>
    <AppLayout :title="'Venta del día'">
        <div class="md:px-10 px-2 py-7 text-sm md:text-base">
            <div class="flex justify-between items-center">
                <Back />
                <div class="flex items-center space-x-2">
                     <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="print(sale)">
                        <template #reference>
                            <i @click.stop class="fa-solid fa-print text-primary hover:bg-gray-200 cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                        </template>
                    </el-popconfirm>
                     <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(Object.values(day_sales)[0].sales[0]?.id)">
                        <template #reference>
                            <i @click.stop class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                </div>
            </div>

            <!-- Información de la venta -->
            <div class="mt-7 lg:mx-16">
                <p class="font-bold px-2">Fecha de venta: <span class="font-thin ml-2 text-gray-600">{{ formatDate(Object.keys(day_sales)[0]) }}</span></p>
                <p class="font-bold px-2">Total de productos vendidos: <span class="font-thin ml-2 text-gray-600">{{ Object.values(day_sales)[0].total_quantity }}</span></p>
                <p class="font-bold px-2">Total de venta: <span class="!font-thin ml-2 text-gray-600">${{ Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>

            </div>

                <!-- Productos -->
                <!-- detalle de productos -->
                <div class="grid grid-cols-3 lg:ml-16 mr-3 self-start mt-9">
                    <p class="font-bold">Producto</p>
                    <p class="font-bold">Cantidad</p>
                    <p class="font-bold ml-8">Total</p>

                    <div class="mt-2">
                        <p @click="viewProduct(product.product_id)" class="text-primary underline cursor-pointer text-sm" v-for="product in Object.values(day_sales)[0].sales" :key="product">{{ product.name }}</p>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm" v-for="product in Object.values(day_sales)[0].sales" :key="product">{{ product.quantity }}</p>
                    </div>
                    <div class="mt-2 ml-8">
                        <p class="text-sm" v-for="product in Object.values(day_sales)[0].sales" :key="product">${{ (product.quantity * product.current_price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    </div>
                    <div class="border-b border-primary w-28 col-start-3 my-3"></div>
                    <p class="col-start-3 text-sm font-bold">Total: <span class="ml-2">${{ Object.values(day_sales)[0].total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                </div>
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
        client_id: this.day_sales.client?.id,
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
components:{
AppLayout,
PrimaryButton,
ThirthButton,
CancelButton,
InputLabel,
Back
},
props:{
day_sales: Object,
},
methods:{
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
        return `${day}-${months[month]}-${year}`;
    },
    viewProduct(productId) {
        window.open(route('products.show', productId), '_blank');
    },
    print(sale) {
        window.open(route('sales.print-ticket', sale.id), '_blank');
        // this.$inertia.get(route('sales.print-ticket', sale.id));
    }
},
}
</script>
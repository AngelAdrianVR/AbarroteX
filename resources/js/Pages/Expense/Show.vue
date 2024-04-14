<template>
    <AppLayout :title="'Egresos del día'">
        <div class="md:px-10 px-2 py-7 text-sm md:text-base">
            <div class="flex justify-between items-center">
                <Back />
                <div class="flex items-center space-x-2">
                     <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="print(expenses[0].id)">
                        <template #reference>
                            <i @click.stop class="fa-solid fa-print text-primary hover:bg-gray-200 cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                        </template>
                    </el-popconfirm>
                     <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(expenses[0].id)">
                        <template #reference>
                            <i @click.stop class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                </div>
            </div>

            <!-- Información de egreso -->
            <div class="mt-7 lg:mx-16">
                <p class="font-bold px-2">Fecha: <span class="font-thin ml-2 text-gray-600">{{ expenses[0].created_at }}</span></p>
                <p class="font-bold px-2">Total de movimientos: <span class="font-thin ml-2 text-gray-600">{{ expenses.length }}</span></p>
                <p class="font-bold px-2">Egreso total: <span class="!font-thin ml-2 text-gray-600">${{ totalExpenses().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
            </div>

                <!-- Productos -->
                <!-- detalle de productos -->
                <div class="grid grid-cols-4 lg:ml-16 mr-3 self-start mt-9">
                    <p class="font-bold col-span-2">Concepto</p>
                    <p class="font-bold">Cantidad</p>
                    <p class="font-bold ml-8">Total</p>

                    <div class="mt-2 col-span-2">
                        <p class="text-primary text-sm" v-for="expense in expenses" :key="expense">{{ expense.concept }}</p>
                    </div>
                    <div class="mt-2">
                        <p class="text-sm" v-for="expense in expenses" :key="expense">{{ expense.quantity }}</p>
                    </div>
                    <div class="mt-2 ml-8">
                        <p class="text-sm" v-for="expense in expenses" :key="expense">${{ (expense.quantity * expense.current_price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    </div>
                    <div class="border-b border-primary w-28 col-start-4 my-3"></div>
                    <p class="col-start-4 text-sm font-bold">Total: <span class="ml-2">${{ totalExpenses().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
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
        amount: null,
        notes: null,
        date: null,
    });

    return {
        form,
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
expenses: Object,
},
methods:{
    async deleteItem(expenseId) {
        try {
            const response = await axios.delete(route('expenses.destroy', expenseId));
            if (response.status == 200) {

                this.$notify({
                    title: 'Correcto',
                    message: 'Se han eliminado los egresos del día',
                    type: 'success',
                    position: 'top-right',
            });

            this.$inertia.get(route('expenses.index'));
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: 'Error',
                message: 'No se pudo eliminar el registro de egresos. Inténte más tarde',
                type: 'error',
                position: 'top-right',
            });
        }
    },
    print(expenseId) {
        window.open(route('expenses.print-expenses', expenseId), '_blank');
    },
    totalExpenses() {
        let total = 0;
        this.expenses.forEach(expense => {
            total += expense.quantity * expense.current_price;
        });
        return total;
    }
},
}
</script>
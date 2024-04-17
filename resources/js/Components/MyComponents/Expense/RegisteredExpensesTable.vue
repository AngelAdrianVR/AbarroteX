<template>
    <div v-if="Object.keys(expenses)?.length" class="mx-auto text-[11px] md:text-sm overflow-auto">
        <div class="text-center md:text-base flex items-center space-x-4 mb-2">
            <div class="font-bold w-[20%]">Fecha</div>
            <div class="font-bold w-[20%]">Movimientos</div>
            <div class="font-bold w-[20%] text-center">Total</div>
            <div class="w-[30%]"></div>
        </div>
        <div>
            <div v-for="(expense, index) in expenses" :key="expense.id" class="*:p-3 h-12 cursor-pointer flex items-center space-x-4 border rounded-full mb-2 hover:border-primary" 
            @click="$inertia.get(route('expenses.show', expense.expenses[0]?.id))">
                <div class="w-[20%] text-center rounded-l-full">{{  formatDate(index) }}</div>
                <div class="w-[20%] text-center rounded-l-full">{{ expense.expenses.length }}</div>
                <div class="w-[20%] text-center">${{ expense.total_expense?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</div>
                <div class="rounded-r-full w-[30%] text-right">
                    <!-- <i @click.stop="$inertia.get(route('expenses.edit', expense.id))" class="fa-solid fa-pencil text-primary cursor-pointer hover:bg-gray-200 rounded-full mr-1 p-2"></i> -->
                    
                    <!-- Se manda el id del primer producto vendido para eliminar todas las ventas de la misma fecha. aqui cada venta es cada producto individual -->
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(expense.expenses[0]?.id)">
                        <template #reference>
                            <i @click.stop class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="print(expense.expenses[0]?.id)">
                        <template #reference>
                            <i @click.stop class="fa-solid fa-print text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                        </template>
                    </el-popconfirm>
                </div>
            </div>
        </div>
    </div>
    <el-empty v-else description="No hay ventas registradas" />
</template>

<script>
import { ElNotification } from 'element-plus'
import axios from 'axios';

export default {
data() {
    return {
        
    };
},
components:{

},
props:{
expenses: Object
},
methods:{
    async deleteItem(expenseId) {
        try {
            const response = await axios.delete(route('expenses.destroy', expenseId));
            if (response.status == 200) {
                ElNotification({
                title: 'Correcto',
                message: 'Se han eliminado los egresos del día',
                type: 'success',
                position: 'bottom-right',
            });
            location.reload();
            }
        } catch (error) {
            console.log(error);
            ElNotification({
                title: 'Error',
                message: 'No se pudo eliminar. Inténte más tarde',
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
    print(expenseId) {
        window.open(route('expenses.print-expenses', expenseId), '_blank');
    }
}
}
</script>

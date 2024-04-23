<template>
    <section v-if="movements.length" class="mt-7 text-sm w-full overflow-auto">
        <table>
            <tr class="font-bold *:pb-3 *:px-2">
                <td class="w-14">Fecha</td>
                <td class="w-40 truncate">Efectivo al iniciar</td>
                <td class="w-40">Efectivo esperado</td>
                <td class="w-36">Recuento manual</td>
                <td class="w-36">Diferencia</td>
                <td></td>
            </tr>
            <tr class="*:px-2 cursor-default hover:text-primary" v-for="item in movements" :key="item">
                <td>{{ formatDate(item.created_at) }}</td>
                <td>${{ item.started_cash }}</td>
                <td>${{ item.expected_cash }}</td>
                <td>${{ item.counted_cash }}</td>
                <td>${{ item.difference }}</td>
            </tr>
        </table>
    </section>
    <p class="text-sm text-center text-gray-400 mt-5" v-else>No hay movimientos registrados</p>
</template>

<script>
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {

    }
},
components:{},
props:{
movements: Object    
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
}
}
</script>

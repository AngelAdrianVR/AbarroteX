<template>
    <div class="flex justify-end space-x-3">
        <ThirthButton class="!rounded-md"><i class="fa-solid fa-arrow-down mr-3"></i>Ingresar efectivo</ThirthButton>
        <ThirthButton class="!rounded-md"><i class="fa-solid fa-arrow-up mr-3"></i>Retirar efectivo</ThirthButton>
        <PrimaryButton>Hacer corte de caja</PrimaryButton>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';

export default {
data() {
    return {

    }
},
components:{
PrimaryButton,
ThirthButton
},
props:{

},
methods:{

}
}
</script>
<template>
    <AppLayout title="Nueva caja registradora">
        <div class="px-3 md:px-10 py-7">
            <Back />

                <form v-if="total_cash_registers < 2" @submit.prevent="store"
                    class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                    <h1 class="font-bold ml-2 col-span-full">Crear caja</h1>
                    <p class="ml-2 col-span-full text-gray99 text-xs">Puedes crear hasta 4 cajas. Si requieres más puedes adquirirlo por un costo extra. 
                        <a class="text-primary" target="_blank" :href="route('profile.show')">Da clic aquí para más información</a></p>
                    <div class="mt-3 col-span-2">
                        <InputLabel value="Nombre de la caja" class="ml-3 mb-1" />
                        <el-input v-model="form.name" placeholder="Ej. Caja 1" :maxlength="100" clearable />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Cantidad inicial de dinero" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.started_cash" placeholder="ingresa el monto inicial"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.started_cash" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Monto máximo permitido" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.max_cash" placeholder="Ej, $5,000"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.max_cash" />
                    </div>

                    <div class="col-span-2 text-right mt-5">
                        <PrimaryButton :disabled="form.processing">Crear caja</PrimaryButton>
                    </div>
                </form>

                <section class="mt-10" v-else>
                    <h1 class="font-bold text-5xl text-center mb-5">Lo sentimos :(</h1>
                    <p class="text-xl text-center">Has alcanzado el límite de cajas (2) de tu plan contratado.</p>
                    <p class="text-xl text-center">Para incrementar el límite de cajas es necesario cambiar tu plan. Para cambiarlo da clic en el siguiente botón.</p>
                    <div class="flex justify-center mt-5">
                        <PrimaryButton @click="$inertia.get(route('profile.show'))" :disabled="form.processing">Cambiar de plan</PrimaryButton>
                    </div>
                </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: null,
        started_cash: null,
        max_cash: null,
    });

    return {
        form,
    }
},
components:{
AppLayout,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{
total_cash_registers: Number
},
methods:{
    store() {
        this.form.post(route("cash-registers.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado una nueva caja",
                    type: "success",
                });
            },
        });
    },
}
}
</script>

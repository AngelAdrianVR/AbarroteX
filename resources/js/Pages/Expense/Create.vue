<template>
    <AppLayout title="Nuevo egreso">
        <div class="md:px-10 lg-px-1 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-[80%] mx-auto mt-7 lg:grid lg:grid-cols-2 gap-5">
                <h1 class="font-bold ml-2 col-span-full">Agregar egreso</h1>

                <div class="border border-grayD9 p-4 rounded-lg my-5 lg:my-0" v-for="(expense, index) in form.expenses" :key="index">
                    <div v-if="form.expenses.length > 1" class="text-right">
                        <button class="text-red-600 text-xs" @click="removeExpense(index)"><i class="fa-solid fa-x"></i></button>
                    </div>
                    <div class="mt-3">
                        <InputLabel :value="'Concepto del egreso ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-input v-model="expense.concept" required placeholder="Escribe el concepto del egreso" :maxlength="100" clearable />
                        <InputError :message="expense.errors.concept" />
                    </div>

                    <div class="mt-3">
                        <InputLabel :value="'Fecha ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-date-picker
                            v-model="expense.date"
                            type="date"
                            placeholder="Selecciona una fecha"
                            :disabled-date="disabledDate"
                        />
                    </div>

                    <div class="mt-3">
                        <InputLabel :value="'Cantidad ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-input v-model="expense.quantity" required step="0.01" type="number" placeholder="Ingresa la cantidad">
                            <template #prefix>
                                <i class="fa-solid fa-hashtag"></i>
                            </template>
                        </el-input>
                    </div>

                    <div class="mt-3">
                        <InputLabel :value="'Costo por unidad de egreso ' + (index + 1) + '*'" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="expense.current_price" required type="number" step="0.1" placeholder="Ingresa el costo">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="expense.errors.current_price" />
                    </div>
                </div>
                <div @click="addExpense" class="flex justify-center items-center cursor-pointer rounded-md border border-dashed border-primary">
                    <p class="text-primary text-sm my-3">+ Agregar otro egreso</p>
                </div>


                <div class="col-span-full text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Crear</PrimaryButton>
                </div>
            </form>
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
            expenses: [{
                concept: null,
                quantity: null,
                current_price: null,
                date: new Date(),
                errors: {} // Assuming you have errors object for each expense
            }],
        });

        return {
            form,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        InputLabel,
        InputError,
        Back
    },
    props: {

    },
    methods: {
        addExpense() {
            this.form.expenses.push({
                concept: null,
                quantity: null,
                current_price: null,
                date: new Date(),
                errors: {} // Assuming you have errors object for each expense
            });
        },
        removeExpense(index) {
            // Verifica si hay más de un gasto antes de eliminarlo
            if (this.form.expenses.length > 1) {
                this.form.expenses.splice(index, 1); // Elimina el gasto en el índice especificado
            }
        },
        store() {
            this.form.post(route("expenses.store", { expenses: this.form.expenses } ), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha registrado el egreso",
                        type: "success",
                    });
                    this.form.reset();
                },
            });
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() > today.getTime();
        },
    }
}
</script>
<template>
    <AppLayout title="Nuevo gasto">
        <div class="px-3 md:px-10 lg-px-1 py-7">
            <Back :to="route('expenses.index')" />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-[80%] mx-auto mt-7 lg:grid lg:grid-cols-2 gap-5">
                <section class="ml-2 col-span-full flex justify-between items-center">
                    <h1 class="font-bold">Agregar gasto</h1>
                    <el-tooltip v-if="isShowCahsOn"
                        content="Para cambiar de caja, ve al punto de venta, click al botón movimientos de caja > cambiar de caja"
                        placement="top">
                        <p class="text-gray99 text-xs">
                            Efectivo en "{{ cash_register?.name }}":
                            <b class="ml-2">
                                ${{ cash_register?.current_cash.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </b>
                        </p>
                    </el-tooltip>
                </section>
                <p class="text-xs col-span-full">
                    Aqui no se registran las compras de tus productos para la venta. Esta sección es para registrar
                    gastos adicionales
                    como <b>pago de servicios, sueldos, etc.</b> Con esto se lleva un control más detallado de los
                    gastos de tu negocio.
                </p>
                <div class="border border-grayD9 p-4 rounded-lg my-5 lg:my-0" v-for="(expense, index) in form.expenses"
                    :key="index">
                    <div v-if="form.expenses.length > 1" class="text-right">
                        <button type="button" class="text-red-600 text-xs" @click="removeExpense(index)"><i
                                class="fa-solid fa-x"></i></button>
                    </div>
                    <div class="mt-3">
                        <InputLabel :value="'Concepto del gasto ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-input v-model="expense.concept" required :placeholder="placeholders[index]?.concept"
                            :maxlength="100" clearable />
                        <InputError :message="expense.errors.concept" />
                    </div>
                    <div class="mt-3">
                        <InputLabel :value="'Fecha ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-date-picker v-model="expense.date" type="date" placeholder="Selecciona una fecha"
                            :disabled-date="disabledDate" />
                    </div>
                    <!-- <div class="mt-3">
                        <InputLabel :value="'Cantidad ' + (index + 1) + '*'" class="ml-3 mb-1" />
                        <el-input v-model="expense.quantity" required type="text"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')" placeholder="Ej. 1" />
                    </div> -->
                    <div class="mt-3">
                        <InputLabel :value="'Costo total de gasto ' + (index + 1) + '*'" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="expense.current_price" required type="text"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/[^\d.]/g, '')"
                            :placeholder="placeholders[index]?.amount">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="expense.errors.current_price" />
                    </div>
                    <div class="mt-3">
                        <el-checkbox v-model="expense.from_cash_register" :label="getCheckboxLabel(index)" size="small"
                            :disabled="!hasEnoughCash(index)" />
                    </div>
                </div>
                <div @click="addExpense"
                    class="flex justify-center items-center cursor-pointer rounded-md border border-dashed border-primary">
                    <p class="text-primary text-sm my-3">+ Agregar otro gasto</p>
                </div>

                <div class="col-span-full text-right mt-3">
                    <PrimaryButton v-if="!exceededCashAmount()" class="!rounded-full" :disabled="form.processing">Crear
                    </PrimaryButton>
                    <p v-else class="text-xs">
                        Has excedido el monto disponible en caja.
                        <button @click="uncheckAll()" type="button" class="underline text-primary">
                            Click aqui para volver a indicar los gastos que se pagarán con el dinero de caja disponible
                        </button>
                    </p>
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
                quantity: 1,
                current_price: null,
                from_cash_register: false,
                date: new Date(),
                errors: {} // Assuming you have errors object for each expense
            }],
        });

        return {
            form,
            placeholders: [
                {
                    concept: 'Ej. Pago de renta',
                    amount: 'Ej. 3,500',
                },
                {
                    concept: 'Ej. Pago de luz',
                    amount: 'Ej. 600',
                },
                {
                    concept: 'Ej. Pago de agua',
                    amount: 'Ej. 300',
                },
                {
                    concept: 'Ej. Insumos de limpieza para local',
                    amount: 'Ej. 456',
                },
                {
                    concept: 'Ej. Sueldo de empleado',
                    amount: 'Ej. 1,200',
                },
                {
                    concept: 'Ej. Compra de nuevo mostrador',
                    amount: 'Ej. 2,150',
                },
                {
                    concept: 'Ej. Compra de báscula nueva',
                    amount: 'Ej. 4,000',
                },
                {
                    concept: 'Ej. Gastos de mantenimiento',
                    amount: 'Ej. 500',
                },
                {
                    concept: 'Ej. Gastos de reparaciones',
                    amount: 'Ej. 1,790',
                },
                {
                    concept: 'Ej. Gastos de transporte',
                    amount: 'Ej. 100',
                },
                {
                    concept: 'Ej. Pago de impuestos',
                    amount: 'Ej. 335',
                },
            ],
            // mostrar dinero en caja activado
            isShowCahsOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Mostrar dinero en caja')?.value,
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
        cash_register: Object,
    },
    methods: {
        uncheckAll() {
            this.form.expenses.forEach(item => item.from_cash_register = false);
        },
        exceededCashAmount() {
            return this.getTotalFromCashRegister() > this.cash_register.current_cash;
        },
        getCheckboxLabel(index) {
            let label = 'Dinero tomado de caja para el pago';
            if (!this.hasEnoughCash(index)) {
                label += ' (No hay suficiente dinero en caja)';
            }

            return label;
        },
        hasEnoughCash(index) {
            const newAmount = this.form.expenses[index].current_price
                ? parseFloat(this.form.expenses[index].current_price)
                : 0;
            const newTotal = this.getTotalFromCashRegister(index) + newAmount;
            return (this.cash_register.current_cash - newTotal) >= 0;
        },
        getTotalFromCashRegister(index = null) { //si index es nulo,
            const total = this.form.expenses.reduce((acc, expense, currentIndex) => {
                // Si from_cash_register es true y current_price es un número, sumarlo
                let amount = 0
                if (expense.from_cash_register && currentIndex != index) {
                    amount = expense.current_price
                        ? parseFloat(expense.current_price)
                        : 0;
                }
                acc += amount;

                return acc;
            }, 0);

            return total;
        },
        shufflePlaceholdersArray() {
            // Algoritmo de Fisher-Yates para ordenar aleatoriamente el array
            for (let i = this.placeholders.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [this.placeholders[i], this.placeholders[j]] = [this.placeholders[j], this.placeholders[i]];
            }
        },
        addExpense() {
            this.form.expenses.push({
                concept: null,
                quantity: 1,
                current_price: null,
                from_cash_register: false,
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
            this.form.post(route("expenses.store", { expenses: this.form.expenses }), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha registrado el gasto",
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
    },
    mounted() {
        this.shufflePlaceholdersArray();
    }
}
</script>
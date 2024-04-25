<template>
    <Loading v-if="loading" />
    <div v-else class="min-h-32">
        <section class="flex justify-end space-x-3 mt-2">
            <ThirthButton class="!rounded-md"><i class="fa-solid fa-arrow-down mr-3"></i>Ingresar efectivo
            </ThirthButton>
            <ThirthButton class="!rounded-md"><i class="fa-solid fa-arrow-up mr-3"></i>Retirar efectivo</ThirthButton>
            <PrimaryButton>Hacer corte de caja</PrimaryButton>
        </section>
        <section v-if="isMaxCashOn" class="mt-5">
            <form @submit.prevent="update">
                <div>
                    <InputLabel value="Máxima cantidad en caja permitida *" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.max_cash" type="text" placeholder="Ingresa el monto"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\D/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.max_cash" />
                </div>
                <div class="mt-2">
                    <PrimaryButton type="submit" :disabled="form.processing">Guardar</PrimaryButton>
                </div>
            </form>
        </section>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            max_cash: null
        });

        return {
            form,
            cashRegister: null,
            loading: true,

            // monto maximo en caja activado
            isMaxCashOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Aviso de monto máximo en caja')?.value,
        }
    },
    components: {
        PrimaryButton,
        ThirthButton,
        InputLabel,
        InputError,
        Loading,
    },
    methods: {
        update() {
            this.form.put(route('cash-registers.update', this.cashRegister.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "",
                        type: "success",
                    });
                }
            });
        },
        async fetchCashRegister() {
            try {
                this.loading = true;
                const response = await axios.get(route('cash-registers.fetch-cash-register'));
                if (response.status === 200) {
                    this.cashRegister = response.data.item;
                    this.form.max_cash = this.cashRegister.max_cash;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchCashRegister();
    }

}
</script>
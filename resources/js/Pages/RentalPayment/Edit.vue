<template>
    <AppLayout title="Editar pago de renta">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 col-span-full mb-4">Editar pago</h1>
                <div>
                    <InputLabel value="Folio de renta *" />
                    <el-select filterable v-model="form.rental_id" placeholder="Selecciona el folio de la renta"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in rentals" :key="item.id" :label="item.folio" :value="item.id" />
                    </el-select>
                    <InputError :message="form.errors.rental_id" />
                </div>
                <div>
                    <InputLabel value="Monto a pagar *" />
                    <el-input v-model="form.amount" placeholder="Ej. $400"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.amount" />
                </div>
                <div>
                    <InputLabel value="Fecha de pago *" />
                    <el-date-picker v-model="form.paid_date" type="date" class="!w-full" placeholder="día/mes/año" />
                </div>
                <div>
                    <InputLabel value="Concepto de pago" />
                    <el-input v-model="form.concept" placeholder="Ej. pago de renta semanal" />
                    <InputError :message="form.errors.concept" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Comentarios" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe tus comentarios" :maxlength="255" show-word-limit clearable />
                    <InputError :message="form.errors.notes" />
                </div>
                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar cambios
                    </PrimaryButton>
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
            rental_id: this.payment.rental_id,
            amount: this.payment.amount,
            paid_date: this.payment.paid_date,
            concept: this.payment.concept,
            notes: this.payment.notes,
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
        payment: Object,
        rentals: Array,
    },
    methods: {
        encodeId(id) {
            const encodedId = btoa(id.toString());
            this.encodedId = encodedId;
        },
        async update() {
            try {
                this.form.put(route("rental-payments.update", this.payment.id), {
                    onSuccess: async () => {
                        this.$notify({
                            title: "Correcto",
                            message: "",
                            type: "success",
                        });
                    },
                });
            } catch (error) {
                console.error(error);
            }
        },
    },
    mounted() {

    }
}
</script>
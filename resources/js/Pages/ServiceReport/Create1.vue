<template>
    <AppLayout title="Nueva orden de servicio">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-2/3 xl:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                <div class="flex items-center justify-between col-span-full mb-3">
                    <h1 class="font-bold ml-2 col-span-full">Crear orden de servicio</h1>
                    <div class="text-sm text-right">
                        <p>Orden de servicio</p>
                        <p>No. {{ String(folio).padStart(4, '0') }}</p>
                    </div>
                </div>
                <div class="col-span-full">
                    <InputLabel value="Fecha del servicio*" />
                    <el-date-picker v-model="form.service_date" type="date" class="!w-1/2"
                        placeholder="Selecciona una fecha" format="DD MMM, YY" value-format="YYYY-MM-DD" />
                    <InputError :message="form.errors.service_date" />
                </div>

                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Datos del cliente</h1>

                <div>
                    <InputLabel value="Nombre del cliente*" />
                    <el-input v-model="form.client_name" placeholder="Ej. Carlos Pérez" clearable />
                    <InputError :message="form.errors.client_name" />
                </div>
                <div>
                    <InputLabel value="Teléfono*" />
                    <el-input v-model="form.phone_number"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                        placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone_number" />
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Datos del equipo</h1>
                <div>
                    <InputLabel value="Marca" />
                    <el-input v-model="form.product_details.brand" placeholder="Ej. Samsung" clearable />
                    <InputError :message="form.errors['product_details.brand']" />
                </div>
                <div>
                    <InputLabel value="Modelo" />
                    <el-input v-model="form.product_details.model" placeholder="Ej. Galaxy s21" clearable />
                    <InputError :message="form.errors['product_details.model']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="IMEI" />
                    <el-input v-model="form.product_details.imei" class="!w-1/2" placeholder="Escribe el códio IMEI del equipo" clearable />
                    <InputError :message="form.errors['product_details.imei']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Estado previo y características del equipo*" />
                    <el-input v-model="form.product_details.description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el estado del equipo" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors['product_details.description']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Servicios a realizar*" />
                    <el-input v-model="form.service_description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el trabajo a realizar" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.service_description" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Comentarios adicionales" />
                    <el-input v-model="form.observations" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Escribe comentarios que consideres necesarios" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.observations" />
                </div>
                
                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Refacciones</h1>

                <div class="col-span-full">
                    <InputLabel value="Servicio realizado" />
                    <el-input v-model="form.description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe lo que se realizo en el servicio" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.description" />
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full">Refacciones referencias</h1>
                <article v-for="(item, index) in form.spare_parts" :key="index" class="col-span-full">
                    <div class="flex items-center space-x-3">
                        <div class="w-[35%]">
                            <InputLabel v-if="index == 0" value="No. De Parte *" />
                            <el-select v-model="item.product_id" @change="handleChangeProduct(index)" placeholder="Busca producto">
                                <el-option v-for="(product, index) in products" :key="index"
                                    :label="`${product.name} (${product.code ?? 'sin No. de parte'})`"
                                    :value="product.id">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray37">{{ product.name }}</span>
                                        <span class="text-gray9A text-xs">{{ product.code ?? 'sin No. de parte'
                                            }}</span>
                                    </div>
                                </el-option>
                            </el-select>
                        </div>
                        <div class="w-[47%]">
                            <InputLabel v-if="index == 0" value="Descripción *" />
                            <el-input v-model="item.description" placeholder="..." clearable />
                        </div>
                        <div class="w-[11%]">
                            <InputLabel v-if="index == 0" value="Cantidad *" />
                            <el-input v-model="item.quantity" :placeholder="`Ej. ${index + 1}`" clearable />
                        </div>
                        <div :class="index == 0 ? '!mt-5' : null">
                            <el-popconfirm v-if="form.spare_parts.length > 1" confirm-button-text="Si"
                                cancel-button-text="No" class="w-[5%]" icon-color="#373737" :title="'¿Desea eliminar?'"
                                @confirm="deleteSparePart(index)">
                                <template #reference>
                                    <button type="button"
                                        class="size-6 flex items-center justify-center rounded-full">
                                        <i class="fa-regular fa-trash-can text-sm text-primary"></i>
                                    </button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </div>
                </article>
                <div class="col-span-full text-sm">
                    <button @click="addSparePart" type="button" class="text-primary">
                        + Agregar refacción
                    </button>
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full">Observaciones</h1>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full">Registro del servicio</h1>
                <div>
                    <InputLabel value="Responsable del servicio*" />
                    <el-input v-model="form.technician_name" placeholder="Ej. Luis García" clearable />
                    <InputError :message="form.errors.technician_name" />
                </div>
                <div>
                    <InputLabel value="Persona que recibio el servicio*" />
                    <el-input v-model="form.receiver_name" placeholder="Ej. Ernesto Ramírez" clearable />
                    <InputError :message="form.errors.receiver_name" />
                </div>
                <div>
                    <InputLabel value="Costo del servicio" />
                    <el-input
                        v-model="form.service_cost"
                        placeholder="Ej. 2,500"
                        clearable
                        :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
                        >
                        <template #append>
                            $
                        </template>
                    </el-input>
                    <InputError :message="form.errors.service_cost" />
                </div>
                <div class="col-span-full text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear reporte
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from "date-fns";

export default {
    data() {
        const form = useForm({
            service_date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto,
            client_name: null,
            client_department: null,
            product_details: {
                brand: null,
                model: null,
                serie: null,
                capacity: null,
                worked_hours: null,
                maintenance_type: null,
                charge_hours: null,
                oil_type: null,
                chiller_type: null,
                voltage: null,
                motor_brand: null,
                fan_brand: null,
                fan_hp: null,
            },
            spare_parts: [
                {
                    product_id: null,
                    code: null,
                    name: null,
                    description: null,
                    quantity: null,
                },
            ],
            observations: null,
            technician_name: this.$page.props.auth.user.name,
            receiver_name: null,
            description: null,
            service_cost: null,
        });

        return {
            form,
        }
    },
    components: {
        AppLayout,
        InputFilePreview,
        PrimaryButton,
        InputLabel,
        InputError,
        Back
    },
    props: {
        products: Array,
        folio: Number
    },
    methods: {
        store() {
            this.form.post(route("service-reports.store"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "",
                        type: "success",
                    });
                },
            });
        },
        handleChangeProduct(index) {
            const productId = this.form.spare_parts[index].product_id;
            const product = this.products.find(i => i.id == productId);
            const description = product?.description ?? 'Sin descripción';

            this.form.spare_parts[index].description = description;
            this.form.spare_parts[index].code = product.code;
            this.form.spare_parts[index].name = product.name;
        },
        deleteSparePart(index) {
            this.form.spare_parts.splice(index, 1);
        },
        addSparePart() {
            const newSparePart = {
                product_id: null,
                code: null,
                name: null,
                description: null,
                quantity: null,
            }

            this.form.spare_parts.push(newSparePart);
        },
    }
}
</script>

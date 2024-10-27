<template>
    <AppLayout title="Nuevo reporte">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-3">
                <h1 class="font-bold ml-2 col-span-full">Reporte de servicio</h1>
                <div>
                    <InputLabel value="Fecha del servicio*" />
                    <el-date-picker v-model="form.service_date" type="date" class="!w-full"
                        placeholder="Selecciona una fecha" format="DD MMM, YY" value-format="YYYY-MM-DD" />
                    <InputError :message="form.errors.service_date" />
                </div>
                <div>
                    <InputLabel value="Persona que solicito el servicio" />
                    <el-input v-model="form.client_name" placeholder="Ej. Carlos Pérez" clearable />
                    <InputError :message="form.errors.client_name" />
                </div>
                <div>
                    <InputLabel value="Departamento" />
                    <el-input v-model="form.client_department" placeholder="Ej. Mantenimiento" clearable />
                    <InputError :message="form.errors.client_department" />
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full">Datos del equipo</h1>
                <div>
                    <InputLabel value="Marca" />
                    <el-input v-model="form.product_details.brand" placeholder="Ej. Altas Copco" clearable />
                    <InputError :message="form.errors['product_details.brand']" />
                </div>
                <div>
                    <InputLabel value="Modelo" />
                    <el-input v-model="form.product_details.model" placeholder="Ej. GA 55 VSD" clearable />
                    <InputError :message="form.errors['product_details.model']" />
                </div>
                <div>
                    <InputLabel value="Serie" />
                    <el-input v-model="form.product_details.serie" placeholder="Ej. AC-2023101-0453" clearable />
                    <InputError :message="form.errors['product_details.serie']" />
                </div>
                <div>
                    <InputLabel value="Capacidad" />
                    <el-input v-model="form.product_details.capacity" placeholder="Ej. 55 kW (~75 HP)" clearable />
                    <InputError :message="form.errors['product_details.capacity']" />
                </div>
                <div>
                    <InputLabel value="Horas de trabajo" />
                    <el-input v-model="form.product_details.worked_hours" placeholder="Ej. 12,340"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            Hrs
                        </template>
                    </el-input>
                    <InputError :message="form.errors['product_details.worked_hours']" />
                </div>
                <div>
                    <InputLabel value="Tipo de mantenimiento" />
                    <el-input v-model="form.product_details.maintenance_type" placeholder="Ej. Preventivo" clearable />
                    <InputError :message="form.errors['product_details.maintenance_type']" />
                </div>
                <div>
                    <InputLabel value="Horas de carga" />
                    <el-input v-model="form.product_details.charge_hours" placeholder="Ej. 8,200"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            Hrs
                        </template>
                    </el-input>
                    <InputError :message="form.errors['product_details.charge_hours']" />
                </div>
                <div>
                    <InputLabel value="Tipo de aceite" />
                    <el-input v-model="form.product_details.oil_type" placeholder="Ej. Aceite sintético ISO VG 46"
                        clearable />
                    <InputError :message="form.errors['product_details.oil_type']" />
                </div>
                <div>
                    <InputLabel value="Sistema de enfriamiento" />
                    <el-input v-model="form.product_details.chiler_type" placeholder="Ej. Enfriamiento por aire"
                        clearable />
                    <InputError :message="form.errors['product_details.chiler_type']" />
                </div>
                <div>
                    <InputLabel value="Voltaje" />
                    <el-input v-model="form.product_details.voltage" placeholder="Ej. 380 V trifásico" clearable />
                    <InputError :message="form.errors['product_details.voltage']" />
                </div>
                <div>
                    <InputLabel value="Marca motor principal" />
                    <el-input v-model="form.product_details.motor_brand" placeholder="Ej. Siemens" clearable />
                    <InputError :message="form.errors['product_details.motor_brand']" />
                </div>
                <div>
                    <InputLabel value="Marca motor ventilador" />
                    <el-input v-model="form.product_details.fan_brand" placeholder="Ej. WEG" clearable />
                    <InputError :message="form.errors['product_details.fan_brand']" />
                </div>
                <div>
                    <InputLabel value="HP en motor ventilador" />
                    <el-input v-model="form.product_details.fan_hp" placeholder="Ej. 2"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            HP
                        </template>
                    </el-input>
                    <InputError :message="form.errors['product_details.fan_hp']" />
                </div>
                <h1 class="font-semibold text-gray37 ml-2 col-span-full">Servicio</h1>
                <div class="col-span-full">
                    <InputLabel value="Servicio realizado" />
                    <el-input v-model="form.desciption" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe lo que se realizo en el servicio" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.desciption" />
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
                            <!-- <InputError :message="form.errors[`${index}.color`]" /> -->
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
                <div>
                    <InputLabel value="Voltaje de placa" />
                    <el-input v-model="form.observations.plate_voltage" placeholder="Ej. 220"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            V
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.plate_voltage']" />
                </div>
                <div>
                    <InputLabel value="Amperaje de placa" />
                    <el-input v-model="form.observations.plate_current" placeholder="Ej. 10"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            A
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.plate_current']" />
                </div>
                <div>
                    <InputLabel value="Voltaje medido" />
                    <el-input v-model="form.observations.measured_voltage" placeholder="Ej. 220"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            V
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.measured_voltage']" />
                </div>
                <div>
                    <InputLabel value="Amperaje medido" />
                    <el-input v-model="form.observations.measured_current" placeholder="Ej. 10"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            A
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.measured_current']" />
                </div>
                <div>
                    <InputLabel value="Temperatura" />
                    <el-input v-model="form.observations.temperature" placeholder="Ej. 65"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            °C
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.temperature']" />
                </div>
                <div>
                    <InputLabel value="Presión de carga" />
                    <el-input v-model="form.observations.charge_pressure" placeholder="Ej. 300"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            psi
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.charge_pressure']" />
                </div>
                <div>
                    <InputLabel value="Presión de corte" />
                    <el-input v-model="form.observations.cut_pressure" placeholder="Ej. 70"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #append>
                            psi
                        </template>
                    </el-input>
                    <InputError :message="form.errors['observations.cut_pressure']" />
                </div>
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
                chiler_type: null,
                voltage: null,
                motor_brand: null,
                fan_brand: null,
                fan_hp: null,
            },
            spare_parts: [
                {
                    product_id: null,
                    description: null,
                    quantity: null,
                },
            ],
            observations: {
                plate_voltage: null,
                plate_current: null,
                measured_voltage: null,
                measured_current: null,
                temperature: null,
                charge_pressure: null,
                cut_pressure: null,
            },
            technician_name: this.$page.props.auth.user.name,
            receiver_name: null,
            desciption: null,
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
    },
    methods: {
        store() {
            this.form.post(route("services.store"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "Se ha creado un nuevo servicio",
                        type: "success",
                    });
                },
            });
        },
        handleChangeProduct(index) {
            const productId = this.form.spare_parts[index].product_id;
            const description = this.products.find(i => i.id == productId)?.description ?? 'Sin descripción';
            this.form.spare_parts[index].description = description;
        },
        deleteSparePart(index) {
            this.form.spare_parts.splice(index, 1);
        },
        addSparePart() {
            const newSparePart = {
                product_id: null,
                description: null,
                quantity: null,
            }

            this.form.spare_parts.push(newSparePart);
        },
        saveImage(image) {
            const currentIndex = this.form.media.length - 1;
            this.form.media[currentIndex] = image;
            this.form.media.push(null);
        },
        handleCleared(index) {
            // Eliminar el componente y su informacion correspondiente cuando se borra la imagen
            this.form.media.splice(index, 1);
        }
    }
}
</script>

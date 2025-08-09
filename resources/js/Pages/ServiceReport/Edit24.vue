<template>
    <AppLayout title="Editar orden de servicio">
        <div class="px-3 md:px-10 py-5">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-[75%] xl:w-[65%] mx-auto mt-1 lg:grid lg:grid-cols-2 gap-3">
                <div class="flex items-center justify-between col-span-full mb-3">
                    <h1 class="font-bold ml-2 col-span-full">Editar orden de servicio</h1>
                    <div class="text-sm text-right">
                        <p>Orden de servicio</p>
                        <p>No. {{ String(report.folio).padStart(4, '0') }}</p>
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
                    <InputLabel value="Número de Teléfono del cliente*" />
                    <el-input v-model="form.client_phone_number"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                        placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.client_phone_number" />
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
                    <el-input v-model="formattedImei" class="!w-1/2" placeholder="Escribe el código IMEI del equipo"
                        clearable maxlength="18" />
                    <InputError :message="form.errors['product_details.imei']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Problema reportado*" />
                    <el-input v-model="form.observations" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el problema mencionado por el cliente" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.observations" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Estado previo y características del equipo" />
                    <el-input v-model="form.description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el estado del equipo" :maxlength="1000" show-word-limit clearable />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Servicios a realizar*" />
                    <el-input v-model="form.service_description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el trabajo a realizar" :maxlength="1000" show-word-limit clearable />
                    <InputError :message="form.errors.service_description" />
                </div>

                <h1 class="font-semibold text-gray37 ml-2 col-span-full mt-3">Refacciones</h1>

                <SparePartInput @syncItems="syncItems" :initialData="report.spare_parts" class="col-span-full" />

                <section class="grid grid-cols-3 gap-3 col-span-full mt-5">
                    <div>
                        <InputLabel value="Costo total del servicio*" />
                        <el-input v-model="form.service_cost" placeholder="Ej. 2,500" clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')">
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.service_cost" />
                    </div>
                    <div>
                        <InputLabel value="Anticipo (opcional)" />
                        <el-input v-model="form.advance_payment" placeholder="Ej. 500" clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')">
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.advance_payment" />
                    </div>
                    <div v-if="form.advance_payment">
                        <InputLabel value="Método de pago del anticipo" />
                        <el-select v-model="form.payment_method" clearable placeholder="Selecciona el método de pago"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="payment_method in payment_methods" :key="payment_method"
                                :label="payment_method"
                                :value="payment_method" />
                        </el-select>
                    </div>
                </section>

                <div>
                    <InputLabel value="Responsable del servicio*" />
                    <el-input v-model="form.technician_name" placeholder="Ej. Luis García" clearable />
                    <InputError :message="form.errors.technician_name" />
                </div>
                <div>
                    <div class="flex space-x-2">
                        <InputLabel value="Porcentage de comisión" />
                        <el-tooltip placement="top">
                            <template #content>
                                <p>Al cambiar la orden al estatus "Pagado", se <br>
                                    registrará automáticamente un gasto con <br>
                                    el concepto "Comisión", incluyendo el <br>
                                    nombre del responsable del servicio.</p>
                            </template>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </el-tooltip>
                    </div>
                    <el-input v-model="form.comision_percentage" placeholder="Ej. 10" clearable
                        @input="onPercentageInput">
                        <template #append>%</template>
                    </el-input>
                    <InputError :message="form.errors.comision_percentage" />
                </div>
                <div v-if="report.media.length" class="mt-4 col-span-full">
                    <InputLabel value="Archivos adjuntos" />
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                        <FileView v-for="file in report.media" :key="file" :file="file" :deletable="true"
                            @delete-file="deleteFile($event)" />
                    </div>
                </div>
                <section class="col-span-full grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <InputLabel value="Evidencias (max. 5 imágenes)" />
                        <el-upload class="upload-demo" drag :on-change="handleChange" :on-remove="handleRemoveImage"
                            :on-exceed="handleExceed" :on-preview="handlePictureCardPreview"
                            v-model:file-list="fileList" :before-upload="beforeUpload" :multiple="true"
                            :limit="5 - report.media?.length" list-type="picture-card" :auto-upload="false">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Arrastra o haz clic para subir</div>
                        </el-upload>
                    </div>

                    <div class="mt-3 ml-4">
                        <p>Accesorios</p>
                        <el-checkbox-group v-model="form.aditionals.accessories">
                            <el-checkbox label="SIM"></el-checkbox>
                            <el-checkbox label="Cargador"></el-checkbox>
                            <el-checkbox label="Memoria"></el-checkbox>
                            <el-checkbox label="Batería"></el-checkbox>
                        </el-checkbox-group>
                    </div>
                </section>

                <section class="grid grid-cols-3 col-span-full mt-5">
                    <PatronMobil @syncPattern="syncPattern" @syncPassword="syncPassword"
                        :initialData="report.aditionals" class="col-span-2" />

                    <article class="mt-24 text-sm space-y-1">
                        <p class="flex">
                            <span class="w-44">Costo total del servicio</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    form.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-44">Anticipo</span><span class="ml-[2px]">- $</span><span
                                class="w-16 text-right">{{
                                    form.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00'
                                }}</span>
                        </p>
                        <!-- <p class="flex">
                            <span class="w-44">Refacciones</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p> -->
                        <p class="flex font-bold">
                            <span class="w-44">Restante por pagar</span><span class="ml-3">$</span><span
                                class="w-16 text-right">{{
                                    total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </article>
                </section>

                <el-dialog v-model="dialogVisible">
                    <img class="mx-auto" w-full :src="dialogImageUrl" alt="Preview Image" />
                </el-dialog>

                <div class="col-span-full text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
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
import FileView from "@/Components/MyComponents/FileView.vue";
import SparePartInput from "@/Components/MyComponents/SparePartInput.vue";
import PatronMobil from "@/Components/MyComponents/PatronMobil.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from "date-fns";

export default {
    data() {
        const form = useForm({
            service_date: format(this.report.service_date, "yyyy-MM-dd"),
            client_name: this.report.client_name,
            client_phone_number: this.report.client_phone_number,
            product_details: this.report.product_details,
            spare_parts: [... this.report.spare_parts],
            observations: this.report.observations,
            technician_name: this.report.technician_name,
            description: this.report.description,
            service_cost: this.report.service_cost, // costo unicamente de mano de obra
            total_cost: this.report.total_cost, // costo total
            service_description: this.report.service_description, //descripcion de los servicios que se harán
            payment_method: this.report.payment_method,
            advance_payment: this.report.advance_payment, // anticipo
            comision_percentage: this.report.comision_percentage, // comisión de la persona que realizó el servicio
            media: [], //imagenes de evidencia
            aditionals: this.report.aditionals
        });

        return {
            form,
            payment_methods: ['Efectivo', 'Tarjeta', 'Transferencia'],

            //uploader
            uploading: false,
            uploadPercentage: 0,
            fileList: [], // archivos
            dialogVisible: false, //imagen element-plus
            dialogImageUrl: '', //imagen element-plus
        }
    },
    components: {
        SparePartInput,
        PrimaryButton,
        PatronMobil,
        InputLabel,
        InputError,
        AppLayout,
        FileView,
        Back
    },
    props: {
        report: Object
    },
    watch: {
        'form.service_cost'(val) {
            this.form.service_cost = Number(val);
        },
        'form.advance_payment'(val) {
            this.form.advance_payment = Number(val);
        }
    },
    computed: {
        formattedImei: {
            get() {
                const raw = this.form.product_details?.imei || '';
                return raw.replace(/\D/g, '').slice(0, 15).replace(/(.{4})/g, '$1 ').trim();
            },
            set(value) {
                const clean = value.replace(/\D/g, '').slice(0, 15);
                if (!this.form.product_details) {
                    this.form.product_details = {};
                }
                this.form.product_details.imei = clean;
            },
        },
        total() {
            this.form.total_cost = this.form.service_cost;
            return this.form.service_cost - this.form.advance_payment;
        },
        totalSpareParts() {
            return this.form.spare_parts.reduce((total, sp) => {
                return total + (Number(sp.quantity) * Number(sp.unitPrice));
            }, 0);
        }

    },
    methods: {
        update() {
            this.form.post(route("service-reports.update-phones", this.report.id), {
                method: '_put',
                onSuccess: () => {
                    this.$notify({
                        title: "Orden actualizada",
                        message: "",
                        type: "success",
                    });
                },
            });
        },
        onPercentageInput(value) {
            // Elimina cualquier carácter no numérico (excepto punto decimal si quieres permitirlo)
            let cleanValue = value.replace(/[^\d]/g, '');

            // Limita entre 0 y 100
            let numericValue = parseInt(cleanValue);
            if (isNaN(numericValue)) {
                numericValue = '';
            } else if (numericValue > 100) {
                numericValue = 100;
            }

            this.form.comision_percentage = numericValue;
        },
        // sincroniza los items de refacciones
        syncItems(spareParts) {
            this.form.spare_parts = spareParts;
        },
        // sincroniza los puntos seleccionados del patron
        syncPattern(unlockItem) {
            this.form.aditionals.unlockPattern = unlockItem;
        },
        syncPassword(unlockItem) {
            this.form.aditionals.unlockPassword = unlockItem;
        },
        // uploader
        beforeUpload(file) {
            const isImage = file.type.startsWith('image/');
            if (!isImage) {
                ElMessage.error('Solo se permiten imágenes.');
            }
            return isImage;
        },
        handleChange(file, fileList) {
            this.form.media = fileList.map(item => item.raw); // Actualiza form.media con los archivos
        },
        handleSuccess(response, file, fileList) {
            ElMessage.success('Archivo subido correctamente');
            this.uploading = false;
            this.uploadPercentage = 0;
        },
        handleRemoveImage(file, fileList) {
            // Remover de form.media
            const mediaIndex = this.form.media.indexOf(file.raw);
            if (mediaIndex !== -1) {
                this.form.media.splice(mediaIndex, 1); // Elimina el archivo de form.media
            }
            // Remover del componente
            const mediaUploadIndex = this.fileList.indexOf(file);
            if (mediaUploadIndex !== -1) {
                this.fileList.splice(mediaUploadIndex, 1); // Elimina el archivo de form.media
            }
        },
        handleExceed() {
            ElMessage.warning('Has alcanzado el límite de archivos');
        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },
        deleteFile(fileId) {
            this.report.media = this.report.media.filter(m => m.id !== fileId);
        }

    },

}
</script>
<style scoped>
.upload-demo {
    border: 2px dashed #d9d9d9;
    border-radius: 6px;
    padding: 20px;
    text-align: center;
}
</style>

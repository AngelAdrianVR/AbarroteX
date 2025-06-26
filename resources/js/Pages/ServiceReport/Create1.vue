<template>
    <AppLayout title="Nueva orden de servicio">
        <div class="px-3 md:px-10 py-5">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-2/3 xl:w-[58%] mx-auto mt-1 lg:grid lg:grid-cols-2 gap-3">
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
                    <el-input
                    v-model="formattedImei"
                    class="!w-1/2"
                    placeholder="Escribe el código IMEI del equipo"
                    clearable
                    maxlength="18"
                    />
                    <InputError :message="form.errors['product_details.imei']" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Estado previo y características del equipo*" />
                    <el-input v-model="form.description" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Describe el estado del equipo" :maxlength="1000" show-word-limit
                        clearable />
                    <InputError :message="form.errors.description" />
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
                
                <SparePartInput @syncItems="syncItems" class="col-span-full" />
                
                <section class="grid grid-cols-3 gap-3 col-span-full mt-5">
                    <div>
                        <InputLabel value="Costo del servicio*" />
                        <el-input
                            v-model="form.service_cost"
                            placeholder="Ej. 2,500"
                            clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')"
                            >
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.service_cost" />
                    </div>
                    <div>
                        <InputLabel value="Anticipo (opcional)" />
                        <el-input
                            v-model="form.advance_payment"
                            placeholder="Ej. 500"
                            clearable
                            :formatter="(value) => `${Number(value).toLocaleString('es-MX')}`"
                            :parser="(value) => value.replace(/[^\d.]/g, '')"
                            >
                            <template #prepend>
                                $
                            </template>
                        </el-input>
                        <InputError :message="form.errors.advance_payment" />
                    </div>
                    <div>
                        <InputLabel value="Método de pago" />
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
                    <InputLabel value="Porcentage de comisión" />
                   <el-input
                        v-model="form.comision_percentage"
                        placeholder="Ej. 10"
                        clearable
                        @input="onPercentageInput"
                    >
                        <template #append>%</template>
                    </el-input>
                    <InputError :message="form.errors.comision_percentage" />
                </div>

                <section class="col-span-full grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <InputLabel value="Evidencias (max. 5 imágenes)" />
                        <el-upload
                            class="upload-demo"
                            drag
                            :on-change="handleChange"
                            :on-remove="handleRemoveImage"
                            :on-exceed="handleExceed"
                            :on-preview="handlePictureCardPreview"
                            v-model:file-list="fileList"
                            :before-upload="beforeUpload"
                            :multiple="true"
                            :limit="5"
                            list-type="picture-card"
                            :auto-upload="false"
                            >
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
                    <PatronMobil @syncPattern="syncPattern" @syncPassword="syncPassword" class="col-span-2" />
                    <article class="mt-24 text-sm space-y-1">
                        <p class="flex">
                            <span class="w-32">Costo del servicio</span><span class="ml-3">$</span><span class="w-24 text-right">{{ form.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-32">Anticipo</span><span class="ml-[2px]">- $</span><span class="w-24 text-right">{{ form.advance_payment ?? '0.00' }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-32">Refacciones</span><span class="ml-3">$</span><span class="w-24 text-right">{{ totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                        <p class="flex font-bold">
                            <span class="w-32">Total restante</span><span class="ml-3">$</span><span class="w-24 text-right">{{ total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                    </article>
                </section>

                <el-dialog v-model="dialogVisible">
                    <img class="mx-auto" w-full :src="dialogImageUrl" alt="Preview Image" />
                </el-dialog>

                <div class="col-span-full text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear reporte
                    </PrimaryButton>
                </div>
                <!-- {{form}} -->
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
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
            service_date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto,
            client_name: null,
            client_phone_number: null,
            product_details: {
                brand: null,
                model: null,
            },
            spare_parts: [
                {
                    name: '',
                    unitPrice: null,
                    quantity: null,
                },
            ],
            observations: null,
            technician_name: null,
            description: null,
            service_cost: null, // costo unicamente de mano de obra
            total_cost: null, // costo total
            service_description: null, //descripcion de los servicios que se harán
            payment_method: null,
            advance_payment: null, // anticipo
            comision_percentage: null, // comisión de la persona que realizó el servicio
            media: [], //imagenes de evidencia
            aditionals:{
                unlockPattern: null, //patrton de desbloqueo
                unlockPassword: null, //contraseña o pin de desbloqueo
                accessories: [], // Arreglo con los accesorios seleccionados
            }
        });

        return {
            form,
            payment_methods: ['Efectivo', 'Tarjeta'],

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
        Back
    },
    props: {
        folio: Number
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
            this.form.total_cost = this.form.service_cost + this.totalSpareParts;
            return this.form.service_cost - this.form.advance_payment + this.totalSpareParts;
        },
        totalSpareParts() {
            return this.form.spare_parts.reduce((total, sp) => {
                return total + (Number(sp.quantity) * Number(sp.unitPrice));
            }, 0);
        }

    },
    methods: {
        store() {
            this.form.post(route("service-reports.store-phones"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
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
        syncPattern(unlockItem){
            this.form.aditionals.unlockPattern = unlockItem;
        },
        syncPassword(unlockItem){
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

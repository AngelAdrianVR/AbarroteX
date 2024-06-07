<template>
    <AppLayout title="Editar cliente">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 w-full lg:w-2/3 xl:w-1/2 mx-auto mt-7 md:grid md:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Editar cliente</h1>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Nombre*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del cliente" :maxlength="100"
                        clearable />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel class="mb-1 ml-2" value="Teléfono*" />
                    <el-input v-model="form.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>

                <label for="addAddress" class="text-xs items-center flex mt-2 col-span-full">
                    <el-checkbox @change="clearAddressInfo" id="addAddress" class="px-2" name="addAddress" v-model="form.addAddress"></el-checkbox>
                    Agregar dirección
                </label>

                <!-- Domicilio -->
                <section class="col-span-full md:grid md:grid-cols-2 gap-x-3" v-if="form.addAddress">
                    <div class="mt-3">
                        <InputLabel value="Calle*" class="ml-3 mb-1" />
                        <el-input v-model="form.street" placeholder="Escribe tu calle" :maxlength="255" clearable />
                        <InputError :message="form.errors.street" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Colonia*" class="ml-3 mb-1" />
                        <el-input v-model="form.suburb" placeholder="Escribe tu colonia" :maxlength="255" clearable />
                        <InputError :message="form.errors.suburb" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Número exterior*" class="ml-3 mb-1" />
                        <el-input v-model="form.ext_number" placeholder="Número de vivienda" :maxlength="255" clearable />
                        <InputError :message="form.errors.ext_number" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Número Interior (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="form.int_number" placeholder="Número de edificio, coto, fraccionamiento" :maxlength="255" clearable />
                        <InputError :message="form.errors.int_number" />
                    </div>

                    <div class="mt-3">
                        <InputLabel class="mb-1 ml-2" value="Código postal*" />
                        <el-input v-model="form.postal_code"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="6" clearable
                        placeholder="Escribe el código postal" />
                        <InputError :message="form.errors.postal_code" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Municipio*" class="ml-3 mb-1" />
                        <el-input v-model="form.town" placeholder="Ej. Guadalajara, Tlajomulco, Tonala" :maxlength="255" clearable />
                        <InputError :message="form.errors.town" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Estado*" class="ml-3 mb-1" />
                        <el-input v-model="form.polity_state" placeholder="Ej. Jalisco, Monterrey, Michoacan" :maxlength="255" clearable />
                        <InputError :message="form.errors.polity_state" />
                    </div>
                </section>

                <label for="addFiscalInfo" class="text-xs items-center flex mt-2 col-span-full">
                    <el-checkbox @change="clearFiscalInfo" id="addFiscalInfo" class="px-2" name="addFiscalInfo" v-model="form.addFiscalInfo"></el-checkbox>
                    Agregar datos de facturación
                </label>

                <!-- datos de facturación -->
                <section class="col-span-full md:grid md:grid-cols-2 gap-x-3" v-if="form.addFiscalInfo">
                    <div class="mt-3">
                        <InputLabel value="Razón social*" class="ml-3 mb-1" />
                        <el-input v-model="form.razon_social" placeholder="Ingresar sin régimen societario (ej. sin S.A de C.V)" :maxlength="255" clearable />
                        <InputError :message="form.errors.razon_social" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="R.F.C*" class="ml-3 mb-1" />
                        <el-input v-model="form.rfc" placeholder="Ingresar R.F.C" :maxlength="255" clearable />
                        <InputError :message="form.errors.rfc" />
                    </div>
                    <div class="mt-3">
                        <InputLabel value="Régimen fiscal*" class="ml-3 mb-1" />
                        <el-select class="w-full" filterable v-model="form.tax_regime" clearable placeholder="Seleccione"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="tax_regime in fiscalRegimes" :key="tax_regime" :label="tax_regime"
                                :value="tax_regime" />
                        </el-select>
                    </div>
                </section>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Notas o comentarios (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Agrega información adicional de tu cliente" :maxlength="255" show-word-limit
                        clearable />
                    <InputError :message="form.errors.notes" />
                </div>

                <div class="col-span-full text-right mt-3">
                    <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
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
            name: this.client.name,
            phone: this.client.phone,
            street: this.client.street,
            suburb: this.client.suburb,
            ext_number: this.client.ext_number,
            int_number: this.client.int_number,
            postal_code: this.client.postal_code,
            town: this.client.town,
            polity_state: this.client.polity_state,
            razon_social: this.client.razon_social,
            rfc: this.client.rfc,
            tax_regime: this.client.tax_regime,
            notes: this.client.notes,

            //banderas ----------------------------------------------------
            addAddress: this.client.street ? true : false, //Bandera para agregar dirección a cliente
            addFiscalInfo: this.client.razon_social ? true : false, //Bandera para agregar información fiscal
        });

    return {
        form,
        fiscalRegimes: [
            'General de Ley Personas Morales (601)',
            'Personas Morales con Fines no Lucrativos (603)',
            'Sueldos y Salarios e Ingresos Asimilados a Salarios (605)',
            'Arrendamiento (606)',
            'Demás ingresos (608)',
            'Consolidación (609)',
            'Residentes en el Extranjero sin Establecimiento Permanente en México (610)',
            'Ingreso por Dividendos (socios y accionistas) (611)',
            'Personas Físicas con Actividades Empresariales y Profesionales (612)',
            'Ingresos por intereses (614)',
            'Sin obligaciones fiscales (6161)',
            'Sociedades Cooperativas de Producción que optan por diferir sus ingresos (620)',
            'Incorporación Fiscal (621)',
            'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras (622)',
            'Opcional para Grupos de Sociedades (623)',
            'Coordinados (624)',
            'Hidrocarburos (628)',
            'Régimen de Enajenación o Adquisición de bienes (607)',
            'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales (629)',
            'Enajenación de acciones en bolda de valores (630)',
            'Régimen de los ingresos por obtención de premios (615)',
            'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas (625)',
            'Régimen Simplificado de Confianza (626)',
        ]
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
client: Object
},
methods:{
    update() {
        this.form.put(route("clients.update", this.client.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Cliente actualizado correctamente la información de tu cliente",
                    type: "success",
                });
            },
        });
    },
    clearAddressInfo() {
        this.form.street = null;
        this.form.suburb = null;
        this.form.ext_number = null;
        this.form.int_number = null;
        this.form.postal_code = null;
        this.form.town = null;
        this.form.polity_state = null;
    },
    clearFiscalInfo() {
         this.form.razon_social = null;
         this.form.rfc = null;
         this.form.tax_regime = null;
    }
}
}
</script>
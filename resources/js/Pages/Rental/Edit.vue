<template>
    <AppLayout title="Editar renta">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="update"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full mb-4">Editar renta de producto</h1>
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel value="Cliente *" />
                        <button @click="showCreateClientModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                        </button>
                    </div>
                    <el-select filterable v-model="form.client_id" placeholder="Selecciona el cliente"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="client in localClients" :key="client.id" :label="client.name"
                            :value="client.id" />
                    </el-select>
                    <InputError :message="form.errors.client_id" />
                </div>
                <div>
                    <InputLabel value="Producto a rentar*" />
                    <el-select filterable v-model="form.product_id" placeholder="Selecciona el producto"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="product in products" :key="product.id" :label="product.name"
                            :value="product.id" />
                    </el-select>
                    <InputError :message="form.errors.product_id" />
                </div>
                <div class="mt-3 grid grid-cols-2 gap-x-3">
                    <div>
                        <InputLabel value="La renta se paga cada *" />
                        <el-select filterable v-model="form.period" placeholder="Selecciona el producto"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="(period, index) in periods" :key="period" :label="period.name"
                                :value="index" />
                        </el-select>
                        <InputError :message="form.errors.period" />
                    </div>
                    <div>
                        <InputLabel value="Costo *" class="ml-3 mb-1 text-sm" />
                        <el-input v-model="form.cost" placeholder="Costo de renta "
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/[^\d.]/g, '')" class="!self-end !justify-self-end">
                            <template #prefix>
                                <i class="fa-solid fa-dollar-sign"></i>
                            </template>
                        </el-input>
                        <InputError :message="form.errors.cost" />
                    </div>
                </div>
                <div class="mt-3">
                    <InputLabel>
                        Estado
                        <el-tooltip v-if="form.status == 'Completado'" placement="right">
                            <template #content>
                                <p>
                                    El equipo se ha devuelto y <br>
                                    todos los pagos se han recibido
                                </p>
                            </template>
                            <i class="fa-solid fa-check text-xs text-[#06B918] ml-2"></i>
                        </el-tooltip>
                        <el-tooltip v-if="form.status == 'En uso'" placement="right">
                            <template #content>
                                <p>
                                    El equipo ha sido entregado y <br>
                                    se encuentra en uso por el cliente
                                </p>
                            </template>
                            <i class="fa-solid fa-rotate text-xs text-[#0355B5] ml-2"></i>
                        </el-tooltip>
                        <el-tooltip v-if="form.status == 'Cancelado'" placement="right">
                            <template #content>
                                <p>
                                    La renta del equipo ha sido cancelada
                                </p>
                            </template>
                            <i class="fa-solid fa-xmark text-xs text-[#D70808] ml-2"></i>
                        </el-tooltip>
                    </InputLabel>
                    <el-select filterable v-model="form.status" placeholder="Selecciona"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="status in statuses" :key="status" :label="status" :value="status" />
                    </el-select>
                    <InputError :message="form.errors.status" />
                </div>
                <div class="mt-3 grid grid-cols-2 gap-x-3">
                    <h2 class="font-bold mt-3 text-sm col-span-full mb-1">Fecha y hora de entrega al cliente *</h2>
                    <div>
                        <el-date-picker v-model="form.rented_date" type="date" class="!w-full" placeholder="día/mes/año"
                            :disabled-date="disabledPrevDays" />
                        <InputError :message="form.errors.rented_date" />
                    </div>
                    <div>
                        <el-time-select v-model="form.rented_time" class="!w-full" start="08:00" step="00:30"
                            end="22:00" placeholder="hh:mm" />
                        <InputError :message="form.errors.rented_time" />
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-x-3">
                    <h2 class="font-bold mt-3 text-sm col-span-full mb-1">Fecha y hora de devolución</h2>
                    <el-date-picker v-model="form.estimated_end_date" type="date" class="!w-full"
                        placeholder="día/mes/año" :disabled-date="disabledNextDays" />
                    <el-time-select v-model="form.estimated_end_time" class="!w-full" start="08:00" step="00:30"
                        end="22:00" placeholder="hh:mm" />
                </div>
                <div class="mt-3 col-span-full">
                    <InputLabel value="Comentarios" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe tus comentarios" :maxlength="400" show-word-limit clearable />
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

        <!-- client form -->
        <DialogModal :show="showCreateClientModal" @close="showCreateClientModal = false; resetClientForm()">
            <template #title> Agregar cliente </template>
            <template #content>
                <form @submit.prevent="storeClient" class="md:grid grid-cols-2 gap-x-3">
                    <div class="mt-3">
                        <InputLabel value="Nombre*" class="ml-3 mb-1" />
                        <el-input v-model="clientForm.name" placeholder="Escribe el nombre del cliente" :maxlength="100"
                            clearable />
                        <InputError :message="clientForm.errors.name" />
                    </div>
                    <div class="mt-3">
                        <InputLabel class="mb-1 ml-2" value="Teléfono *" />
                        <el-input v-model="clientForm.phone"
                            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                            :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                            placeholder="Escribe el número de teléfono" />
                        <InputError :message="clientForm.errors.phone" />
                    </div>
                    <div class="mt-3 col-span-full">
                        <InputLabel value="RFC (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="clientForm.rfc" placeholder="Escribe el RFC en caso de tenerlo"
                            :maxlength="100" clearable />
                        <InputError :message="clientForm.errors.rfc" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-2">
                    <CancelButton @click="showCreateClientModal = false; resetClientForm()"
                        :disabled="clientForm.processing">
                        Cancelar</CancelButton>
                    <PrimaryButton @click="storeClient()" :disabled="clientForm.processing">Crear</PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        const form = useForm({
            client_id: this.rental.client_id,
            product_id: this.rental.product_id,
            period: null, //se carga en mounted
            cost: this.rental.cost,
            status: this.rental.status,
            rented_date: format(new Date(this.rental.rented_at), 'yyyy-MM-dd'),
            rented_time: format(new Date(this.rental.rented_at), 'HH:mm'),
            estimated_end_date: this.rental.estimated_end_date,
            estimated_end_time: this.rental.estimated_end_time,
            notes: this.rental.notes,
        });

        const clientForm = useForm({
            name: null,
            rfc: null,
            phone: null,
        });

        return {
            form,
            clientForm,
            showCreateClientModal: false,
            localClients: this.clients,
            // periodos de renta predefinidos
            periods: [
                { name: 'Día', days: 1 },
                { name: '3er Día', days: 3 },
                { name: 'Semana', days: 7 },
                { name: '2 semanas', days: 14 },
                { name: 'Mes', days: 30 },
                { name: 'Bimestre', days: 60 },
                { name: 'Trimestre', days: 90 },
                { name: 'Cuatrimestre', days: 120 },
                { name: 'Semestre', days: 180 },
                { name: 'Año', days: 365 },
                // { name: 'Personalizado', days: 0 },
            ],
            statuses: [
                'En uso',
                'Completado',
                'Cancelado',
            ],
        };
    },
    components: {
        AppLayout,
        InputFilePreview,
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputLabel,
        InputError,
        Back
    },
    props: {
        clients: Array,
        products: Array,
        rental: Object,
    },
    methods: {
        storeClient() {
            this.clientForm.post(route('clients.store'), {
                onSuccess: () => {
                    this.showCreateClientModal = false;
                    this.localClients = this.clients;
                    // seleccionar el cliente recien agregado
                    this.form.client_id = this.clients[this.clients.length - 1].id;
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha creado un nuevo cliente",
                        type: "success",
                    });
                },
            });
        },
        resetClientForm() {
            this.clientForm.reset();
        },
        disabledPrevDays(date) {
            if (this.form.estimated_end_date) {
                return date.getTime() > new Date(this.form.estimated_end_date).getTime();
            }
            return false;
        },
        disabledNextDays(date) {
            if (this.form.rented_date) {
                return date.getTime() < new Date(this.form.rented_date).getTime();
            }
            return false;
        },
        async update() {
            try {
                this.form.transform((data) => ({
                    ...data,
                    period: this.periods[this.form.period],
                })).put(route("rentals.update", this.rental.id), {
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
        const index = this.periods.findIndex(item => item.name == this.rental.period.name);
        this.form.period = index;
    }
}
</script>
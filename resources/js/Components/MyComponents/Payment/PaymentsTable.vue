<template>
    <div>
        <table v-if="payments?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-grayD0">
                    <th class="w-24 md:w-[5%]">Folio</th>
                    <th class="w-32 md:w-[15%]">Tipo de suscripción</th>
                    <th class="w-32 md:w-[15%]">Monto pagado</th>
                    <th class="w-32 md:w-[20%]">Fecha y hora de pago</th>
                    <th class="w-32 md:w-[15%]">Factura</th>
                    <th class="w-10 md:w-[5%]"></th>
                </tr>
            </thead>
            <tbody class="divide-y-[1px]">
                <tr v-for="(payment, index) in payments" :key="index" class="*:text-xs *:py-2 *:px-4 border-grayD9">
                    <td class="rounded-s-full">#{{ String(payment.id).padStart(4, '0') }}</td>
                    <td>{{ payment.suscription_period }}</td>
                    <td>
                        <div class="flex items-center space-x-1">
                            <el-tooltip v-if="payment.status === 'En revisión'"
                                content="El pago se encuentra en proceso de ser validado" placement="top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </el-tooltip>
                            <el-tooltip v-else-if="payment.status === 'Aprobado'"
                                :content="'Pago aprobado el ' + formatDateTime(payment.validated_at)" placement="top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-[#20B808]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </el-tooltip>
                            <el-tooltip v-else-if="payment.status === 'Rechazado'"
                                :content="'Pago rechazado el ' + formatDateTime(payment.validated_at)" placement="top">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-[#CA0A0A]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </el-tooltip>
                            <span>
                                ${{ payment.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </div>
                    </td>
                    <td>{{ formatDateTime(payment.created_at) }}</td>
                    <td>
                        <div v-if="payment.status == 'Aprobado'">
                            <button @click="openInvoiceModal(payment.id)" type="button"
                                v-if="payment.invoice_status === 'No solicitada'" class="text-primary underline">
                                Solicitar factura
                            </button>

                            <el-tooltip v-else-if="payment.invoice_status === 'Solicitada'"
                                content="La factura será enviada en un plazo no mayor a 48 horas." placement="top">
                                <span class="bg-green-100 text-green-500 py-1 px-2 rounded-full cursor-default"><i
                                        class="fa-regular fa-clock mr-2"></i>Solicitada</span>
                            </el-tooltip>

                            <div v-else-if="payment.media.find(m => m.collection_name == 'invoice')">
                                <FileView :file="payment.media.find(m => m.collection_name == 'invoice')"
                                    :url="changeDomain(payment.media.find(m => m.collection_name == 'invoice').original_url)" />
                            </div>

                            <p v-else class="text-gray-400">Hubo problemas al intentar mostrar la factura</p>
                        </div>
                        <p v-else class="text-gray-400">Debe de estar aprobado el pago para solicitar factura</p>
                    </td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'support|' + payment.id">
                                        <span class="text-xs">Contactar con soporte</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay pagos registrados aún" />

        <DialogModal :show="showInvoiceRequestModal" @close="showInvoiceRequestModal = false">
            <template #title>
                <h1>Solicitud de factura</h1>
            </template>
            <template #content>
                <div v-if="$page.props.auth.user.store.csf">
                    <p>
                        <b>Actualmente contamos con una constancia registrada. </b> <br>
                        Es importante revisar que tu <a :href="$page.props.auth.user.store.csf" target="_blank"
                            class="text-primary">Contancia de situación fiscal</a>
                        sea correcta y esté actualizada.
                        <br><br>
                        <span v-if="!editCSF">
                            En caso de querer actualizarlo, da <button @click="editCSF = true" class="text-primary">clic
                                aqui</button>
                        </span>
                    </p>
                </div>
                <div v-else>
                    No has registrado ninguna constancia de situación fiscal. Esta es necesaria para poder
                    emitir la factura.
                </div>
                <div v-if="editCSF || !$page.props.auth.user.store.csf" class="mt-5">
                    <InputLabel value="Subir Constancia de situación fiscal" />
                    <FileUploader @files-selected="fiscalForm.csf = $event" :multiple="false" />
                    <InputError :message="fiscalForm.errors.csf" />
                </div>
            </template>
            <template #footer>
                <div v-if="$page.props.auth.user.store.csf && !editCSF">
                    <PrimaryButton @click="updateInvoiceStatus" :disabled="requestingInvoice">
                        <i v-if="requestingInvoice"
                            class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Solicitar factura
                    </PrimaryButton>
                </div>
                <div v-else class="flex items-center space-x-1">
                    <CancelButton @click="editCSF = false" :disabled="fiscalForm.processing">
                        Cancelar</CancelButton>
                    <PrimaryButton @click="storeCSF" :disabled="fiscalForm.processing || !fiscalForm.csf.length">
                        <i v-if="fiscalForm.processing"
                            class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar Constancia
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
    </div>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import FileView from '@/Components/MyComponents/FileView.vue';

export default {
    data() {
        const fiscalForm = useForm({
            csf: [],
        });

        const requestInvoiceForm = useForm({
            invoice_status: 'Solicitada',
        });

        return {
            fiscalForm,
            requestInvoiceForm,
            showInvoiceRequestModal: false,
            editCSF: false,
            requestingInvoice: false,
            paymentUpdateId: null,
        }
    },
    components: {
        DialogModal,
        FileUploader,
        InputError,
        InputLabel,
        PrimaryButton,
        CancelButton,
        FileView,
    },
    props: {
        payments: Array
    },
    methods: {
        changeDomain(url) {
            // en local no se hace ningun cambio de dominio
            if (import.meta.env.VITE_APP_ENV == 'local') return url;

            const oldDomain = 'https://ezyventas.com';
            const newDomain = 'https://admin.ezyventas.com';

            // Reemplaza el dominio antiguo con el nuevo
            if (url.startsWith(oldDomain)) {
                return url.replace(oldDomain, newDomain);
            }
            return url; // Si no coincide, devuelve la URL sin cambios
        },
        openInvoiceModal(paymentId) {
            this.showInvoiceRequestModal = true;
            this.paymentUpdateId = paymentId
        },
        storeCSF() {
            this.fiscalForm.post(route('stores.store-csf'), {
                onSuccess: () => {
                    this.fiscalForm.reset();
                    this.editCSF = false;
                }
            });
        },
        updateInvoiceStatus() {
            this.requestingInvoice = true;
            this.requestInvoiceForm.put(route('payments.update', this.paymentUpdateId), {
                onSuccess: () => {
                    this.showInvoiceRequestModal = false;
                    this.fiscalForm.reset();
                    this.$notify({
                        title: 'Factura solicitada',
                        message: '',
                        type: 'success',
                    });
                },
                onFinish: () => {
                    this.requestingInvoice = false;
                }
            });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        formatDateTime(dateTimeString) {
            return format(parseISO(dateTimeString), 'dd MMMM, yyyy • hh:mm a', { locale: es });
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'support') {
                this.$inertia.get(route('support-reports.create'));
            }
        },
    }
}
</script>
<template>
    <AppLayout :title="'Venta del día'">
        <div class="md:px-10 px-2 py-7 text-xs md:text-sm">
            <div class="flex justify-between items-center">
                <Back :to="route('sales.index')" />
            </div>
            <section class="flex justify-around mx-auto mt-2 w-1/2 lg:w-1/3 xl:w-1/4">
                <button @click="SeePrevDaySales()" type="button"
                    class="text-primary text-[10px] disabled:text-grayD9 disabled:cursor-not-allowed"
                    :disabled="changingDay || !previous_sale_date">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="text-gray99 flex items-center space-x-2">
                    <p class="flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <span>|</span>
                        <span>Fecha:</span>
                    </p>
                    <p v-if="changingDay" class="text-gray37">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </p>
                    <p v-else class="text-gray37">
                        {{ formatDate(Object.keys(day_sales)[0]) }}
                    </p>
                </div>
                <button @click="SeeNextDaySales()" type="button"
                    class="text-primary text-[10px] disabled:text-grayD9 disabled:cursor-not-allowed"
                    :disabled="changingDay || !next_sale_date">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </section>
            <!-- Información de la venta -->
            <header
                class="w-80 inline-block mt-7 lg:mx-16 rounded-[5px] border border-grayD9 text-gray99 px-4 py-2 space-y-2">
                <section>
                    <div v-if="hasOnlineSales" class="flex items-center space-x-3">
                        <span class="w-2/3">Total de pedidos en línea: </span>
                        <p class="flex text-gray37 w-1/3 font-bold">
                            <span class="w-1/4"></span>
                            <span class="w-1/4">$</span>
                            <span class="w-2/3 ml-3 text-gray37 text-end">
                                {{
                                    Object.values(day_sales)[0]?.online_sales_total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}
                            </span>
                        </p>
                    </div>
                </section>
                <section>
                    <div v-if="hasOrderServices" class="flex items-center space-x-3">
                        <span class="w-2/3">Total Ordenes de servicio: </span>
                        <p class="flex text-gray37 w-1/3 font-bold">
                            <span class="w-1/4"></span>
                            <span class="w-1/4">$</span>
                            <span class="w-2/3 ml-3 text-gray37 text-end">
                                {{
                                    Object.values(day_sales)[0]?.total_service_orders.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}
                            </span>
                        </p>
                    </div>
                </section>
                <section>
                    <div v-if="hasQuotes" class="flex items-center space-x-3">
                        <span class="w-2/3">Total por cotizaciones: </span>
                        <p class="flex text-gray37 w-1/3 font-bold">
                            <span class="w-1/4"></span>
                            <span class="w-1/4">$</span>
                            <span class="w-2/3 ml-3 text-gray37 text-end">
                                {{
                                    Object.values(day_sales)[0]?.total_quotes_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}
                            </span>
                        </p>
                    </div>
                </section>
                <section>
                    <div class="flex items-center space-x-3">
                        <span class="w-2/3">Total de ventas en tienda: </span>
                        <p class="flex text-gray37 w-1/3 font-bold">
                            <span class="w-1/4"></span>
                            <span class="w-1/4">$</span>
                            <span class="w-2/3 ml-3 text-gray37 text-end">
                                {{ Object.values(day_sales)[0]?.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",")
                                }}
                            </span>
                        </p>
                    </div>
                </section>
                <div class="flex items-center space-x-3 border-t border-grayD9 pt-1">
                    <span class="w-2/3 font-bold">Total de ventas del Día: </span>
                    <p class="flex text-gray37 w-1/3 font-bold">
                        <span class="w-1/4"></span>
                        <span class="w-1/4">$</span>
                        <span class="w-2/3 ml-3 text-gray37 text-end">
                            {{ (Object.values(day_sales)[0]?.total_day_sale).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                            ",") }}
                        </span>
                    </p>
                </div>
            </header>
            <!-- Productos -->
            <main class="flex flex-col space-y-5 lg:mx-16 mt-8">
                <el-tabs v-model="activeTab" @tab-click="updateURL">
                    <el-tab-pane label="Ventas en tienda" name="1">
                        <template #label>
                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                </svg>
                                <p>Ventas en tienda</p>
                            </div>
                        </template>
                        <StoreSales @show-modal="handleShowModal" :sales="getGroupedSales" />
                    </el-tab-pane>
                    <el-tab-pane v-if="hasOnlineSales" label="Pedidos en línea" name="2">
                        <template #label>
                            <div class="flex items-center space-x-1">
                                <svg width="20" height="22" viewBox="0 0 10 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4 1H2.5C2.10218 1 1.72064 1.15804 1.43934 1.43934C1.15804 1.72064 1 2.10218 1 2.5V13.5C1 13.8978 1.15804 14.2794 1.43934 14.5607C1.72064 14.842 2.10218 15 2.5 15H7.5C7.89782 15 8.27936 14.842 8.56066 14.5607C8.84196 14.2794 9 13.8978 9 13.5V2.5C9 2.10218 8.84196 1.72064 8.56066 1.43934C8.27936 1.15804 7.89782 1 7.5 1H6M4 1V2H6V1M4 1H6M4 13.5H6"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M6.48168 9.1185H3.93649C3.80702 9.1109 3.77403 9.0824 3.71987 8.9741L2.90758 6.55526H2.32995C2.00414 6.55526 2.00505 6.08594 2.32995 6.08594H3.12419C3.32275 6.08594 3.39496 6.55526 3.48521 6.55526H7.00516C7.20372 6.55526 7.29011 6.74059 7.22177 6.93433L6.73439 8.10765C6.66219 8.25206 6.60803 8.30621 6.39142 8.30621H4.00869L4.13505 8.63113H6.48168C6.82465 8.63113 6.8066 9.1185 6.48168 9.1185Z"
                                        fill="currentColor" />
                                    <path
                                        d="M4.69564 9.49821C4.69564 9.70757 4.52593 9.87728 4.31657 9.87728C4.10722 9.87728 3.9375 9.70757 3.9375 9.49821C3.9375 9.28886 4.10722 9.11914 4.31657 9.11914C4.52593 9.11914 4.69564 9.28886 4.69564 9.49821Z"
                                        fill="currentColor" />
                                    <path
                                        d="M6.5401 9.49821C6.5401 9.70757 6.37039 9.87728 6.16103 9.87728C5.95168 9.87728 5.78196 9.70757 5.78196 9.49821C5.78196 9.28886 5.95168 9.11914 6.16103 9.11914C6.37039 9.11914 6.5401 9.28886 6.5401 9.49821Z"
                                        fill="currentColor" />
                                </svg>
                                <p>Pedidos en línea</p>
                            </div>
                        </template>
                        <OnlineSales @show-modal="handleShowModal" :date="Object.keys(day_sales)[0]" />
                    </el-tab-pane>
                    <el-tab-pane v-if="hasQuotes" label="Cotizaciones" name="3">
                        <template #label>
                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <p>Cotizaciones</p>
                            </div>
                        </template>
                        <QuoteSales @show-modal="handleShowModal" :sales="getGroupedQuoteSales" />
                    </el-tab-pane>
                    <el-tab-pane v-if="hasOrderServices" label="Ordenes de servicio" name="4">
                        <template #label>
                            <div class="flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                </svg>
                                <p>Ordenes de servicio</p>
                            </div>
                        </template>
                        <ServiceSales @show-modal="handleShowModal" :sales="getGroupedServiceSales" />
                    </el-tab-pane>
                </el-tabs>
            </main>
        </div>

        <PrintingModal :show="showPrintingModal" @close="showPrintingModal = false" ref="printingModal" />
        <DialogModal :show="showInstallmentModal" @close="closeInstallmentModal()">
            <template #title>
                <h1>Abonos</h1>
            </template>
            <template #content>
                <section class="border-t pt-1 border-grayD9">
                    <div class="flex items-center justify-between">
                        <span>Resumen de venta</span>
                        <span :class="statusStyles" class="rounded-full px-4 py-[2px]">{{
                            saleToSeeInstallments.credit_data.status }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 pb-2 border-b border-grayD9">
                        <p class="text-gray99">Folio de venta: <span class="text-gray37">{{ saleToSeeInstallments.folio
                                }}</span></p>
                        <span class="text-grayD9">|</span>
                        <p class="text-gray99">Total de venta: <span class="text-gray37">
                                ${{ saleToSeeInstallments.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span></p>
                        <span class="text-grayD9">|</span>
                        <p class="text-gray99">Fecha de vencimiento: <span class="text-gray37">
                                {{ saleToSeeInstallments.credit_data?.expired_date ?
                                    formatDate(saleToSeeInstallments.credit_data.expired_date) : 'No especificado' }}
                            </span></p>
                    </div>
                    <table class="w-full mt-4 *:text-sm">
                        <thead>
                            <tr class="*:text-start">
                                <th class="w-[13%]">Folio</th>
                                <th class="w-[29%]">Fecha</th>
                                <th class="w-[29%]">Monto abonado</th>
                                <th class="w-[29%]">Deuda restante</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-[1px]">
                            <tr v-for="(installment, index) in saleToSeeInstallments.credit_data.installments"
                                :key="index" class="*:py-[6px] *:align-top border-grayD9">
                                <td>{{ 'A' + String(installment.id).padStart(3, '0') }}</td>
                                <td>{{ formatDateTime(installment.created_at) }}</td>
                                <td>${{ installment.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                                <td>${{ calcRemainingDebt(index).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </td>
                            </tr>
                            <tr v-if="addInstallment">
                                <td>-</td>
                                <td>{{ formatDateTime(new Date().toISOString()) }}</td>
                                <td>
                                    <el-input v-model="installmentForm.amount" required type="text" class="!w-11/12"
                                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="0.00">
                                        <template #prefix>
                                            <i class="fa-solid fa-dollar-sign"></i>
                                        </template>
                                    </el-input>
                                </td>
                                <td>
                                    ${{
                                        calcRemainingDebtWithNewInstallment().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ",")
                                    }}
                                </td>
                                <td>
                                    <div
                                        class="flex items-center space-x-1 *:size-5 *:flex *:items-center *:justify-center *:rounded-full *:text-[10px]">
                                        <button @click="storeInstallment" type="button"
                                            class="bg-primary text-white disabled:cursor-not-allowed disabled:bg-gray99"
                                            :disabled="addingInstallment || !installmentForm.isDirty || installmentForm.amount > calcRemainingDebt(saleToSeeInstallments.credit_data.installments.length - 1)">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button @click="addInstallment = false" type="button"
                                            class="bg-grayF2 text-gray37" :disabled="addingInstallment">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-else-if="!['Pagado', 'Reembolsado'].includes(saleToSeeInstallments.credit_data.status)">
                                <td colspan="4">
                                    <button @click="addInstallment = true" type="button" class="text-primary mt-2">
                                        + Agregar abono
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="closeInstallmentModal()" :disabled="addingInstallment">Cancelar</CancelButton>
                </div>
            </template>
        </DialogModal>
        <DialogModal :show="showEditModal" @close="closeEditModal()">
            <template #title>
                <h1>Editar venta</h1>
            </template>
            <template #content>
                <form @submit.prevent="update">
                    <section v-for="(product, index) in form.products" :key="index"
                        class="flex items-center space-x-2 mb-1">
                        <div class="w-1/3 md:w-1/2">
                            <InputLabel value="Producto" />
                            <el-select
                                v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
                                v-model="product.product_id" filterable placeholder="Selecciona el producto"
                                no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in products" :key="item.id"
                                    :label="item.name + ` (${item.additional?.color.name}-${item.additional?.size.name})`"
                                    :value="item.id">
                                    <p>
                                        {{ item.name }}
                                        <span>({{ item.additional?.color.name }}-{{ item.additional?.size.name
                                            }})</span>
                                    </p>
                                </el-option>
                            </el-select>
                            <el-select v-else v-model="product.product_id" filterable
                                placeholder="Selecciona el producto" no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors[`products.${index}.product_id`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="$ por unidad" />
                            <el-input v-model.number="product.current_price" type="number"
                                placeholder="No olvides llenar este campo" required>
                            </el-input>
                            <InputError :message="form.errors[`products.${index}.current_price`]" />
                        </div>
                        <div class="w-1/3 md:w-1/4">
                            <InputLabel value="Cantidad" />
                            <el-input v-model.number="product.quantity" type="number"
                                placeholder="No olvides llenar este campo" required>
                            </el-input>
                            <InputError :message="form.errors[`products.${index}.quantity`]" />
                        </div>
                    </section>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="closeEditModal()" :disabled="editing">Cancelar</CancelButton>
                    <PrimaryButton @click="update" :disabled="editing">Guardar cambios</PrimaryButton>
                </div>
            </template>
        </DialogModal>
        <ConfirmationModal :show="showRefundConfirm" @close="showRefundConfirm = false">
            <template #title>
                <h1>Reembolsar venta</h1>
            </template>
            <template #content>
                <p>
                    Se devolverán los productos de la venta al inventario y se retirará el monto de dinero
                    correspondiente de la caja.
                    Si en caja no hay suficiente dinero, quedará en $0.00
                    ¿Deseas continuar?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showRefundConfirm = false" :disabled="refunding">Cancelar</CancelButton>
                    <PrimaryButton @click="refundSale" :disabled="refunding">Continuar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import StoreSales from './Tabs/StoreSales.vue';
import OnlineSales from './Tabs/OnlineSales.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SaleDetails from "@/Components/MyComponents/Sale/SaleDetails.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { getAll } from '@/dbService.js';
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import QuoteSales from './Tabs/QuoteSales.vue';
import ServiceSales from './Tabs/ServiceSales.vue';
import PrintingModal from '@/Components/MyComponents/Sale/PrintingModal.vue';

export default {
    data() {
        const form = useForm({
            folio: null,
            products: []
        });

        const installmentForm = useForm({
            credit_sale_data_id: null,
            amount: '',
        });

        return {
            form,
            installmentForm,
            products: [],
            // modales
            showEditModal: false,
            showInstallmentModal: false,
            showRefundConfirm: false,
            saleToSeeInstallments: null,
            saleFolioToRefund: null,
            addInstallment: false,
            showPrintingModal: false,
            saleType: 'Normal',
            // cargas
            addingInstallment: false,
            refunding: false,
            editing: false,
            changingDay: false,
            // perimsos o roles
            isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
            hasOnlineSales: this.$page.props.auth.user.store.activated_modules.includes('Tienda en línea'),
            hasQuotes: this.$page.props.auth.user.store.activated_modules.includes('Cotizaciones'),
            hasOrderServices: this.$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio'),
            // tabs
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        ConfirmationModal,
        DialogModal,
        SaleDetails,
        StoreSales,
        OnlineSales,
        QuoteSales,
        ServiceSales,
        PrintingModal,
    },
    props: {
        day_sales: Object,
        previous_sale_date: String,
        next_sale_date: String,
    },
    computed: {
        getGroupedSales() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            return Object.values(sales);
        },
        getGroupedQuoteSales() {
            // Obtener las ventas por cotizacion del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].quote_sales;

            return Object.values(sales);
        },
        getGroupedServiceSales() {
            // Obtener las ventas por ordenes de venta del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].service_orders;

            return Object.values(sales);
        },
        statusStyles() {
            const status = this.saleToSeeInstallments?.credit_data?.status;
            if (status === 'Pendiente') {
                return 'bg-[#FAFFDD] text-[#EFCE21]';
            } else if (status === 'Parcial') {
                return 'bg-[#F1F2FE] text-[#2D29FF]';
            } else if (status === 'Pagado') {
                return 'bg-[#E6FDDB] text-[#08B91A]';
            }
            return 'bg-[#C4FBAA] text-[#0AA91A]';
        },
    },
    methods: {
        SeePrevDaySales() {
            this.changingDay = true;
            const params = {
                date: this.previous_sale_date?.split('T')[0],
                cashRegisterId: this.$page.props.auth.user.cash_register_id
            };

            this.$inertia.visit(route('sales.show', params));
        },
        SeeNextDaySales() {
            this.changingDay = true;
            const params = {
                date: this.next_sale_date?.split('T')[0],
                cashRegisterId: this.$page.props.auth.user.cash_register_id
            };

            this.$inertia.visit(route('sales.show', params));
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        },
        calcRemainingDebtWithNewInstallment() {
            const totalSale = this.saleToSeeInstallments.total_sale;
            const installments = this.saleToSeeInstallments.credit_data.installments;

            let totalPaid = 0;
            installments.forEach(installment => {
                totalPaid += installment.amount;
            });

            const newInstallmentAmount = this.installmentForm.amount ? parseFloat(this.installmentForm.amount) : 0;
            return totalSale - totalPaid - newInstallmentAmount;
        },
        calcRemainingDebt(index) {
            const totalSale = this.saleToSeeInstallments.total_sale;
            const installments = this.saleToSeeInstallments.credit_data.installments;

            let totalPaid = 0;
            for (let i = 0; i <= index; i++) {
                totalPaid += installments[i].amount;
            }

            return totalSale - totalPaid;
        },
        formatSalesProductId() {
            // Obtener las ventas del primer día (si hay múltiples días, se debe ajustar esta parte)
            const sales = Object.values(this.day_sales)[0].sales;

            // cambiar el id de cada venta para que coincida con id de productos en indexedDB
            Object.values(sales).forEach(sale => {
                sale.products.forEach(product => {
                    // revisar si el id es un numero
                    if (Number.isFinite(product.product_id)) {
                        product.product_id = product.is_global_product
                            ? 'global_' + product.product_id
                            : 'local_' + product.product_id;
                    }
                })
            });
        },
        closeInstallmentModal() {
            this.showInstallmentModal = false;
        },
        closeEditModal() {
            this.showEditModal = false;
        },
        openEditModal(saleFolio) {
            // Preparar form con una copia profunda de las ventas seleccionadas
            let sale = this.getGroupedSales.find(item => item.folio == saleFolio);
            this.form.folio = sale.folio;
            this.form.products = JSON.parse(JSON.stringify(sale.products));

            // Abrir modal
            this.showEditModal = true;
        },
        openRefundModal(saleFolio) {
            this.showRefundConfirm = true;
            this.saleFolioToRefund = saleFolio;
        },
        openInstallmentModal(saleFolio) {
            this.showInstallmentModal = true;
            if (this.saleType == 'quote') {
                this.saleToSeeInstallments = this.getGroupedQuoteSales.find(item => item.folio == saleFolio);
            } else {
                this.saleToSeeInstallments = this.getGroupedSales.find(item => item.folio == saleFolio);
            }
        },
        openPrintingModal(saleFolio) {
            this.$refs.printingModal.saleFolio = saleFolio;
            this.showPrintingModal = true;
            if (!this.$page.props.auth.user.printer_config?.name) {
                this.$refs.printingModal.getAvailablePrinters();
            }
        },
        handleShowModal(modal, saleFolio, type = 'Normal') {
            this.saleType = type;
            if (modal == 'edit') this.openEditModal(saleFolio);
            else if (modal == 'refund') this.openRefundModal(saleFolio);
            else if (modal == 'installment') this.openInstallmentModal(saleFolio);
            else if (modal == 'printing') this.openPrintingModal(saleFolio);
        },
        update() {
            this.editing = true;
            this.form.post(route('sales.update-group-sale'), {
                onSuccess: async () => {
                    this.showEditModal = false;
                    this.$notify({
                        title: 'Venta actualizada',
                        message: '',
                        type: 'success',
                    });

                    // volver a formatear id de productos para que no de error al querer editar de nuevo la venta
                    this.formatSalesProductId();
                },
                onFinish: () => {
                    this.editing = false;
                }
            });
        },
        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '';
            return format(parseISO(dateTimeString), 'dd MMM yy, hh:mm a', { locale: es });
        },
        formatDate(dateString) {
            if (!dateString) return '';
            return format(parseISO(dateString), 'dd MMMM, yyyy', { locale: es });
        },
        storeInstallment() {
            this.addingInstallment = true;
            this.installmentForm.transform((data) => ({
                ...data,
                credit_sale_data_id: this.saleToSeeInstallments.credit_data.id,
            })).post(route('installments.store'), {
                onSuccess: () => {
                    this.addInstallment = false;
                    this.installmentForm.reset();
                    this.saleToSeeInstallments = this.getGroupedQuoteSales.find(item => item.folio == this.saleToSeeInstallments.folio);
                },
                onFinish: () => {
                    this.addingInstallment = false;
                    this.installmentForm.reset();
                }
            });
        },
        async refundSale() {
            this.refunding = true;
            try {
                let response = await axios.post(route('sales.refund', this.saleFolioToRefund));
                if (response.status === 200) {
                    this.showRefundConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    if (this.saleType == 'quote') {
                        let sale = this.getGroupedQuoteSales.find(item => item.folio == this.saleFolioToRefund);
                        sale.products.forEach(element => {
                            element.refunded_at = new Date().toISOString();
                        });
                    } else {
                        let sale = this.getGroupedSales.find(item => item.folio == this.saleFolioToRefund);
                        sale.products.forEach(element => {
                            element.refunded_at = new Date().toISOString();
                        });
                    }

                    this.$notify({
                        title: 'Venta reembolsada',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la peticion de reembolso',
                    message: '',
                    type: 'error',
                });
            } finally {
                this.refunding = false;
            }
        },
        async refundQuoteSale() {
            this.refunding = true;
            try {
                let response = await axios.post(route('sales.refund', this.saleFolioToRefund));
                if (response.status === 200) {
                    this.showRefundConfirm = false;

                    // actualizar elementos de la vista (reactividad)
                    let sale = this.getGroupedQuoteSales.find(item => item.folio == this.saleFolioToRefund);
                    sale.products.forEach(element => {
                        element.refunded_at = new Date().toISOString();
                    });

                    this.$notify({
                        title: 'Venta reembolsada',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la peticion de reembolso',
                    message: '',
                    type: 'error',
                });
            } finally {
                this.refunding = false;
            }
        },
    },
    watch: {
        'installmentForm.amount': function () {
            this.calcRemainingDebtWithNewInstallment();
        }
    },
    async mounted() {
        this.products = await getAll('products');
        this.formatSalesProductId();
        this.setActiveTabFromURL();
        // resetear variable de local storage a false
        localStorage.setItem('pendentProcess', false);
    }
}
</script>
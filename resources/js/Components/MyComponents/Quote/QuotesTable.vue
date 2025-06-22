<template>
    <div class="overflow-auto">
        <table v-if="quotes?.length" class="w-full table-fixed">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm border-b border-primary">
                    <th class="w-24">Folio</th>
                    <th class="w-32">Fecha de creación</th>
                    <th class="w-32">Cliente</th>
                    <th class="w-32">Total</th>
                    <th class="w-32">Saldo pendiente</th>
                    <th class="w-32"></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="handleShow(encodeId(quote.id))" v-for="(quote, index) in quotes" :key="index"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">
                        <div class="flex items-center space-x-1">
                            <el-tooltip :content="quote.status" placement="right">
                                <!-- <i class="mr-1" :class="getStatusIcont(quote.status)"></i> -->
                                <span v-html="getStatusIcont(quote.status)"></span>
                            </el-tooltip>
                            <span>{{ 'C-' + String(quote.folio).padStart(4, '0') }}</span>
                        </div>
                    </td>
                    <td>{{ formatDate(quote.created_at) }}</td>
                    <td>
                        {{ quote.company ? quote.company + ' - ' : '' }}
                        {{ quote.contact_name }}
                    </td>
                    <td>
                        <p>$ {{ grandTotal(quote).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                    </td>
                    <td>
                        <p>
                            {{ quote.remaining
                                ? '$ ' + quote.remaining.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                : '-' }}
                        </p>
                    </td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu
                                    :class="canChangeStatus && quote.status != 'Pagado' ? '!w-[210px]' : null">
                                    <el-dropdown-item :command="'see|' + encodeId(quote.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="canEdit" :command="'edit|' + encodeId(quote.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="canDelete && !['Pagado', 'Pago parcial'].includes(quote.status)"
                                        :command="'delete|' + quote.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-[14px] mr-2 text-red-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span class="text-xs text-red-600">Eliminar</span>
                                    </el-dropdown-item>
                                    <div v-if="canChangeStatus && !['Pagado', 'Pago parcial'].includes(quote.status)">
                                        <p class="my-1 text-center text-xs text-gray99">Cambiar estado de cotización</p>
                                        <el-dropdown-item v-if="quote.status != 'Pago parcial'"
                                            :command="'status|' + quote.id + '|Sin enviar a cliente'">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor" class="size-4 mr-2">
                                                <path fill-rule="evenodd"
                                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 0 1-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 0 1-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 0 1-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584ZM12 18a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs">Sin enviar a cliente</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="quote.status != 'Pago parcial'"
                                            :command="'status|' + quote.id + '|Esperando respuesta'">
                                            <svg width="16" height="18" viewBox="0 0 13 16" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.57423 8.04782L6.69533 8.83421L4.42867 6.66007C2.16201 10.4995 6.16502 13.5988 8.83423 11.1471C9.64555 10.4019 10.3652 8.83421 9.23953 6.66007L10.7171 5.31857C11.6085 6.27663 12.1538 7.56113 12.1538 8.97298C12.1538 11.9365 9.75138 14.3389 6.78784 14.3389C3.8243 14.3389 1.42188 11.9365 1.42188 8.97298C1.42188 6.00944 3.8243 3.60702 6.78784 3.60702V1.52539M6.78784 1.52539H4.42867M6.78784 1.52539H8.83423"
                                                    stroke="currentColor" stroke-width="1.38775" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-xs">Esperando respuesta</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="quote.status != 'Pago parcial'"
                                            :command="'status|' + quote.id + '|Autorizada'">
                                            <svg width="15" height="15" viewBox="0 0 14 13" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M6.05078 0C7.55666 0 8.93423 0.549765 9.99316 1.45996C10.9763 0.695148 11.5787 0.424377 12.6973 0.0917969C12.8806 0.0372847 13.0214 0.0457867 13.2021 0.0917969C13.2938 0.137854 13.4287 0.335015 13.5225 0.503906C13.5988 0.641428 13.6068 0.807884 13.5225 0.871094C12.573 1.58285 12.0774 2.0335 11.21 2.8877C11.7754 3.80807 12.1016 4.89139 12.1016 6.05078C12.1015 9.39261 9.39262 12.1016 6.05078 12.1016C2.70898 12.1015 2.75663e-05 9.39258 0 6.05078C0 2.70895 2.70896 4.42574e-05 6.05078 0ZM12.9268 0.274414C9.06853 1.70532 7.93919 3.53495 5.50098 6.50879C4.89253 5.61448 3.84278 4.78422 3.62207 4.6748C3.34639 4.53816 2.93468 4.56042 2.79688 4.6748L1.97168 5.36328C1.88204 5.44228 3.95809 7.56266 5.73047 9.03027C5.82128 9.10543 6.31795 9.1217 6.37207 9.03027C8.72062 5.0012 10.7261 2.52178 13.293 0.641602C13.3617 0.596533 13.0186 0.240685 12.9268 0.274414Z"
                                                    fill="currentColor" />
                                            </svg>
                                            <span class="text-xs">Autorizada</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'status|' + quote.id + '|Pago parcial'">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M3 0C3.13261 0 3.25979 0.0526784 3.35355 0.146447C3.44732 0.240215 3.5 0.367392 3.5 0.5V1.5H9.5V0.5C9.5 0.367392 9.55268 0.240215 9.64645 0.146447C9.74021 0.0526784 9.86739 0 10 0C10.1326 0 10.2598 0.0526784 10.3536 0.146447C10.4473 0.240215 10.5 0.367392 10.5 0.5V1.5H11C11.5304 1.5 12.0391 1.71071 12.4142 2.08579C12.7893 2.46086 13 2.96957 13 3.5V11C13 11.5304 12.7893 12.0391 12.4142 12.4142C12.0391 12.7893 11.5304 13 11 13H2C1.46957 13 0.960859 12.7893 0.585787 12.4142C0.210714 12.0391 0 11.5304 0 11V3.5C0 2.96957 0.210714 2.46086 0.585787 2.08579C0.960859 1.71071 1.46957 1.5 2 1.5H2.5V0.5C2.5 0.367392 2.55268 0.240215 2.64645 0.146447C2.74021 0.0526784 2.86739 0 3 0ZM12 6C12 5.73478 11.8946 5.48043 11.7071 5.29289C11.5196 5.10536 11.2652 5 11 5H2C1.73478 5 1.48043 5.10536 1.29289 5.29289C1.10536 5.48043 1 5.73478 1 6V11C1 11.2652 1.10536 11.5196 1.29289 11.7071C1.48043 11.8946 1.73478 12 2 12H11C11.2652 12 11.5196 11.8946 11.7071 11.7071C11.8946 11.5196 12 11.2652 12 11V6Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M6.00086 9.6103C6.43553 9.74663 6.82957 9.51831 6.81532 9.26971C6.8038 9.06871 6.69817 8.98525 6.31884 8.8764C5.70938 8.7015 4.97006 8.54573 4.73072 7.8706C4.47416 7.14688 5.1106 6.56292 5.77873 6.40871C5.77873 6.31045 5.72797 6 5.86846 6H6.69164C6.879 6 6.81532 6.28805 6.81532 6.40871C7.33441 6.54145 7.6035 6.66753 7.90506 7.03572C7.95384 7.09527 7.95384 7.18775 7.90506 7.21916L7.25958 7.63485C6.95225 7.33789 6.11864 6.67075 5.8163 7.24893C5.51859 7.81825 6.42227 8.04361 6.81532 8.11169C7.64895 8.25607 8 8.79287 8 9.26971C8 9.74654 7.68405 10.2214 6.81532 10.4277C6.81532 10.5463 6.87135 10.7716 6.69774 10.7683H5.89895C5.72594 10.7683 5.77873 10.5432 5.77873 10.4277C5.21751 10.2713 4.95557 10.1181 4.71176 9.82929C4.64283 9.74764 4.66114 9.64477 4.71176 9.61935L5.40852 9.26971C5.55652 9.45408 5.78168 9.54156 6.00086 9.6103Z"
                                                    fill="currentColor" />
                                            </svg>
                                            <span class="text-xs">Pago parcial</span>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="quote.status != 'Pago parcial'"
                                            :command="'status|' + quote.id + '|Pagado'">
                                            <svg width="15" height="15" viewBox="0 0 13 13" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M6.40723 0.1875C9.94524 0.187741 12.8132 3.05574 12.8135 6.59375C12.8135 10.132 9.94539 13.0007 6.40723 13.001C2.86886 13.001 0 10.1321 0 6.59375C0.000241568 3.05559 2.86901 0.1875 6.40723 0.1875ZM5.39062 2.17383C5.30073 2.17383 5.22461 2.25331 5.22461 2.37695V2.92773C4.33449 3.13325 3.70466 3.59905 3.39551 4.14551C2.96468 4.90714 3.23683 5.76466 3.77441 6.37793C4.4657 7.02088 5.34356 7.22319 6.21973 7.47461C6.91855 7.67515 7.11345 7.82904 7.13477 8.19922C7.16102 8.65734 6.43478 9.07837 5.63379 8.82715C5.25611 8.70868 4.87196 8.56612 4.59961 8.2666C4.5799 8.24492 4.56139 8.22217 4.54297 8.19922L4.00684 8.46875L3.25879 8.84375C3.16553 8.89058 3.13183 9.08001 3.25879 9.23047C3.70804 9.76267 4.19064 10.0448 5.22461 10.333V10.793C5.22489 10.905 5.3341 10.9609 5.44629 10.9609H6.91797C7.05093 10.9635 7.13458 10.9274 7.13477 10.793V10.333C8.73561 9.95275 9.31836 9.0779 9.31836 8.19922C9.31822 7.32061 8.67078 6.33148 7.13477 6.06543C6.2347 5.9095 5.65074 5.6681 5.38184 5.33203C5.17724 5.07624 5.14195 4.6661 5.34277 4.39746C6.02277 3.48838 7.37668 4.62857 7.9541 5.18652L9.14355 4.4209C9.23309 4.3629 9.2331 4.19266 9.14355 4.08301C8.60406 3.42433 7.92877 3.13076 7.13477 2.92773V2.37695C7.13477 2.23089 7.01955 2.17387 6.90723 2.17383H5.39062Z"
                                                    fill="currentColor" />
                                            </svg>
                                            <span class="text-xs">
                                                Pagado (contado)
                                            </span>
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'status|' + quote.id + '|Rechazada'">
                                            <svg width="15" height="15" viewBox="0 0 13 13" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z"
                                                    stroke="currentColor" stroke-width="1.13898" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="text-xs">Rechazada</span>
                                        </el-dropdown-item>
                                    </div>
                                    <div v-else-if="quote.status == 'Pago parcial'">
                                        <el-dropdown-item :command="'status|' + quote.id + '|Pago parcial'">
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" class="mr-2"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M3 0C3.13261 0 3.25979 0.0526784 3.35355 0.146447C3.44732 0.240215 3.5 0.367392 3.5 0.5V1.5H9.5V0.5C9.5 0.367392 9.55268 0.240215 9.64645 0.146447C9.74021 0.0526784 9.86739 0 10 0C10.1326 0 10.2598 0.0526784 10.3536 0.146447C10.4473 0.240215 10.5 0.367392 10.5 0.5V1.5H11C11.5304 1.5 12.0391 1.71071 12.4142 2.08579C12.7893 2.46086 13 2.96957 13 3.5V11C13 11.5304 12.7893 12.0391 12.4142 12.4142C12.0391 12.7893 11.5304 13 11 13H2C1.46957 13 0.960859 12.7893 0.585787 12.4142C0.210714 12.0391 0 11.5304 0 11V3.5C0 2.96957 0.210714 2.46086 0.585787 2.08579C0.960859 1.71071 1.46957 1.5 2 1.5H2.5V0.5C2.5 0.367392 2.55268 0.240215 2.64645 0.146447C2.74021 0.0526784 2.86739 0 3 0ZM12 6C12 5.73478 11.8946 5.48043 11.7071 5.29289C11.5196 5.10536 11.2652 5 11 5H2C1.73478 5 1.48043 5.10536 1.29289 5.29289C1.10536 5.48043 1 5.73478 1 6V11C1 11.2652 1.10536 11.5196 1.29289 11.7071C1.48043 11.8946 1.73478 12 2 12H11C11.2652 12 11.5196 11.8946 11.7071 11.7071C11.8946 11.5196 12 11.2652 12 11V6Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M6.00086 9.6103C6.43553 9.74663 6.82957 9.51831 6.81532 9.26971C6.8038 9.06871 6.69817 8.98525 6.31884 8.8764C5.70938 8.7015 4.97006 8.54573 4.73072 7.8706C4.47416 7.14688 5.1106 6.56292 5.77873 6.40871C5.77873 6.31045 5.72797 6 5.86846 6H6.69164C6.879 6 6.81532 6.28805 6.81532 6.40871C7.33441 6.54145 7.6035 6.66753 7.90506 7.03572C7.95384 7.09527 7.95384 7.18775 7.90506 7.21916L7.25958 7.63485C6.95225 7.33789 6.11864 6.67075 5.8163 7.24893C5.51859 7.81825 6.42227 8.04361 6.81532 8.11169C7.64895 8.25607 8 8.79287 8 9.26971C8 9.74654 7.68405 10.2214 6.81532 10.4277C6.81532 10.5463 6.87135 10.7716 6.69774 10.7683H5.89895C5.72594 10.7683 5.77873 10.5432 5.77873 10.4277C5.21751 10.2713 4.95557 10.1181 4.71176 9.82929C4.64283 9.74764 4.66114 9.64477 4.71176 9.61935L5.40852 9.26971C5.55652 9.45408 5.78168 9.54156 6.00086 9.6103Z"
                                                    fill="currentColor" />
                                            </svg>
                                            <span class="text-xs">Registrar abono</span>
                                        </el-dropdown-item>
                                    </div>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay cotizaciones registradas" />

        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar cotización</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará al cotización seleccionado, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
        <DialogModal :show="showPaymentModal" @close="showPaymentModal = false">
            <template #title>
                <div v-if="!paymentConfirmed">
                    <h1 v-if="paymentModalStep === 1" class="font-bold mt-2">Opciones de pago</h1>
                    <h1 v-else-if="paymentModalStep === 2" class="font-bold mt-2 text-lg">Pagar</h1>
                    <h1 v-else-if="paymentModalStep === 3" class="font-bold mt-2 text-lg">Registrar pago</h1>
                </div>
                <h1 v-else></h1>
            </template>
            <template #content>
                <div v-if="!paymentConfirmed">
                    <div v-if="paymentModalStep === 1" class="px-4 relative">
                        <p class="text-gray99">Seleccione el método de pago</p>
                        <section class="grid grid-cols-2 gap-4 mt-2">
                            <button
                                @click="paymentModalStep = 2; statusForm.payment_method = 'Efectivo'; receivedInputFocus()"
                                type="button"
                                class="bg-[#E0FEC5] text-[#37672B] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
                                <p class="text-lg text-center font-bold">EFECTIVO</p>
                                <img src="@/../../public/images/dollar.webp"
                                    alt="Billete verde indica Pago en efectivo">
                            </button>
                            <button
                                @click="paymentModalStep = 3; statusForm.payment_method = 'Tarjeta'; receivedInputFocus()"
                                type="button"
                                class="bg-[#DAE6FF] text-[#063B52] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
                                <p class="text-lg text-center font-bold">TARJETA</p>
                                <img src="@/../../public/images/card.webp"
                                    alt="Tarjeta azul que indica Pago con tarjeta">
                            </button>
                        </section>
                    </div>
                    <!-- Pago con efectivo (step 2) -->
                    <div v-if="paymentModalStep === 2" class="px-4 relative">
                        <section class="flex items-center justify-between">
                            <div @click="paymentModalStep = 1"
                                class="flex items-center space-x-4 text-primary cursor-pointer">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Regresar</span>
                            </div>
                        </section>
                        <section class="mx-auto mt-2 md:w-2/3">
                            <div
                                class="rounded-full border border-[#D9D9D9D] bg-[#E0FEC5] py-2 px-4 flex items-center justify-between mt-3">
                                <span class="font-bold text-[#37672B]">EFECTIVO</span>
                                <img src="@/../../public/images/dollar.webp" alt="Pago en efectivo" class="h-7">
                            </div>
                        </section>
                    </div>
                    <!-- Pago con tarjeta (step 3) -->
                    <div v-if="paymentModalStep === 3" class="px-4 relative">
                        <section class="flex items-center justify-between">
                            <div @click="paymentModalStep = 1"
                                class="flex items-center space-x-4 text-primary cursor-pointer">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Regresar</span>
                            </div>
                        </section>
                        <section class="mx-auto mt-2 md:w-2/3">
                            <div v-if="!paymentConfirmed">
                                <p class="my-3 text-sm text-center text-black">
                                    El sistema no procesa pagos con tarjeta. Usa tu terminal
                                    bancaria externa y luego registra aquí el pago.
                                </p>
                                <div
                                    class="rounded-full border border-[#D9D9D9D] bg-[#DAE6FF] py-2 px-4 flex items-center justify-between mt-3">
                                    <span class="font-bold text-[#05394F]">TARJETA</span>
                                    <img src="@/../../public/images/card.webp" alt="Pago con tarjeta" class="h-7">
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- formulario, botón de pago y mensaje de exito -->
                    <div v-if="paymentModalStep === 2 || paymentModalStep === 3" class="mt-3">
                        <div v-if="statusForm.status == 'Pagado'"
                            class="md:w-2/3 rounded-full border border-[#D9D9D9D] bg-[#F2F2F2] py-2 px-4 flex items-center justify-between mt-2 mx-auto">
                            <span class="font-bold">Total a pagar</span>
                            <p class="font-bold">
                                <span class="mr-4">$</span>
                                {{ grandTotal(quoteSelected)?.toLocaleString('en-US', { minimumFractionDigits: 2 })
                                }}
                            </p>
                        </div>
                        <div v-else
                            class="md:w-2/3 rounded-full border border-[#D9D9D9D] bg-[#F2F2F2] py-2 px-4 flex items-center justify-between mt-2 mx-auto">
                            <span class="font-bold">Saldo pendiente</span>
                            <p class="font-bold">
                                <span class="mr-4">$</span>
                                {{ calculateRemaining()?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                            </p>
                        </div>
                        <div v-if="!isCreditPayment && !quoteSelected.client_id"
                            class="flex items-center justify-center space-x-1 mt-2">
                            <el-checkbox v-model="statusForm.create_client" label="Crear cliente" size="large" />
                            <el-tooltip placement="top">
                                <template #content>
                                    <p class="text-center">
                                        ¿Deseas dar de alta al cliente para <br>
                                        consultar sus cotizaciones?
                                    </p>
                                </template>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </el-tooltip>
                        </div>
                        <div v-if="!paymentConfirmed && isCreditPayment"
                            class="mt-5 flex flex-col items-center space-y-3">
                            <div>
                                <p class="text-center font-bold">¿Cuánto paga el cliente?</p>
                                <el-input-number ref="receivedInput" @keydown.enter="updateStatus"
                                    v-model="statusForm.amount" :min="1" :max="statusForm.grand_total - 1">
                                    <template #prefix>
                                        <span>$</span>
                                    </template>
                                </el-input-number>
                                <InputError :message="statusForm.errors.amount" />
                            </div>
                            <div v-if="!quoteSelected.remaining">
                                <p class="text-center font-bold">Fecha de vencimiento</p>
                                <el-date-picker v-model="statusForm.limit_date" class="!w-full" type="date"
                                    placeholder="dd/mm/aa" :disabled-date="disabledDate" />
                            </div>
                        </div>
                        <p v-if="isCreditPayment && !quoteSelected.client_id"
                            class="flex items-center justify-center space-x-1 text-[#52116C] mt-6">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.99078 12.1599H1.14485C0.0379915 8.17201 5.55392 5.89796 8.19572 9.04622M7.23111 11.5306L9.01163 13C10.5692 11.1965 11.4424 10.1854 13 8.38192M7.37355 3.37901C7.37355 4.6929 6.2894 5.75802 4.95203 5.75802C3.61467 5.75802 2.53052 4.6929 2.53052 3.37901C2.53052 2.06512 3.61467 1 4.95203 1C6.2894 1 7.37355 2.06512 7.37355 3.37901Z"
                                    stroke="currentColor" />
                            </svg>
                            <span>
                                El cliente se creará automáticamente para el registro de abonos.
                            </span>
                        </p>
                        <div class="flex justify-center mt-3">
                            <PrimaryButton :disabled="statusForm.processing" class="!px-20" @click="updateStatus">
                                <i v-if="statusForm.processing"
                                    class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                                Registrar pago
                            </PrimaryButton>
                        </div>
                        <p class="text-gray37 mt-3 mx-4 lg:mx-16 text-center">
                            Se generará una nueva venta y los productos se descontarán automáticamente del inventario
                        </p>
                    </div>
                </div>
                <!-- Confirmacion de pago -->
                <div v-else>
                    <div class="flex flex-col items-center space-y-4 animate-fade-in-up">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-green-600 font-bold text-lg">¡Pago registrado con éxito!</p>
                    </div>
                </div>
            </template>
            <template #footer></template>
        </DialogModal>
    </div>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import axios from 'axios';
import DialogModal from '@/Components/DialogModal.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

export default {
    data() {
        const statusForm = useForm({
            status: null,
            grand_total: null,
            payment_method: null,
            amount: null,
            create_client: false,
            limit_date: null,
        });

        return {
            statusForm,
            showDeleteConfirm: false,
            showPaymentModal: false,
            paymentConfirmed: false,
            isCreditPayment: false,
            quoteSelected: null,
            paymentModalStep: 1,
            canEdit: this.$page.props.auth.user.permissions.includes('Editar cotizaciones'),
            canDelete: this.$page.props.auth.user.permissions.includes('Eliminar cotizaciones'),
            canChangeStatus: this.$page.props.auth.user.permissions.includes('Cambiar status cotizaciones'),
        }
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputError,
    },
    props: {
        quotes: Array
    },
    methods: {
        calculateRemaining() {
            if (this.quoteSelected.remaining) {
                // ya tiene abonos registrados
                return this.quoteSelected.remaining - this.statusForm.amount;
            } else {
                return this.statusForm.grand_total - this.statusForm.amount;
            }
        },
        receivedInputFocus() {
            this.$nextTick(() => {
                try {
                    this.$refs.receivedInput.focus(); // Enfocar el input de recibido cuando se abre el modal
                } catch (error) {
                    // console.log(error);                   
                }
            });
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() < today.getTime();
        },
        percentageDiscount(item) {
            return item.percentage * 0.01 * this.subtotal(item);
        },
        subtotal(item) {
            if (item.iva_included) {
                return (item.total * 0.84);
            }

            return item.total;
        },
        grandTotal(item) {
            let discounted = 0;
            if (item.is_percentage_discount != null) {
                discounted = item.is_percentage_discount
                    ? this.percentageDiscount(item)
                    : item.discount;
            }

            if (item.iva_included === false) {
                return (item.total * 1.16) + item.delivery_cost - discounted;
            }

            return item.total + item.delivery_cost - discounted;
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('quotes.show', data));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('quotes.edit', data));
            } else if (commandName == 'status') {
                this.statusForm.reset();
                const status = command.split('|')[2];
                this.statusForm.status = status;
                this.quoteSelected = this.quotes.find(q => q.id == data);
                if (!['Pagado', 'Pago parcial'].includes(status)) {
                    this.updateStatus();
                } else {
                    this.paymentModalStep = 1;
                    this.isCreditPayment = status == 'Pago parcial';
                    this.showPaymentModal = true;
                    this.statusForm.grand_total = this.grandTotal(this.quoteSelected);
                    this.statusForm.create_client = (status == 'Pago parcial' && !this.quoteSelected.client_id);
                }
            }
            else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.quoteSelected = data;
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        handleShow(encodedId) {
            window.open(route('quotes.show', encodedId, '_blank'));
        },
        getStatusIcont(status) {
            if (status === 'Esperando respuesta') {
                return '<svg width="17" height="21" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.57423 8.04782L6.69533 8.83421L4.42867 6.66007C2.16201 10.4995 6.16502 13.5988 8.83423 11.1471C9.64555 10.4019 10.3652 8.83421 9.23953 6.66007L10.7171 5.31857C11.6085 6.27663 12.1538 7.56113 12.1538 8.97298C12.1538 11.9365 9.75138 14.3389 6.78784 14.3389C3.8243 14.3389 1.42188 11.9365 1.42188 8.97298C1.42188 6.00944 3.8243 3.60702 6.78784 3.60702V1.52539M6.78784 1.52539H4.42867M6.78784 1.52539H8.83423" stroke="#D0BF0A" stroke-width="1.38775" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else if (status === 'Rechazada') {
                return '<svg width="17" height="17" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z" stroke="#B80505" stroke-width="1.13898" stroke-linecap="round" stroke-linejoin="round" /></svg>';
            } else if (status === 'Sin enviar a cliente') {
                return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="size-[19px] text-[#B80505]">
                        <path fill-rule="evenodd"
                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 0 1-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 0 1-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 0 1-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584ZM12 18a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                            clip-rule="evenodd" />
                    </svg>`;
            } else if (status === 'Autorizada') {
                return '<svg width="16" height="18" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 0C10.6155 0 12.5505 0.773098 14.0381 2.05176C15.4194 0.977138 16.266 0.596218 17.8379 0.128906C18.0953 0.0524493 18.2922 0.0643033 18.5459 0.128906C18.6746 0.193372 18.8643 0.470628 18.9961 0.708008C19.1033 0.901193 19.1146 1.13484 18.9961 1.22363C17.6622 2.22354 16.9666 2.85673 15.748 4.05664C16.5423 5.3495 17 6.87139 17 8.5C17 13.1945 13.1945 17 8.5 17C3.80556 16.9999 0 13.1945 0 8.5C8.71011e-05 3.80562 3.80562 8.71035e-05 8.5 0ZM18.1582 0.385742C12.7385 2.39578 11.1525 4.9662 7.72754 9.14355C6.87293 7.88724 5.39716 6.72224 5.08691 6.56836C4.69937 6.37624 4.12092 6.40737 3.92773 6.56836L2.76855 7.53418C2.64278 7.64528 5.55916 10.624 8.04883 12.6855C8.17645 12.7912 8.8761 12.8143 8.95117 12.6855C12.2504 7.0256 15.0669 3.54258 18.6729 0.901367C18.7694 0.838075 18.2873 0.338678 18.1582 0.385742Z" fill="#0EA598"/></svg>';
            } else if (status === 'Pagado') {
                return `<svg width="16" height="16" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.40723 0.1875C9.94524 0.187741 12.8132 3.05574 12.8135 6.59375C12.8135 10.132 9.94539 13.0007 6.40723 13.001C2.86886 13.001 0 10.1321 0 6.59375C0.000241568 3.05559 2.86901 0.1875 6.40723 0.1875ZM5.39062 2.17383C5.30073 2.17383 5.22461 2.25331 5.22461 2.37695V2.92773C4.33449 3.13325 3.70466 3.59905 3.39551 4.14551C2.96468 4.90714 3.23683 5.76466 3.77441 6.37793C4.4657 7.02088 5.34356 7.22319 6.21973 7.47461C6.91855 7.67515 7.11345 7.82904 7.13477 8.19922C7.16102 8.65734 6.43478 9.07837 5.63379 8.82715C5.25611 8.70868 4.87196 8.56612 4.59961 8.2666C4.5799 8.24492 4.56139 8.22217 4.54297 8.19922L4.00684 8.46875L3.25879 8.84375C3.16553 8.89058 3.13183 9.08001 3.25879 9.23047C3.70804 9.76267 4.19064 10.0448 5.22461 10.333V10.793C5.22489 10.905 5.3341 10.9609 5.44629 10.9609H6.91797C7.05093 10.9635 7.13458 10.9274 7.13477 10.793V10.333C8.73561 9.95275 9.31836 9.0779 9.31836 8.19922C9.31822 7.32061 8.67078 6.33148 7.13477 6.06543C6.2347 5.9095 5.65074 5.6681 5.38184 5.33203C5.17724 5.07624 5.14195 4.6661 5.34277 4.39746C6.02277 3.48838 7.37668 4.62857 7.9541 5.18652L9.14355 4.4209C9.23309 4.3629 9.2331 4.19266 9.14355 4.08301C8.60406 3.42433 7.92877 3.13076 7.13477 2.92773V2.37695C7.13477 2.23089 7.01955 2.17387 6.90723 2.17383H5.39062Z" fill="#3EA50E"/>
                </svg>
                `;
            } else if (status === 'Pago parcial') {
                return '<svg width="16" height="16" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3 0C3.13261 0 3.25979 0.0526784 3.35355 0.146447C3.44732 0.240215 3.5 0.367392 3.5 0.5V1.5H9.5V0.5C9.5 0.367392 9.55268 0.240215 9.64645 0.146447C9.74021 0.0526784 9.86739 0 10 0C10.1326 0 10.2598 0.0526784 10.3536 0.146447C10.4473 0.240215 10.5 0.367392 10.5 0.5V1.5H11C11.5304 1.5 12.0391 1.71071 12.4142 2.08579C12.7893 2.46086 13 2.96957 13 3.5V11C13 11.5304 12.7893 12.0391 12.4142 12.4142C12.0391 12.7893 11.5304 13 11 13H2C1.46957 13 0.960859 12.7893 0.585787 12.4142C0.210714 12.0391 0 11.5304 0 11V3.5C0 2.96957 0.210714 2.46086 0.585787 2.08579C0.960859 1.71071 1.46957 1.5 2 1.5H2.5V0.5C2.5 0.367392 2.55268 0.240215 2.64645 0.146447C2.74021 0.0526784 2.86739 0 3 0ZM12 6C12 5.73478 11.8946 5.48043 11.7071 5.29289C11.5196 5.10536 11.2652 5 11 5H2C1.73478 5 1.48043 5.10536 1.29289 5.29289C1.10536 5.48043 1 5.73478 1 6V11C1 11.2652 1.10536 11.5196 1.29289 11.7071C1.48043 11.8946 1.73478 12 2 12H11C11.2652 12 11.5196 11.8946 11.7071 11.7071C11.8946 11.5196 12 11.2652 12 11V6Z" fill="#1944B0"/><path d="M6.00086 9.6103C6.43553 9.74663 6.82957 9.51831 6.81532 9.26971C6.8038 9.06871 6.69817 8.98525 6.31884 8.8764C5.70938 8.7015 4.97006 8.54573 4.73072 7.8706C4.47416 7.14688 5.1106 6.56292 5.77873 6.40871C5.77873 6.31045 5.72797 6 5.86846 6H6.69164C6.879 6 6.81532 6.28805 6.81532 6.40871C7.33441 6.54145 7.6035 6.66753 7.90506 7.03572C7.95384 7.09527 7.95384 7.18775 7.90506 7.21916L7.25958 7.63485C6.95225 7.33789 6.11864 6.67075 5.8163 7.24893C5.51859 7.81825 6.42227 8.04361 6.81532 8.11169C7.64895 8.25607 8 8.79287 8 9.26971C8 9.74654 7.68405 10.2214 6.81532 10.4277C6.81532 10.5463 6.87135 10.7716 6.69774 10.7683H5.89895C5.72594 10.7683 5.77873 10.5432 5.77873 10.4277C5.21751 10.2713 4.95557 10.1181 4.71176 9.82929C4.64283 9.74764 4.66114 9.64477 4.71176 9.61935L5.40852 9.26971C5.55652 9.45408 5.78168 9.54156 6.00086 9.6103Z" fill="#1944B0"/></svg>';
            }
        },
        updateStatus() {
            this.statusForm.post(route('quotes.update-status', this.quoteSelected.id), {
                onSuccess: () => {
                    // actualizar info de la vista
                    this.quoteSelected.remaining = this.quoteSelected.remaining != null
                        ? this.quoteSelected.remaining - this.statusForm.amount
                        : null;

                    this.quoteSelected.status = this.quoteSelected.remaining === 0
                        ? 'Pagado'
                        : this.statusForm.status;

                    this.paymentConfirmed = true; //indica que el pago ha sido confirmado
                    setTimeout(() => {
                        this.paymentConfirmed = false;
                        // Aquí cierra el modal como lo manejes normalmente
                        this.showPaymentModal = false; // ajusta este método a tu implementación
                        this.paymentModalStep = 1; //reinicia el paso del modal de pago
                    }, 1500);
                },
                onError: (error) => {
                    console.error(error);
                }
            })
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('quotes.destroy', this.quoteSelected));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la cotización',
                        type: 'success',
                    });
                    //se busca el index del cliente eliminado para removerlo del arreglo
                    const indexQuoteDeleted = this.quotes.findIndex(item => item.id == this.quoteSelected);
                    if (indexQuoteDeleted != -1) {
                        this.quotes.splice(indexQuoteDeleted, 1);
                    }
                    this.showDeleteConfirm = false;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar la cotización. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'warning',
                });
            }
        },
    },
}
</script>
<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
}
</style>
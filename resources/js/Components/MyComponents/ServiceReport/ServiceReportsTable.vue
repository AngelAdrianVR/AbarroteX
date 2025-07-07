<template>
    <div class="overflow-auto">
        <!-- <div class="flex justify-end h-5">
            <el-button v-show="selectedReports.length" :disabled="!selectedReports.length" type="danger"
                @click="deleteSelected">Eliminar ({{ selectedReports.length }})</el-button>
        </div> -->
        <el-table ref="tableRef" :data="internalReports" @row-click="handleRowClick" max-height="640"
            :row-class-name="tableRowClassName" :default-sort="{ prop: 'folio', order: 'descending' }"
            class="!w-full mx-auto">
            <el-table-column prop="folio" sortable label="Orden" width="110" :filters="[
                { text: 'Recibida', value: 'Recibida' },
                { text: 'En proceso', value: 'En proceso' },
                { text: 'Listo para entregar', value: 'Listo para entregar' },
                { text: 'Entregado/Pagado', value: 'Entregado/Pagado' },
            ]" :filter-method="filterStatus">
                <template #default="scope">
                    <td class="mt-[5px]">
                        <div class="flex items-center space-x-2">
                            <el-tooltip placement="top">
                                <template #content>
                                    <div class="text-sm">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm">
                                                {{ scope.row.status }}
                                            </span>
                                        </div>
                                        <div v-if="scope.row.status === 'Cancelada'">
                                            <p class="text-blue-300">
                                                Razón: <span class="text-white">{{ scope.row.cancellation_reason ?? '-'
                                                }}</span>
                                            </p>
                                            <p class="text-blue-300">
                                                Monto de revisión: <span class="text-white">${{
                                                    scope.row.aditionals?.review_amount ?? '0.00' }}</span>
                                            </p>
                                            <p class="text-blue-300">
                                                Adelanto regresado: <span class="text-white">${{
                                                    scope.row.aditionals?.advance_amount ?? '0.00' }}</span>
                                            </p>
                                        </div>
                                        <div v-if="scope.row.status === 'Entregado/Pagado'">
                                            <p class="text-green-300">
                                                Pagado el: <span class="text-white">{{ formatDate(scope.row.paid_at) ??
                                                    '-'
                                                }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                                <span v-html="getStatusIcon(scope.row.status)"></span>
                            </el-tooltip>
                            <span>{{ String(scope.row.folio).padStart(3, '0') }}</span>
                        </div>
                    </td>
                </template>
            </el-table-column>
            <el-table-column label="Equipo" width="150">
                <template #default="scope">
                    <p>
                        <span v-if="scope.row.product_details?.brand">{{ scope.row.product_details?.brand }}</span>
                        <span v-if="scope.row.product_details?.model">{{ ' ' + scope.row.product_details?.model
                            }}</span>
                    </p>
                </template>
            </el-table-column>
            <el-table-column prop="service_date" sortable label="Fecha del servicio">
                <template #default="scope">
                    <p>{{ formatDate(scope.row.service_date) }}</p>
                </template>
            </el-table-column>
            <el-table-column label="Cliente">
                <template #default="scope">
                    <p>{{ scope.row.client_name ?? 'No especificado' }}</p>
                </template>
            </el-table-column>
            <el-table-column prop="service_description" label="Servicio" />
            <el-table-column label="Total a pagar">
                <template #default="scope">
                    <p>
                        ${{ scope.row.total_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                            ?? scope.row.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                    </p>
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template #default="scope">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button @click.stop
                            class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item :command="'see|' + encodeId(scope.row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="text-xs">Ver</span>
                                </el-dropdown-item>
                                <el-dropdown-item
                                    v-if="canEdit && scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                    :command="'edit|' + encodeId(scope.row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                    <span class="text-xs">Editar</span>
                                </el-dropdown-item>
                                <el-dropdown-item v-if="canDelete" :command="'delete|' + scope.row.id">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2 text-red-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span class="text-xs text-red-600">Eliminar</span>
                                </el-dropdown-item>
                                <div v-if="canEdit">
                                    <p v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                        class="text-gray-400 text-xs text-center px-3 my-1">Cambiar estatus</p>
                                    <el-dropdown-item
                                        v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                        :command="'changeStatus|' + scope.row.id + '|Recibida'">
                                        <svg class="size-[14px] mr-2" width="17" height="13" viewBox="0 0 17 13"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M5.99232 8.28937C6.49168 11.3688 10.4533 11.5186 11.219 8.28937H16.5766L13.72 6.63869C13.5993 6.56894 13.4785 6.51799 13.4328 6.51799H12.0013C11.8692 6.51799 11.8692 6.39728 12.0013 6.39181H13.4328C13.5826 6.39181 13.7324 6.49117 13.8489 6.55826C15.136 7.2993 15.5913 7.54833 16.8784 8.28937V11.6517C16.8784 12.1511 16.6453 13 15.9629 13H1.38156C0.516005 13 0 12.3841 0 12.0845V8.28937L3.44558 6.44174C3.50845 6.40803 3.66134 6.39181 3.82228 6.39181C4.20512 6.39181 4.92702 6.37516 5.3598 6.39181C5.47189 6.39728 5.4773 6.51799 5.3598 6.51799H3.82228C3.70157 6.51799 3.6211 6.51547 3.54063 6.55822L0.281641 8.28937H5.99232Z"
                                                fill="currentColor" />
                                            <path
                                                d="M8.03969 5.01024V0H9.18822V5.01024H10.8028L8.50576 7.27401L6.242 5.01024H8.03969Z"
                                                fill="currentColor" />
                                        </svg>
                                        <span class="text-xs">Recibida</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                        :command="'changeStatus|' + scope.row.id + '|En proceso'">
                                        <svg class="size-[14px] mr-2" width="13" height="16" viewBox="0 0 13 16"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.57423 8.04782L6.69533 8.83421L4.42867 6.66007C2.16201 10.4995 6.16502 13.5988 8.83423 11.1471C9.64555 10.4019 10.3652 8.83421 9.23953 6.66007L10.7171 5.31857C11.6085 6.27663 12.1538 7.56113 12.1538 8.97298C12.1538 11.9365 9.75138 14.3389 6.78784 14.3389C3.8243 14.3389 1.42188 11.9365 1.42188 8.97298C1.42188 6.00944 3.8243 3.60702 6.78784 3.60702V1.52539M6.78784 1.52539H4.42867M6.78784 1.52539H8.83423"
                                                stroke="currentColor" stroke-width="1.38775" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <span class="text-xs">En proceso</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                        :command="'changeStatus|' + scope.row.id + '|Listo para entregar'">
                                        <svg class="size-[14px] mr-2" width="14" height="13" viewBox="0 0 14 13"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.05078 0C7.55666 0 8.93423 0.549765 9.99316 1.45996C10.9763 0.695148 11.5787 0.424377 12.6973 0.0917969C12.8806 0.0372847 13.0214 0.0457867 13.2021 0.0917969C13.2938 0.137854 13.4287 0.335015 13.5225 0.503906C13.5988 0.641428 13.6068 0.807884 13.5225 0.871094C12.573 1.58285 12.0774 2.0335 11.21 2.8877C11.7754 3.80807 12.1016 4.89139 12.1016 6.05078C12.1015 9.39261 9.39262 12.1016 6.05078 12.1016C2.70898 12.1015 2.75663e-05 9.39258 0 6.05078C0 2.70895 2.70896 4.42574e-05 6.05078 0ZM12.9268 0.273438C9.06853 1.70435 7.93919 3.53397 5.50098 6.50781C4.89269 5.61383 3.84319 4.7846 3.62207 4.6748C3.34619 4.53804 2.9344 4.5602 2.79688 4.6748L1.97168 5.3623C1.88146 5.44078 3.95789 7.5615 5.73047 9.0293C5.82127 9.10449 6.31822 9.12083 6.37207 9.0293C8.72076 4.99999 10.7269 2.52085 13.2939 0.640625C13.3617 0.594925 13.0184 0.239662 12.9268 0.273438Z"
                                                fill="currentColor" />
                                        </svg>
                                        <span class="text-xs">Listo para entregar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Entregado/Pagado'"
                                        :command="'changeStatus|' + scope.row.id + '|Entregado/Pagado'">
                                        <svg class="size-[14px] mr-2" width="13" height="13" viewBox="0 0 13 13"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.40723 0.1875C9.94524 0.187741 12.8132 3.05574 12.8135 6.59375C12.8135 10.132 9.94539 13.0007 6.40723 13.001C2.86886 13.001 0 10.1321 0 6.59375C0.000241568 3.05559 2.86901 0.1875 6.40723 0.1875ZM5.39062 2.17383C5.30076 2.17383 5.22465 2.25239 5.22461 2.37598V2.92676C5.15226 2.94346 5.08157 2.96237 5.0127 2.98242C4.87109 3.02365 4.73687 3.07186 4.61035 3.12598C4.45527 3.19231 4.31115 3.2677 4.17969 3.35059C4.02829 3.44608 3.89299 3.55199 3.77441 3.66504C3.61844 3.81377 3.49146 3.97588 3.39551 4.14551C3.31392 4.28981 3.25448 4.43958 3.21875 4.5918C3.19214 4.70535 3.17871 4.82081 3.17871 4.93555C3.17871 4.99099 3.17798 4.93565 3.18457 5.10449C3.19118 5.27394 3.23233 5.44728 3.29395 5.62109C3.33807 5.7455 3.39662 5.86975 3.47168 5.99219C3.55228 6.12365 3.77441 6.37793 3.77441 6.37793C3.78219 6.38515 3.99397 6.58161 4.125 6.6748C4.24214 6.7581 4.37287 6.83781 4.51758 6.91309C4.64679 6.9803 4.78742 7.0441 4.94043 7.10352C5.06309 7.15114 5.19406 7.19545 5.33301 7.2373C5.42943 7.26635 5.53013 7.29433 5.63477 7.32031L5.74512 7.34863C5.92466 7.39383 6.0829 7.43506 6.2207 7.47461C6.91937 7.67514 7.11354 7.8289 7.13477 8.19922C7.1609 8.65721 6.4355 9.07803 5.63477 8.82715C5.61678 8.82151 5.5478 8.80571 5.5293 8.7998C5.38084 8.75152 5.2483 8.70013 5.13086 8.64453C5.01725 8.59072 4.91656 8.53218 4.82715 8.46777C4.74231 8.40662 4.66683 8.33956 4.59961 8.26562C4.58007 8.24412 4.56124 8.22197 4.54297 8.19922L4.00684 8.46777L3.25879 8.84375C3.16568 8.89076 3.13186 9.08011 3.25879 9.23047C3.28036 9.25602 3.30243 9.28123 3.32422 9.30566C3.42222 9.41555 3.52316 9.51421 3.63184 9.60449C3.74171 9.69577 3.85969 9.77892 3.99121 9.85547C4.12628 9.93407 4.27588 10.0064 4.44531 10.0752C4.56237 10.1227 4.68898 10.1687 4.82715 10.2139C4.94997 10.254 5.08223 10.2933 5.22461 10.333V10.792C5.22461 10.9043 5.33403 10.9608 5.44629 10.9609H6.91895C7.05182 10.9634 7.13477 10.9267 7.13477 10.792V10.333C8.73553 9.95277 9.3183 9.07786 9.31836 8.19922C9.31836 7.32054 8.67094 6.3315 7.13477 6.06543C7.07268 6.05467 7.0122 6.04282 6.95312 6.03125C6.79834 6.00093 6.65368 5.9686 6.51953 5.93262C6.3521 5.8877 6.20073 5.83812 6.06543 5.78418C5.91946 5.72598 5.7922 5.66175 5.68359 5.59277C5.55819 5.51309 5.45743 5.42649 5.38184 5.33203C5.315 5.24851 5.26814 5.15887 5.24023 5.06348C5.22809 5.02196 5.21962 4.97935 5.21484 4.93555C5.21134 4.90334 5.20948 4.87037 5.20996 4.83691C5.21069 4.78765 5.21525 4.73657 5.22461 4.68457C5.22875 4.66159 5.23462 4.63941 5.24023 4.61816C5.24669 4.59378 5.25341 4.57003 5.26172 4.54785C5.27123 4.52249 5.28216 4.49817 5.29395 4.47559C5.30856 4.44764 5.32494 4.42133 5.34277 4.39746C5.36803 4.36368 5.39672 4.33377 5.42773 4.30762C5.4443 4.29366 5.46146 4.28047 5.47949 4.26855C5.50172 4.25386 5.52556 4.24124 5.5498 4.22949C5.59181 4.20915 5.63652 4.19287 5.68359 4.18066C5.71695 4.17202 5.75173 4.16511 5.78711 4.16016C5.83049 4.15409 5.87522 4.15047 5.9209 4.14941C5.96815 4.14832 6.01658 4.1497 6.06543 4.15332C6.13013 4.15812 6.19603 4.167 6.26172 4.17871C6.30646 4.18669 6.35111 4.19645 6.39551 4.20703C6.45485 4.22118 6.51393 4.23708 6.57129 4.25488C6.6241 4.27127 6.67572 4.28908 6.72559 4.30762C6.74639 4.31536 6.76751 4.32363 6.78809 4.33203C6.87173 4.3662 6.95416 4.40604 7.03613 4.45215C7.12141 4.50014 7.20669 4.55555 7.29395 4.61816C7.3742 4.67577 7.45594 4.74015 7.54102 4.81152C7.66957 4.91938 7.80594 5.04336 7.9541 5.18652L9.14355 4.4209C9.23344 4.36301 9.23344 4.19178 9.14355 4.08203C8.87033 3.74846 8.61086 3.52295 8.30469 3.35059C8.19481 3.28875 8.07906 3.23323 7.9541 3.18262C7.84482 3.13835 7.72843 3.09789 7.60352 3.05859C7.52103 3.03265 7.43444 3.00742 7.34375 2.98242C7.2767 2.96394 7.20682 2.94518 7.13477 2.92676V2.37598C7.13471 2.22994 7.01956 2.17383 6.90723 2.17383H5.39062Z"
                                                fill="currentColor" />
                                        </svg>
                                        <span class="text-xs">Entregado/Pagado</span>
                                    </el-dropdown-item>
                                </div>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <Modal :show="showPaymentModal" @close="showPaymentModal = false">
            <div v-if="paymentModalStep === 1" class="py-4 px-7 relative">
                <ThirthButton class="absolute right-3 !py-1 flex items-center space-x-2 !text-red-600 !bg-[#FFB8B8]"
                    @click="showPaymentModal = false">
                    <span>Cancelar pago</span>
                    <i class="fa-solid fa-xmark"></i>
                </ThirthButton>
                <h1 class="font-bold mt-2">Opciones de pago</h1>
                <p class="text-gray99">Seleccione el método de pago</p>
                <section class="grid grid-cols-2 gap-4 mt-3 py-7">
                    <button @click="paymentModalStep = 2; paymentMethod = 'Efectivo'; receivedInputFocus()"
                        type="button"
                        class="bg-[#E0FEC5] text-[#37672B] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
                        <p class="text-lg text-center font-bold">EFECTIVO</p>
                        <img src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
                    </button>
                    <button @click="paymentModalStep = 3; paymentMethod = 'Tarjeta'" type="button"
                        class="bg-[#DAE6FF] text-[#063B52] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
                        <p class="text-lg text-center font-bold">TARJETA</p>
                        <img src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                    </button>
                </section>
            </div>
            <!-- Pago con efectivo (step 2) -->
            <div v-if="paymentModalStep === 2" class="py-4 px-7 relative">
                <section class="flex items-center justify-between">
                    <h1 class="font-bold mt-2 text-lg">Pagar</h1>
                    <div @click="paymentModalStep = 1" class="flex items-center space-x-4 text-primary cursor-pointer">
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
                    <div
                        class="rounded-full border border-[#D9D9D9D] bg-[#F2F2F2] py-2 px-4 flex items-center justify-between mt-3">
                        <span class="font-bold">Total a pagar</span>
                        <p class="font-bold"><span class="mr-4">$</span>{{
                            reportSelected.total_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                    </div>
                    <div v-if="!paymentConfirmed" class="mt-5 flex flex-col items-center space-y-3">
                        <p class="text-center font-bold">¿Con cuánto paga el cliente?</p>
                        <el-input-number ref="receivedInput" @keydown.enter="changeStatus('Entregado/Pagado')"
                            v-model="moneyReceived" :min="1" :max="999999">
                            <template #prefix>
                                <span>$</span>
                            </template>
                        </el-input-number>
                        <p class="pt-5 font-bold">Cambio</p>
                        <div
                            class="rounded-full border border-[#D9D9D9D] bg-[#E0FEC5] py-2 px-7 flex items-center justify-between">
                            <p v-if="reportSelected.total_cost <= moneyReceived" class="font-bold">
                                <span class="mr-5">$</span>
                                {{
                                    (moneyReceived - reportSelected.total_cost)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",")
                                }}
                            </p>
                        </div>
                        <p v-if="(reportSelected.total_cost > moneyReceived) && moneyReceived"
                            class="text-base font-bold text-red-600 text-center mb-3">
                            La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total a pagar.
                        </p>
                        <div class="flex justify-center mt-7">
                            <PrimaryButton :disabled="updatingStatus" class="!px-20"
                                @click="changeStatus('Entregado/Pagado', reportSelected.id)">
                                <i v-if="updatingStatus"
                                    class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                                Confirmar pago
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Confirmacion de pago -->
                    <template v-else>
                        <div class="flex flex-col items-center space-y-4 animate-fade-in-up">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-green-600 font-bold text-lg">¡Pago realizado con éxito!</p>
                        </div>
                    </template>
                </section>
            </div>

            <!-- Pago con tarjeta (step 3) -->
            <div v-if="paymentModalStep === 3" class="py-4 px-7 relative">
                <section class="flex items-center justify-between">
                    <h1 class="font-bold mt-2 text-lg">Registrar pago</h1>
                    <div @click="paymentModalStep = 1" class="flex items-center space-x-4 text-primary cursor-pointer">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Regresar</span>
                    </div>
                </section>

                <section class="mx-auto mt-2 md:w-2/3">
                    <div v-if="!paymentConfirmed">
                        <p class="my-3 text-sm text-center">El sistema no procesa pagos con tarjeta. Usa tu terminal
                            bancaria
                            externa y
                            luego registra aquí el pago.</p>
                        <div
                            class="rounded-full border border-[#D9D9D9D] bg-[#DAE6FF] py-2 px-4 flex items-center justify-between mt-3">
                            <span class="font-bold text-[#05394F]">TARJETA</span>
                            <img src="@/../../public/images/card.webp" alt="Pago con tarjeta" class="h-7">
                        </div>

                        <div
                            class="rounded-full border border-[#D9D9D9D] bg-[#F2F2F2] py-2 px-4 flex items-center justify-between mt-3">
                            <span class="font-bold">Total a pagar</span>
                            <p class="font-bold"><span class="mr-4">$</span>{{
                                reportSelected.total_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>

                        <div class="flex justify-center mt-7">
                            <PrimaryButton @click="changeStatus('Entregado/Pagado', reportSelected.id)"
                                :disabled="updatingStatus" class="!px-20">
                                <i v-if="updatingStatus"
                                    class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                                Confirmar pago
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Confirmacion de pago -->
                    <template v-else>
                        <div class="flex flex-col items-center space-y-4 animate-fade-in-up">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-green-600 font-bold text-lg">¡Pago realizado con éxito!</p>
                        </div>
                    </template>
                </section>
            </div>
        </Modal>
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar servicio</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará el servicio seleccionado, esto es un proceso irreversible. ¿Continuar
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
    </div>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import axios from 'axios';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        return {
            showDeleteConfirm: false,
            itemIdToDelete: null,
            canEdit: this.$page.props.auth.user.permissions.includes('Editar ordenes de servicio'),
            canDelete: this.$page.props.auth.user.permissions.includes('Eliminar ordenes de servicio'),
            selectedReports: [],
            checkAll: false,
            showPaymentModal: false,
            internalReports: [...this.reports], // copia local editable
            reportSelected: null, // Reporte seleccionado para pagar

            // modal de pago
            paymentModalStep: 1, //paso del modal de pago
            paymentMethod: '', //Método de pago seleccionado
            paymentConfirmed: false, //indica si el pago ha sido confirmado
            moneyReceived: null, // Monto recibido en efectivo
            updatingStatus: false, // Estado para indicar si se está actualizando el estado
        }
    },
    watch: {
        reports(newReports) {
            this.internalReports = [...newReports]
        }
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        Modal
    },
    props: {
        reports: Object,
    },
    computed: {
        isIndeterminate() {
            return (
                this.selectedReports.length > 0 &&
                this.selectedReports.length < this.internalReports.length
            )
        },
    },
    methods: {
        filterStatus(status, row) {
            return row.status == status;
        },
        handleRowClick(row) {
            this.$inertia.get(route('service-reports.show', this.encodeId(row.id)));
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        getStatusIcon(status) {
            if (status === 'Recibida') {
                return '<svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.99232 8.28937C6.49168 11.3688 10.4533 11.5186 11.219 8.28937H16.5766L13.72 6.63869C13.5993 6.56894 13.4785 6.51799 13.4328 6.51799H12.0013C11.8692 6.51799 11.8692 6.39728 12.0013 6.39181H13.4328C13.5826 6.39181 13.7324 6.49117 13.8489 6.55826C15.136 7.2993 15.5913 7.54833 16.8784 8.28937V11.6517C16.8784 12.1511 16.6453 13 15.9629 13H1.38156C0.516005 13 0 12.3841 0 12.0845V8.28937L3.44558 6.44174C3.50845 6.40803 3.66134 6.39181 3.82228 6.39181C4.20512 6.39181 4.92702 6.37516 5.3598 6.39181C5.47189 6.39728 5.4773 6.51799 5.3598 6.51799H3.82228C3.70157 6.51799 3.6211 6.51547 3.54063 6.55822L0.281641 8.28937H5.99232Z" fill="#008796"/><path d="M8.03969 5.01024V0H9.18822V5.01024H10.8028L8.50576 7.27401L6.242 5.01024H8.03969Z" fill="#008796"/></svg>';
            } else if (status === 'En proceso') {
                return '<svg width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.30603 7.68534L6.40517 8.49138L4.0819 6.26293C1.75862 10.1983 5.86163 13.375 8.59751 10.8621C9.42908 10.0983 10.1667 8.49138 9.01293 6.26293L10.5274 4.88793C11.4411 5.86991 12 7.1865 12 8.63362C12 11.6712 9.53757 14.1336 6.5 14.1336C3.46243 14.1336 1 11.6712 1 8.63362C1 5.59605 3.46243 3.13362 6.5 3.13362V1M6.5 1H4.0819M6.5 1H8.59751" stroke="#D0BF0A" stroke-width="1.42241" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else if (status === 'Listo para entregar') {
                return '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.79102 0C7.23226 0 8.55098 0.526321 9.56445 1.39746C10.5052 0.665618 11.0817 0.4062 12.1523 0.0878906C12.3277 0.0357537 12.462 0.043932 12.6348 0.0878906C12.7225 0.131741 12.8525 0.320628 12.9424 0.482422C13.0154 0.614025 13.0231 0.773476 12.9424 0.833984C12.0334 1.51535 11.5589 1.94701 10.7285 2.76465C11.2696 3.64538 11.582 4.6816 11.582 5.79102C11.582 8.98935 8.98935 11.582 5.79102 11.582C2.59272 11.582 0 8.98932 0 5.79102C0.000165821 2.59285 2.59282 3.99418e-05 5.79102 0ZM12.3633 0.265625C8.67076 1.63508 7.59033 3.3863 5.25684 6.23242C4.67491 5.37696 3.67011 4.58305 3.45801 4.47754C3.19398 4.34665 2.79958 4.36786 2.66797 4.47754L1.87793 5.13477C1.78989 5.20833 3.7785 7.23922 5.47559 8.64453C5.56253 8.71653 6.0387 8.73228 6.08984 8.64453C8.33761 4.7884 10.2571 2.41565 12.7139 0.616211C12.7794 0.572938 12.4514 0.233826 12.3633 0.265625Z" fill="#AA5409"/></svg>';
            } else if (status === 'Entregado/Pagado') {
                return '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 0C10.0898 4.60442e-05 13 2.91022 13 6.5C12.9999 10.0897 10.0897 13 6.5 13C2.91023 13 5.05231e-05 10.0898 0 6.5C0 2.91019 2.91019 0 6.5 0ZM5.4707 2.01562C5.37968 2.01575 5.30286 2.09554 5.30273 2.2207V2.78027C5.22939 2.79721 5.15772 2.81561 5.08789 2.83594C4.94423 2.87775 4.80804 2.92656 4.67969 2.98145C4.52242 3.04871 4.37649 3.12494 4.24316 3.20898C4.08938 3.30595 3.95147 3.41352 3.83105 3.52832C3.67288 3.67915 3.5446 3.84361 3.44727 4.01562C3.36431 4.16228 3.30384 4.31502 3.26758 4.46973C3.24063 4.58479 3.22657 4.70112 3.22656 4.81738C3.22656 4.87389 3.2267 4.81759 3.2334 4.98926C3.2401 5.16108 3.28129 5.33645 3.34375 5.5127C3.38852 5.639 3.44821 5.76535 3.52441 5.88965C3.60438 6.02006 3.82144 6.26929 3.83105 6.28027C3.83105 6.28027 4.05208 6.48574 4.1875 6.58203C4.30631 6.6665 4.43821 6.74787 4.58496 6.82422C4.71609 6.89244 4.85935 6.9563 5.01465 7.0166C5.13899 7.06488 5.27127 7.11089 5.41211 7.15332C5.50996 7.1828 5.61257 7.21093 5.71875 7.2373L5.83105 7.26562C6.01306 7.31145 6.17278 7.35346 6.3125 7.39355C7.02167 7.59706 7.21967 7.75313 7.24121 8.12891C7.26757 8.59353 6.53111 9.02023 5.71875 8.76562C5.70037 8.75986 5.62946 8.74417 5.61133 8.73828C5.46072 8.68929 5.32715 8.63746 5.20801 8.58105C5.09254 8.52639 4.99026 8.4668 4.89941 8.40137C4.81334 8.33932 4.73715 8.2713 4.66895 8.19629C4.64909 8.17445 4.62989 8.15202 4.61133 8.12891L3.30859 8.78223C3.21397 8.82974 3.17972 9.02215 3.30859 9.1748C3.33042 9.20066 3.35198 9.22625 3.37402 9.25098C3.47354 9.36259 3.57616 9.463 3.68652 9.55469C3.79801 9.64731 3.91831 9.73092 4.05176 9.80859C4.18868 9.88827 4.33999 9.96152 4.51172 10.0312C4.63053 10.0795 4.75916 10.127 4.89941 10.1729C5.02405 10.2136 5.15825 10.2537 5.30273 10.2939V10.7598C5.30297 10.8735 5.41445 10.9307 5.52832 10.9307H7.02148C7.15628 10.9332 7.24106 10.8962 7.24121 10.7598V10.2939C8.86516 9.90819 9.45595 9.02028 9.45605 8.12891C9.45605 7.23747 8.79963 6.23384 7.24121 5.96387C7.17819 5.95295 7.11661 5.94143 7.05664 5.92969C6.89958 5.89893 6.75233 5.86562 6.61621 5.8291C6.44663 5.7836 6.29334 5.73335 6.15625 5.67871C6.00828 5.61971 5.87868 5.55526 5.76855 5.48535C5.64118 5.40446 5.53866 5.31564 5.46191 5.21973C5.3942 5.13504 5.34664 5.04397 5.31836 4.94727C5.30605 4.90515 5.29682 4.86182 5.29199 4.81738C5.28844 4.78464 5.28758 4.75081 5.28809 4.7168C5.28885 4.66686 5.29323 4.6152 5.30273 4.5625C5.30695 4.53918 5.31265 4.51668 5.31836 4.49512C5.32493 4.47031 5.33236 4.44638 5.34082 4.42383C5.3505 4.39804 5.36105 4.37353 5.37305 4.35059C5.38789 4.32219 5.40472 4.29573 5.42285 4.27148C5.44844 4.23729 5.47738 4.20714 5.50879 4.18066C5.52556 4.16653 5.54327 4.15367 5.56152 4.1416C5.58408 4.12669 5.60822 4.11348 5.63281 4.10156C5.67544 4.08093 5.72079 4.06414 5.76855 4.05176C5.80233 4.04301 5.83723 4.03627 5.87305 4.03125C5.91702 4.02509 5.96248 4.02159 6.00879 4.02051C6.05677 4.0194 6.10665 4.02074 6.15625 4.02441C6.22166 4.02928 6.28809 4.03798 6.35449 4.0498C6.39999 4.05791 6.44607 4.06736 6.49121 4.07812C6.55132 4.09247 6.61084 4.10892 6.66895 4.12695C6.72265 4.14362 6.77547 4.16181 6.82617 4.18066C6.84713 4.18846 6.86795 4.19662 6.88867 4.20508C6.97346 4.2397 7.05656 4.28043 7.13965 4.32715C7.2263 4.37589 7.31368 4.43149 7.40234 4.49512C7.48381 4.55358 7.56696 4.61895 7.65332 4.69141C7.78367 4.80077 7.92108 4.92712 8.07129 5.07227L9.27832 4.29492C9.36951 4.23619 9.36951 4.06349 9.27832 3.95215C9.0011 3.61369 8.73838 3.38484 8.42773 3.20996C8.31617 3.14716 8.19818 3.09144 8.07129 3.04004C7.9604 2.99513 7.84257 2.95295 7.71582 2.91309C7.6323 2.88682 7.54493 2.86125 7.45312 2.83594C7.38515 2.8172 7.31425 2.79895 7.24121 2.78027V2.2207C7.24105 2.07269 7.12368 2.01562 7.00977 2.01562H5.4707Z" fill="#3EA50E"/></svg>';
            } else if (status === 'Cancelada') {
                return '<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.7 4.3L6.5 6.5M4.3 8.7L6.5 6.5M6.5 6.5L4.3 4.3M6.5 6.5L8.7 8.7M12 6.5C12 9.53757 9.53757 12 6.5 12C3.46243 12 1 9.53757 1 6.5C1 3.46243 3.46243 1 6.5 1C9.53757 1 12 3.46243 12 6.5Z" stroke="#B80505" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
        },
        toggleSelectAll(val) {
            this.selectedReports = val ? this.internalReports.map(r => r.id) : []
        },
        handleCheckChange() {
            const total = this.internalReports.length
            const checked = this.selectedReports.length
            this.checkAll = checked === total
        },
        handleCheckboxChange(checked, id) {
            if (checked) {
                if (!this.selectedReports.includes(id)) {
                    this.selectedReports.push(id)
                }
            } else {
                this.selectedReports = this.selectedReports.filter(rid => rid !== id)
            }
            this.handleCheckChange()
        },
        async deleteSelected() {
            try {
                const response = await axios.post(route('service-reports.massive-delete'), {
                    ids: this.selectedReports,
                })

                if (response.status === 200) {
                    // Filtrar los reportes eliminados
                    this.internalReports = this.internalReports.filter(
                        report => !this.selectedReports.includes(report.id)
                    )
                    this.selectedReports = []
                    this.checkAll = false

                    this.$notify({
                        title: 'Correcto',
                        message: response.data.message,
                        type: 'success',
                    });
                }
            } catch (error) {
                console.error('Error al eliminar:', error)
                this.$notify({
                    title: 'Error',
                    message: 'Hubo un error al eliminar las ordenes',
                    type: 'error',
                });
            }
        },
        formatDate(dateString) {
            if (!dateString) return '';
            return format(parseISO(dateString), 'dd MMM yyyy', { locale: es });
        },
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];
            const newStatus = command.split('|')[2];

            if (commandName == 'see') {
                this.$inertia.get(route('service-reports.show', data));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('service-reports.edit', data));
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            } else if (commandName === 'changeStatus') {
                if (newStatus === 'Entregado/Pagado') {
                    this.reportSelected = this.internalReports.find(r => r.id == data);
                    this.showPaymentModal = true;
                } else {
                    this.changeStatus(newStatus, data);
                }
            }
        },
        async changeStatus(status, reportId) {
            try {
                const response = await axios.post(route('service-reports.change-status', reportId), { status, paymentMethod: this.paymentMethod });
                if (response.status === 200) {
                    const reportIndex = this.internalReports.findIndex(r => r.id == reportId);

                    if (reportIndex !== -1) {
                        this.internalReports[reportIndex].status = status;
                        this.$notify({
                            title: 'Correcto',
                            message: 'Se ha cambiado el estatus',
                            type: 'success',
                        });
                    }

                    if (status === 'Entregado/Pagado') {
                        this.paymentConfirmed = true; //indica que el pago ha sido confirmado
                        setTimeout(() => {
                            this.paymentConfirmed = false;
                            // Aquí cierra el modal como lo manejes normalmente
                            this.showPaymentModal = false; // ajusta este método a tu implementación
                            this.paymentModalStep = 1; //reinicia el paso del modal de pago
                            this.updatingStatus = false; // Restablece el estado de actualización
                        }, 1000);
                    }
                }
            } catch (error) {
                console.log(error);
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        receivedInputFocus() {
            this.$nextTick(() => {
                this.$refs.receivedInput.focus(); // Enfocar el input de recibido cuando se abre el modal
            });
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('service-reports.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado la orden',
                        type: 'success',
                    });
                    //se busca el index del cliente eliminado para removerlo del arreglo
                    const indexServiceDeleted = this.internalReports.findIndex(item => item.id == this.itemIdToDelete);
                    if (indexServiceDeleted != -1) {
                        this.internalReports.splice(indexServiceDeleted, 1);
                    }
                    this.showDeleteConfirm = false;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'El servidor no pudo procesar la petición',
                    message: 'No se pudo eliminar la orden. Intente más tarde o si el problema persiste, contacte a soporte',
                    type: 'error',
                });
            }
        },
    },
}
</script>

<style>
@keyframes fade-in {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in 0.4s ease-out;
}

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
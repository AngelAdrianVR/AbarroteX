<template>
    <AppLayout :title="'Orden-' + String(report.folio).padStart(3, '0')">

        <main class="py-4 px-1 lg:px-8">
            <Back :to="route('service-reports.index')" />
            <h1 class="text-lg md:text-2xl font-bold">Orden de servicio No. {{ String(report.folio).padStart(3, '0') }}
            </h1>
            <div class="md:flex justify-between mt-3 mb-5">
                <div>
                    <p class="text-[#999999]">Fecha de recepción: <span class="text-black">{{
                        formatDateTime(report.created_at) }}</span></p>
                    <p class="text-[#999999]">Fecha de entrega: <span class="text-black">
                            {{ report.paid_at ? formatDateTime(report.paid_at) : 'Equipo no entregado aún' }}
                        </span></p>
                </div>
                <el-dropdown v-if="report.status !== 'Cancelada' && canEdit"
                    :disabled="report.status === 'Cancelada'" :split-button="canEdit" trigger="click" type="primary" @click="report.status !== 'Cancelada' && report.status !== 'Entregado/Pagado'
                        ? $inertia.get(route('service-reports.edit', encodeId(report.id)))
                        : ''">
                    <span v-if="canEdit">Editar</span>
                    <span v-else class="bg-primary text-white px-3 py-2 rounded-md cursor-pointer self-start">
                        Opciones
                        <i class="fa-solid fa-chevron-down ml-2 text-sm"></i>
                    </span>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item @click="printTemplate" :disabled="report.status === 'Cancelada'">
                                <svg class="mr-1" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.375 8.3125V6.78125C11.375 6.2591 11.1676 5.75835 10.7984 5.38913C10.4292 5.01992 9.92839 4.8125 9.40625 4.8125H8.53125C8.3572 4.8125 8.19028 4.74336 8.06721 4.62029C7.94414 4.49722 7.875 4.3303 7.875 4.15625V3.28125C7.875 2.7591 7.66758 2.25835 7.29837 1.88913C6.92915 1.51992 6.42839 1.3125 5.90625 1.3125H4.8125M4.8125 8.75H9.1875M4.8125 10.5H7M6.125 1.3125H3.28125C2.919 1.3125 2.625 1.6065 2.625 1.96875V12.0312C2.625 12.3935 2.919 12.6875 3.28125 12.6875H10.7187C11.081 12.6875 11.375 12.3935 11.375 12.0312V6.5625C11.375 5.17011 10.8219 3.83476 9.83731 2.85019C8.85274 1.86562 7.51739 1.3125 6.125 1.3125Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Comprobante de servicio</el-dropdown-item>
                            <el-dropdown-item :disabled="report.status === 'Cancelada'"
                                @click="handleTicketPrinting('ESC/POS', 'orden')">
                                <svg class="mr-1" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.92 8.06692C3.78 8.08442 3.64 8.10308 3.5 8.12292M3.92 8.06692C5.96532 7.81031 8.03468 7.81031 10.08 8.06692M3.92 8.06692L3.69833 10.5M10.08 8.06692C10.22 8.08442 10.36 8.10308 10.5 8.12292M10.08 8.06692L10.3017 10.5L10.4352 11.9718C10.4435 12.0626 10.4328 12.1541 10.4037 12.2405C10.3746 12.3269 10.3279 12.4063 10.2664 12.4737C10.2049 12.541 10.1301 12.5948 10.0467 12.6316C9.96327 12.6684 9.87309 12.6875 9.78192 12.6875H4.21808C3.83192 12.6875 3.52975 12.3562 3.56475 11.9718L3.69833 10.5M3.69833 10.5H3.0625C2.7144 10.5 2.38056 10.3617 2.13442 10.1156C1.88828 9.86944 1.75 9.5356 1.75 9.1875V5.516C1.75 4.88542 2.198 4.34058 2.82158 4.24725C3.19255 4.19176 3.5646 4.14372 3.9375 4.10317M10.3005 10.5H10.9369C11.1093 10.5001 11.2801 10.4662 11.4394 10.4003C11.5987 10.3343 11.7434 10.2377 11.8654 10.1158C11.9873 9.9939 12.084 9.84918 12.15 9.68991C12.216 9.53063 12.25 9.35991 12.25 9.1875V5.516C12.25 4.88542 11.802 4.34058 11.1784 4.24725C10.8074 4.19176 10.4354 4.14372 10.0625 4.10317M10.0625 4.10317C8.02684 3.88168 5.97316 3.88168 3.9375 4.10317M10.0625 4.10317V1.96875C10.0625 1.6065 9.7685 1.3125 9.40625 1.3125H4.59375C4.2315 1.3125 3.9375 1.6065 3.9375 1.96875V4.10317M10.5 6.125H10.5047V6.12967H10.5V6.125ZM8.75 6.125H8.75467V6.12967H8.75V6.125Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Imprimir ticket de orden
                            </el-dropdown-item>
                            <el-dropdown-item v-if="report.status == 'Entregado/Pagado'"
                                :disabled="report.status === 'Cancelada'"
                                @click="handleTicketPrinting('ESC/POS', 'comprobante')">
                                <svg class="mr-1" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.92 8.06692C3.78 8.08442 3.64 8.10308 3.5 8.12292M3.92 8.06692C5.96532 7.81031 8.03468 7.81031 10.08 8.06692M3.92 8.06692L3.69833 10.5M10.08 8.06692C10.22 8.08442 10.36 8.10308 10.5 8.12292M10.08 8.06692L10.3017 10.5L10.4352 11.9718C10.4435 12.0626 10.4328 12.1541 10.4037 12.2405C10.3746 12.3269 10.3279 12.4063 10.2664 12.4737C10.2049 12.541 10.1301 12.5948 10.0467 12.6316C9.96327 12.6684 9.87309 12.6875 9.78192 12.6875H4.21808C3.83192 12.6875 3.52975 12.3562 3.56475 11.9718L3.69833 10.5M3.69833 10.5H3.0625C2.7144 10.5 2.38056 10.3617 2.13442 10.1156C1.88828 9.86944 1.75 9.5356 1.75 9.1875V5.516C1.75 4.88542 2.198 4.34058 2.82158 4.24725C3.19255 4.19176 3.5646 4.14372 3.9375 4.10317M10.3005 10.5H10.9369C11.1093 10.5001 11.2801 10.4662 11.4394 10.4003C11.5987 10.3343 11.7434 10.2377 11.8654 10.1158C11.9873 9.9939 12.084 9.84918 12.15 9.68991C12.216 9.53063 12.25 9.35991 12.25 9.1875V5.516C12.25 4.88542 11.802 4.34058 11.1784 4.24725C10.8074 4.19176 10.4354 4.14372 10.0625 4.10317M10.0625 4.10317C8.02684 3.88168 5.97316 3.88168 3.9375 4.10317M10.0625 4.10317V1.96875C10.0625 1.6065 9.7685 1.3125 9.40625 1.3125H4.59375C4.2315 1.3125 3.9375 1.6065 3.9375 1.96875V4.10317M10.5 6.125H10.5047V6.12967H10.5V6.125ZM8.75 6.125H8.75467V6.12967H8.75V6.125Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Imprimir comprobante de pago
                            </el-dropdown-item>
                            <el-dropdown-item :disabled="report.status === 'Cancelada'"
                                @click="handleTicketPrinting('TSPL')">
                                <svg class="mr-1" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.1875 2.84375C2.1875 2.4815 2.4815 2.1875 2.84375 2.1875H5.46875C5.831 2.1875 6.125 2.4815 6.125 2.84375V5.46875C6.125 5.831 5.831 6.125 5.46875 6.125H2.84375C2.6697 6.125 2.50278 6.05586 2.37971 5.93279C2.25664 5.80972 2.1875 5.6428 2.1875 5.46875V2.84375ZM2.1875 8.53125C2.1875 8.169 2.4815 7.875 2.84375 7.875H5.46875C5.831 7.875 6.125 8.169 6.125 8.53125V11.1562C6.125 11.5185 5.831 11.8125 5.46875 11.8125H2.84375C2.6697 11.8125 2.50278 11.7434 2.37971 11.6203C2.25664 11.4972 2.1875 11.3303 2.1875 11.1562V8.53125ZM7.875 2.84375C7.875 2.4815 8.169 2.1875 8.53125 2.1875H11.1562C11.5185 2.1875 11.8125 2.4815 11.8125 2.84375V5.46875C11.8125 5.831 11.5185 6.125 11.1562 6.125H8.53125C8.3572 6.125 8.19028 6.05586 8.06721 5.93279C7.94414 5.80972 7.875 5.6428 7.875 5.46875V2.84375Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M3.9375 3.9375H4.375V4.375H3.9375V3.9375ZM3.9375 9.625H4.375V10.0625H3.9375V9.625ZM9.625 3.9375H10.0625V4.375H9.625V3.9375ZM7.875 7.875H8.3125V8.3125H7.875V7.875ZM7.875 11.375H8.3125V11.8125H7.875V11.375ZM11.375 7.875H11.8125V8.3125H11.375V7.875ZM11.375 11.375H11.8125V11.8125H11.375V11.375ZM9.625 9.625H10.0625V10.0625H9.625V9.625Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Generar etiqueta
                            </el-dropdown-item>
                            <el-dropdown-item @click="confirmDeleteModal = true"
                                v-if="hasPermission('Eliminar ordenes de servicio')">
                                <svg class="mr-1" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8.59833 5.2503L8.3965 10.5003M5.6035 10.5003L5.40167 5.2503M11.2163 3.3778C11.4158 3.40814 11.6142 3.44022 11.8125 3.47464M11.2163 3.3778L10.5933 11.4762C10.5679 11.8059 10.419 12.1139 10.1763 12.3385C9.93357 12.5632 9.61503 12.6879 9.28433 12.6878H4.71567C4.38497 12.6879 4.06643 12.5632 3.82374 12.3385C3.58105 12.1139 3.43209 11.8059 3.40667 11.4762L2.78367 3.3778M11.2163 3.3778C10.5431 3.27602 9.86636 3.19878 9.1875 3.14622M2.78367 3.3778C2.58417 3.40755 2.38583 3.43964 2.1875 3.47405M2.78367 3.3778C3.45691 3.27602 4.13364 3.19878 4.8125 3.14622M9.1875 3.14622V2.61189C9.1875 1.92355 8.65667 1.34955 7.96833 1.32797C7.32294 1.30734 6.67706 1.30734 6.03167 1.32797C5.34333 1.34955 4.8125 1.92414 4.8125 2.61189V3.14622M9.1875 3.14622C7.73134 3.03368 6.26866 3.03368 4.8125 3.14622"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Eliminar orden
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>

            <body class="lg:grid grid-cols-4 gap-4">
                <section class="col-span-3">
                    <StatusStep v-if="report.status !== 'Cancelada'"
                        :activeStep="statuses.findIndex(status => status === report.status) + 1" :steps="statuses">
                        <template #icon-0>
                            <svg class="size-[19px] mr-2" width="17" height="13" viewBox="0 0 17 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.99232 8.28937C6.49168 11.3688 10.4533 11.5186 11.219 8.28937H16.5766L13.72 6.63869C13.5993 6.56894 13.4785 6.51799 13.4328 6.51799H12.0013C11.8692 6.51799 11.8692 6.39728 12.0013 6.39181H13.4328C13.5826 6.39181 13.7324 6.49117 13.8489 6.55826C15.136 7.2993 15.5913 7.54833 16.8784 8.28937V11.6517C16.8784 12.1511 16.6453 13 15.9629 13H1.38156C0.516005 13 0 12.3841 0 12.0845V8.28937L3.44558 6.44174C3.50845 6.40803 3.66134 6.39181 3.82228 6.39181C4.20512 6.39181 4.92702 6.37516 5.3598 6.39181C5.47189 6.39728 5.4773 6.51799 5.3598 6.51799H3.82228C3.70157 6.51799 3.6211 6.51547 3.54063 6.55822L0.281641 8.28937H5.99232Z"
                                    fill="#373737" />
                                <path
                                    d="M8.03969 5.01024V0H9.18822V5.01024H10.8028L8.50576 7.27401L6.242 5.01024H8.03969Z"
                                    fill="#373737" />
                            </svg>
                        </template>
                        <template #label-0>Recibida</template>

                        <template #icon-1>
                            <svg class="size-[19px] mr-2" width="13" height="16" viewBox="0 0 13 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.57423 8.04782L6.69533 8.83421L4.42867 6.66007C2.16201 10.4995 6.16502 13.5988 8.83423 11.1471C9.64555 10.4019 10.3652 8.83421 9.23953 6.66007L10.7171 5.31857C11.6085 6.27663 12.1538 7.56113 12.1538 8.97298C12.1538 11.9365 9.75138 14.3389 6.78784 14.3389C3.8243 14.3389 1.42188 11.9365 1.42188 8.97298C1.42188 6.00944 3.8243 3.60702 6.78784 3.60702V1.52539M6.78784 1.52539H4.42867M6.78784 1.52539H8.83423"
                                    stroke="#373737" stroke-width="1.38775" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </template>
                        <template #label-1>En proceso</template>

                        <template #icon-2>
                            <svg class="size-[14px] mr-2" width="14" height="13" viewBox="0 0 14 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.05078 0C7.55666 0 8.93423 0.549765 9.99316 1.45996C10.9763 0.695148 11.5787 0.424377 12.6973 0.0917969C12.8806 0.0372847 13.0214 0.0457867 13.2021 0.0917969C13.2938 0.137854 13.4287 0.335015 13.5225 0.503906C13.5988 0.641428 13.6068 0.807884 13.5225 0.871094C12.573 1.58285 12.0774 2.0335 11.21 2.8877C11.7754 3.80807 12.1016 4.89139 12.1016 6.05078C12.1015 9.39261 9.39262 12.1016 6.05078 12.1016C2.70898 12.1015 2.75663e-05 9.39258 0 6.05078C0 2.70895 2.70896 4.42574e-05 6.05078 0ZM12.9268 0.273438C9.06853 1.70435 7.93919 3.53397 5.50098 6.50781C4.89269 5.61383 3.84319 4.7846 3.62207 4.6748C3.34619 4.53804 2.9344 4.5602 2.79688 4.6748L1.97168 5.3623C1.88146 5.44078 3.95789 7.5615 5.73047 9.0293C5.82127 9.10449 6.31822 9.12083 6.37207 9.0293C8.72076 4.99999 10.7269 2.52085 13.2939 0.640625C13.3617 0.594925 13.0184 0.239662 12.9268 0.273438Z"
                                    fill="#373737" />
                            </svg>
                        </template>
                        <template #label-2>Listo para entregar</template>

                        <template #icon-3>
                            <svg class="size-[14px] mr-2" width="13" height="13" viewBox="0 0 13 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M6.40723 0.1875C9.94524 0.187741 12.8132 3.05574 12.8135 6.59375C12.8135 10.132 9.94539 13.0007 6.40723 13.001C2.86886 13.001 0 10.1321 0 6.59375C0.000241568 3.05559 2.86901 0.1875 6.40723 0.1875ZM5.39062 2.17383C5.30076 2.17383 5.22465 2.25239 5.22461 2.37598V2.92676C5.15226 2.94346 5.08157 2.96237 5.0127 2.98242C4.87109 3.02365 4.73687 3.07186 4.61035 3.12598C4.45527 3.19231 4.31115 3.2677 4.17969 3.35059C4.02829 3.44608 3.89299 3.55199 3.77441 3.66504C3.61844 3.81377 3.49146 3.97588 3.39551 4.14551C3.31392 4.28981 3.25448 4.43958 3.21875 4.5918C3.19214 4.70535 3.17871 4.82081 3.17871 4.93555C3.17871 4.99099 3.17798 4.93565 3.18457 5.10449C3.19118 5.27394 3.23233 5.44728 3.29395 5.62109C3.33807 5.7455 3.39662 5.86975 3.47168 5.99219C3.55228 6.12365 3.77441 6.37793 3.77441 6.37793C3.78219 6.38515 3.99397 6.58161 4.125 6.6748C4.24214 6.7581 4.37287 6.83781 4.51758 6.91309C4.64679 6.9803 4.78742 7.0441 4.94043 7.10352C5.06309 7.15114 5.19406 7.19545 5.33301 7.2373C5.42943 7.26635 5.53013 7.29433 5.63477 7.32031L5.74512 7.34863C5.92466 7.39383 6.0829 7.43506 6.2207 7.47461C6.91937 7.67514 7.11354 7.8289 7.13477 8.19922C7.1609 8.65721 6.4355 9.07803 5.63477 8.82715C5.61678 8.82151 5.5478 8.80571 5.5293 8.7998C5.38084 8.75152 5.2483 8.70013 5.13086 8.64453C5.01725 8.59072 4.91656 8.53218 4.82715 8.46777C4.74231 8.40662 4.66683 8.33956 4.59961 8.26562C4.58007 8.24412 4.56124 8.22197 4.54297 8.19922L4.00684 8.46777L3.25879 8.84375C3.16568 8.89076 3.13186 9.08011 3.25879 9.23047C3.28036 9.25602 3.30243 9.28123 3.32422 9.30566C3.42222 9.41555 3.52316 9.51421 3.63184 9.60449C3.74171 9.69577 3.85969 9.77892 3.99121 9.85547C4.12628 9.93407 4.27588 10.0064 4.44531 10.0752C4.56237 10.1227 4.68898 10.1687 4.82715 10.2139C4.94997 10.254 5.08223 10.2933 5.22461 10.333V10.792C5.22461 10.9043 5.33403 10.9608 5.44629 10.9609H6.91895C7.05182 10.9634 7.13477 10.9267 7.13477 10.792V10.333C8.73553 9.95277 9.3183 9.07786 9.31836 8.19922C9.31836 7.32054 8.67094 6.3315 7.13477 6.06543C7.07268 6.05467 7.0122 6.04282 6.95312 6.03125C6.79834 6.00093 6.65368 5.9686 6.51953 5.93262C6.3521 5.8877 6.20073 5.83812 6.06543 5.78418C5.91946 5.72598 5.7922 5.66175 5.68359 5.59277C5.55819 5.51309 5.45743 5.42649 5.38184 5.33203C5.315 5.24851 5.26814 5.15887 5.24023 5.06348C5.22809 5.02196 5.21962 4.97935 5.21484 4.93555C5.21134 4.90334 5.20948 4.87037 5.20996 4.83691C5.21069 4.78765 5.21525 4.73657 5.22461 4.68457C5.22875 4.66159 5.23462 4.63941 5.24023 4.61816C5.24669 4.59378 5.25341 4.57003 5.26172 4.54785C5.27123 4.52249 5.28216 4.49817 5.29395 4.47559C5.30856 4.44764 5.32494 4.42133 5.34277 4.39746C5.36803 4.36368 5.39672 4.33377 5.42773 4.30762C5.4443 4.29366 5.46146 4.28047 5.47949 4.26855C5.50172 4.25386 5.52556 4.24124 5.5498 4.22949C5.59181 4.20915 5.63652 4.19287 5.68359 4.18066C5.71695 4.17202 5.75173 4.16511 5.78711 4.16016C5.83049 4.15409 5.87522 4.15047 5.9209 4.14941C5.96815 4.14832 6.01658 4.1497 6.06543 4.15332C6.13013 4.15812 6.19603 4.167 6.26172 4.17871C6.30646 4.18669 6.35111 4.19645 6.39551 4.20703C6.45485 4.22118 6.51393 4.23708 6.57129 4.25488C6.6241 4.27127 6.67572 4.28908 6.72559 4.30762C6.74639 4.31536 6.76751 4.32363 6.78809 4.33203C6.87173 4.3662 6.95416 4.40604 7.03613 4.45215C7.12141 4.50014 7.20669 4.55555 7.29395 4.61816C7.3742 4.67577 7.45594 4.74015 7.54102 4.81152C7.66957 4.91938 7.80594 5.04336 7.9541 5.18652L9.14355 4.4209C9.23344 4.36301 9.23344 4.19178 9.14355 4.08203C8.87033 3.74846 8.61086 3.52295 8.30469 3.35059C8.19481 3.28875 8.07906 3.23323 7.9541 3.18262C7.84482 3.13835 7.72843 3.09789 7.60352 3.05859C7.52103 3.03265 7.43444 3.00742 7.34375 2.98242C7.2767 2.96394 7.20682 2.94518 7.13477 2.92676V2.37598C7.13471 2.22994 7.01956 2.17383 6.90723 2.17383H5.39062Z"
                                    fill="#373737" />
                            </svg>
                        </template>
                        <template #label-3>Entregado/Pagado</template>
                    </StatusStep>

                    <div v-else
                        class="max-w-md mx-auto mt-2 p-6 bg-red-50 border border-red-200 rounded-2xl shadow-lg flex items-center space-x-4 animate-fade-in">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-red-700">¡Orden Cancelada!</h2>
                        </div>
                    </div>

                    <div class="flex items-start justify-between my-2 ml-4">
                        <button v-if="report.status !== 'Entregado/Pagado' && report.status !== 'Cancelada' && canEdit && hasPermission('Cambiar status en ordenes de servicio')"
                            @click="confirmCancelModal = true"
                            class="flex items-center space-x-2 text-gray37 text-[11px] md:text-[13px] border-b border-gray37">
                            <svg class="size-[12px]" width="12" height="12" viewBox="0 0 13 13" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z"
                                    stroke="currentColor" stroke-width="1.13898" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span>Cancelar orden de servicio</span>
                        </button>
                        <el-popconfirm
                            v-if="report.status !== 'Entregado/Pagado' && report.status !== 'Cancelada' && canEdit && hasPermission('Cambiar status en ordenes de servicio')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#6F6E72"
                            :title="'Cambiar estatus?'"
                            @confirm="handleChangeStatus(statuses[statuses.findIndex(status => status === report.status) + 1])">
                            <template #reference>
                                <PrimaryButton
                                    class="bg-gradient-to-r from-[#751F8B] via-[#754681] to-[#1F0825] disabled:bg-gray-500 !disabled:cursor-not-allowed"
                                    :disabled="updatingStatus">
                                    <i v-if="updatingStatus"
                                        class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                                    {{statuses[statuses.findIndex(status => status === report.status) + 1]}}
                                </PrimaryButton>
                            </template>
                        </el-popconfirm>
                    </div>

                    <div class="mt-5 ml-4 text-[15px]">
                        <h2 class="font-bold text-lg text-[#373737] mb-3">Datos del servicio</h2>

                        <div v-if="report.status === 'Cancelada'"
                            class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-red-500 w-56">Razón de cancelación: </p>
                            <p class="lg:w-1/2">{{ report.cancellation_reason ?? '-' }}</p>
                        </div>
                        <div class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-[#373737] w-56">Equipo: </p>
                            <p class="lg:w-1/2">
                                <span v-if="report.product_details?.brand">{{ report.product_details?.brand }}</span>
                                <span v-if="report.product_details?.model">{{ ' ' + report.product_details?.model
                                    }}</span>
                            </p>
                        </div>
                        <div v-if="report.observations" class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-[#373737] w-56">Problema reportado: </p>
                            <p class="lg:w-1/2">{{ report.observations }}</p>
                        </div>
                        <div class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-[#373737] w-56">Estado previo y características del equipo: </p>
                            <p class="lg:w-1/2">{{ report.description }}</p>
                        </div>
                        <div class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-[#373737] w-56">Servicios a realizar: </p>
                            <p class="lg:w-1/2">{{ report.service_description }}</p>
                        </div>
                        <div class="flex space-x-4 border-b border-[#D9D9D9] py-2 px-1">
                            <p class="text-[#373737] w-56">Responsable del servicio: </p>
                            <p class="lg:w-1/2">{{ report.technician_name ?? '-' }}</p>
                        </div>
                        <div class="flex space-x-4 py-2 px-1">
                            <p class="text-[#373737] w-56">Porcentaje de comisión: </p>
                            <p v-if="report.comision_percentage" class="lg:w-1/2">{{ report.comision_percentage ?? '0'
                            }}% (${{
                                    ((report.comision_percentage / 100)
                                        * report.service_cost)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }})</p>
                            <p v-else class="lg:w-1/2">No aplica</p>
                        </div>
                        <el-collapse v-model="activeCollapseSections" class="mt-8">
                            <el-collapse-item name="1"
                                v-if="hasPermission('Ver información financiera en ordenes de servicio')">
                                <template #title>
                                    <h2 class="text-gray37 font-semibold text-base">
                                        Detalles del pago
                                    </h2>
                                </template>
                                <div class="mt-2 text-[15px] space-y-1 flex flex-col items-end">
                                    <p class="flex">
                                        <span class="w-52">Costo total del servicio</span><span
                                            class="ml-3">$</span><span class="w-24 text-right">{{
                                                report.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                ?? '0.00' }}</span>
                                    </p>
                                    <p class="flex">
                                        <span class="w-52">Anticipo</span><span class="ml-[2px]">- $</span><span
                                            class="w-24 text-right">{{
                                                report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                ?? '0.00'
                                            }}</span>
                                    </p>
                                    <p class="flex">
                                        <span class="w-52">Pago al entergar equipo</span><span class="ml-[2px]">-
                                            $</span><span class="w-24 text-right">{{
                                                (report.service_cost - (report.advance_payment ||
                                                    0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                ?? '0.00'
                                            }}</span>
                                    </p>
                                    <p v-if="report.status != 'Entregado/Pagado'"
                                        class="flex font-bold border-t border-[#D9D9D9] pt-1">
                                        <span class="w-52">Restante por pagar</span><span class="ml-3">$</span><span
                                            class="w-24 text-right">{{
                                                (report.total_cost -
                                                    report.advance_payment)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                        ",")
                                            }}</span>
                                    </p>
                                    <p v-else class="w-80 flex justify-end font-bold border-t border-[#D9D9D9] pt-1">
                                        <span>Pagado</span>
                                    </p>

                                    <!-- Información de cancelación -->
                                    <div v-if="report.status === 'Cancelada'">
                                        <div class="flex items-center text-red-600 text-xs md:text-base italic my-1">
                                            <svg class="size-[14px] mr-2" width="13" height="13" viewBox="0 0 13 13"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z"
                                                    stroke="currentColor" stroke-width="1.13898" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            Orden cancelada
                                        </div>

                                        <p class="flex">
                                            <span class="w-52">Costo de Revisión</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">{{
                                                    report.aditionals.review_amount ?
                                                        parseFloat(report.aditionals.review_amount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                            ",") : '0.00' }}</span>
                                        </p>
                                        <p class="flex">
                                            <span class="w-52">Anticipo</span><span class="ml-[2px]">- $</span><span
                                                class="w-24 text-right">{{
                                                    report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                        ",") ??
                                                    '0.00' }}</span>
                                        </p>
                                        <p v-if="report.aditionals?.review_amount && report.aditionals?.review_amount < report.advance_payment"
                                            class="flex">
                                            <span class="w-52">Total a devolver</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">{{
                                                    report.aditionals.review_amount
                                                        ?
                                                        (report.advance_payment -
                                                            parseFloat(report.aditionals.review_amount))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                                ",")
                                                        :
                                                        report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                }}</span>
                                        </p>
                                        <p v-else class="flex">
                                            <span class="w-52">Total a pagar</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">
                                                {{
                                                    (parseFloat(report.aditionals.review_amount || 0) -
                                                        parseFloat(report.advance_payment || 0))
                                                        ?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </el-collapse-item>
                            <el-collapse-item name="2"
                                v-if="hasPermission('Ver información financiera en ordenes de servicio')">
                                <template #title>
                                    <h2 class="text-gray37 font-semibold text-base">
                                        Invertido en refacciones
                                    </h2>
                                </template>
                                <div v-if="report.spare_parts?.length">
                                    <div class="flex justify-between text-[#999999] font-bold">
                                        <p class="text-base">Refacciones</p>
                                        <p class="text-base">Total</p>
                                    </div>
                                    <div v-for="part in report.spare_parts" :key="part"
                                        class="flex justify-between mt-3">
                                        <div>
                                            <p class="text-[#373737] mb-1 text-[16px]">{{ part.name }}</p>
                                            <p class="text-[#999999] text-[14px]">$ {{
                                                parseFloat(part.unitPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                    ",") }}
                                            </p>
                                            <p class="text-[#999999] text-[14px]">Cantidad: {{ part.quantity }}</p>
                                        </div>
                                        <p><span class="mr-3">$</span> {{ (parseFloat(part.unitPrice) *
                                            parseFloat(part.quantity))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                ",") }}</p>
                                    </div>
                                </div>
                                <div v-else class="text-gray37 text-sm italic">
                                    No se registraron refacciones para este servicio.
                                </div>
                            </el-collapse-item>
                            <el-collapse-item name="3"
                                v-if="hasPermission('Ver información financiera en ordenes de servicio')">
                                <template #title>
                                    <h2 class="text-gray37 font-semibold text-base">
                                        Desglose de ganancia
                                    </h2>
                                </template>
                                <div class="mt-2 text-[15px] space-y-1 flex flex-col items-end">
                                    <p class="flex">
                                        <span class="w-48">Costo total del servicio</span><span
                                            class="ml-3">$</span><span class="w-20 text-right">{{
                                                report.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                                ?? '0.00' }}</span>
                                    </p>
                                    <p class="flex">
                                        <span class="w-48">Invertido en refacciones</span><span
                                            class="ml-3">-$</span><span class="w-20 text-right">
                                            {{
                                                totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                            }}
                                        </span>
                                    </p>
                                    <p class="flex font-semibold border-t border-[#D9D9D9] pt-1">
                                        <span class="w-48">Total neto</span><span class="ml-3">$</span><span
                                            class="w-20 text-right">
                                            {{ (report.total_cost - totalSpareParts)
                                                ?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                            }}
                                        </span>
                                    </p>
                                    <p class="flex">
                                        <span class="w-48">Comisión del técnico ({{ report.comision_percentage ?? '0'
                                        }}%)</span><span class="ml-3">-$</span><span class="w-20 text-right">
                                            {{
                                                (((report.comision_percentage ?? 0) / 100)
                                                    * report.service_cost)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                        </span>
                                    </p>
                                    <p class="flex font-bold border-t border-[#D9D9D9] pt-1">
                                        <span class="w-48">Ganancia </span><span class="ml-3">$</span><span
                                            class="w-20 text-right">
                                            {{
                                                (report.service_cost
                                                    - totalSpareParts
                                                    - (((report.comision_percentage ?? 0) / 100)
                                                        * report.service_cost))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                            }}
                                        </span>
                                    </p>

                                    <!-- Información de cancelación -->
                                    <div v-if="report.status === 'Cancelada'">
                                        <div class="flex items-center text-red-600 text-xs md:text-base italic my-1">
                                            <svg class="size-[14px] mr-2" width="13" height="13" viewBox="0 0 13 13"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.97288 4.41695L6.69492 6.69492M4.41695 8.97288L6.69492 6.69492M6.69492 6.69492L4.41695 4.41695M6.69492 6.69492L8.97288 8.97288M12.3898 6.69492C12.3898 9.84013 9.84013 12.3898 6.69492 12.3898C3.5497 12.3898 1 9.84013 1 6.69492C1 3.5497 3.5497 1 6.69492 1C9.84013 1 12.3898 3.5497 12.3898 6.69492Z"
                                                    stroke="currentColor" stroke-width="1.13898" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            Orden cancelada
                                        </div>

                                        <p class="flex">
                                            <span class="w-40">Costo de Revisión</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">{{
                                                    report.aditionals.review_amount ?
                                                        parseFloat(report.aditionals.review_amount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                            ",") : '0.00' }}</span>
                                        </p>
                                        <p class="flex">
                                            <span class="w-40">Anticipo</span><span class="ml-[2px]">- $</span><span
                                                class="w-24 text-right">{{
                                                    report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                        ",") ??
                                                    '0.00' }}</span>
                                        </p>
                                        <p v-if="report.aditionals?.review_amount < report.advance_payment"
                                            class="flex">
                                            <span class="w-40">Total devuelto</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">{{
                                                    report.aditionals.review_amount ?
                                                        (report.advance_payment -
                                                            parseFloat(report.aditionals.review_amount))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                                ",")
                                                        :
                                                        report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                            ",")
                                                }}</span>
                                        </p>
                                        <p v-else class="flex">
                                            <span class="w-40">Total pagado</span><span class="ml-3">$</span><span
                                                class="w-24 text-right">{{
                                                    (parseFloat(report.aditionals.review_amount) -
                                                        report.advance_payment)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                                            ",")
                                                }}</span>
                                        </p>
                                    </div>
                                </div>
                            </el-collapse-item>
                            <el-collapse-item name="4">
                                <template #title>
                                    <h2 class="text-gray37 font-semibold text-base">
                                        Evidencias
                                    </h2>
                                </template>
                                <div v-if="report.media.length" class="my-7">
                                    <h2 class="font-bold text-lg text-[#373737] mt-5 mb-2">Evidencias</h2>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 space-x-2">
                                        <figure @click="openImage(media?.original_url)"
                                            class="border rounded-xl border-[#D9D9D9] overflow-hidden group"
                                            v-for="media in report.media" :key="media">
                                            <img class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110 cursor-zoom-in"
                                                :src="media?.original_url" alt="">
                                        </figure>
                                    </div>
                                </div>
                                <div v-else class="text-gray37 text-sm italic">
                                    No se registraron evidencias para este servicio.
                                </div>
                            </el-collapse-item>
                        </el-collapse>
                    </div>
                </section>

                <!-- Lado derecho -->
                <section>
                    <div class="border border-[#D9D9D9] rounded-lg p-4 space-y-2 h-[650px]">
                        <section v-if="hasPermission('Ver información de cliente en ordenes de servicio')">
                            <h2 class="font-bold text-lg text-[#373737] mb-2">Detalles del cliente</h2>
                            <div class="flex items-center space-x-4 text-sm">
                                <i class="fa-solid fa-user"></i>
                                <p>{{ report.client_name }}</p>
                            </div>
                            <div class="flex justify-between">
                                <div class="flex items-center space-x-4 text-sm">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    <p>{{ formatPhoneNumber(report.client_phone_number) }}</p>
                                </div>
                                <button @click="sendWhatsAppMessage(report.client_phone_number)"
                                    class="text-primary bg-primarylight rounded-full px-3 text-sm">
                                    Enviar mensaje
                                </button>
                            </div>
                        </section>

                        <h2 class="font-bold text-lg text-[#373737] pt-3">Datos del equipo</h2>

                        <div class="text-sm space-y-2">
                            <div class="flex items-center">
                                <p class="w-14">Marca: </p>
                                <p class="ml-3">{{ report.product_details?.brand ?? '-' }}</p>
                            </div>
                            <div class="flex items-center">
                                <p class="w-14">Modelo: </p>
                                <span class="ml-3">{{ report.product_details?.model ?? '-' }}</span>
                            </div>
                            <div class="flex items-center">
                                <p class="w-14">IMEI: </p>
                                <span class="ml-3">{{ report.product_details?.imei ?? '-' }}</span>
                            </div>
                        </div>

                        <h2 class="font-bold text-lg text-[#373737] pt-3">Accesorios</h2>

                        <div class="text-sm">
                            <div v-for="item in allAccessories" :key="item" class="flex items-center gap-2 mb-1">
                                <i v-if="report.aditionals?.accessories?.includes(item)"
                                    class="fa-solid fa-check text-green-500"></i>
                                <i v-else class="fa-solid fa-xmark text-red-500"></i>
                                <p>{{ item }}</p>
                            </div>
                        </div>

                        <h2 class="font-bold text-lg text-[#373737] pt-3">Desbloqueo</h2>

                        <DrawPatternMobil v-if="report.aditionals?.unlockPattern?.length"
                            :points="report.aditionals.unlockPattern" :size="100" />

                        <div class="border border-[#D9D9D9] rounded-xl py-1 px-4" v-else>
                            <p class="text-[#373737]">Contraseña:</p>
                            <p class="mt-2 text-center">{{ report.aditionals?.unlockPassword }}</p>
                        </div>
                    </div>
                </section>
            </body>
        </main>

        <!-- modal de impresión -->
        <PrintingModal :show="showPrintingModal" @close="showPrintingModal = false" ref="printingModal" />
        <!-- -------------- Modal de cancelacion ----------------------- -->
        <Modal :show="confirmCancelModal" @close="confirmCancelModal = false" maxWidth="2xl">
            <div class="p-5 relative">
                <i @click="confirmCancelModal = false"
                    class="fa-solid fa-xmark cursor-pointer text-sm flex items-center justify-center absolute right-5"></i>

                <h2 class="font-bold text-lg mb-1">Cancelar orden de servicio</h2>
                <p class="text-sm">Confirma los detalles de la cancelación. Indica si se cobrará revisión o si se
                    requiere
                    devolución de anticipo.</p>

                <div>
                    <p class="font-semibold text-right">Resumen de la orden</p>
                    <div class="mt-2 text-[15px] space-y-1 flex flex-col items-end">
                        <p class="flex">
                            <span class="w-44">Costo total del servicio</span><span class="ml-3">$</span><span
                                class="w-24 text-right">{{
                                    report.service_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                    ?? '0.00' }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-44">Anticipo</span><span class="ml-[2px]">- $</span><span
                                class="w-24 text-right">{{
                                    report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00'
                                }}</span>
                        </p>
                        <!-- <p class="flex">
                            <span class="w-44">Refacciones</span><span class="ml-3">$</span><span
                                class="w-24 text-right">{{
                                    totalSpareParts?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p> -->
                        <p class="flex font-bold">
                            <span class="w-44">Restante por pagar</span><span class="ml-3">$</span><span
                                class="w-24 text-right">
                                {{
                                    (report.total_cost - report.advance_payment)
                                        ?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <InputLabel value="Motivo de cancelación (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="cancellation_reason" placeholder="Ej. El cliente no aceptó el presupuesto"
                        clearable />
                </div>

                <div class="mx-4 mt-3">
                    <!-- Cobrar revisión -->
                    <el-checkbox v-model="chargeReview">Cobrar revisión</el-checkbox>
                    <div v-if="chargeReview">
                        <InputLabel value="Monto de revisión" class="ml-3 mb-1" />
                        <el-input class="!w-1/2" v-model="reviewAmount" @input="onReviewInput" placeholder="Ej. $300"
                            :disabled="!chargeReview">
                            <template #prepend>
                                <span class="text-gray-500">$</span>
                            </template>
                        </el-input>
                    </div>

                    <!-- Devolver anticipo -->
                    <div v-if="report.advance_payment && reviewAmount < report.advance_payment">
                        <el-checkbox v-model="returnAdvance">Devolver anticipo al cliente</el-checkbox>
                        <div v-if="returnAdvance">
                            <InputLabel value="Monto a devolver" class="ml-3 mb-1" />
                            <el-input class="!w-1/2" v-model="advanceAmount" placeholder="Ej. $100"
                                :disabled="!returnAdvance" @input="onAdvanceInput">
                                <template #prepend>
                                    <span class="text-gray-500">$</span>
                                </template>
                            </el-input>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div v-if="returnAdvance || chargeReview"
                        class="mt-2 text-[15px] space-y-1 flex flex-col items-end">
                        <p class="flex">
                            <span class="w-40">Costo de Revisión</span><span class="ml-3">$</span><span
                                class="w-24 text-right">{{ reviewAmount ?
                                    parseFloat(reviewAmount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0.00'
                                }}</span>
                        </p>
                        <p class="flex">
                            <span class="w-[162px]">Anticipo</span><span class="ml-[2px]">-$</span><span
                                class="w-24 text-right">{{
                                    report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") ?? '0.00' }}</span>
                        </p>
                        <p v-if="reviewAmount < report.advance_payment" class="flex">
                            <span class="w-40">Total a devolver</span><span class="ml-3">$</span><span
                                class="w-24 text-right">{{ reviewAmount ? (report.advance_payment -
                                    parseFloat(reviewAmount))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") :
                                    report.advance_payment?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </p>
                        <p v-else class="flex">
                            <span class="w-40">Total a pagar</span><span class="ml-3">$</span><span
                                class="w-24 text-right">{{
                                    reviewAmount ? ((parseFloat(reviewAmount) -
                                        report.advance_payment)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ",")) :
                                        '0.00' }}</span>
                        </p>

                    </div>
                </div>

                <div class="flex justify-end mt-5">
                    <CancelButton @click="confirmCancelModal = false" class="mr-2">Cerrar</CancelButton>
                    <PrimaryButton @click="changeStatus('Cancelada')">Confirmar cancelación</PrimaryButton>
                </div>

            </div>
        </Modal>
        <!-- ------------- Modal finalizar venta (pago) starts----------------------- -->
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
                    <button @click="paymentModalStep = 4; paymentMethod = 'Transferencia'" type="button"
                        class="bg-[#FAFFBA] text-[#B38A00] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
                        <p class="text-lg text-center font-bold">Transferencia</p>
                        <img src="@/../../public/images/transfer.png" alt="Pago con transferencia">
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
                            (report.total_cost - (report.advance_payment ||
                                0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                    ",") }}</p>
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
                            <p v-if="(report.total_cost - (report.advance_payment || 0)) <= moneyReceived"
                                class="font-bold">
                                <span class="mr-5">$</span>
                                {{
                                    (moneyReceived - (report.total_cost - (report.advance_payment ||
                                        0)))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }}
                            </p>
                        </div>
                        <p v-if="((report.total_cost - (report.advance_payment || 0)) > moneyReceived) && moneyReceived"
                            class="text-base font-bold text-red-600 text-center mb-3">
                            La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total a pagar.
                        </p>
                        <div class="flex justify-center mt-7">
                            <PrimaryButton :disabled="updatingStatus" class="!px-20"
                                @click="changeStatus('Entregado/Pagado')">
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
                                (report.total_cost - (report.advance_payment ||
                                    0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}</p>
                        </div>

                        <div class="flex justify-center mt-7">
                            <PrimaryButton @click="changeStatus('Entregado/Pagado')" :disabled="updatingStatus"
                                class="!px-20">
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

            <!-- Pago con transferencia (step 4) -->
            <div v-if="paymentModalStep === 4" class="py-4 px-7 relative">
                <section class="flex items-center justify-between">
                    <h1 class="font-bold mt-2 text-lg">Registrar pago</h1>
                    <div @click="paymentModalStep = 1" class="flex items-center space-x-4 text-primary cursor-pointer">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Regresar</span>
                    </div>
                </section>

                <section class="mx-auto mt-2 md:w-2/3">
                    <div v-if="!paymentConfirmed">
                        <p class="my-3 text-sm text-center">Recibir pago por transferencia.</p>
                        <div
                            class="rounded-full border border-[#D9D9D9D] bg-[#FAFFBA] py-2 px-4 flex items-center justify-between mt-3">
                            <span class="font-bold text-[#B38A00]">TRANSFERENCIA</span>
                            <img src="@/../../public/images/transfer.png" alt="Pago por transferencia" class="h-7">
                        </div>

                        <div
                            class="rounded-full border border-[#D9D9D9D] bg-[#F2F2F2] py-2 px-4 flex items-center justify-between mt-3">
                            <span class="font-bold">Total a pagar</span>
                            <p class="font-bold"><span class="mr-4">$</span>{{
                                (report.total_cost - (report.advance_payment ||
                                    0))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}</p>
                        </div>

                        <div class="flex justify-center mt-7">
                            <PrimaryButton @click="changeStatus('Entregado/Pagado')" :disabled="updatingStatus"
                                class="!px-20">
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
        <ConfirmationModal :show="confirmDeleteModal" @close="confirmDeleteModal = false">
            <template #title>
                Eliminar Orden de Servicio
            </template>
            <template #content>
                ¿Desea continuar con la eliminación?
            </template>
            <template #footer>
                <div>
                    <CancelButton @click="confirmDeleteModal = false" class="mr-2">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Back from "@/Components/MyComponents/Back.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DrawPatternMobil from '@/Components/MyComponents/DrawPatternMobil.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import StatusStep from '@/Components/MyComponents/StatusStep.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import PrintingModal from "@/Components/MyComponents/Sale/PrintingModal.vue";

export default {
    data() {
        return {
            statuses: ['Recibida', 'En proceso', 'Listo para entregar', 'Entregado/Pagado'],
            allAccessories: ['SIM', 'Cargador', 'Memoria', 'Bateria'],
            updatingStatus: false, // Estado para indicar si se está actualizando el estado
            confirmDeleteModal: false, // Estado para el modal de confirmación de eliminación
            confirmCancelModal: false, // Estado para el modal de confirmación de cancelación
            cancellation_reason: '', // Razón de cancelación
            chargeReview: false,
            reviewAmount: null, // monto que se cobra por revision al cancelar orden
            returnAdvance: false,
            advanceAmount: null, // monto de regreso de anticipo al cancelar orden
            showPaymentModal: false,
            paymentModalStep: 1, //paso del modal de pago
            paymentMethod: '', //Método de pago seleccionado
            paymentConfirmed: false, //indica si el pago ha sido confirmado
            moneyReceived: null, // Monto recibido en efectivo
            showPrintingModal: false,
            canEdit: this.$page.props.auth.user.permissions.includes('Editar ordenes de servicio'),
            activeCollapseSections: ['1'], // Para el collapse de detalles
        }
    },
    components: {
        ConfirmationModal,
        DrawPatternMobil,
        PrimaryButton,
        CancelButton,
        ThirthButton,
        StatusStep,
        InputLabel,
        AppLayout,
        Modal,
        Back,
        PrintingModal,
    },
    props: {
        report: Object,
    },
    computed: {
        totalSpareParts() {
            if (!this.report.spare_parts?.length) return 0;

            return this.report.spare_parts?.reduce((total, sp) => {
                return total + (Number(sp.quantity) * Number(sp.unitPrice));
            }, 0);
        },
        maxAdvanceAmount() {
            const max = this.report.advance_payment - parseFloat(this.reviewAmount || 0)
            return max > 0 ? max : 0
        }
    },
    methods: {
        hasPermission(permission) {
            return this.$page.props.auth.user.permissions.includes(permission);
        },
        handleTicketPrinting(language, type) {
            // enviar comandos al componente de impresión dependiendo del tipo de ticket
            if (language === 'TSPL') {
                this.$refs.printingModal.setLabelMode();
                this.$refs.printingModal.customData = this.generateTSPLLabelCommands();
            } else if (language === 'ESC/POS') {
                this.$refs.printingModal.setTicketMode();
                if (type === 'orden') {
                    this.$refs.printingModal.customData = this.generateESCPOSTicketCommands();
                } else if (type === 'comprobante') {
                    this.$refs.printingModal.customData = this.generatePaymentTicketCommands();
                }
            }
            this.showPrintingModal = true;
        },
        generatePaymentTicketCommands() {
            const ESC = '\x1B';
            const GS = '\x1D';
            const INICIALIZAR_IMPRESORA = ESC + '@';
            const NEGRITA_ON = ESC + 'E' + '\x01';
            const NEGRITA_OFF = ESC + 'E' + '\x00';
            const ALINEAR_IZQUIERDA = ESC + 'a' + '\x00';
            const ALINEAR_CENTRO = ESC + 'a' + '\x01';
            const CORTAR_PAPEL = GS + 'V' + '\x00' + '\x00';

            // Determinar el ancho del ticket
            const ticketWidthSetting = this.$page.props.auth.user.printer_config?.ticketWidth;
            const anchoTicket = ticketWidthSetting === '80mm' ? 48 : 32;
            const separador = '-'.repeat(anchoTicket) + '\n';

            let ticket = INICIALIZAR_IMPRESORA;

            // Encabezado del comprobante
            ticket += ALINEAR_CENTRO;
            if (this.$page.props.auth.user.store) {
                ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
                if (this.$page.props.auth.user.store.address) {
                    ticket += this.$page.props.auth.user.store.address + '\n';
                }
                if (this.$page.props.auth.user.printer_config?.ticketContactInfo) {
                    ticket += this.$page.props.auth.user.printer_config?.ticketContactInfo + '\n';
                }
            }
            ticket += separador;
            ticket += NEGRITA_ON + 'COMPROBANTE DE PAGO' + NEGRITA_OFF + '\n';
            ticket += separador;

            // Datos del servicio y cliente
            ticket += ALINEAR_IZQUIERDA;
            ticket += `Folio: ${this.report.folio || 'N/A'}\n`;
            ticket += `Fecha: ${this.formatDateTime()}\n`;
            ticket += `Cliente: ${this.report.client_name || 'N/A'}\n`;
            ticket += `Servicio: ${this.report.service_description || 'N/A'}\n`;
            ticket += `Equipo: ${this.report.product_details.brand || '-'} ${this.report.product_details.model || '-'}\n`;
            ticket += separador;

            // Sección de costos y pago
            const formatCurrency = (value) => {
                return '$' + parseFloat(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };

            // Total del servicio
            let totalServicioStr = 'Total del Servicio: ' + formatCurrency(this.report.total_cost);
            ticket += totalServicioStr.padStart(anchoTicket) + '\n';

            // Anticipo
            const anticipo = this.report.advance_payment || 0;
            if (anticipo > 0) {
                let anticipoStr = 'Anticipo: ' + formatCurrency(anticipo);
                ticket += anticipoStr.padStart(anchoTicket) + '\n';
            }

            // Saldo restante
            const saldoRestante = this.report.total_cost - anticipo;
            let restanteStr = 'Saldo Pagado: ' + formatCurrency(saldoRestante);
            ticket += NEGRITA_ON + restanteStr.padStart(anchoTicket) + NEGRITA_OFF + '\n';

            // Método de pago
            if (this.report.payment_method) {
                let metodoPagoStr = 'Metodo de Pago: ' + this.report.payment_method;
                ticket += metodoPagoStr.padStart(anchoTicket) + '\n';
            }

            ticket += separador;

            // Mensaje final
            ticket += '\n' + ALINEAR_CENTRO;
            ticket += 'PAGO RECIBIDO\n\n';
            ticket += 'GRACIAS POR SU PREFERENCIA';

            // Corte de papel
            ticket += CORTAR_PAPEL;

            return ticket;
        },
        generateTSPLLabelCommands() {
            // --- 1. Configuración de la Etiqueta ---
            // Define aquí las dimensiones de tu etiqueta en milímetros.
            const labelConfig = {
                widthMM: this.$page.props.auth.user.printer_config?.labelWidth,  // Ancho de la etiqueta en mm
                heightMM: this.$page.props.auth.user.printer_config?.labelHeight, // Alto de la etiqueta en mm
                gapMM: this.$page.props.auth.user.printer_config?.labelGap,     // Espacio entre etiquetas en mm
                dotsPerMM: Math.round(this.$page.props.auth.user.printer_config?.labelResolution / 25.4)  // Resolución de la impresora (203 dpi = 8 dots/mm)
            };

            // --- 2. Comandos Iniciales (¡La parte más importante!) ---
            // SIZE: Define el tamaño de la etiqueta.
            // GAP: Define la separación entre etiquetas. Esto corrige el problema de sobreimpresión.
            // CODEPAGE: Define la tabla de caracteres. 1252 es para Latin-1 (incluye acentos, ñ).
            // CLS: Limpia el búfer de la impresora antes de empezar a dibujar.
            let commands = '';
            commands += `SIZE ${labelConfig.widthMM} mm, ${labelConfig.heightMM} mm\n`;
            commands += `GAP ${labelConfig.gapMM} mm, 0 mm\n`;
            // commands += `COUNTRY 003\n`;
            // commands += `CODEPAGE 1252\n`;
            commands += `CLS\n`;

            // --- 3. Coordenadas y Diseño ---
            let currentY = 15; // Posición Y inicial (margen superior en dots)
            const startX = 15; // Posición X inicial (margen izquierdo en dots)
            const rightMargin = 15;
            const lineHeight = this.$page.props.auth.user.printer_config?.labelLineHeight; // Espacio entre líneas
            const font = `"${this.$page.props.auth.user.printer_config?.labelFont}"`; // Fuente a utilizar. Las comillas dobles son importantes.
            const fontAvgCharWidth = 12; // Ancho promedio de un carácter en dots. Ajusta según la fuente.

            /**
             * Función auxiliar para añadir texto y manejar saltos de línea automáticos.
             * @param {string} label - La etiqueta del campo (ej. "Nombre:").
             * @param {string} value - El valor del campo.
             */
            const addTextLine = (label, value) => {
                if (!value) return; // No añadir si el valor está vacío

                let fullText = `${label} ${value}`;

                // --- CÁLCULO DINÁMICO DEL MÁXIMO DE CARACTERES ---
                // 1. Calcula el ancho total disponible para el texto en dots.
                const availableWidth = (labelConfig.widthMM * labelConfig.dotsPerMM) - startX - rightMargin;
                // 2. Calcula cuántos caracteres caben en ese espacio. Usamos Math.floor para redondear hacia abajo.
                const maxLength = Math.floor(availableWidth / fontAvgCharWidth);

                const textParts = [];
                while (fullText.length > maxLength) {
                    let chunk = fullText.substring(0, maxLength);
                    let lastSpace = chunk.lastIndexOf(' ');
                    if (lastSpace > 0) {
                        chunk = chunk.substring(0, lastSpace);
                    }
                    textParts.push(chunk);
                    fullText = fullText.substring(chunk.length).trim();
                }
                textParts.push(fullText);

                // Imprime cada parte del texto
                textParts.forEach(part => {
                    // Se usa TEXT y se escapa el contenido para evitar conflictos con comillas
                    commands += `TEXT ${startX},${currentY},${font},0,1,1,"${part.replace(/"/g, '\\"')}"\n`;
                    currentY += lineHeight;
                });
            };

            // --- 4. Contenido de la Etiqueta ---
            addTextLine("Fecha:", this.formatDateTime(this.report.created_at));
            addTextLine("Nombre:", this.removeAccents(this.report.client_name.slice(0, 20)));
            addTextLine("Equipo:", this.removeAccents(this.report.product_details?.brand) + ' ' + this.removeAccents(this.report.product_details?.model));
            addTextLine("Total:", '$' + (parseFloat(this.report.service_cost))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            addTextLine("Desbloqueo:", this.report.aditionals?.unlockPassword ?? 'Por patron');
            addTextLine("Problemas:", this.removeAccents(this.report.observations));
            // addTextLine("Servicio:", this.removeAccents(this.report.service_description));
            // addTextLine("Tecnico:", this.removeAccents(this.report.technician_name));
            addTextLine("Telefono cliente:", this.report.client_phone_number);

            // --- 5. Código de Barras ---
            if (this.report.folio) {
                // currentY += 5; // Espacio extra antes del código de barras
                const folioPadded = String(this.report.folio).padStart(3, '0');
                const humanReadable = this.$page.props.auth.user.printer_config?.labelBarCodeHumanReadable || 0;

                // BARCODE X,Y,"TIPO",ALTURA,LEER_HUMANO,ROTACION,ANCHO_ESTRECHO,ANCHO_ANCHO,"CONTENIDO"
                const barcodeHeight = 22;    // Altura del código en dots
                const narrowWidth = 2;     // Ancho de la barra más estrecha
                const wideWidth = 5;       // Ancho de la barra más ancha

                // Centrar el código de barras (opcional)
                const barcodeX = startX;

                commands += `BARCODE ${barcodeX},${currentY},"128",${barcodeHeight},${humanReadable},0,${narrowWidth},${wideWidth},"${folioPadded}"\n`;
                currentY += barcodeHeight + 20; // Actualizar Y después del barcode
            }

            // --- 6. Comando de Impresión ---
            // PRINT N, M -> Imprime N copias de la etiqueta M veces.
            // Usamos PRINT 1 para imprimir una sola copia de la etiqueta diseñada.
            commands += 'PRINT 1\n';

            // console.log("Comandos TSPL Generados:\n", commands); // Útil para depuración
            return commands;
        },
        removeAccents(text = '') {
            if (!text) return '';
            // cambiar ñ por n
            text = text.replace(/ñ/g, 'n').replace(/Ñ/g, 'N');
            // Normalizar el texto y eliminar los acentos
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        },
        generateESCPOSTicketCommands() {
            const ESC = '\x1B';
            const GS = '\x1D';
            const INICIALIZAR_IMPRESORA = ESC + '@';
            const NEGRITA_ON = ESC + 'E' + '\x01';
            const NEGRITA_OFF = ESC + 'E' + '\x00';
            const ALINEAR_IZQUIERDA = ESC + 'a' + '\x00';
            const ALINEAR_CENTRO = ESC + 'a' + '\x01';
            const ALINEAR_DERECHA = ESC + 'a' + '\x02';
            const CORTAR_PAPEL = GS + 'V' + '\x00' + '\x00';

            // --- 1. Determinar el ancho del ticket dinámicamente ---
            const ticketWidthSetting = this.$page.props.auth.user.printer_config?.ticketWidth;
            // Ancho en caracteres: 48 para 80mm, 32 para 58mm (o por defecto)
            const anchoTicket = ticketWidthSetting === '80mm' ? 48 : 32;
            const separador = '-'.repeat(anchoTicket) + '\n';

            let ticket = INICIALIZAR_IMPRESORA;

            // Encabezado
            ticket += ALINEAR_CENTRO;
            if (this.$page.props.auth.user.store) {
                ticket += NEGRITA_ON + this.$page.props.auth.user.store.name + NEGRITA_OFF + '\n';
                if (this.$page.props.auth.user.store.address) {
                    ticket += this.$page.props.auth.user.store.address + '\n';
                }

                if (this.$page.props.auth.user.printer_config?.ticketContactInfo) {
                    ticket += this.$page.props.auth.user.printer_config?.ticketContactInfo + '\n';
                }
            }
            ticket += separador;
            ticket += NEGRITA_ON + 'ORDEN DE SERVICIO' + NEGRITA_OFF + '\n';
            ticket += separador;

            // Datos del Cliente y Equipo
            ticket += ALINEAR_IZQUIERDA;
            ticket += 'Fecha: ' + this.formatDate(this.report.service_date) + '\n';
            ticket += 'Cliente: ' + (this.report.client_name || 'N/A') + '\n';
            ticket += 'Telefono: ' + (this.report.client_phone_number || 'N/A') + '\n\n';

            ticket += NEGRITA_ON + 'Detalles del Equipo:' + NEGRITA_OFF + '\n';
            ticket += 'Marca: ' + (this.report.product_details.brand || 'N/A') + '\n';
            ticket += 'Modelo: ' + (this.report.product_details.model || 'N/A') + '\n';
            ticket += separador;

            // Descripción del Servicio y problemas (se ajustan automáticamente al saltar de línea)
            if (this.report.observations) {
                ticket += NEGRITA_ON + 'Problemas reportados:' + NEGRITA_OFF + '\n';
                ticket += this.report.observations + '\n\n';
            }
            if (this.report.service_description) {
                ticket += NEGRITA_ON + 'Servicio:' + NEGRITA_OFF + '\n';
                ticket += this.report.service_description + '\n';
            }
            ticket += separador;

            // --- 2. Ajustar sección de refacciones con columnas dinámicas ---
            // if (this.report.spare_parts && this.report.spare_parts.length > 0 && this.report.spare_parts[0].name) {
            //     ticket += ALINEAR_CENTRO + NEGRITA_ON + 'Refacciones' + NEGRITA_OFF + '\n';

            //     // Definir anchos de columna
            //     const colAnchoPrecio = 10; // " $1,234.56"
            //     const colAnchoCant = 4;    // "Cant "
            //     const colAnchoNombre = anchoTicket - colAnchoPrecio - colAnchoCant;

            //     // Crear encabezado dinámico
            //     let header = 'Pzs'.padEnd(colAnchoCant);
            //     header += 'Concepto'.padEnd(colAnchoNombre);
            //     header += 'Importe'.padStart(colAnchoPrecio);

            //     ticket += NEGRITA_ON + header + NEGRITA_OFF + '\n';
            //     ticket += separador;
            //     ticket += ALINEAR_IZQUIERDA;

            //     this.report.spare_parts.forEach(part => {
            //         const cantidad = part.quantity.toString();
            //         // Trunca el nombre del producto para que quepa en su columna
            //         const nombre = part.name.substring(0, colAnchoNombre - 1); // -1 por el espacio
            //         const totalProducto = (part.quantity * part.unitPrice).toFixed(2);

            //         let linea = '';
            //         linea += cantidad.padEnd(colAnchoCant);
            //         linea += this.removeAccents(nombre).padEnd(colAnchoNombre);
            //         linea += ('$' + totalProducto).padStart(colAnchoPrecio);

            //         ticket += linea + '\n';
            //     });
            //     ticket += separador;
            // }

            // --- 3. Ajustar sección de costos para alineación derecha ---
            const formatCurrency = (value) => {
                return '$' + parseFloat(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            };

            // let subtotalRefacciones = 0;
            // if (this.report.spare_parts && this.report.spare_parts.length > 0 && this.report.spare_parts[0].name) {
            //     subtotalRefacciones = this.report.spare_parts.reduce((acc, part) => acc + (part.quantity * part.unitPrice), 0);
            //     let subtotalStr = 'Subtotal Refacciones: ' + formatCurrency(subtotalRefacciones);
            //     ticket += subtotalStr.padStart(anchoTicket) + '\n';
            // }

            // let costoServicioStr = 'Mano de Obra: ' + formatCurrency(this.report.service_cost);
            // ticket += costoServicioStr.padStart(anchoTicket) + '\n';

            let totalStr = 'Total del servicio: ' + formatCurrency(this.report.total_cost);
            ticket += NEGRITA_ON + totalStr.padStart(anchoTicket) + NEGRITA_OFF + '\n';

            if (this.report.advance_payment > 0) {
                let anticipoStr = 'Anticipo: ' + formatCurrency(this.report.advance_payment);
                ticket += NEGRITA_OFF + anticipoStr.padStart(anchoTicket) + '\n';

                let restanteStr = 'Resta: ' + formatCurrency(this.report.total_cost - this.report.advance_payment);
                ticket += NEGRITA_ON + restanteStr.padStart(anchoTicket) + NEGRITA_OFF + '\n';
            }

            // --- 4. Generar Código de Barras con el Folio ---
            // Asegúrate que el folio exista y no esté vacío
            if (this.report.folio) {
                const SET_BARCODE_HEIGHT = GS + 'h' + '\x50'; // Altura del código: 80 dots
                const SET_BARCODE_WIDTH = GS + 'w' + '\x03';  // Ancho del código: multiplicador 3
                const PRINT_HRI_BELOW = GS + 'H' + '\x02';   // Imprimir texto legible debajo del código

                // Comando para imprimir CODE128: GS k m n [d1...dn]
                // m = 73 (0x49) para CODE128
                // n = longitud de los datos
                const folio = String(this.report.folio).padStart(3, '0');
                const printBarcodeCommand = GS + 'k' + '\x49' + String.fromCharCode(folio.length) + folio;

                ticket += ALINEAR_CENTRO;
                ticket += SET_BARCODE_HEIGHT;
                ticket += SET_BARCODE_WIDTH;
                ticket += PRINT_HRI_BELOW;
                ticket += '\n' + printBarcodeCommand + '\n';
            }
            // --- Fin del código de barras ---

            // terminos y condiciones
            ticket += '\n' + this.$page.props.auth.user.printer_config?.ticketTerms + '\n\n';
            // firma
            ticket += '_______________________________\n';
            ticket += 'Firma de cliente acepta condiciones\n';

            // Pie de página
            ticket += '\n' + ALINEAR_CENTRO;
            ticket += 'GRACIAS POR SU PREFERENCIA';

            return ticket;
        },
        formatDateTime(dateString = new Date().toISOString()) {
            return format(parseISO(dateString), 'dd MMMM yyyy, h:mm a', { locale: es });
        },
        formatDate(dateString = new Date().toISOString()) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        formatPhoneNumber(number) {
            if (!number) return '-';

            // Elimina espacios previos y todo lo que no sea dígito
            const cleaned = number.toString().replace(/\D/g, '');

            // Divide en grupos de 2 dígitos
            return cleaned.match(/.{1,2}/g).join(' ');
        },
        onAdvanceInput(value) {
            // Solo permite números y punto decimal
            const numeric = value.replace(/[^\d.]/g, '')

            const num = parseFloat(numeric)
            if (!isNaN(num) && num > this.maxAdvanceAmount) {
                this.advanceAmount = this.maxAdvanceAmount.toFixed(2)
            } else {
                this.advanceAmount = numeric
            }
        },
        onReviewInput() {
            if (this.reviewAmount < this.report.advance_payment) {
                this.advanceAmount = (this.report.advance_payment - this.reviewAmount).toFixed(2);
            } else {
                this.advanceAmount = 0; // Si el monto de revisión es mayor o igual al anticipo, no hay devolución
            }
        },
        encodeId(id) {
            const encodedId = btoa(id.toString());
            return encodedId;
        },
        openImage(url) {
            if (url) {
                window.open(url, '_blank');
            } else {
                this.$toast.error('No hay imagen disponible');
            }
        },
        sendWhatsAppMessage(phoneNumber) {
            if (!phoneNumber) return;

            // Elimina espacios, guiones o paréntesis
            const cleanNumber = phoneNumber.replace(/\D/g, '');

            // Asegúrate de que el número incluya código de país, por ejemplo: 52 para México
            const internationalNumber = cleanNumber.startsWith('52') ? cleanNumber : `52${cleanNumber}`;

            const url = `https://wa.me/${internationalNumber}`;
            window.open(url, '_blank');
        },
        printTemplate() {
            const url = route('service-reports.print-template', this.report.id);
            window.open(url, '_blank');
        },
        receivedInputFocus() {
            this.$nextTick(() => {
                this.$refs.receivedInput.focus(); // Enfocar el input de recibido cuando se abre el modal
            });
        },
        handleChangeStatus(newStatus) {
            if (newStatus === 'Entregado/Pagado') {
                this.showPaymentModal = true; // Abre el modal de pago
            } else {
                this.changeStatus(newStatus);
            }
        },
        async changeStatus(newStatus) {
            this.updatingStatus = true; // Indica que se está actualizando el estado
            try {
                const response = await axios.post(route('service-reports.change-status', this.report.id), {
                    status: newStatus,
                    cancellation_reason: this.cancellation_reason,
                    reviewAmount: this.reviewAmount,
                    advanceAmount: this.advanceAmount,
                    paymentMethod: this.paymentMethod,
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Estatus actualizado",
                        message: "",
                        type: "success",
                    });
                    this.report.status = newStatus; // Actualiza el estado del reporte

                    if (newStatus === 'Entregado/Pagado') {
                        this.paymentConfirmed = true; //indica que el pago ha sido confirmado
                        // this.report.paid_at = new Date().toDateString();
                        setTimeout(() => {
                            this.paymentConfirmed = false;
                            // Aquí cierra el modal como lo manejes normalmente
                            this.showPaymentModal = false; // ajusta este método a tu implementación
                            this.paymentModalStep = 1; //reinicia el paso del modal de pago
                            this.handleTicketPrinting('ESC/POS', 'comprobante');
                        }, 1000);
                    } else if (newStatus === 'Cancelada') {
                        this.report.aditionals.review_amount = this.reviewAmount;
                    }
                } else {
                    this.$notify({
                        title: "Error al actualizar status",
                        message: "Actualiza la página e intenta de nuevo",
                        type: "error",
                    });
                }
            } catch (error) {
                console.log('Error al cambiar el estatus:', error);
            } finally {
                this.updatingStatus = false; // Restablece el estado de actualización
                this.confirmCancelModal = false; // Cierra el modal de confirmación de cancelación
            }
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('service-reports.destroy', this.report.id));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: '',
                        type: 'success'
                    });

                    this.$inertia.get(route('service-reports.index'));

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: '',
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            } finally {
                this.confirmDeleteModal = false;
            }
        },
    }
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

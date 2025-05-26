<template>
    <section class="mt-4">
        <h1 class="mx-3 flex items-center justify-between">
            <span>Suscripción</span>
            <el-tag :type="$page.props.auth.user.store.is_active ? 'success' : 'danger'" effect="light">
                {{ $page.props.auth.user.store.is_active ? 'Activa' : 'Inactiva' }}
            </el-tag>
        </h1>
        <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3 relative">
            <p
                class="absolute top-2 left-0 py-1 px-4 rounded-e-full text-xs text-white bg-gradient-to-r from-[#2e19af] from-10% via-[#670ff7] via-40% to-[#853ff9] to-80%">
                Suscrito desde {{ formatDate($page.props.auth.user.store.created_at) }}
            </p>
            <article class="mt-6">
                <div>
                    <div>
                        <div v-if="!edit" class="grid grid-cols-3 gap-2">
                            <p class="flex flex-col">
                                <b>Suscripción: <span>{{ $page.props.auth.user.store.suscription_period
                                        }}</span></b>
                                <span class="text-xs text-gray99">
                                    Próximo pago: {{ formatDate($page.props.auth.user.store.next_payment) }}
                                </span>
                            </p>
                            <p class="flex flex-col">
                                <b>$ {{ getSuscriptionAmount().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</b>
                            </p>
                            <button @click="edit = true"
                                class="text-xs justify-self-end text-primary">
                                Pagar suscripción/ Agregar más tiempo
                            </button>
                            <!-- <p v-else class="flex items-center space-x-2 text-xs justify-self-end rounded-full px-3"
                                :class="getStatusStyles()">
                                <span v-html="getStatusIcon()"></span>
                                Periodo pagado
                            </p> -->
                        </div>
                        <div v-else class="flex flex-col space-y-3 col-span-full mt-7">
                            <div class="flex justify-between">
                                <button @click="edit = false" type="button" class="my-px text-[9px] self-start hover:bg-gray-200 rounded-full flex items-center justify-center size-5">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>      
                            </div>
                            <h2 class="font-bold my-4">Selecciona la suscripción que deseas obtener</h2>
                            <div v-for="(item, index) in suscriptions" :key="index"
                                class="relative pb-2 border-b border-grayD9">
                                <!-- mensaje de mejor opcion -->
                                <div v-if="item.name == 'Anual'" class="bg-primarylight absolute top-0 -left-4 w-48">
                                    <div class="relative">
                                        <p class="text-primary font-bold text-center">Mejor opción</p>
                                        <i
                                            class="fa-solid fa-play text-lg text-white absolute right-0 -bottom-1 rotate-180"></i>
                                    </div>
                                </div>
                                <div :class="item.name == 'Anual' ? 'mt-7' : ''" class="flex">
                                    <div class="flex items-center h-5">
                                        <input v-model="form.suscription_period"
                                            @change="handleChangeSuscriptionPeriod()" :id="'suscription-' + index"
                                            :aria-describedby="'suscription-text-' + index" type="radio"
                                            :value="item.name" class="size-3 text-primary border-gray-300 focus:ring-0">
                                    </div>
                                    <div class="ms-2 text-sm text-gray37 w-full">
                                        <div class="grid grid-cols-2">
                                            <label :for="'suscription-' + index" class="font-medium">
                                                {{ item.title }}
                                            </label>
                                            <label :for="'suscription-' + index" class="font-bold">
                                                $ {{ item.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                            </label>
                                        </div>
                                        <p :id="'suscription-text-' + index" class="text-xs font-normal text-gray99">
                                            {{ item.description }}
                                        </p>
                                    </div>
                                    <p v-if="$page.props.auth.user.store.suscription_period == item.title.split(' ')[0]"
                                        class="text-xs text-green-600 bg-green-50 p-1 self-start">
                                        Actual
                                    </p>
                                </div>
                            </div>

                            <div v-if="form.suscription_period" class="col-span-full md:pl-5 pt-7 pb-3 rounded-lg">
                                <div class="flex justify-between items-center relative">
                                    <p>Para completar el pago de tu suscripción, por favor, deposita o transfiere el dinero
                                        a los siguientes datos bancarios:</p>
                                    
                                    <el-tooltip class="" content="Contactar con soporte" placement="left">
                                        <a href="https://api.whatsapp.com/send?phone=523322268824" target="_blank" class="absolute top-0 right-0 flex items-center justify-center rounded-full size-7 bg-primary text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                            </svg>
                                        </a>
                                    </el-tooltip>
                                </div>

                                <div class="flex">
                                    <div class="w-44 pt-5 space-y-1">
                                        <p>Número de cuenta:</p>
                                        <p>Clabe interbancaria:</p>
                                        <p>Nombre de beneficiario:</p>
                                        <p>Banco:</p>
                                        <p>Monto de pago:</p>
                                    </div>
                                    <div class="w-44 pt-5 space-y-1">
                                        <p>537286726492</p>
                                        <p>678679678665342245</p>
                                        <p>Miguel O. Vazquez</p>
                                        <p>Nu México</p>
                                        <p class="font-bold flex items-center space-x-2">
                                            <span>${{ amountToPay }}</span>
                                             <el-tooltip v-if="verifiedTicket" placement="right">
                                                <template #content>
                                                <div>
                                                    <p class="text-cyan-500">Cupón de descuento utilizado</p>
                                                    <p>Descuento: {{ verifiedTicket?.is_percentage_discount ? '%' : '$' }}{{ verifiedTicket?.discount_amount }}</p>
                                                    <button class="text-white rounded-md px-3 bg-red-500 mt-3 mr-auto" @click="verifiedTicket = null">
                                                    Quitar cupón
                                                    </button>
                                                </div>
                                                </template>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-4 text-primary">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                                </svg>
                                            </el-tooltip>
                                        </p>
                                    </div>
                                </div>
                                <p class="pt-5">A continuación, ingresa una foto del comprobante de pago, es importante
                                    que se muestren todos los datos.</p>

                                <a v-if="this.$page.props.auth.user.store.last_payment"
                                    :href="this.$page.props.auth.user.store.last_payment?.media[0]?.original_url"
                                    target="_blank" class="underline text-primary">Ver comprobante anterior</a>
                                <div class="my-2">
                                    <InputFilePreview @imagen="saveImage" @cleared="form.image = null" />
                                </div>
                                <p class="text-gray99 text-xs">Tu información será validada en un plazo de 24 horas. Una
                                    vez
                                    confirmada, podrás continuar disfrutando de tu suscripción. Puedes verificar el
                                    estado de tu suscripción en cualquier momento.</p>

                                <!-- Codigo promocional -->
                                <div class="w-1/2 ml-auto rounded-xl bg-gray-100 p-4">
                                    <div v-if="!verifiedTicket">
                                        <h2 class="font-bold">Agrega el código promocional</h2>
                                        <div class="mt-3 col-span-full">
                                            <InputLabel value="Código" class="ml-3 mb-1" />
                                            <el-input v-model="ticketCode" @keydown.enter="VerifyTicket()" placeholder="Escribe el código de promoción" :maxlength="100" clearable />
                                            <span v-if="ticketCodeError" class="text-red-600 text-sm ml-4"><i class="fa-solid fa-xmark"></i> El cupón no es válido. Verifica nuevamente</span>
                                            <span v-if="ticketReferredError" class="text-red-600 text-sm ml-4"><i class="fa-solid fa-xmark"></i> Ya has utilizado un cupón de referido</span>
                                        </div>

                                        <div class="flex justify-start mt-5">
                                        <PrimaryButton @click="VerifyTicket()">Verificar</PrimaryButton>
                                        </div>
                                    </div>

                                    <div v-else>
                                        <p class="text-cyan-500">Cupón de descuento utilizado</p>
                                        <p>Descuento: {{ verifiedTicket?.is_percentage_discount ? '%' : '$' }}{{ verifiedTicket?.discount_amount }}</p>
                                        <button class="text-white rounded-md px-3 py-1 bg-red-500 mt-3 mr-auto" @click="verifiedTicket = null">
                                        Quitar cupón
                                        </button>
                                    </div>
                                </div>

                                <form @submit.prevent="checkout" class="flex items-center justify-end space-x-2 md:space-x-8 mx-4 mt-4">
                                    <button @click="edit = false" type="button" class="text-primary">Ahora no</button>
                                    <PrimaryButton @click="storePayment()" id="btn-bottom"
                                        :disabled="!form.image || form.processing">
                                        Enviar comprobante de pago</PrimaryButton>
                                    <PrimaryButton class="flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                        <span>Pagar con tarjeta</span>
                                    </PrimaryButton>
                                </form>
                            </div>

                            <div class="pt-4">
                                <h2 class="ml-3 font-bold">Modulos activos</h2>
                                <article class="md:flex items-center justify-between rounded-[5px] px-4 pt-1 mt-3">
                                    <section>
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" height="16" width="16"
                                                id="Shopping-Basket-2--Streamline-Core">
                                                <desc>Shopping Basket 2 Streamline Icon: https://streamlinehq.com</desc>
                                                <g id="shopping-basket-2--shopping-basket">
                                                <path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.625389714285713 7.932533357142858H2.6646102857142857C2.4237190714285712 7.927597714285714 2.184897642857143 7.976954142857143 1.9656595714285712 8.076940714285715C1.7464214999999998 8.176927285714285 1.5524984999999998 8.32499657142857 1.398219857142857 8.510162785714286C1.2439412142857142 8.695169785714285 1.1332872857142857 8.9126565 1.0745372142857144 9.146223857142857C1.0157871428571428 9.379950428571428 1.0102146428571428 9.623866714285715 1.0584565714285714 9.859822285714285L2.8252574999999998 18.693667714285713C2.900406642857143 19.061771142857143 3.1021311428571425 19.392140785714286 3.3957222857142857 19.626822642857142C3.689154214285714 19.861663714285715 4.0556655 19.986169285714286 4.431411214285714 19.978527H17.858588785714286C18.234493714285712 19.986169285714286 18.601005 19.861663714285715 18.894436928571427 19.626822642857142C19.187868857142856 19.392140785714286 19.38975257142857 19.061771142857143 19.4647425 18.693667714285713L21.231543428571428 9.859822285714285C21.279785357142856 9.623866714285715 21.274212857142857 9.379950428571428 21.215462785714287 9.146223857142857C21.156712714285714 8.9126565 21.046058785714283 8.695169785714285 20.891780142857144 8.510162785714286C20.7375015 8.32499657142857 20.5435785 8.176927285714285 20.32434042857143 8.076940714285715S19.866280928571427 7.927597714285714 19.625389714285713 7.932533357142858Z"
                                                    stroke-width="1.71"></path>
                                                <path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.357307428571428 2.3111545714285713L17.569614857142856 7.932533357142858" stroke-width="1.71">
                                                </path>
                                                <path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.720385142857142 7.932533357142858L7.932692571428571 2.3111545714285713" stroke-width="1.71">
                                                </path>
                                                </g>
                                            </svg>
                                            <p class="w-36 text-gray-500">Módulos esenciales</p>
                                            <span>$229.00/ mes</span>
                                        </div>
                                        <div class="my-1 flex items-center space-x-2" v-for="item in modules" :key="item">
                                            <span v-if="$page.props.auth.user.store.activated_modules.includes(item.name)" v-html="item.icon"></span>
                                            <p class="w-36 text-gray-500" v-if="$page.props.auth.user.store.activated_modules.includes(item.name)">{{ item.name }}</p>
                                            <span v-if="$page.props.auth.user.store.activated_modules.includes(item.name)">${{ item.cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}/ mes</span>
                                        </div>
                                    </section>
                                    <button @click="$inertia.get('/support/suscription')"
                                        class="text-xs text-primary justify-self-end my-2 md:my-0">
                                        Editar módulos de suscripción
                                    </button>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- <div v-if="!loading">
                        <div v-if="edit"
                            class="flex items-center space-x-1 *:size-5 *:rounded-full *:flex *:items-center *:justify-center *:border">
                            <el-tooltip content="Cancelar" placement="left">
                                <button @click="edit = false; form.reset()"
                                    class="text-gray-600 text-[9px] hover:bg-gray-100 transition-all duration-150"><i
                                        class="fa-solid fa-x"></i></button>
                            </el-tooltip>
                            <el-tooltip content="Guardar" placement="right">
                                <button @click="store"
                                    class="text-green-600 text-[9px] hover:bg-green-100 transition-all duration-150"><i
                                        class="fa-solid fa-check"></i></button>
                            </el-tooltip>
                        </div>
                        <button @click="edit = true" v-else class="text-primary text-xs">Adquirir más días</button>
                    </div>
                    <p v-else class="text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-primary mr-px"></i>
                        Cargando...
                    </p> -->
                </div>

                <!-- <div class="grid grid-cols-2 gap-2 mt-4">
                    <div v-if="!edit" class="flex flex-col space-y-1">
                        <b>Método de pago</b>
                        <div class="flex items-center space-x-2">
                            <figure class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                <img :src="getInstitutionImage(getDefultCard.institution)" class="size-6 rounded-full">
                            </figure>
                            <div class="flex flex-col text-xs">
                                <span>{{ getDefultCard.name }}</span>
                                <span>{{ getDefultCard.bank }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col space-y-3 col-span-full mt-3">
                        <h1 class="mx-3">
                            <span>Mis tarjetas guardadas</span>
                        </h1>
                        <div v-for="(item, index) in cards" :key="index" class="flex mt-2">
                            <div class="flex items-center">
                                <input v-model="form.default_card_id" :id="'card-' + index"
                                    :aria-describedby="'card-text-' + index" type="radio" :value="item.id"
                                    class="size-3 text-primary border-gray-300 focus:ring-0">
                            </div>
                            <div class="ms-2 text-sm w-full">
                                <label :for="'card-' + index" class="font-medium text-gray-900">
                                    <div class="flex items-center space-x-3 justify-between">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex flex-col">
                                                <div class="flex items-center space-x-2">
                                                    <figure
                                                        class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                                        <img :src="getInstitutionImage(item.institution)"
                                                            class="size-6 rounded-full">
                                                    </figure>
                                                    <div class="flex flex-col text-xs">
                                                        <span>{{ item.name }}</span>
                                                        <span>{{ item.bank }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <p v-if="$page.props.auth.user.store.default_card_id == item.id"
                                                class="ml-4 text-xs text-green-600">Predeterminado</p>
                                        </div>
                                        <button class="text-primary text-xs">Editar</button>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div @click="$inertia.visit(route('cards.create'))"
                            class="rounded-[5px] border border-grayD9 px-5 py-2 mt-2 cursor-pointer">
                            <div class="flex items-center space-x-3 justify-between">
                                <div class="grid grid-cols-2 gap-2 w-5/6">
                                    <div class="flex flex-col">
                                        <div class="flex items-center space-x-2">
                                            <figure
                                                class="size-8 flex items-center justify-center rounded-full border border-grayD9">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <mask id="mask0_8939_78" style="mask-type:alpha"
                                                        maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14">
                                                        <rect width="14" height="14" fill="#D9D9D9" />
                                                    </mask>
                                                    <g mask="url(#mask0_8939_78)">
                                                        <path
                                                            d="M2.33464 11.6673C2.0138 11.6673 1.73915 11.5531 1.51068 11.3246C1.2822 11.0961 1.16797 10.8215 1.16797 10.5007V3.50065C1.16797 3.17982 1.2822 2.90516 1.51068 2.67669C1.73915 2.44822 2.0138 2.33398 2.33464 2.33398H11.668C11.9888 2.33398 12.2635 2.44822 12.4919 2.67669C12.7204 2.90516 12.8346 3.17982 12.8346 3.50065V7.00065H2.33464V10.5007H8.16797V11.6673H2.33464ZM2.33464 4.66732H11.668V3.50065H2.33464V4.66732ZM11.0846 12.834V11.084H9.33463V9.91732H11.0846V8.16732H12.2513V9.91732H14.0013V11.084H12.2513V12.834H11.0846Z"
                                                            fill="#999999" />
                                                    </g>
                                                </svg>
                                            </figure>
                                            <div class="flex flex-col text-xs">
                                                <span>Nueva tarjeta</span>
                                                <span>Débito o crédito</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="text-primary text-xs"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div> -->
            </article>
        </div>
    </section>
    <!-- -------------- Modal de código aplicado correctamente ----------------------- -->
    <Modal :show="showApliedDiscountTicket" @close="showApliedDiscountTicket = false" maxWidth="sm">
      <div class="p-4 relative">
        <i @click="showApliedDiscountTicket = false"
          class="fa-solid fa-xmark cursor-pointer text-sm flex items-center justify-center absolute right-5"></i>

        <h2 class="font-bold text-center">Descuento aplicado con éxito</h2>

        <section class="mt-4 text-center">
          <p class="flex items-center space-x-2 rounded-full border border-dashed border-primary text-primary bg-primarylight py-1 px-3 w-1/2 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-5">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            <span>{{ verifiedTicket.discount_amount }}{{ verifiedTicket.is_percentage_discount ? '%' : '$' }} descuento</span>
          </p>

          <div class="mt-5 flex flex-col items-center justify-center">
            <svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5.91758 19.8418C4.12647 14.7142 0.232709 10.6358 0.0124311 10.3936C-0.207847 10.1514 2.52212 9.69147 6.39 13.7005C13.7218 5.3998 20.8295 -0.0222255 24.8141 0.000509436C24.9676 -0.0151292 25.2031 0.333452 25.0503 0.472922C16.2283 4.67346 11.3447 12.1369 7.09861 19.8418C7.07299 20.0753 5.90745 20.0289 5.91758 19.8418Z" fill="#189203"/>
            </svg>
            <p class="text-[#189203]">¡Disfruta de tu descuento!</p>
          </div>
        </section>
      </div>
    </Modal>
</template>
<script>
import InputLabel from "@/Components/InputLabel.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import Modal from "@/Components/Modal.vue";
import visaImage from "@/../../public/images/visa.jpg";
import mastercardImage from "@/../../public/images/mastercard.png";
import americanExpressImage from "@/../../public/images/american_express.png";

export default {
    data() {
        const form = useForm({
            amount: null,
            suscription_period: null,
            days: null,
            days_gifted: null,
            image: null,
            activeModules: null,
            discountTicketUsed: null,
            // default_card_id: this.$page.props.auth.user.store.default_card_id,
        });

        return {
            form,
            edit: false,
            loading: false,
            showPaymentModal: false,
            showApliedDiscountTicket: false,
            discountTickets: null, //cupones de descuento activos
            ticketCode: null, //codigo del ticket ingresado
            verifiedTicket: null, //codigo del ticket correctamente verificado
            ticketCodeError: false, //codigo del ticket no encontrado, bandera de error
            ticketReferredError: false, //codigo del ticket de referido usado
            modules: [
                {
                    name: 'Gastos',
                    cost: 30,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="17" width="17" id="Cash-Payment-Bills--Streamline-Ultimate"><desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path></svg>',
                },
                {
                    name: 'Tienda en línea',
                    cost: 120,
                    icon: '<svg width="18" height="20" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 1H2.5C2.10218 1 1.72064 1.15804 1.43934 1.43934C1.15804 1.72064 1 2.10218 1 2.5V13.5C1 13.8978 1.15804 14.2794 1.43934 14.5607C1.72064 14.842 2.10218 15 2.5 15H7.5C7.89782 15 8.27936 14.842 8.56066 14.5607C8.84196 14.2794 9 13.8978 9 13.5V2.5C9 2.10218 8.84196 1.72064 8.56066 1.43934C8.27936 1.15804 7.89782 1 7.5 1H6M4 1V2H6V1M4 1H6M4 13.5H6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.48168 9.1185H3.93649C3.80702 9.1109 3.77403 9.0824 3.71987 8.9741L2.90758 6.55526H2.32995C2.00414 6.55526 2.00505 6.08594 2.32995 6.08594H3.12419C3.32275 6.08594 3.39496 6.55526 3.48521 6.55526H7.00516C7.20372 6.55526 7.29011 6.74059 7.22177 6.93433L6.73439 8.10765C6.66219 8.25206 6.60803 8.30621 6.39142 8.30621H4.00869L4.13505 8.63113H6.48168C6.82465 8.63113 6.8066 9.1185 6.48168 9.1185Z" fill="currentColor"/><path d="M4.69564 9.49821C4.69564 9.70757 4.52593 9.87728 4.31657 9.87728C4.10722 9.87728 3.9375 9.70757 3.9375 9.49821C3.9375 9.28886 4.10722 9.11914 4.31657 9.11914C4.52593 9.11914 4.69564 9.28886 4.69564 9.49821Z" fill="currentColor"/><path d="M6.5401 9.49821C6.5401 9.70757 6.37039 9.87728 6.16103 9.87728C5.95168 9.87728 5.78196 9.70757 5.78196 9.49821C5.78196 9.28886 5.95168 9.11914 6.16103 9.11914C6.37039 9.11914 6.5401 9.28886 6.5401 9.49821Z" fill="currentColor"/></svg>',
                },
                {
                    name: 'Clientes',
                    cost: 80,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" id="User-Check-Validate--Streamline-Core" height="16" width="16"><desc>User Check Validate Streamline Icon: https://streamlinehq.com</desc><g id="user-check-validate--actions-close-checkmark-check-geometric-human-person-single-success-up-user"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 8.756785714285714c2.1982875642857143 0 3.9803571428571427 -1.7820695785714284 3.9803571428571427 -3.9803571428571427S10.15900185 0.7960714285714285 7.960714285714285 0.7960714285714285 3.9803571428571427 2.578141007142857 3.9803571428571427 4.776428571428571 5.762426721428571 8.756785714285714 7.960714285714285 8.756785714285714Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 19.901785714285715H0.7960714285714285l0 -0.8631006428571428c0.012675049285714285 -1.2135312857142857 0.33272123785714286 -2.4041357142857143 0.930241307142857 -3.4604110500000003 0.5975312142857143 -1.0563231 1.4530373357142858 -1.9439586642857143 2.4866087142857145 -2.580003814285714 1.0335873 -0.6360610714285715 2.211518271428571 -0.9997542642857142 3.423759921428571 -1.0571510142857143 0.10807465714285713 -0.005110778571428572 0.2161174714285714 -0.007785578571428572 0.3240329142857143 -0.00800847857142857 0.10791544285714287 0.00022289999999999997 0.21597417857142856 0.0028977 0.32404883571428567 0.00800847857142857 1.21224165 0.057396749999999996 2.3901726214285715 0.42108994285714285 3.423759921428571 1.0571510142857143 0.5943469285714286 0.36574705714285716 1.1298004928571428 0.8146835785714285 1.5911875714285713 1.3310632714285713" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m21.49392857142857 13.533214285714285 -6.368571428571428 7.960714285714285 -3.184285714285714 -2.3882142857142856" stroke-width="1.71"></path></g></svg>',
                },
                {
                    name: 'Cotizaciones',
                    cost: 80,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
                },
                {
                    name: 'Renta de productos',
                    cost: 30,
                    icon: '<svg stroke="currentColor" width="18" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.32656 2.03516H1.67656C1.45776 2.03516 1.24791 2.1406 1.0932 2.32828C0.938482 2.51596 0.851562 2.77052 0.851562 3.03594V15.0453C0.851562 15.3107 0.938482 15.5653 1.0932 15.753C1.24791 15.9407 1.45776 16.0461 1.67656 16.0461H12.4016C12.6204 16.0461 12.8302 15.9407 12.9849 15.753C13.1397 15.5653 13.2266 15.3107 13.2266 15.0453V3.03594C13.2266 2.77052 13.1397 2.51596 12.9849 2.32828C12.8302 2.1406 12.6204 2.03516 12.4016 2.03516H6.62656" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.62813 8.03866L4.97813 6.53816L3.32813 8.03932V1.53426C3.32805 1.46849 3.33867 1.40335 3.35937 1.34256C3.38007 1.28178 3.41044 1.22654 3.44875 1.18C3.48706 1.13346 3.53256 1.09655 3.58264 1.07136C3.63273 1.04617 3.68641 1.0332 3.74063 1.0332H6.21563C6.32502 1.0332 6.42996 1.08592 6.50729 1.17976C6.58468 1.27361 6.62813 1.40088 6.62813 1.53359V8.03866Z" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 13.043H9.10313" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 10.041H10.7531" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.7523 7.03906H8.27734" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                },
                {
                    name: 'Servicios',
                    cost: 60,
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>',
                },
            ],
            suscriptions: [
                {
                    name: "Mensual",
                    title: "Mensual (30 días)",
                    amount: 229,
                    description: "",
                    days: 30,
                    daysGifted: 0,
                },
                {
                    name: "Anual",
                    title: "Anual (12 meses)",
                    amount: 2290,
                    description: "Te regalamos 2 meses",
                    days: 365,
                    daysGifted: 0,
                },
            ],
            cards: [
                {
                    id: 1,
                    name: "Tarjeta de débito terminada en 0141",
                    bank: "BBVA",
                    institution: 'visa',
                },
                {
                    id: 2,
                    name: "Tarjeta de crédito terminada en 1187",
                    bank: "BANORTE",
                    institution: 'mastercard',
                },
                {
                    id: 3,
                    name: "Tarjeta de crédito terminada en 1780",
                    bank: "NU",
                    institution: 'american_express',
                },
            ],
            images: {
                visa: visaImage,
                mastercard: mastercardImage,
                american_express: americanExpressImage,
            },
        };
    },
    props: {
        user: Object,
    },
    components: {
        InputFilePreview,
        PrimaryButton,
        InputLabel,
        Modal
    },
    computed: {
        getDefultCard() {
            return this.cards.find(item => item.id == this.$page.props.auth.user.store.default_card_id);
        },
        amountToPay() {
            const subscription = this.suscriptions.find(item => item.name == this.form.suscription_period);
            if (!subscription) return null; // Si no se encuentra la suscripción, retorna null o un valor predeterminado
            
            let amount = subscription.amount;

            // Verifica si hay un descuento
            if (this.verifiedTicket) {
                const discount = this.verifiedTicket.discount_amount;

                if (this.verifiedTicket.is_percentage_discount) {
                    // Si es un descuento porcentual, aplica el porcentaje
                    amount = amount - (amount * discount / 100);
                } else {
                    // Si no es un descuento porcentual, resta la cantidad fija
                    amount = amount - discount;
                }
            }

            // Formatea el monto final con comas y 2 decimales
            return amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        //habilita el pago segun los días que se desee para la expiración del plan actual
        // enablePayment() {
        //     const today = new Date();
        //     const nextPaymentDate = new Date(this.$page.props.auth.user.store.next_payment);
        //     const diffDays = (nextPaymentDate - today) / (1000 * 60 * 60 * 24);

        //     return diffDays <= 7;
        // }
    },
    methods: {
        checkout() {
            this.form.activeModules = this.modules.filter(item => this.$page.props.auth.user.store.activated_modules.includes(item.name));
            this.form.activeModules.unshift({ name: "Módulos básicos", cost: 229 });
            this.form.discountTicketUsed = this.verifiedTicket;
            this.form.post(route('stripe.index'));
        },
        calculateTotalPeriosPrice() {
            // Filtra los módulos activados
            const activatedModules = this.$page.props.auth.user.store.activated_modules;

            // Calcula el costo total de los módulos activados
            let totalCost = 229;
            this.modules.forEach(module => {
                if (activatedModules.includes(module.name)) {
                    totalCost += module.cost;
                }
            });

            // Actualiza el amount de la suscripción mensual
            this.suscriptions.forEach(suscription => {
                if (suscription.name === "Mensual") {
                    suscription.amount = totalCost;
                } else {
                    suscription.amount = (totalCost * 10);
                }
            });
        },
        prepareReUpload() {
            const lastPayment = this.$page.props.auth.user.store.last_payment;
            this.form.suscription_period = lastPayment.suscription_period;
            this.form.amount = lastPayment.amount;
            this.form.days = 0;
            this.form.days_gifted = 0;
            this.edit = true;

            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },
        getStatusStyles() {
            const storeStatus = this.$page.props.auth.user.store.is_active;

            if (storeStatus) {
                return 'text-green-600';
            }
        },
        getStatusIcon() {
            const storeStatus = this.$page.props.auth.user.store.is_active;

            if (storeStatus) {
                return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                `;
            }
        },
        handleChangeSuscriptionPeriod() {
            const suscription = this.suscriptions.find(item => item.name == this.form.suscription_period);
            this.form.amount = suscription.amount;
            this.form.days = suscription.days;
            this.form.days_gifted = suscription.daysGifted;

            this.scrollToBottom();
        },
        scrollToBottom() {
            const btnBottom = document.getElementById('btn-bottom');
            if (btnBottom) {
                btnBottom.scrollIntoView({ behavior: 'smooth' });
            }
        },
        getSuscriptionAmount() {
            // Filtra los módulos activados
            const activatedModules = this.$page.props.auth.user.store.activated_modules;

            // Calcula el costo total de los módulos activados
            let totalCost = 229;
            this.modules.forEach(module => {
                if (activatedModules.includes(module.name)) {
                    totalCost += module.cost;
                }
            });
            return this.$page.props.auth.user.store.suscription_period === 'Mensual' ? totalCost : totalCost * 10;
        },
        getInstitutionImage(institution) {
            return this.images[institution];
        },
        storePayment() {
            this.loading = true;
            this.form.post(route('payments.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Comprobante enviado',
                        message: '',
                        type: 'success',
                    });
                    this.edit = false;
                    this.loading = false;
                    this.form.reset();
                    this.verifiedTicket = null;
                },
            });
        },
        // store() {
        //     this.loading = true;
        //     this.form.put(route('ezy-profile.update-suscription'), {
        //         onSuccess: () => {
        //             this.$notify({
        //                 title: 'Correcto',
        //                 message: '',
        //                 type: 'success',
        //             });
        //             this.edit = false;
        //             this.loading = false;
        //         },
        //     });
        // },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        saveImage(image) {
            this.form.image = image;
        },
        async fetchActiveDiscountTickets() {
            try {
                const response = await axios.get(route('discount-tickets.fetch-active-tickets'))
                if (response.status === 200) {
                    this.discountTickets = response.data.discount_tickets;
                }

            } catch (error) {
                console.log(error);
            }
        },
        VerifyTicket() {
            if ( this.discountTickets.some(ticket => ticket.code === this.ticketCode )) {
                this.verifiedTicket = this.discountTickets.find(ticket => ticket.code === this.ticketCode);
                if ( this.$page.props.auth.user.store.partner_cupon && this.verifiedTicket.description?.startsWith('Descuento de bienvenida para referidos') ) {
                    this.ticketReferredError = true;
                    this.verifiedTicket = null;
                    return;
                }
                this.showApliedDiscountTicket = true;
                this.ticketCodeError = false;
                this.ticketReferredError = false;
                this.ticketCode = null;
            } else {
                this.ticketCodeError = true;
            }
        },
    },
    mounted() {
        this.calculateTotalPeriosPrice();
        //carga los cupones de descuento disponibles
        this.fetchActiveDiscountTickets();
    }
}
</script>
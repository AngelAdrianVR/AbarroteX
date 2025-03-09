<template>
  <main class="w-full lg:flex lg:space-x-5 lg:divide-x-2 divide-gray-D9">

    <!-- Lado izquierdo -->
    <section class="lg:w-1/2 lg:pr-7">

      <div class="mt-1">
        
        <p class="mx-7 mb-2 text-center">Los siguientes módulos son opcionales. Seleccionalos según tus
          necesidades </p>

        <!-- componente de modulo -->
        <article v-for="item in modules" :key="item"
          class="flex items-center space-x-1 border border-grayD9 rounded-md py-3 px-4 mb-2">
          <div class="w-3/4">
            <div class="flex items-center space-x-3">
              <span v-html="item.icon"></span>
              <p>{{ item.name }}</p>
            </div>

            <p class="text-gray99">{{ item.description }}</p>
            <p>${{ item.cost }}.00/mes</p>
          </div>

            <div class="w-1/4">
              <el-switch @change="handleSwitchModule(item)" v-model="item.activated" class="ml-2"
                style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #515151" />
            </div>
        </article>
      </div>
    </section>

    <!-- Lado Derecho -->
    <section class="lg:w-1/2 lg:pl-12 mt-9 lg:mt-0">
      <article class="rounded-xl border border-grayD9 p-4 mt-2 shadow-md">
       <!-- botones de periodo -->
        <div class="flex items-center justify-between space-x-4">
          <!-- boton de mensual -->
          <button @click="period = 'Mensual'"
            :class="period === 'Mensual' ? 'bg-primarylight text-[#373737] border-primary' : 'bg-transparent border-grayD9'"
            class="cursor-pointer text-left rounded-lg border-2 p-3 font-bold w-full disabled:cursor-not-allowed disabled:bg-grayD9 shadow-md">
            <p>Mensual</p>
          </button>
          <!-- boton de Anual -->
          <button @click="period = 'Anual'"
            :class="period === 'Anual' ? 'bg-primarylight text-[#373737] border-primary' : 'bg-transparent border-grayD9'"
            class="cursor-pointer rounded-lg border-2 p-3 font-bold w-full relative shadow-md">
            <!-- popular -->
            <div
              class="absolute -top-3 right-2 rounded-full bg-[#006847] text-white flex items-center space-x-2 text-[10px] px-2 py-[2px]">
              <i class="fa-solid fa-star"></i>
              <p>Popular</p>
            </div>
            <p>Anual <span :class="period === 'Anual' ? 'text-[#494949]' : ''" class="text-[11px] text-[#626262] ml-2 font-thin">2 meses de regalo</span></p>
          </button>
        </div>

        <!-- Detalles del pago -->
        <div class="mt-4 px-3">
          <h2 class="font-bold mb-2">Detalles del pago</h2>
          <div class="flex border-b border-grayD9 pb-2">
            <p class="w-1/2">Módulos esenciales</p>
            <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
              'Mensual' ? '229.00' : '2,290.00' }}</span></p>
          </div>
          <p v-if="modules.filter(item => item.activated === true).length" class="text-[#929292] font-bold my-3">Adicionales</p>

          <!-- Módulos adicionales -->
          <section class="border-b border-grayD9 pb-2">
            <div class="flex" v-for="item in modules.filter(item => item.activated === true)" :key="item">
              <p class="w-1/2">{{ item.name }}</p>
              <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
                'Mensual' ? item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : (item.cost *
                  10).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
            </div>
          </section>

          <!-- Cupon de descuento -->
          <button @click="showDiscountModal = true"
            :disabled="calculateTotalPayment(calculateTotal) == 0 || verifiedTicket"
            class="text-primary flex items-center space-x-2 mt-3 disabled:text-gray-400 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            <span>Agregar código promocional</span>
          </button>

          <!-- Total -->
          <div class="flex font-bold mt-3">
            <div class="flex items-center space-x-2 w-1/2">
              <span>Total</span>
              <el-tooltip v-if="verifiedTicket" placement="right">
                <template #content>
                  <div>
                    <p class="text-cyan-500">Cupón de descuento utilizado</p>
                    <!-- Total sin descuento -->
                    <p>Subtotal: {{ verifiedTicket?.is_percentage_discount ? (calculateTotalPayment(calculateTotal) / (1 - verifiedTicket.discount_amount / 100)).toFixed(2) : (calculateTotalPayment(calculateTotal) + verifiedTicket.discount_amount).toFixed(2) }}</p>
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
            </div>
            <p class="w-1/2 text-right">
              <span class="mr-1">$</span>
              <span class="w-20 inline-block">{{
                (calculateTotalPayment(calculateTotal)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
          </div>

          <form @submit.prevent="checkout" class="text-center mt-8">
            <PrimaryButton :disabled="loading || $page.props.auth.user.store.is_active" class="!px-28">
              <i v-if="loading" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Confirmar y pagar
            </PrimaryButton>
          </form>

          <p class="text-gray99 text-xs mt-3">
            Puedes cancelar tu suscripción cuando quieras desde la página de ajustes de la suscripción.
            Al realizar el pago aceptas nuestros
            <a target="_blank" :href="route('terms.show')" class="underline focus:outline-none text-primary font-thin">
              Términos y Condiciones
            </a>
            y confirmas que has leído nuestra
            <a target="_blank" :href="route('policy.show')" class="underline focus:outline-none text-primary font-thin">
              política de privacidad
            </a>.
          </p>
        </div>
      </article>
    </section>

     <!-- -------------- Modal de código promocional ----------------------- -->
    <Modal :show="showDiscountModal" @close="showDiscountModal = false" maxWidth="lg">
      <div class="p-4 relative">
        <i @click="showDiscountModal = false"
          class="fa-solid fa-xmark cursor-pointer text-sm flex items-center justify-center absolute right-5"></i>

        <h2 class="font-bold">Agrega el código promocional</h2>

        <div class="mt-3 col-span-full mx-10">
            <InputLabel value="Código" class="ml-3 mb-1" />
            <el-input @keydown.enter="VerifyTicket()" v-model="ticketCode" placeholder="Escribe el código de promoción" :maxlength="100" clearable />
            <span v-if="ticketCodeError" class="text-red-600 text-sm ml-4"><i class="fa-solid fa-xmark"></i> El cupón no es válido. Verifica nuevamente</span>
            <span v-if="ticketReferredError" class="text-red-600 text-sm ml-4"><i class="fa-solid fa-xmark"></i> Ya has utilizado un cupón de referido</span>
        </div>

        <div class="flex justify-end mt-5">
          <PrimaryButton @click="VerifyTicket()">Verificar</PrimaryButton>
        </div>
      
      </div>
    </Modal>

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
  </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';;

export default {
  data() {
    const form = useForm({
        amount: null,
        suscription_period: null,
        activeModules: null,
        discountTicketUsed: null, //cupon de descuento utilizado
        modulesUpdated: [],
    });

    return {
      form,
      loading: false, // estado de carga de peticion de update modules
      nextPayment: this.$page.props.auth.user.store.next_payment, // proximo pago
      period: this.$page.props.auth.user.store.suscription_period === 'Periodo de prueba' ? 'Anual' : this.$page.props.auth.user.store.suscription_period, //Periodo de pago seleccionado
      activated_modules: [],

      //cupon de descuento
      showDiscountModal: false, 
      showApliedDiscountTicket: false,
      discountTickets: null, //cupones de descuento activos
      ticketCode: null, //codigo del ticket ingresado
      verifiedTicket: null, //codigo del ticket correctamente verificado
      ticketCodeError: false, //codigo del ticket no encontrado, bandera de error
      ticketReferredError: false, //codigo de cupón de referencia usado

      modules: [
        {
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="20" width="20" id="Cash-Payment-Bills--Streamline-Ultimate"><desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path></svg>',
          name: 'Gastos',
          description: 'Registra y monitorea tus gastos facílmente.',
          cost: 30,
          activated: false,
        },
        {
          icon: '<svg width="17" height="24" viewBox="0 0 15 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.71429 1H3.35714C2.73199 1 2.13244 1.24834 1.69039 1.69039C1.24834 2.13244 1 2.73199 1 3.35714V20.6429C1 21.268 1.24834 21.8676 1.69039 22.3096C2.13244 22.7517 2.73199 23 3.35714 23H11.2143C11.8394 23 12.439 22.7517 12.881 22.3096C13.3231 21.8676 13.5714 21.268 13.5714 20.6429V3.35714C13.5714 2.73199 13.3231 2.13244 12.881 1.69039C12.439 1.24834 11.8394 1 11.2143 1H8.85714M5.71429 1V2.57143H8.85714V1M5.71429 1H8.85714M5.71429 20.6429H8.85714" stroke="currentColor" stroke-width="1.57143" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.61072 13.7576H5.61113C5.40769 13.7457 5.35584 13.7009 5.27074 13.5307L3.99428 9.7297H3.08657C2.57459 9.7297 2.57601 8.99219 3.08657 8.99219H4.33467C4.64669 8.99219 4.76016 9.7297 4.90198 9.7297H10.4333C10.7454 9.7297 10.8811 10.0209 10.7737 10.3254L10.0078 12.1692C9.89438 12.3961 9.80928 12.4812 9.46889 12.4812H5.72459L5.92316 12.9918H9.61072C10.1497 12.9918 10.1213 13.7576 9.61072 13.7576Z" fill="currentColor" stroke="currentColor" stroke-width="0.0567317" stroke-linecap="round"/><path d="M6.72314 14.3535C6.72314 14.6396 6.49119 14.8716 6.20506 14.8716C5.91893 14.8716 5.68698 14.6396 5.68698 14.3535C5.68698 14.0674 5.91893 13.8354 6.20506 13.8354C6.49119 13.8354 6.72314 14.0674 6.72314 14.3535ZM9.62158 14.3535C9.62158 14.6396 9.38962 14.8716 9.1035 14.8716C8.81737 14.8716 8.58541 14.6396 8.58541 14.3535C8.58541 14.0674 8.81737 13.8354 9.1035 13.8354C9.38962 13.8354 9.62158 14.0674 9.62158 14.3535Z" fill="currentColor" stroke="currentColor" stroke-width="0.155203"/></svg>',
          name: 'Tienda en línea',
          description: 'Gestiona tus ventas y pedidos en línea.',
          cost: 120,
          activated: false,
        },
        {
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24" id="User-Check-Validate--Streamline-Core" height="19" width="19"><desc>User Check Validate Streamline Icon: https://streamlinehq.com</desc><g id="user-check-validate--actions-close-checkmark-check-geometric-human-person-single-success-up-user"><path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 8.756785714285714c2.1982875642857143 0 3.9803571428571427 -1.7820695785714284 3.9803571428571427 -3.9803571428571427S10.15900185 0.7960714285714285 7.960714285714285 0.7960714285714285 3.9803571428571427 2.578141007142857 3.9803571428571427 4.776428571428571 5.762426721428571 8.756785714285714 7.960714285714285 8.756785714285714Z" stroke-width="1.71"></path><path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.960714285714285 19.901785714285715H0.7960714285714285l0 -0.8631006428571428c0.012675049285714285 -1.2135312857142857 0.33272123785714286 -2.4041357142857143 0.930241307142857 -3.4604110500000003 0.5975312142857143 -1.0563231 1.4530373357142858 -1.9439586642857143 2.4866087142857145 -2.580003814285714 1.0335873 -0.6360610714285715 2.211518271428571 -0.9997542642857142 3.423759921428571 -1.0571510142857143 0.10807465714285713 -0.005110778571428572 0.2161174714285714 -0.007785578571428572 0.3240329142857143 -0.00800847857142857 0.10791544285714287 0.00022289999999999997 0.21597417857142856 0.0028977 0.32404883571428567 0.00800847857142857 1.21224165 0.057396749999999996 2.3901726214285715 0.42108994285714285 3.423759921428571 1.0571510142857143 0.5943469285714286 0.36574705714285716 1.1298004928571428 0.8146835785714285 1.5911875714285713 1.3310632714285713" stroke-width="1.71"></path><path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m21.49392857142857 13.533214285714285 -6.368571428571428 7.960714285714285 -3.184285714285714 -2.3882142857142856" stroke-width="1.71"></path></g></svg>',
          name: 'Clientes',
          description: 'Gestiona tus clientes y sus compras.',
          cost: 80,
          activated: false,
        },
        {
          icon: '<svg width="20" height="18" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.72727 9.72727C1 9.72727 0.999995 9.36363 1 8.63636V1.72727C1 1.36364 1 1 1.72727 1M1.72727 9.72727V1M1.72727 9.72727C1.90647 9.72727 2.08094 9.72727 2.25124 9.72727M1.72727 1C1.72727 1 1.93741 1 2.25124 1M5 1C4.97588 1 4.78219 1 4.5 1M5 1V9.72727M5 1C5.06289 1 5.32199 1.40998 5.59688 1.72727M6.09091 2.09091C5.9502 2.09091 5.77039 1.92756 5.59688 1.72727M6.09091 2.09091V9.72727M6.09091 2.09091C6.16182 2.09091 6.39866 2.09091 6.73402 2.09091M11.1818 2.09091C11.5455 2.09091 11.9091 2.45455 11.9091 2.81818C11.9091 3.67023 11.9091 4.14795 11.9091 5C11.6502 5 11.4076 5.03464 11.1818 5.09863M11.1818 2.09091C11.1818 2.09091 10.9778 2.09091 10.6514 2.09091M11.1818 2.09091V5.09863M10.4545 9.9863L10.8275 10.2659C11.0759 10.4524 11.4016 10.5456 11.7273 10.5456M11.7273 10.5456C12.0529 10.5455 12.3785 10.4523 12.6271 10.2659C13.1243 9.89297 13.1243 9.28885 12.6271 8.91594C12.3789 8.72927 12.0531 8.63636 11.7273 8.63636C11.4197 8.63636 11.1121 8.54303 10.8775 8.35679C10.4083 7.98388 10.4083 7.37976 10.8775 7.00685C11.1121 6.82039 11.4197 6.72717 11.7273 6.72717M11.7273 10.5456V11.1818M11.7273 6.72717C12.0348 6.72717 12.3424 6.82039 12.577 7.00685M11.7273 6.72717V6.09091M2.25124 9.72727V1M2.25124 9.72727C2.37306 9.72727 2.49274 9.72727 2.61051 9.72727M2.25124 1C2.38633 1 2.54064 1 2.70567 1M2.70567 1L2.61051 9.72727M2.70567 1C2.86231 1 3.02862 1 3.19732 1M2.61051 9.72727C2.83929 9.72727 3.06081 9.72727 3.27661 9.72727M3.27661 9.72727L3.19732 1M3.27661 9.72727C3.47674 9.72727 3.67193 9.72727 3.86342 9.72727M3.19732 1C3.42077 1 3.64843 1 3.86342 1M3.86342 1V9.72727M3.86342 1C4.09801 1 4.31751 1 4.5 1M3.86342 9.72727C4.07976 9.72727 4.29137 9.72727 4.5 9.72727M4.5 9.72727V1M4.5 9.72727C4.6683 9.72727 4.83466 9.72727 5 9.72727M5 9.72727C5.20363 9.72727 5.40572 9.72727 5.60798 9.72727M5.60798 9.72727L5.59688 1.72727M5.60798 9.72727C5.76856 9.72727 5.92925 9.72727 6.09091 9.72727M6.09091 9.72727C6.30296 9.72727 6.51668 9.72727 6.73402 9.72727M6.73402 9.72727V2.09091M6.73402 9.72727C6.93653 9.72727 7.14219 9.72727 7.35255 9.72727M6.73402 2.09091C6.91503 2.09091 7.12474 2.09091 7.35255 2.09091M7.35255 2.09091V9.72727M7.35255 2.09091C7.52841 2.09091 7.71507 2.09091 7.90764 2.09091M7.35255 9.72727C7.53375 9.72727 7.71844 9.72727 7.90764 9.72727M7.90764 9.72727V2.09091M7.90764 9.72727C8.10008 9.72727 8.29719 9.72727 8.5 9.72727M7.90764 2.09091C8.10078 2.09091 8.29988 2.09091 8.5 2.09091M8.5 2.09091V9.72727M8.5 2.09091C8.6669 2.09091 8.83452 2.09091 9 2.09091M8.5 9.72727C8.77638 9.72727 9.06337 9.72727 9.36364 9.72727C8.67037 8.34074 9.17798 6.1341 10.6514 5.31744M9 2.09091C9 4.20329 9 5.38762 9 7.5M9 2.09091C9.22933 2.09091 9.45455 2.09091 9.66806 2.09091M9.66806 2.09091C9.66806 3.6175 9.66806 4.47341 9.66806 6M9.66806 2.09091C9.88968 2.09091 10.0987 2.09091 10.2866 2.09091M10.2866 2.09091C10.2866 3.38248 10.2866 4.10661 10.2866 5.39819M10.2866 2.09091C10.4198 2.09091 10.5424 2.09091 10.6514 2.09091M10.6514 2.09091C10.6514 3.35095 10.6514 4.0574 10.6514 5.31744M10.6514 5.31744C10.8162 5.22607 10.9931 5.1521 11.1818 5.09863" stroke="currentColor" stroke-width="0.727273"/></svg>',
          name: 'Cotizaciones',
          description: 'Crea cotizaciones precisas en segundos.',
          cost: 80,
          activated: false,
        },
        {
          icon: '<svg stroke="currentColor" width="20" height="20" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.32656 2.03516H1.67656C1.45776 2.03516 1.24791 2.1406 1.0932 2.32828C0.938482 2.51596 0.851562 2.77052 0.851562 3.03594V15.0453C0.851562 15.3107 0.938482 15.5653 1.0932 15.753C1.24791 15.9407 1.45776 16.0461 1.67656 16.0461H12.4016C12.6204 16.0461 12.8302 15.9407 12.9849 15.753C13.1397 15.5653 13.2266 15.3107 13.2266 15.0453V3.03594C13.2266 2.77052 13.1397 2.51596 12.9849 2.32828C12.8302 2.1406 12.6204 2.03516 12.4016 2.03516H6.62656" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.62813 8.03866L4.97813 6.53816L3.32813 8.03932V1.53426C3.32805 1.46849 3.33867 1.40335 3.35937 1.34256C3.38007 1.28178 3.41044 1.22654 3.44875 1.18C3.48706 1.13346 3.53256 1.09655 3.58264 1.07136C3.63273 1.04617 3.68641 1.0332 3.74063 1.0332H6.21563C6.32502 1.0332 6.42996 1.08592 6.50729 1.17976C6.58468 1.27361 6.62813 1.40088 6.62813 1.53359V8.03866Z" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 13.043H9.10313" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 10.041H10.7531" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.7523 7.03906H8.27734" stroke="currentColor" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/></svg>',
          name: 'Renta de productos',
          description: 'Gestiona el alquiler de tus productos.',
          cost: 30,
          activated: false,
        },
        {
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" /></svg>',
          name: 'Servicios',
          description: 'Administra tus servicios de manera eficiente.',
          cost: 60,
          activated: false,
        },
      ]
    };
  },
  components: {
    PrimaryButton,
    InputLabel,
    Modal
  },
  props: {},
  methods: {
    checkout() {
      this.form.amount = this.calculateTotalPayment(this.calculateTotal); //monto total a pagar
      this.form.suscription_period = this.period; //periodo de tiempo a pagar (mes, año)
      this.form.activeModules = this.modules.filter(item => item.activated === true); //detalle de modulos a pagar
      this.form.activeModules.unshift({ name: "Módulos básicos", cost: 229 }); //se agregan los modulos escenciales o basicos para mostrarlo en detalles de pago
      this.form.modulesUpdated = this.activated_modules; //modulos pagados para agregarlos en base de datos en caso de agregados o quitados
      this.form.discountTicketUsed = this.verifiedTicket //cupón de descuento utilizado
      this.form.post(route('stripe.index'));
    },
    async updateStoreModules() {
      this.loading = true;
      try {
        const response = await axios.post(route('store.update-modules', this.$page.props.auth.user.store.id), { period: this.period, activated_modules: this.activated_modules });
        if ( response.status === 200 ) {
          location.reload();          
        }

      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
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
            this.showDiscountModal = false;
            this.showApliedDiscountTicket = true;
            this.ticketCodeError = false;
            this.ticketReferredError = false;
            this.ticketCode = null;
        } else {
            this.ticketCodeError = true;
        }
    },
    calculateTotalPayment(calculateTotal) {
      let total;

      if (this.period === 'Mensual') {
        total = calculateTotal;
      } else {
        total = calculateTotal * 10;
      }

      // Aplicar descuento si existe un cupón verificado
      if (this.verifiedTicket) {
        if (this.verifiedTicket.is_percentage_discount) {
          // Aplicar descuento porcentual
          total -= (total * this.verifiedTicket.discount_amount) / 100;
        } else {
          // Aplicar descuento fijo
          total -= this.verifiedTicket.discount_amount;
        }
      }

      // Asegurar que el total no sea negativo
      return Math.max(0, total);
    },
    handleSwitchModule(module) {
      if (module.activated) {
        this.activated_modules.push(module.name);
      } else {
        this.activated_modules = this.activated_modules.filter(name => name !== module.name);
      }
    },
    saveExtraModules() {
      localStorage.setItem('EzyExtraModules', JSON.stringify(this.activated_modules));
      this.$inertia.get(route('register'));
    }
  },
  computed: {
    calculateTotal() {
      return this.modules
        .filter(item => item.activated === true)
        .reduce((sum, item) => sum + item.cost, 0) + 229;
    }
  },
  mounted() {
    //carga los cupones de descuento disponibles
    this.fetchActiveDiscountTickets();

    // Filtra los módulos activados
    const activatedModules = this.$page.props.auth.user.store.activated_modules;

    //se guardan los modulos activados de la tienda en una variable global de la vista
    this.activated_modules = activatedModules;

    //se calcula el total pagado por los modulos actualmente activados y se muestran activados los ya pagados
    this.modules.forEach(module => {
        if (activatedModules.includes(module.name)) {
          module.activated = true;
          this.totalPaid += module.cost;
        }
    });

    //si el periodo pagado es anual el monto pagado se multiplica por 10
    if ( this.$page.props.auth.user.store.suscription_period === 'Anual' ) {
      this.totalPaid *= 10;
    }
  }
};
</script>
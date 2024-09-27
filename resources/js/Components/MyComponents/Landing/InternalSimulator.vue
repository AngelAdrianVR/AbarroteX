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
      <p class="font-font">Detalles del Pago</p>

      <article class="rounded-xl border border-grayD9 p-4 mt-2">
        <!-- botones de periodo -->
        <div class="flex items-center justify-between space-x-4">
          <!-- boton de mensual -->
          <div @click="period = 'Mensual'"
            :class="period === 'Mensual' ? 'bg-primarylight text-primary border-primary' : 'bg-transparent border-grayD9'"
            class="cursor-pointer rounded-lg border-2 p-3 font-bold w-full">
            <p>Mensual</p>
          </div>
          <!-- boton de Anual -->
          <div @click="period = 'Anual'"
            :class="period === 'Anual' ? 'bg-primarylight text-primary border-primary' : 'bg-transparent border-grayD9'"
            class="cursor-pointer rounded-lg border-2 p-3 font-bold w-full relative">
            <!-- popular -->
            <div
              class="absolute -top-3 right-2 rounded-full bg-[#006847] text-white flex items-center space-x-2 text-[10px] px-2 py-[2px]">
              <i class="fa-solid fa-star"></i>
              <p>Popular</p>
            </div>
            <p>Anual <span :class="period === 'Anual' ? 'text-[#494949]' : ''"
                class="text-xs ml-2 font-thin">2 meses de regalo</span></p>
          </div>
        </div>

        <!-- Detalles del pago -->
        <div class="mt-7 px-3">
          <div class="flex">
            <p class="w-1/2">Módulos esenciales</p>
            <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
              'Mensual' ? '199.00' : '1,990.00' }}</span></p>
          </div>
          <p v-if="modules.filter(item => item.activated === true).length" class="text-gray99 my-3">Otros módulos</p>

          <!-- Otros modulos -->
          <div v-for="item in modules.filter(item => item.activated === true)" :key="item" class="flex">
            <p class="w-1/2">{{ item.name }}</p>
            <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
              'Mensual' ? item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : (item.cost *
                10).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
          </div>

          <!-- <p class="text-gray99 my-3">Descuentos</p> -->

          <!-- Descuento por modulos ya pagados -->
          <!-- <div class="flex">
            <p class="w-1/2">Monto ya pagado</p>
            <p class="w-1/2 text-right"><span class="mr-1">$</span>
              <span class="w-20 inline-block">- {{ totalPaid.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
          </div> -->

          <!-- Descuento por tiempo transcurrido -->
          <!-- <div class="flex">
            <p class="w-1/2">Desc. por tiempo transcurrido</p>
            <p class="w-1/2 text-right"><span class="mr-1">$</span>
              <span class="w-20 inline-block">
                 {{ calculateDiscountForPastDays(calculateTotalPayment(calculateTotal)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
              </span>
            </p>
          </div> -->

          <!-- Total -->
          <div class="flex mt-7">
            <p class="w-1/2">Total</p>
            <p class="w-1/2 text-right">
              <span class="mr-1">$</span>
              <span class="w-20 inline-block">{{ (calculateTotalPayment(calculateTotal)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
          </div>

          <form @submit.prevent="checkout" class="text-center mt-8">
            <PrimaryButton :disabled="loading || $page.props.auth.user.store.is_active" class="!px-28">
              <i v-if="loading" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Confirmar y pagar
            </PrimaryButton>
            <p v-if="$page.props.auth.user.store.is_active" class="text-xs text-red-600 mt-2">*No puedes pagar si aún no ha vencido tu plan actual</p>
          </form>

          <p class="text-gray99 text-xs mt-3">
            Tu suscripción se renovará automáticamente cada año por
            ${{ period === 'Mensual' ? calculateTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
              : (calculateTotal * 10).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN.
            Puedes cancelarla cuando quieras desde la página de ajustes de la suscripción.
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
  </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  data() {
    const form = useForm({
        amount: null,
        suscription_period: null,
        activeModules: null,
        // default_card_id: this.$page.props.auth.user.store.default_card_id,
    });

    return {
      form,
      loading: false, // estado de carga de peticion de update modules
      nextPayment: this.$page.props.auth.user.store.next_payment, // proximo pago
      // totalPaid: 199, // El total pagado por los modulos que actualmente tiene.
      // daysForNextPayment: 0, // dias para el proximo pago
      period: this.$page.props.auth.user.store.suscription_period, //Periodo de pago seleccionado
      activated_modules: [],

      modules: [
        {
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.565 -0.565 18 18" height="20" width="20" id="Cash-Payment-Bills--Streamline-Ultimate"><desc>Cash Payment Bills Streamline Icon: https://streamlinehq.com</desc><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M2.2289628083333333 8.990585333333334h1.7572916666666667c0.04804435416666666 -0.0018275833333333332 0.09594109583333332 0.0065371249999999995 0.14051304166666664 0.024531791666666667 0.04457194583333333 0.018064958333333332 0.08479986666666665 0.045338125 0.11802673749999999 0.0801325 0.03322687083333333 0.03472408333333333 0.05869354166666667 0.076125875 0.0747130125 0.121464 0.016019470833333334 0.045338125 0.022226225000000002 0.09355820833333332 0.018205541666666665 0.141497125v6.613742916666667c0.004020683333333334 0.047938916666666664 -0.0021860708333333334 0.096159 -0.018205541666666665 0.141497125 -0.016019470833333334 0.045338125 -0.041486141666666664 0.08673991666666667 -0.0747130125 0.121464 -0.03322687083333333 0.034794375 -0.07345479166666666 0.06206754166666666 -0.11802673749999999 0.0801325 -0.04457194583333333 0.017994666666666666 -0.0924686875 0.026359374999999997 -0.14051304166666664 0.024531791666666667h-1.7572916666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m7.255505833333332 7.43967 -1.350991775 1.682079583333333c-0.08872917083333333 0.11056879166666665 -0.20030313333333333 0.20068270833333332 -0.3270811833333333 0.2640857916666666 -0.12678507916666668 0.063473375 -0.2657727916666667 0.09875979166666667 -0.4074667333333333 0.10353962499999998h-0.8322533333333333" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M4.337712808333333 14.430808875c1.5070533333333334 1.1422395833333332 2.881944275 1.909051375 3.769728025 1.909051375h4.4093962499999995c0.5342166666666667 0 0.8702108333333333 -0.0379575 1.1021733333333332 -0.7345479166666666 0.3541997083333333 -1.7781682916666666 0.5997285 -3.576229125 0.7352508333333333 -5.384341666666667 0 -0.3669225 -0.36762541666666665 -0.7345479166666666 -1.1021733333333332 -0.7345479166666666h-4.169701666666667" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.4185122083333335 8.579097916666667 6.330432354166667 0.9651045833333333c-0.007753170833333333 -0.054391691666666665 -0.0037324874999999993 -0.10981667083333334 0.011787912499999999 -0.1625213625 0.015513370833333331 -0.052704691666666664 0.04216797083333333 -0.10146180333333332 0.07815027499999999 -0.14297887333333334 0.03598933333333333 -0.04151636708333333 0.0804699 -0.0748226675 0.13043321666666666 -0.09766745916666665 0.04997034583333333 -0.022844791666666666 0.1042566 -0.03469456083333333 0.15920359583333332 -0.03474938833333333h5.309797354166666" stroke-width="1.13"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m9.72766375 9.489445291666666 -0.6438716666666666 -6.437999691666666c-0.005271875 -0.049183079166666664 -0.00007029166666666666 -0.09892146249999999 0.015253291666666667 -0.14596064583333332 0.015253291666666667 -0.047039183333333325 0.04034741666666666 -0.0903177625 0.07352508333333332 -0.1270100125 0.03317766666666667 -0.03669225 0.07373595833333332 -0.06596872916666667 0.11900379166666666 -0.08591047499999999 0.045267833333333334 -0.019948775 0.09419083333333333 -0.03011295 0.14367616666666666 -0.02983178333333333h4.850125c0.05110204166666667 -0.0006115375 0.10164175 0.009925183333333334 0.148245125 0.03086507083333333 0.046603374999999995 0.020946916666666666 0.08807545833333333 0.051797929166666666 0.12153429166666666 0.09040211249999999 0.033458833333333333 0.0386112125 0.05813120833333333 0.08404774583333333 0.07218954166666666 0.13315350416666666 0.014128624999999999 0.04909872916666666 0.017362041666666665 0.10068578333333333 0.009489375 0.15116222916666666l-0.9700249999999999 6.466819275" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c-0.14557404166666665 0 -0.26359374999999996 -0.11801267916666668 -0.26359374999999996 -0.26359374999999996s0.11801970833333332 -0.26359374999999996 0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path><path stroke="currentColor" d="M11.735896666666667 6.5869266458333335c0.14564433333333332 0 0.26359374999999996 -0.11801267916666668 0.26359374999999996 -0.26359374999999996s-0.11794941666666667 -0.26359374999999996 -0.26359374999999996 -0.26359374999999996" stroke-width="1.13"></path></svg>',
          name: 'Gastos',
          description: 'Registra y monitorea tus gastos facílmente.',
          cost: 30,
          activated: false,
        },
        {
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>',
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
          icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
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
    PrimaryButton
  },
  props: {},
  methods: {
    checkout() {
      this.form.amount = this.calculateTotalPayment(this.calculateTotal);
      this.form.suscription_period = this.period;
      this.form.activeModules = this.modules.filter(item => item.activated === true);
      this.form.activeModules.unshift({ name: "Módulos básicos", cost: 199 });
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
     calculateTotalPayment(calculateTotal) {
      const total = this.period === 'Mensual' ? (calculateTotal) : (calculateTotal * 10);
      return Math.max(0, total);
    },
    // calculateDiscountForPastDays(calculateTotal) {
    //   if ( calculateTotal > 0 && this.daysForNextPayment > 0 ) {
    //     const totalDiscountForPastDays = (calculateTotal / 30) * (30 - this.daysForNextPayment);
    //     return totalDiscountForPastDays;
    //   } else {
    //     return 0
    //   }
    // },
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
        .reduce((sum, item) => sum + item.cost, 0) + 199;
    }
  },
  mounted() {
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

    const today = new Date();
    const nextPaymentDate = new Date(this.nextPayment);
    this.daysForNextPayment = Math.ceil((nextPaymentDate - today) / (1000 * 60 * 60 * 24));
  }
};
</script>

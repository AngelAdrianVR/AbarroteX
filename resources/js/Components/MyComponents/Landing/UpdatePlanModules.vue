<template>
  <main class="w-full lg:flex lg:space-x-5 lg:divide-x-2 divide-gray-D9">

    <!-- Lado izquierdo -->
    <section class="lg:w-2/3 lg:pr-4">
      <article class="flex space-x-3">
        <!-- Suscripción actual -->
        <div class="border border-grayD9 rounded-[30px] w-1/2 pb-7">
          <header class="font-bold py-2 bg-[#F5E6FF] rounded-t-[30px] border-b border-grayD9 text-center uppercase">
            Suscripción actual</header>
          <div class="p-2">
            <p class="text-center">Tu suscripción actual cuenta con las siguientes funciones</p>
            <h2 class="relative ml-10 mt-4 inline-block">
              <span class="font-bold text-xl text-[#D38CFF] absolute -top-1 -left-5">$</span>
              <span class="text-5xl font-bold">{{ totalPaid?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
              <span class="text-md absolute -top-1 -rigth-5">00</span>
              <span class="text-md absolute bottom-0 -rigth-5"> /{{ period === 'Anual' ? 'año' : 'mes' }}</span>
            </h2>
          </div>
          <h3 class="border-y border-grayD9 py-2 font-bold text-center text-[#D38CFF]">MÓDULOS ESENCIALES</h3>
          <!-- modulos esenciales -->
          <article v-for="item in esencialModules" :key="item"
            class="flex items-center space-x-1 border-b border-grayD9 py-3 px-4">
            <div class="w-3/4">
              <div class="flex items-center space-x-3">
                <span v-html="item.icon"></span>
                <p>{{ item.name }}</p>
              </div>
              <p class="text-gray99 ml-7">{{ item.description }}</p>
            </div>
          </article>
          <!-- Modulos adicionales -->
          <h3 class="border-b border-grayD9 py-2 font-bold text-center text-[#00BD9B]">MÓDULOS ADICIONALES</h3>
          <article v-for="item in aditionalActivatedModules" :key="item"
            class="flex items-center space-x-1 border-b border-grayD9 py-3 px-4">
            <div class="w-3/4">
              <div class="flex items-center space-x-3">
                <span v-html="item.icon"></span>
                <p>{{ item.name }}</p>
              </div>

              <p class="text-gray99 ml-7">{{ item.description }}</p>
            </div>
            <div class="w-1/5">
              <el-switch @change="handleSwitchModule(item)" v-model="item.activated" disabled class="ml-2"
                style="--el-switch-on-color: #00BD9B; --el-switch-off-color: #D4D4D4" />
            </div>
          </article>
        </div>

        <!-- Módulos adicionales -->
        <div class="border border-grayD9 rounded-[30px] w-1/2 pb-7 self-start">
          <header
            class="font-bold py-2 bg-[#CCFFF6] rounded-t-[30px] border-b border-grayD9 text-center text-[#00BD9B] uppercase">
            Adicionales</header>
          <div class="p-2 border-b border-grayD9">
            <p class="text-center">Activa funciones completas para crecer</p>
            <h2 class="relative ml-10 mt-4 inline-block">
              <span class="font-bold text-xl text-[#00BD9B] absolute -top-1 -left-5">$</span>
              <span class="text-5xl font-bold">
                {{ formattedTotalPaymentInteger }}
              </span>
              <span class="text-md absolute -top-1 -rigth-5">{{ formattedTotalPaymentDecimals }}</span>
              <span class="text-md absolute bottom-0 -rigth-5"> /{{ period === 'Anual' ? 'año' : 'mes' }}</span>
            </h2>
          </div>
          <!-- Modulos adicionales -->
          <article v-for="item in aditionalInactiveModules" :key="item"
            class="flex items-center space-x-1 border-b border-grayD9 py-3 px-4">
            <div class="w-3/4">
              <div class="flex items-center space-x-3">
                <span v-html="item.icon"></span>
                <p>{{ item.name }}</p>
              </div>
              <p class="text-gray99 ml-7">{{ item.description }}</p>
            </div>
            <div class="w-1/5">
              <el-switch @change="handleSwitchModule(item)" v-model="item.activated" class="ml-2"
                style="--el-switch-on-color: #00BD9B; --el-switch-off-color: #D4D4D4" />
            </div>
          </article>
        </div>
      </article>
    </section>

    <!-- Lado Derecho -->
    <section class="lg:w-1/3 lg:pl-10 mt-9 lg:mt-0">
      <article class="rounded-xl border border-grayD9 p-4 mt-2 shadow-md">
        <!-- botones de periodo -->
        <div class="flex items-center justify-between space-x-4">
          <!-- boton de mensual -->
          <button :disabled="disableOptionCriterial" @click="period = 'Mensual'"
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
        <div class="mt-2">
          <section class="bg-[#F2F2F2] rounded-lg p-1 !text-sm">
            <div class="flex justify-between">
              <p class="font-bold">**Tu suscripción actual**</p>
              <p class="font-bold text-[#373737]">Pago {{ period }}</p>
            </div>
            <div class="flex justify-between">
              <span class="font-bold"></span>
              <p class="font-bold text-[#373737]">Vence {{ formattedNextPayment }}</p>
            </div>
            <div class="flex justify-between mt-1">
              <p>Módulos esenciales</p>
              <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
                'Mensual' ? '229.00' : '2,290.00' }}</span></p>
            </div>

            <div>
              <p class="text-[#686767] mt-3">Adicionales</p>
              <!-- modulos actuales -->
              <div v-for="item in modules.filter(item => currentActivatedModules.includes(item.name))" :key="item"
                class="flex">
                <p class="w-1/2">{{ item.name }}</p>
                <p class="w-1/2 text-right"><span class="mr-1">$</span><span class="w-20 inline-block">{{ period ===
                  'Mensual' ? item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : (item.cost *
                    10).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
              </div>
            </div>

            <!-- Total -->
            <div class="flex font-bold mt-3">
              <p class="w-1/2">Total</p>
              <p class="w-1/2 text-right">
                <span class="mr-1">$</span>
                <span class="w-20 inline-block">{{ totalPaid.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
              </p>
            </div>
          </section>

          <p class="font-bold mt-2">Detalles del Pago</p>
          <p class="text-[#373737]">Adicionales</p>
          <div v-if="modules.filter(item => item.activated === true).length">
            <!-- modulos adicionales -->
            <div
              v-for="item in modules.filter(item => item.activated === true && !currentActivatedModules.includes(item.name))"
              :key="item" class="flex">
              <p class="w-1/2">{{ item.name }}</p>
              <p class="w-1/2 text-right">
                <span class="mr-1">$</span>
                <span class="w-20 inline-block">
                  {{ period === 'Mensual'
                    ? (item.cost * adjustmentFactor).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    : (item.cost * 10 * adjustmentFactor).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                </span>
              </p>
            </div>
          </div>

          <div class="flex items-center justify-center space-x-2 mt-2" v-else>
            <i class="fa-solid fa-arrow-left"></i>
            <p>Activa funciones adicionales</p>
          </div>

          <!-- Cupon de descuento -->
          <button @click="showDiscountModal = true"
            :disabled="!modules.filter(item => item.activated === true && !currentActivatedModules.includes(item.name)).length || verifiedTicket"
            class="text-primary flex items-center space-x-2 mt-3 disabled:text-gray-400 disabled:cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
            </svg>
            <span>Agregar código promocional</span>
          </button>

          <!-- Total -->
          <div class="flex font-bold my-3">
            <div class="flex items-center space-x-2 w-1/2">
              <span>Total a pagar</span>
              <el-tooltip v-if="verifiedTicket" placement="right">
                <template #content>
                  <div>
                    <p class="text-cyan-500">Cupón de descuento utilizado</p>
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

          <small v-if="this.calculateTotalPayment(this.calculateTotal) < 10">*Si el costo total es menor a $10.00 no se paga por la modificación</small>
          <!-- <form @submit.prevent="checkout" class="text-center mt-8"> -->
            <PrimaryButton @click="this.calculateTotalPayment(this.calculateTotal) > 10 ? checkout() : showUpdateModulesConfirmModal = true"
              :disabled="loading || this.calculateTotalPayment(this.calculateTotal) == 0"
              class="!px-28">
              <i v-if="loading" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              {{ this.calculateTotalPayment(this.calculateTotal) > 10 ? 'Confirmar y pagar' : 'Confirmar modificación' }}
            </PrimaryButton>
          <!-- </form> -->

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

    <!-- Modal de confirmación de modificación de módulos -->
    <ConfirmationModal :show="showUpdateModulesConfirmModal" @close="showUpdateModulesConfirmModal = false">
      <template #title> Atención </template>
      <template #content>Vas a agregar nuevos módulos a tu plan actual. ¿Deseas continuar?</template>
      <!-- <template #content>Si estas quitando módulos de tu plan actual y quieres volverlos a agregar tendrás que pagar. ¿Deseas continuar?</template> -->
      <template #footer>
        <div class="space-x-2">
          <CancelButton @click="showUpdateModulesConfirmModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="updateStoreModules()">Confirmar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
  data() {
    const form = useForm({
      amount: null, //monto total a pagar
      suscription_period: null, //periodo de suscripción (anual o mensual) 
      remainingPlanDays: null, //días restantes para que expire el plan actual
      modulesUpdated: [], //módulos adicionales agregados al plan actual (para desglose de pago)
      activeModules: [], //modulos activos en la tienda (todos, para guardarlo en la base de datos)
      discountTicketUsed: null, //cupón de descuento utilizado
      // default_card_id: this.$page.props.auth.user.store.default_card_id,
    });

    return {
      form,
      loading: false, // estado de carga de peticion de update modules
      showUpdateModulesConfirmModal: false, //modal de confirmacion para modificar modulos al plab actual
      showDiscountModal: false, 
      showApliedDiscountTicket: false,
      discountTickets: null, //cupones de descuento activos
      ticketCode: null, //codigo del ticket ingresado
      verifiedTicket: null, //codigo del ticket correctamente verificado
      ticketCodeError: false, //codigo del ticket no encontrado, bandera de error
      nextPayment: this.$page.props.auth.user.store.next_payment, // proximo pago
      totalPaid: 229, // El total pagado por los modulos que actualmente tiene.
      period: this.$page.props.auth.user.store.suscription_period, //Periodo de pago seleccionado
      remainingPlanDays: 0, //dias restantes para que expire el plan pagado
      activated_modules: [],
      currentActivatedModules: [],

      esencialModules: [
        {
          icon: '<svg width="16" height="16" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.63921 10.97C1.24897 9.26852 1.06889 7.32701 1.00146 6.0828C0.971063 5.52179 1.41799 5.06641 1.97323 5.06641H12.0268C12.582 5.06641 13.0289 5.52179 12.9985 6.0828C12.9311 7.32701 12.751 9.26852 12.3607 10.97C12.18 11.7584 11.6063 12.3588 10.8147 12.475C9.99905 12.5948 8.76041 12.6963 7 12.6963C5.23958 12.6963 4.00095 12.5948 3.18527 12.475C2.39365 12.3588 1.82004 11.7584 1.63921 10.97Z" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.97656 5.06927L5.48995 1" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.0212 5.06927L8.50781 1" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.5 5.69922C5.99103 5.69922 8.50897 5.69922 13 5.69922M1.5 6.69922C5.99103 6.69922 8.50897 6.69922 13 6.69922M1.5 7.69922C5.79577 7.69922 8.20423 7.69922 12.5 7.69922M1.5 8.69922C5.79577 8.69922 8.20423 8.69922 12.5 8.69922M1.5 9.69922C5.79577 9.69922 8.20423 9.69922 12.5 9.69922M1.5 10.6992C5.60051 10.6992 7.89949 10.6992 12 10.6992M2 11.6992H12" stroke="#D38CFF"/></svg>',
          name: 'Punto de venta',
          description: 'Registra ventas y genera tickets de manera rápida y sencilla.',
        },
        {
          icon: '<svg width="16" height="17" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.46875 6.85834L12.5012 1.78125" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.4609 1L12.5235 1.78632L11.7561 3.87346" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.9999 13.6127H10.6016V6.81741C10.6016 6.68868 10.6521 6.56522 10.742 6.4742C10.832 6.38317 10.954 6.33203 11.0812 6.33203H12.5203C12.6475 6.33203 12.7695 6.38317 12.8595 6.4742C12.9494 6.56522 12.9999 6.68868 12.9999 6.81741V13.6127Z" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.17962 13.6254H5.78125V8.28616C5.78125 8.15743 5.83179 8.03397 5.92175 7.94295C6.01169 7.85192 6.1337 7.80078 6.26092 7.80078H7.69994C7.82716 7.80078 7.94917 7.85192 8.03912 7.94295C8.12908 8.03397 8.17962 8.15743 8.17962 8.28616V13.6254Z" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.39837 13.6145H1V9.73147C1 9.60274 1.05054 9.47928 1.14049 9.38826C1.23045 9.29723 1.35245 9.24609 1.47967 9.24609H2.91869C3.04591 9.24609 3.16792 9.29723 3.25787 9.38826C3.34783 9.47928 3.39837 9.60274 3.39837 9.73147V13.6145Z" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/><path d="M1.5 9.40234V13.4023H2V9.40234H2.5C2.5 10.9644 2.5 11.8402 2.5 13.4023M6.5 7.90234V13.4023H7V7.90234H7.5C7.5 10.0502 7.5 11.2545 7.5 13.4023M11.5 6.40234V13.4023H12C12 10.6687 12 9.13601 12 6.40234" stroke="#D38CFF"/></svg>',
          name: 'Reportes',
          description: 'Consulta y analiza las ventas, gastos y desempeño del negocio.',
        },
        {
          icon: '<svg width="14" height="16" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 12.3633V1.82598C1 1.24058 1.23572 1.01428 1.80994 1H7.23095L11 4.6648V12.3633C11 12.8124 10.6791 13.1572 10.3344 13.1572H1.6255C1.10413 13.1572 1 12.7081 1 12.3633Z" stroke="#D38CFF"/><path d="M1.5 1V13H2.5V1H3.5V13H4.5V1H5.5V13H6.5C6.5 8.31371 6.5 5.68629 6.5 1M7.5 13C7.5 8.50897 7.5 5.99103 7.5 1.5M8.5 13C8.5 8.70423 8.5 6.29577 8.5 2M9.5 13C9.5 9.09476 9.5 6.90524 9.5 3M10.5 13V4" stroke="#D38CFF"/><path d="M2.86719 6.01844L3.62902 6.75621C4.22404 6.07037 4.55765 5.68584 5.15267 5M2.86719 9.33841L3.62902 10.012C4.22404 9.32305 4.55765 8.93677 5.15267 8.24779M6.48387 6.21893C7.37014 6.21893 7.86704 6.21893 8.75331 6.21893M6.48387 9.50682C7.37014 9.50682 7.86704 9.50682 8.75331 9.50682" stroke="white"/></svg>',
          name: 'Registro de ventas',
          description: 'Organiza cada transacción para un mejor control y seguimiento.',
        },
        {
          icon: '<svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 11.1184H10.9294C11.1532 11.1188 11.3696 11.032 11.5394 10.8739C11.7091 10.7158 11.8208 10.4968 11.854 10.257L13.0139 1.86141C13.0471 1.62185 13.1585 1.40316 13.3278 1.24509C13.4971 1.08702 13.7132 1.00005 13.9366 1H15" stroke="#D38CFF" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.39635 5.04688H5.19635C5.19635 5.04688 5.66302 5.04687 5.66302 5.55279V8.5883C5.66302 8.5883 5.66302 9.09422 5.19635 9.09422H2.39635C2.39635 9.09422 1.92969 9.09422 1.92969 8.5883V5.55279C1.92969 5.55279 1.92969 5.04688 2.39635 5.04688Z" stroke="#D38CFF" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.5 5.25781V8.75781H3.5V5.25781H4.5V8.75781H5V5.25781" stroke="#D38CFF"/><path d="M6.13073 3.02344H9.86407C9.86407 3.02344 10.3307 3.02344 10.3307 3.52936V8.58854C10.3307 8.58854 10.3307 9.09446 9.86407 9.09446H6.13073C6.13073 9.09446 5.66406 9.09446 5.66406 8.58854V3.52936C5.66406 3.52936 5.66406 3.02344 6.13073 3.02344Z" stroke="#D38CFF" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.5 3.25781V8.75781H7.5V3.25781H8.5V8.75781H10V3.75781V3.25781H9.5H9V8.25781" stroke="#D38CFF"/><path d="M1.92969 13.9015C1.92969 14.0676 1.95987 14.232 2.0185 14.3855C2.07712 14.539 2.16306 14.6784 2.2714 14.7958C2.37973 14.9133 2.50834 15.0064 2.64989 15.0701C2.79144 15.1336 2.94314 15.1663 3.09635 15.1663C3.24956 15.1663 3.40127 15.1336 3.54282 15.0701C3.68437 15.0064 3.81298 14.9133 3.92131 14.7958C4.02965 14.6784 4.11558 14.539 4.17422 14.3855C4.23284 14.232 4.26302 14.0676 4.26302 13.9015C4.26302 13.5661 4.14011 13.2444 3.92131 13.0072C3.70252 12.7699 3.40577 12.6367 3.09635 12.6367C2.78693 12.6367 2.49019 12.7699 2.2714 13.0072C2.05261 13.2444 1.92969 13.5661 1.92969 13.9015Z" stroke="#D38CFF" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.46875 13.9015C8.46875 14.237 8.59164 14.5587 8.81047 14.7958C9.02925 15.0331 9.32599 15.1663 9.63542 15.1663C9.94485 15.1663 10.2416 15.0331 10.4604 14.7958C10.6791 14.5587 10.8021 14.237 10.8021 13.9015C10.8021 13.5661 10.6791 13.2444 10.4604 13.0072C10.2416 12.7699 9.94485 12.6367 9.63542 12.6367C9.32599 12.6367 9.02925 12.7699 8.81047 13.0072C8.59164 13.2444 8.46875 13.5661 8.46875 13.9015Z" stroke="#D38CFF" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 13.2578C9.39052 13.8436 9.60948 14.172 10 14.7578L10.5 14.2578L10 13.5078M9.5 12.7578L10 13.5078M10 13.5078L9 14.7578M2 13.7578L3 14.7578C3.39052 14.5626 3.60948 14.4531 4 14.2578M2.5 13.2578L3 14.2578L3.5 13.2578L4 13.7578L3 12.7578" stroke="#D38CFF"/></svg>',
          name: 'Productos',
          description: 'Administra tu catálogo, incluyendo precios, detalles y stock disponible.',
        },
        {
          icon: '<svg width="16" height="16" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_16042_213" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="14" height="14"><rect width="14" height="14" fill="#D9D9D9"/></mask><g mask="url(#mask0_16042_213)"><path d="M3.41771 6.90275L3.24271 6.01733C3.12604 5.96814 3.01667 5.9165 2.91458 5.86239C2.8125 5.80828 2.70799 5.74187 2.60104 5.66317L1.75521 5.92879L1.17188 4.92533L1.84271 4.33505C1.82326 4.20716 1.81354 4.07927 1.81354 3.95137C1.81354 3.82348 1.82326 3.69559 1.84271 3.56769L1.17188 2.97742L1.75521 1.97395L2.60104 2.23958C2.70799 2.16087 2.8125 2.09447 2.91458 2.04036C3.01667 1.98625 3.12604 1.9346 3.24271 1.88541L3.41771 1H4.58437L4.75937 1.88541C4.87604 1.9346 4.98542 1.98625 5.0875 2.04036C5.18958 2.09447 5.2941 2.16087 5.40104 2.23958L6.24687 1.97395L6.83021 2.97742L6.15937 3.56769C6.17882 3.69559 6.18854 3.82348 6.18854 3.95137C6.18854 4.07927 6.17882 4.20716 6.15937 4.33505L6.83021 4.92533L6.24687 5.92879L5.40104 5.66317C5.2941 5.74187 5.18958 5.80828 5.0875 5.86239C4.98542 5.9165 4.87604 5.96814 4.75937 6.01733L4.58437 6.90275H3.41771ZM4.00104 5.13192C4.32187 5.13192 4.59653 5.01633 4.825 4.78514C5.05347 4.55394 5.16771 4.27602 5.16771 3.95137C5.16771 3.62672 5.05347 3.3488 4.825 3.11761C4.59653 2.88642 4.32187 2.77082 4.00104 2.77082C3.68021 2.77082 3.40556 2.88642 3.17708 3.11761C2.94861 3.3488 2.83438 3.62672 2.83438 3.95137C2.83438 4.27602 2.94861 4.55394 3.17708 4.78514C3.40556 5.01633 3.68021 5.13192 4.00104 5.13192ZM8.63854 13.3958L8.37604 12.1562C8.21076 12.0972 8.05764 12.0258 7.91667 11.9422C7.77569 11.8586 7.63715 11.7627 7.50104 11.6545L6.33437 12.0381L5.51771 10.6215L6.45104 9.79509C6.4316 9.61801 6.42188 9.44093 6.42188 9.26384C6.42188 9.08676 6.4316 8.90968 6.45104 8.7326L5.51771 7.90621L6.33437 6.48955L7.50104 6.87323C7.63715 6.76502 7.77569 6.6691 7.91667 6.58547C8.05764 6.50185 8.21076 6.43053 8.37604 6.3715L8.63854 5.13192H10.2719L10.5344 6.3715C10.6997 6.43053 10.8528 6.50185 10.9937 6.58547C11.1347 6.6691 11.2733 6.76502 11.4094 6.87323L12.576 6.48955L13.3927 7.90621L12.4594 8.7326C12.4788 8.90968 12.4885 9.08676 12.4885 9.26384C12.4885 9.44093 12.4788 9.61801 12.4594 9.79509L13.3927 10.6215L12.576 12.0381L11.4094 11.6545C11.2733 11.7627 11.1347 11.8586 10.9937 11.9422C10.8528 12.0258 10.6997 12.0972 10.5344 12.1562L10.2719 13.3958H8.63854ZM9.45521 11.0347C9.94132 11.0347 10.3545 10.8625 10.6948 10.5182C11.0351 10.1739 11.2052 9.75574 11.2052 9.26384C11.2052 8.77195 11.0351 8.35384 10.6948 8.00951C10.3545 7.66518 9.94132 7.49302 9.45521 7.49302C8.9691 7.49302 8.5559 7.66518 8.21562 8.00951C7.87535 8.35384 7.70521 8.77195 7.70521 9.26384C7.70521 9.75574 7.87535 10.1739 8.21562 10.5182C8.5559 10.8625 8.9691 11.0347 9.45521 11.0347Z" fill="#D38CFF"/></g></svg>',
          name: 'Configuraciones',
          description: 'Personaliza las opciones del sistema según las necesidades de tu negocio.',
        },
        {
          icon: '<svg width="17" height="15" viewBox="0 0 15 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 7.81953H1.73755M6.42733 9.84333C6.28194 9.84336 6.13805 9.822 6 9.78052M6.42733 9.84333V12.3731M6.42733 9.84333H7M11.4273 7.81953C11.3187 7.81951 11.2108 7.83143 11.1057 7.85478M11.4273 7.81953V12.3731M11.4273 7.81953H12.7137M14 7.81953H13.3568M2.5 12.3731C2.10218 12.3731 1.72064 12.2132 1.43934 11.9285C1.15804 11.6439 1 11.2578 1 10.8552V8.04755C1.00015 7.89638 1.02262 7.74609 1.06667 7.60164L2.674 2.31345C2.76836 2.0033 2.95847 1.73191 3.21643 1.53911C3.47439 1.34631 3.78663 1.24225 4.10733 1.24219H10.8933C11.214 1.24225 11.5263 1.34631 11.7842 1.53911C12.0422 1.73191 12.2323 2.0033 12.3267 2.31345L13.9333 7.60164C13.9773 7.746 14 7.89644 14 8.04755V10.8552C14 11.2578 13.842 11.6439 13.5607 11.9285C13.4975 11.9925 13.4292 12.0501 13.3568 12.1011M2.5 12.3731L1.73755 11.9285V7.81953M2.5 12.3731V7.81953M2.5 12.3731H3M12.5 12.3731H11.9637M12.5 12.3731L12.1488 7.85478L11.9637 12.3731M12.5 12.3731C12.672 12.3731 12.8409 12.3432 13 12.2863M1.73755 7.81953H2.5M2.5 7.81953H2.65544H3.21643M3 12.3731L3.21643 7.81953M3 12.3731H3.89433M3.21643 7.81953H3.57333C3.68177 7.81956 3.78937 7.83148 3.89433 7.85478M3.89433 7.85478C4.05897 7.89132 4.21711 7.95585 4.36178 8.04637C4.47164 8.1151 4.57172 8.19756 4.65989 8.29137M3.89433 7.85478V12.3731M3.89433 12.3731H4.5M4.5 12.3731L4.65989 8.29137M4.5 12.3731H5.08533M4.65989 8.29137C4.76185 8.39986 4.84788 8.52353 4.91467 8.65873L5.08533 9.00413C5.16068 9.15666 5.26052 9.29452 5.3801 9.41246M5.3801 9.41246C5.45824 9.48953 5.54482 9.55809 5.6385 9.61667C5.75186 9.68756 5.87347 9.74249 6 9.78052M5.3801 9.41246L5.08533 12.3731M5.08533 12.3731H5.6385M5.6385 12.3731L6 9.78052M5.6385 12.3731H6H6.42733M6.42733 12.3731H7M7 12.3731V9.84333M7 12.3731H7.5M7 9.84333H7.5M7.5 12.3731V9.84333M7.5 12.3731H8M7.5 9.84333H8.03633M8 12.3731L8.03633 9.84333M8 12.3731H9M8.03633 9.84333H8.57267C8.66711 9.84335 8.76092 9.83434 8.85291 9.81666M9 12.3731L8.85291 9.81666M9 12.3731H9.91467M8.85291 9.81666C9.03233 9.78217 9.2048 9.71466 9.3615 9.61667C9.543 9.50317 9.69784 9.35221 9.81636 9.17447M9.91467 12.3731L9.81636 9.17447M9.91467 12.3731H10.5M9.81636 9.17447C9.85262 9.1201 9.88548 9.06321 9.91467 9.00413L10.0853 8.65873C10.1846 8.45771 10.3265 8.28215 10.5 8.14412M10.5 12.3731V8.14412M10.5 12.3731H10.8933M10.5 8.14412C10.5442 8.10898 10.5904 8.07627 10.6385 8.04619C10.7831 7.95577 10.9411 7.8913 11.1057 7.85478M10.8933 12.3731L11.1057 7.85478M10.8933 12.3731H11.4273M11.4273 12.3731H11.9637M12.7137 7.81953L13 12.2863M12.7137 7.81953H13.3568M13 12.2863C13.1263 12.2411 13.2463 12.1789 13.3568 12.1011M13.3568 7.81953V12.1011" stroke="#D38CFF" stroke-linecap="round" stroke-linejoin="round"/></svg>',
          name: 'Caja',
          description: 'Controla ingresos, ventas y retiros al instante.',
        },
      ],

      modules: [
        {
          icon: '<svg width="16" height="20" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 9.56275H2.75729C2.80534 9.5609 2.85323 9.56936 2.8978 9.58757C2.94238 9.60585 2.9826 9.63345 3.01583 9.66866C3.04906 9.7038 3.07452 9.74569 3.09054 9.79157C3.10656 9.83745 3.11277 9.88624 3.10875 9.93475V16.6272C3.11277 16.6757 3.10656 16.7245 3.09054 16.7704C3.07452 16.8162 3.04906 16.8581 3.01583 16.8933C2.9826 16.9285 2.94238 16.9561 2.8978 16.9744C2.85323 16.9926 2.80534 17.001 2.75729 16.9992H1" stroke="#00BD9B" stroke-width="1.13" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.02717 7.99609L4.67618 9.69819C4.58745 9.81007 4.47587 9.90126 4.3491 9.96542C4.22231 10.0296 4.08332 10.0654 3.94163 10.0702H3.10938" stroke="#00BD9B" stroke-width="1.13" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 10.0078V15.5078L5 16.0078V9.50781L6 9.00781C6 11.9367 6 13.5789 6 16.5078M12.5 10.5078L11.5 17.0078H10.5L11.5 10.5078H10.5L9.5 17.0078H8.5L9.5 10.5078H8.5L7.5 17.0078H7L8 10.5078L7.57061 10.0784M7.57061 10.0784L7.5 10.0078L6.5 17.0078V9.00781L7.57061 10.0784Z" stroke="#00BD9B"/><path d="M3.10938 15.0696C4.61643 16.2255 5.99132 17.0014 6.8791 17.0014H11.2885C11.8227 17.0014 12.1587 16.963 12.3907 16.2581C12.7449 14.4588 12.9904 12.6393 13.1259 10.8097C13.1259 10.4384 12.7583 10.0664 12.0238 10.0664H7.85405" stroke="#00BD9B" stroke-width="1.13" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.18567 9.14772L5.09759 1.44313C5.08984 1.38809 5.09386 1.332 5.10938 1.27867C5.12489 1.22534 5.15154 1.176 5.18753 1.13399C5.22352 1.09198 5.268 1.05828 5.31796 1.03516C5.36793 1.01205 5.42222 1.00006 5.47716 1H10.787" stroke="#00BD9B" stroke-width="1.13" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.50524 10.072L7.86137 3.55743C7.8561 3.50766 7.8613 3.45733 7.87662 3.40973C7.89188 3.36213 7.91697 3.31834 7.95015 3.28121C7.98333 3.24408 8.02389 3.21445 8.06915 3.19427C8.11442 3.17409 8.16334 3.1638 8.21283 3.16409H13.063C13.1141 3.16347 13.1646 3.17413 13.2112 3.19532C13.2578 3.21652 13.2993 3.24773 13.3327 3.2868C13.3662 3.32587 13.3909 3.37185 13.4049 3.42154C13.4191 3.47122 13.4223 3.52342 13.4144 3.5745L12.4444 10.1183" stroke="#00BD9B" stroke-width="1.13" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.5136 7.13112C10.368 7.13112 10.25 7.0117 10.25 6.86439C10.25 6.71707 10.368 6.59766 10.5136 6.59766" stroke="#00BD9B" stroke-width="1.13"/><path d="M10.5078 7.13112C10.6535 7.13112 10.7714 7.0117 10.7714 6.86439C10.7714 6.71707 10.6535 6.59766 10.5078 6.59766" stroke="#00BD9B" stroke-width="1.13"/></svg>',
          name: 'Gastos',
          description: 'Registra y monitorea tus gastos facílmente.',
          cost: 30,
          activated: false,
        },
        {
          icon: '<svg width="15" height="24" viewBox="0 0 15 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.71429 1H3.35714C2.73199 1 2.13244 1.24834 1.69039 1.69039C1.24834 2.13244 1 2.73199 1 3.35714V20.6429C1 21.268 1.24834 21.8676 1.69039 22.3096C2.13244 22.7517 2.73199 23 3.35714 23H11.2143C11.8394 23 12.439 22.7517 12.881 22.3096C13.3231 21.8676 13.5714 21.268 13.5714 20.6429V3.35714C13.5714 2.73199 13.3231 2.13244 12.881 1.69039C12.439 1.24834 11.8394 1 11.2143 1H8.85714M5.71429 1V2.57143H8.85714V1M5.71429 1H8.85714M5.71429 20.6429H8.85714" stroke="#00BD9B" stroke-width="1.57143" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.61072 13.7576H5.61113C5.40769 13.7457 5.35584 13.7009 5.27074 13.5307L3.99428 9.7297H3.08657C2.57459 9.7297 2.57601 8.99219 3.08657 8.99219H4.33467C4.64669 8.99219 4.76016 9.7297 4.90198 9.7297H10.4333C10.7454 9.7297 10.8811 10.0209 10.7737 10.3254L10.0078 12.1692C9.89438 12.3961 9.80928 12.4812 9.46889 12.4812H5.72459L5.92316 12.9918H9.61072C10.1497 12.9918 10.1213 13.7576 9.61072 13.7576Z" fill="#00BD9B" stroke="#00BD9B" stroke-width="0.0567317" stroke-linecap="round"/><path d="M6.72314 14.3535C6.72314 14.6396 6.49119 14.8716 6.20506 14.8716C5.91893 14.8716 5.68698 14.6396 5.68698 14.3535C5.68698 14.0674 5.91893 13.8354 6.20506 13.8354C6.49119 13.8354 6.72314 14.0674 6.72314 14.3535ZM9.62158 14.3535C9.62158 14.6396 9.38962 14.8716 9.1035 14.8716C8.81737 14.8716 8.58541 14.6396 8.58541 14.3535C8.58541 14.0674 8.81737 13.8354 9.1035 13.8354C9.38962 13.8354 9.62158 14.0674 9.62158 14.3535Z" fill="#00BD9B" stroke="#00BD9B" stroke-width="0.155203"/></svg>',
          name: 'Tienda en línea',
          description: 'Gestiona tus ventas y pedidos en línea.',
          cost: 120,
          activated: false,
        },
        {
          icon: '<svg width="17" height="17" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.15417 13.0899C0.623266 11.143 1.52393 9.57308 2.93104 8.74799M1.15417 13.0899L2.93104 8.74799M1.15417 13.0899H2.65322M7.63188 12.4082L9.52692 14C11.1847 12.0462 12.1141 10.9508 13.7718 8.99708M5.20622 6.15452C4.96134 6.15452 4.72443 6.12036 4.5 6.05656M5.20622 6.15452C6.3305 6.15452 7.28664 5.43463 7.63883 4.43066M5.20622 6.15452L7.63883 4.43066M5.20622 1C4.02631 1 3.03159 1.7929 2.72582 2.87493M5.20622 1L2.72582 2.87493M5.20622 1C5.54918 1 5.87648 1.06699 6.17578 1.18859M2.93104 8.74799C3.26319 8.55322 3.62357 8.39996 4 8.29303M4 8.29303L2 13L2.65322 13.0899M4 8.29303C4.27301 8.21548 4.55447 8.1623 4.83973 8.13535M2.65322 13.0899L4.83973 8.13535M2.65322 13.0899H3.37431M4.83973 8.13535C5.1512 8.10591 5.46721 8.10774 5.7817 8.14324M3.37431 13.0899L5.7817 8.14324M3.37431 13.0899H3.93378M5.7817 8.14324C6.03185 8.17147 6.28103 8.221 6.52622 8.29303M6.52622 8.29303L3.93378 13.0899M6.52622 8.29303C6.74155 8.35629 6.9538 8.43691 7.1609 8.53571M3.93378 13.0899H4.59062M4.59062 13.0899H5.24746C5.24746 12.2426 5.88721 11.136 6.66019 10.396M4.59062 13.0899L7.1609 8.53571M7.1609 8.53571C7.34126 8.62175 7.51773 8.72157 7.68893 8.83573M7.68893 8.83573C8.03782 9.06837 8.36487 9.36052 8.65854 9.71674C8.11742 9.38061 7.33764 9.7475 6.66019 10.396M7.68893 8.83573L6.66019 10.396M2.72582 2.87493C2.66272 3.0982 2.62896 3.33379 2.62896 3.57726C2.62896 3.62051 2.63003 3.6635 2.63213 3.70623M6.17578 1.18859L2.63213 3.70623M6.17578 1.18859C6.40309 1.28095 6.61424 1.40481 6.80382 1.55476M2.63213 3.70623C2.64459 3.95899 2.69346 4.20218 2.77361 4.43066M6.80382 1.55476L2.77361 4.43066M6.80382 1.55476C6.96815 1.68475 7.11627 1.83434 7.24464 2M2.77361 4.43066C2.90565 4.80704 3.12256 5.14349 3.4014 5.41706M7.24464 2C5.58701 3.17157 4.65764 3.82843 3 5M7.24464 2C7.36379 2.15376 7.46593 2.32137 7.54823 2.5M3.4014 5.41706L7.54823 2.5M3.4014 5.41706C3.54215 5.55515 3.69868 5.67722 3.86803 5.78031M7.54823 2.5C7.63663 2.69186 7.70214 2.89644 7.74128 3.11024M7.74128 3.11024C7.769 3.2617 7.78348 3.41779 7.78348 3.57726C7.78348 3.72124 7.77168 3.86245 7.74898 4M7.74128 3.11024L3.86803 5.78031M3.86803 5.78031C4.06317 5.8991 4.27533 5.99269 4.5 6.05656M4.5 6.05656L7.74898 4M7.74898 4C7.72453 4.1482 7.68743 4.29213 7.63883 4.43066" stroke="#00BD9B" stroke-width="0.851455"/></svg>',
          name: 'Clientes',
          description: 'Gestiona tus clientes y sus compras.',
          cost: 80,
          activated: false,
        },
        {
          icon: '<svg width="16" height="14" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.72727 9.72727C1 9.72727 0.999995 9.36363 1 8.63636V1.72727C1 1.36364 1 1 1.72727 1M1.72727 9.72727V1M1.72727 9.72727C1.90647 9.72727 2.08094 9.72727 2.25124 9.72727M1.72727 1C1.72727 1 1.93741 1 2.25124 1M5 1C4.97588 1 4.78219 1 4.5 1M5 1V9.72727M5 1C5.06289 1 5.32199 1.40998 5.59688 1.72727M6.09091 2.09091C5.9502 2.09091 5.77039 1.92756 5.59688 1.72727M6.09091 2.09091V9.72727M6.09091 2.09091C6.16182 2.09091 6.39866 2.09091 6.73402 2.09091M11.1818 2.09091C11.5455 2.09091 11.9091 2.45455 11.9091 2.81818C11.9091 3.67023 11.9091 4.14795 11.9091 5C11.6502 5 11.4076 5.03464 11.1818 5.09863M11.1818 2.09091C11.1818 2.09091 10.9778 2.09091 10.6514 2.09091M11.1818 2.09091V5.09863M10.4545 9.9863L10.8275 10.2659C11.0759 10.4524 11.4016 10.5456 11.7273 10.5456M11.7273 10.5456C12.0529 10.5455 12.3785 10.4523 12.6271 10.2659C13.1243 9.89297 13.1243 9.28885 12.6271 8.91594C12.3789 8.72927 12.0531 8.63636 11.7273 8.63636C11.4197 8.63636 11.1121 8.54303 10.8775 8.35679C10.4083 7.98388 10.4083 7.37976 10.8775 7.00685C11.1121 6.82039 11.4197 6.72717 11.7273 6.72717M11.7273 10.5456V11.1818M11.7273 6.72717C12.0348 6.72717 12.3424 6.82039 12.577 7.00685M11.7273 6.72717V6.09091M2.25124 9.72727V1M2.25124 9.72727C2.37306 9.72727 2.49274 9.72727 2.61051 9.72727M2.25124 1C2.38633 1 2.54064 1 2.70567 1M2.70567 1L2.61051 9.72727M2.70567 1C2.86231 1 3.02862 1 3.19732 1M2.61051 9.72727C2.83929 9.72727 3.06081 9.72727 3.27661 9.72727M3.27661 9.72727L3.19732 1M3.27661 9.72727C3.47674 9.72727 3.67193 9.72727 3.86342 9.72727M3.19732 1C3.42077 1 3.64843 1 3.86342 1M3.86342 1V9.72727M3.86342 1C4.09801 1 4.31751 1 4.5 1M3.86342 9.72727C4.07976 9.72727 4.29137 9.72727 4.5 9.72727M4.5 9.72727V1M4.5 9.72727C4.6683 9.72727 4.83466 9.72727 5 9.72727M5 9.72727C5.20363 9.72727 5.40572 9.72727 5.60798 9.72727M5.60798 9.72727L5.59688 1.72727M5.60798 9.72727C5.76856 9.72727 5.92925 9.72727 6.09091 9.72727M6.09091 9.72727C6.30296 9.72727 6.51668 9.72727 6.73402 9.72727M6.73402 9.72727V2.09091M6.73402 9.72727C6.93653 9.72727 7.14219 9.72727 7.35255 9.72727M6.73402 2.09091C6.91503 2.09091 7.12474 2.09091 7.35255 2.09091M7.35255 2.09091V9.72727M7.35255 2.09091C7.52841 2.09091 7.71507 2.09091 7.90764 2.09091M7.35255 9.72727C7.53375 9.72727 7.71844 9.72727 7.90764 9.72727M7.90764 9.72727V2.09091M7.90764 9.72727C8.10008 9.72727 8.29719 9.72727 8.5 9.72727M7.90764 2.09091C8.10078 2.09091 8.29988 2.09091 8.5 2.09091M8.5 2.09091V9.72727M8.5 2.09091C8.6669 2.09091 8.83452 2.09091 9 2.09091M8.5 9.72727C8.77638 9.72727 9.06337 9.72727 9.36364 9.72727C8.67037 8.34074 9.17798 6.1341 10.6514 5.31744M9 2.09091C9 4.20329 9 5.38762 9 7.5M9 2.09091C9.22933 2.09091 9.45455 2.09091 9.66806 2.09091M9.66806 2.09091C9.66806 3.6175 9.66806 4.47341 9.66806 6M9.66806 2.09091C9.88968 2.09091 10.0987 2.09091 10.2866 2.09091M10.2866 2.09091C10.2866 3.38248 10.2866 4.10661 10.2866 5.39819M10.2866 2.09091C10.4198 2.09091 10.5424 2.09091 10.6514 2.09091M10.6514 2.09091C10.6514 3.35095 10.6514 4.0574 10.6514 5.31744M10.6514 5.31744C10.8162 5.22607 10.9931 5.1521 11.1818 5.09863" stroke="#00BD9B" stroke-width="0.727273"/></svg>',
          name: 'Cotizaciones',
          description: 'Crea cotizaciones precisas en segundos.',
          cost: 80,
          activated: false,
        },
        {
          icon: '<svg width="16" height="16" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.32656 1.67969H2.715M12.4016 1.67969H11.7008M12.4016 1.67969L11.7008 13.2106V1.67969M12.4016 1.67969C12.5117 1.67969 12.6196 1.70173 12.7194 1.74336M6.62656 1.67969H7.06328M1.5 1.6988C1.55755 1.68619 1.61672 1.67969 1.67656 1.67969H2.10343M1.5 1.6988C1.34711 1.7323 1.2056 1.80892 1.0932 1.92132C0.938482 2.07604 0.851562 2.28589 0.851562 2.50469V12.4047C0.851562 12.6235 0.938482 12.8333 1.0932 12.9881C1.2056 13.1005 1.34711 13.1771 1.5 13.2106M1.5 1.6988V13.2106M1.5 13.2106C1.55755 13.2232 1.61672 13.2297 1.67656 13.2297H2.10343M2.10343 13.2297V1.67969M2.10343 13.2297H2.715M2.10343 1.67969H2.715M2.715 1.67969V13.2297M2.715 13.2297H3.28397M3.28397 13.2297H3.85293H4M3.28397 13.2297C3.28397 10.7677 3.28397 9.38732 3.28397 6.92531M4 13.2297V6.5L4.5 6M4 13.2297H4.5M5 13.2297V5.5L4.5 6M5 13.2297H4.5M5 13.2297H5.66042M4.5 13.2297C4.5 10.4063 4.5 8.82336 4.5 6M5.66042 13.2297C5.66042 10.4359 5.66042 8.86955 5.66042 6.07578M5.66042 13.2297H6.35689M6.35689 13.2297C6.35689 10.6514 6.35689 9.20594 6.35689 6.62771M6.35689 13.2297H7.06328M7.06328 1.67969H7.5H7.83945M7.06328 1.67969V13.2297M7.06328 13.2297H7.83945M7.83945 1.67969V13.2297M7.83945 1.67969H8.6434M7.83945 13.2297H8.6434M8.6434 1.67969V13.2297M8.6434 1.67969H9.5M8.6434 13.2297H9.37641M9.5 1.67969L9.37641 13.2297M9.5 1.67969H10.3222M9.37641 13.2297H10.1567M10.3222 1.67969L10.1567 13.2297M10.3222 1.67969H11M10.1567 13.2297H11M11 1.67969V13.2297M11 1.67969H11.7008M11 13.2297H12.4016C12.4614 13.2297 12.5206 13.2232 12.5781 13.2106M12.5781 13.2106C12.731 13.1771 12.8725 13.1005 12.9849 12.9881C13.1397 12.8333 13.2266 12.6235 13.2266 12.4047V2.50469C13.2266 2.28589 13.1397 2.07604 12.9849 1.92132C12.9081 1.84451 12.8177 1.78441 12.7194 1.74336M12.5781 13.2106L12.7194 1.74336" stroke="#00BD9B" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.62813 6.62655L4.97813 5.38961L3.32813 6.6271V1.26461C3.32805 1.2104 3.33867 1.1567 3.35937 1.10658C3.38007 1.05647 3.41044 1.01094 3.44875 0.972576C3.48706 0.934213 3.53256 0.903779 3.58264 0.883015C3.63273 0.862251 3.68641 0.851562 3.74063 0.851562H6.21563C6.32502 0.851562 6.42996 0.895022 6.50729 0.972381C6.58468 1.04974 6.62813 1.15466 6.62813 1.26406V6.62655Z" stroke="#00BD9B" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 10.9453H9.10313" stroke="white" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.32812 8.47266H10.7531" stroke="white" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.7562 6H8.28125" stroke="white" stroke-width="0.88" stroke-linecap="round" stroke-linejoin="round"/></svg>',
          name: 'Renta de productos',
          description: 'Gestiona el alquiler de tus productos.',
          cost: 30,
          activated: false,
        },
        {
          icon: '<svg width="15" height="16" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.35087 8.63416L9.75171 12.0755C10.0434 12.3612 10.4343 12.5194 10.8403 12.5161C11.2462 12.5127 11.6345 12.3481 11.9216 12.0576C12.2086 11.7672 12.3714 11.3742 12.3746 10.9634C12.3779 10.5527 12.2216 10.1571 11.9392 9.86193L8.51096 6.39289M6.35087 8.63416L7.80687 6.84563C7.99179 6.61896 8.23796 6.47553 8.51096 6.39289M6.35087 8.63416L3.63546 11.971C3.50385 12.1333 3.34012 12.266 3.15479 12.3604C2.96946 12.4548 2.76665 12.5089 2.5594 12.5193C2.42908 12.5257 2.29882 12.5148 2.17194 12.487M8.51096 6.39289C8.55964 6.3782 8.60918 6.36543 8.65938 6.35443M1.1588 10.3406C1.06548 10.5282 1.012 10.7334 1.0018 10.9431C0.99558 11.0709 1.00552 11.1987 1.03114 11.3233M1.1588 10.3406C1.25211 10.1531 1.3832 9.98743 1.54362 9.85426L5.53187 6.53101M1.1588 10.3406L9.26534 3.8913M5.53187 6.53101L3.13612 4.10675H2.31421L1.00171 1.89322L1.87671 1.00781L4.06421 2.33593V3.16763L6.54979 5.68279M5.53187 6.53101L6.54979 5.68279M9.52829 6.31084C9.98436 6.35047 10.4427 6.26873 10.8579 6.07373C11.2732 5.87874 11.6307 5.57727 11.8953 5.19924C12.1598 4.82121 12.322 4.37975 12.3658 3.91869C12.3713 3.86052 12.375 3.80228 12.3767 3.74409M9.52829 6.31084C9.24129 6.2868 8.94002 6.29293 8.65938 6.35443M9.52829 6.31084L12.3767 3.74409M7.13662 3.8913C7.09746 3.42981 7.17824 2.96598 7.37094 2.54583C7.56364 2.12568 7.86157 1.76383 8.23516 1.49618C8.60874 1.22852 9.045 1.06437 9.50064 1.02002C9.66849 1.00368 9.83683 1.00381 10.0034 1.02002M7.13662 3.8913L10.0034 1.02002M7.13662 3.8913C7.16377 4.21614 7.15232 4.55828 7.06964 4.86921M6.54979 5.68279L6.60929 5.63261C6.84669 5.43479 6.99066 5.16623 7.06964 4.86921M10.408 10.526L8.87671 8.97652M10.0034 1.02002C10.2889 1.04782 10.5691 1.1229 10.8326 1.24333L8.92162 3.17707C8.93049 3.21587 8.94062 3.25426 8.952 3.29221M8.952 3.29221L7.06964 4.86921M8.952 3.29221C9.01746 3.51063 9.12393 3.71403 9.26534 3.8913M9.26534 3.8913C9.30423 3.94006 9.34577 3.98684 9.38982 4.03142C9.4155 4.05741 9.44192 4.08252 9.46904 4.10675M1.03114 11.3233C1.04756 11.4032 1.07041 11.4818 1.09957 11.5583C1.17424 11.7542 1.28861 11.9321 1.43534 12.0806C1.58206 12.229 1.75788 12.3448 1.95147 12.4203C2.02333 12.4484 2.09705 12.4706 2.17194 12.487M1.03114 11.3233L9.46904 4.10675M9.46904 4.10675L1.5 12M9.46904 4.10675C9.55652 4.18493 9.65125 4.25383 9.75171 4.31261M2.17194 12.487L9.75171 4.31261M9.75171 4.31261C9.90146 4.40024 10.0639 4.46537 10.2341 4.50519L12.1451 2.57086C12.1958 2.68438 12.2383 2.80097 12.2726 2.91974M12.2726 2.91974L8.65938 6.35443M12.2726 2.91974C12.3497 3.1869 12.385 3.46508 12.3767 3.74409" stroke="#00BD9B" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.5 10.9688H2.50467" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>',
          name: 'Servicios',
          description: 'Administra tus servicios de manera eficiente.',
          cost: 60,
          activated: false,
        },
      ]
    };
  },
  components: {
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    InputLabel,
    Modal
  },
  props: {},
  methods: {
    checkout() {
      this.form.amount = parseFloat(this.calculateTotalPayment(this.calculateTotal).toFixed(2)); //monto total a pagar
      this.form.suscription_period = this.period; //periodo de tiempo a pagar (mes, año)
      this.form.remainingPlanDays = this.remainingPlanDays; //días restantes para que expire el plan pagado
      this.form.modulesUpdated = this.activated_modules.filter(item => !this.currentActivatedModules.includes(item)); //modulos adicionales agregados al plan actual
      this.form.activeModules = this.activated_modules; //modulos pagados para agregarlos en base de datos en caso de agregados o quitados
      this.form.discountTicketUsed = this.verifiedTicket //cupón de descuento utilizado
      this.form.post(route('stripe.upgrade-subscription')); //Desgloce de pago cuando la suscripción no ha vencido. Agregar o quitar módulos
    },
    // modifica los módulos activos en la tienda cuando no se ha pagado la suscripción y esta vencido el pago.
    async updateStoreModules() {
      this.loading = true;
      try {
        const response = await axios.post(route('store.update-modules', this.$page.props.auth.user.store.id), { period: this.period, activated_modules: this.activated_modules });
        if (response.status === 200) {
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
        this.showDiscountModal = false;
        this.showApliedDiscountTicket = true;
        this.ticketCodeError = false;
        this.ticketCode = null;
      } else {
        this.ticketCodeError = true;
      }
    },
    calculateTotalPayment(calculateTotal) {
      let total;

      if (this.period === 'Mensual') {
        total = calculateTotal * this.adjustmentFactor;
      } else {
        total = calculateTotal * 10 * this.adjustmentFactor;
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
    calculateRemainingPlanDays() {
      const store = this.$page.props.auth.user.store;
      const today = new Date();
      const nextPaymentDate = new Date(store.next_payment);
      const timeDiff = nextPaymentDate - today;
      const remainingPlanDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
      this.remainingPlanDays = remainingPlanDays; //guarda los dias 
    },
    handleSwitchModule(module) {
      if (module.activated) {
        this.activated_modules.push(module.name);
      } else {
        this.activated_modules = this.activated_modules.filter(name => name !== module.name);
      }
    },
  },
  computed: {
    formattedTotalPaymentInteger() {
      const totalPayment = this.calculateTotalPayment(this.calculateTotal);
      return totalPayment ? Math.floor(totalPayment).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0';
    },
    formattedTotalPaymentDecimals() {
      const totalPayment = this.calculateTotalPayment(this.calculateTotal);
      return totalPayment ? totalPayment.toFixed(2).split('.')[1] : '00';
    },
    adjustmentFactor() {
      const store = this.$page.props.auth.user.store;
      if (store.suscription_period === 'Mensual') {
        return this.remainingPlanDays / 30;
      } else if (store.suscription_period === 'Anual') {
        return this.remainingPlanDays / 365;
      } else {
        return 1;
      }
    },
    calculateTotal() {
      return this.modules
        .filter(item => item.activated === true && !this.currentActivatedModules.includes(item.name))
        .reduce((sum, item) => sum + item.cost, 0);
    },
    //filtra los modulos adicionales activos en mi suscripción
    aditionalActivatedModules() {
      return this.modules.filter(item => this.currentActivatedModules.includes(item.name));
    },
    //filtra los modulos adicionales inactivos en mi suscripción
    aditionalInactiveModules() {
      return this.modules.filter(item => !this.currentActivatedModules.includes(item.name));
    },
    disableOptionCriterial() {
      const today = new Date();
      const nextPaymentDate = new Date(this.nextPayment);

      // si la fecha de pago no ha expirado se muestra la opción de mensual
      if (nextPaymentDate < today) {
        return false;
      } else {
        if (this.period === 'Mensual') {
          return false;
        } else {
          return true;
        }
      }
    },
    formattedNextPayment() {
      return this.nextPayment
        ? format(new Date(this.nextPayment), "dd MMM yyyy", { locale: es })
        : "";
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

    //se clona el arreglo de módulos activados para separar la lógica
    // Se crea una copia independiente
    this.currentActivatedModules = [...activatedModules];

    //si el periodo pagado es anual el monto pagado se multiplica por 10
    if (this.$page.props.auth.user.store.suscription_period === 'Anual') {
      this.totalPaid *= 10;
    }
    
    this.calculateRemainingPlanDays();
  }
};
</script>

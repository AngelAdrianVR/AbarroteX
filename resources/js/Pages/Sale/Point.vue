<template>
  <AppLayout title="Registrar venta">
    <div v-if="!isOnline" class="w-2/3 ml-auto mt-3 rounded-s-[5px] px-4 py-1 bg-[#232323] text-white text-xs">
      <p class="text-sm flex items-center space-x-3 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.855 -0.855 24 24"
          id="Wifi-Disabled--Streamline-Core" height="16" width="16">
          <desc>Wifi Disabled Streamline Icon: https://streamlinehq.com</desc>
          <g id="wifi-disabled--wireless-wifi-internet-server-network-disabled-off-offline-connection">
            <path id="Vector 2432" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="m0.7960714285714285 0.7960714285714285 20.697857142857142 20.697857142857142" stroke-width="2.1">
            </path>
            <path id="Vector" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M11.145 21.09573364285714c1.151915357142857 0 2.0857071428571428 -0.9337917857142857 2.0857071428571428 -2.0857071428571428s-0.9337917857142857 -2.0857071428571428 -2.0857071428571428 -2.0857071428571428c-1.1518994357142855 0 -2.0857071428571428 0.9337917857142857 -2.0857071428571428 2.0857071428571428s0.9338077071428571 2.0857071428571428 2.0857071428571428 2.0857071428571428Z"
              stroke-width="2.1"></path>
            <path id="Vector_2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M7.212454907142857 14.32936532142857c0.5177170928571428 -0.53150505 1.1366626285714285 -0.9539483142857142 1.820281007142857 -1.2423809142857143"
              stroke-width="2.1"></path>
            <path id="Vector_3" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M3.757441221428571 11.6385006c0.9691532785714285 -0.9748053857142858 2.0014509428571428 -1.5694389 2.0014509428571428 -1.5694389"
              stroke-width="2.1"></path>
            <path id="Vector_4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M8.83744367142857 8.825040878571428s0.9409086642857143 -0.26260804285714284 2.3155170428571425 -0.26260804285714284c1.3745924571428572 0 2.7356357785714285 0.2717628642857143 4.004828378571428 0.7996378285714286 1.2691448357142856 0.5278749642857142 2.4215378357142856 1.3014812571428571 3.390675192857143 2.276286642857143"
              stroke-width="2.1"></path>
            <path id="Vector_5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M5.543936957142857 5.503400999999999c1.775701007142857 -0.7357929 3.678980421428571 -1.1145159214285714 5.601094885714286 -1.1145159214285714 1.9221144642857142 0 3.8253938785714285 0.3787230214285714 5.601126728571429 1.1145159214285714 1.7757169285714285 0.7357929 3.389035285714286 1.8142308642857141 4.74777 3.1737298071428572"
              stroke-width="2.1"></path>
            <path id="Vector_6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
              d="M0.7960714285714285 8.677305942857142c0.57410124 -0.5744133 1.1653052785714284 -1.0461015428571427 1.87291725 -1.5767946"
              stroke-width="2.1"></path>
          </g>
        </svg>
        <span>Sin conexión a Internet</span>
      </p>
      <p class="text-xs">
        Las ventas que realices se guardan en el dispositivo que estas utilizando y
        luego se transfieren automáticamente a la nube cuando tengas internet.
        ¡Así nunca perderán información!. <br>
        <b>Es importante que no recargues la página para poder registrar ventas</b>
      </p>
    </div>
    <div v-if="syncingData || syncingIDB"
      class="w-2/3 ml-auto mt-3 rounded-s-[5px] px-4 py-1 bg-secondary text-gray37 text-xs">
      <p class="text-sm flex items-center space-x-3 font-semibold">
        <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          id="Rotate-Right--Streamline-Sharp" height="16" width="16">
          <desc>Rotate Right Streamline Icon: https://streamlinehq.com</desc>
          <g id="rotate-right">
            <path id="Vector 2754" stroke="currentColor" d="M20.2047 0.5135V4.8893H15.8289" stroke-width="2"></path>
            <path id="Ellipse 1206" stroke="currentColor"
              d="M20.2047 4.764C18.2001 2.4929 15.2674 1.0605 12.0001 1.0605C5.9583 1.0605 1.0605 5.9583 1.0605 12C1.0605 16.194 3.4207 19.8367 6.8853 21.6726"
              stroke-width="2"></path>
            <path id="Ellipse 1207" stroke="currentColor"
              d="M9.1081 22.5533C10.0293 22.8051 10.999 22.9395 11.9999 22.9395C13.4231 22.9395 14.7826 22.6678 16.0297 22.1734"
              stroke-width="2"></path>
            <path id="Ellipse 1208" stroke="currentColor"
              d="M17.7655 21.2986C19.2694 20.3641 20.5299 19.0749 21.4301 17.548" stroke-width="2"></path>
            <path id="Ellipse 1209" stroke="currentColor" d="M22.9395 12C22.9395 13.2879 22.717 14.5237 22.3083 15.6713"
              stroke-width="2"></path>
          </g>
        </svg>
        <span>Sincronizando datos</span>
      </p>
      <p v-if="syncingIDB" class="text-xs">
        Por favor, evita recargar la página y espera a que los datos se carguen en el dispositivo. Una vez terminado el
        proceso, ya podrás registrar ventas
      </p>
      <p v-else class="text-xs">
        Por favor, evita recargar la página y espera a que los datos se carguen a la nube.
      </p>
    </div>
    <main class="pt-2 h-[calc(94vh-1px)]"><!--mover altura para productos sin codigo -->
      <section class="overflow-auto px-2 lg:px-6" :class="showNoCodeProducts ? 'h-[60%]' : 'h-[94%]'">
        <!-- header botones -->
        <header class="lg:flex justify-between items-center mt-1 mx-3">
          <h1 class="font-bold text-lg">Registrar venta</h1>
          <!-- Dinero en caja -->
          <div v-if="isShowCahsOn && $page.props.auth.user.permissions.includes('Ver dinero en caja')"
            class="mt-4 lg:mt-0 flex items-center justify-center space-x-3 text-gray99">
            <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-if="showCashRegisterMoney"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
              class="size-4 cursor-pointer">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg @click="showCashRegisterMoney = !showCashRegisterMoney" v-else xmlns="http://www.w3.org/2000/svg"
              fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4 cursor-pointer">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
            <p v-if="this.$page.props.auth?.user?.cash_register_id" class="text-sm flex items-center space-x-2">
              Efectivo en "{{ asignedCashRegister?.name }}":
              <b :class="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn ? 'text-red-600' : ''"
                class="ml-2">
                {{ showCashRegisterMoney ? '$' + localCurrentCash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") :
                  '*****' }}
              </b>
              <el-tooltip
                v-if="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn && showCashRegisterMoney"
                content="Se llegó al límite de dinero permitido en caja. Es recomendable hacer corte"
                placement="bottom">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                  stroke="currentColor" class="size-4 text-red-600">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
              </el-tooltip>
            </p>
            <p v-else class="text-sm text-red-600">No tienes una caja asignada</p>
          </div>
          <article class="flex items-center space-x-3">
            <!-- Indicadores activos como báscula y descuentos -->
            <el-tooltip v-if="$page.props.auth.user.scale_config.is_enabled"
              :content="isConnectedScale ? 'Báscula sincronizada' : 'Sincronizar báscula'" placement="bottom">
              <button @click="!isConnectedScale ? connectScale() : ''"
                class="rounded-full p-1 flex items-center justify-center cursor-default"
                :class="isConnectedScale ? 'bg-green-300' : 'bg-red-300'">
                <svg width="77" height="73" viewBox="0 0 77 73" fill="none" xmlns="http://www.w3.org/2000/svg"
                  stroke="currentColor" class="size-4">
                  <path
                    d="M5.83797 42.9021L4.29033 64.6428C4.29033 65.6746 4.80621 66.4115 5.46948 66.4115H69.7336C71.2636 66.4232 72.313 65.8956 72.313 64.6428L70.4706 42.9021M5.83797 42.9021H70.4706M5.83797 42.9021C2.0802 42.9021 1.04867 40.6174 2.89008 35.3849L13.871 5.09529C14.3869 3.54765 15.4923 2 17.04 2H58.0894C60.5951 2 62.0691 2.88437 63.0272 5.09529L74.8187 37.7432C75.6294 40.0279 73.6396 42.9021 70.4706 42.9021M52.2685 57.2731L51.7527 57.8626M51.9001 58.3048L52.637 57.4204L52.7107 57.3467L51.8264 58.3785M60.8174 57.2731L61.6281 58.3785M60.8174 50.0507L61.7018 51.1562M51.8264 50.0507L52.8581 51.0825M62.4388 68.6961H68.2609M8.63968 68.7698H14.0933M14.4618 52.2616V56.8309C14.4618 57.2731 14.8303 57.6415 15.2724 57.6415H36.6447C37.6765 57.6415 37.7501 57.2731 37.7501 56.8309V52.2616C37.7501 51.672 37.3817 51.672 36.6447 51.672H15.1987C14.7566 51.672 14.4618 51.7457 14.4618 52.2616ZM69.5874 67.0748C69.5874 69.3541 67.7397 71.2019 65.4604 71.2019C63.1811 71.2019 61.3333 69.3541 61.3333 67.0748H69.5874ZM15.3461 67.0748C15.3461 69.3541 13.4984 71.2019 11.2191 71.2019C8.93978 71.2019 7.09203 69.3541 7.09203 67.0748H15.3461ZM53.0792 57.8626C53.0792 58.3104 52.7163 58.6733 52.2685 58.6733C51.8208 58.6733 51.4579 58.3104 51.4579 57.8626C51.4579 57.4149 51.8208 57.052 52.2685 57.052C52.7163 57.052 53.0792 57.4149 53.0792 57.8626ZM61.9966 57.8626C61.9966 58.3104 61.6336 58.6733 61.1859 58.6733C60.7382 58.6733 60.3753 58.3104 60.3753 57.8626C60.3753 57.4149 60.7382 57.052 61.1859 57.052C61.6336 57.052 61.9966 57.4149 61.9966 57.8626ZM53.0792 50.6403C53.0792 51.088 52.7163 51.451 52.2685 51.451C51.8208 51.451 51.4579 51.088 51.4579 50.6403C51.4579 50.1926 51.8208 49.8296 52.2685 49.8296C52.7163 49.8296 53.0792 50.1926 53.0792 50.6403ZM61.9966 50.6403C61.9966 51.088 61.6336 51.451 61.1859 51.451C60.7382 51.451 60.3753 51.088 60.3753 50.6403C60.3753 50.1926 60.7382 49.8296 61.1859 49.8296C61.6336 49.8296 61.9966 50.1926 61.9966 50.6403ZM16.3042 53.4408V55.6517H35.5392V53.4408H16.3042Z"
                    stroke="black" stroke-width="2.9479" />
                </svg>
              </button>
            </el-tooltip>
            <!-- Dropdown -->
            <div v-if="$page.props.auth.user.permissions.includes('Registrar movimientos de caja')"
              class="inline-block border border-primary rounded-full px-4 pt-[3px] mt-3 md:mt-0">
              <el-col :span="3">
                <el-dropdown trigger="click">
                  <p class="text-sm text-primary w-44 flex items-center">
                    <svg class="mr-2" width="12" height="12" viewBox="0 0 12 12" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <mask id="mask0_9380_424" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                        width="12" height="12">
                        <rect width="12" height="12" fill="#D9D9D9" />
                      </mask>
                      <g mask="url(#mask0_9380_424)">
                        <path
                          d="M2.5 10.5C2.225 10.5 1.98958 10.4021 1.79375 10.2063C1.59792 10.0104 1.5 9.775 1.5 9.5V2.5C1.5 2.225 1.59792 1.98958 1.79375 1.79375C1.98958 1.59792 2.225 1.5 2.5 1.5H9.5C9.775 1.5 10.0104 1.59792 10.2063 1.79375C10.4021 1.98958 10.5 2.225 10.5 2.5V9.5C10.5 9.775 10.4021 10.0104 10.2063 10.2063C10.0104 10.4021 9.775 10.5 9.5 10.5H2.5ZM6 8C6.31667 8 6.60417 7.90833 6.8625 7.725C7.12083 7.54167 7.3 7.3 7.4 7H9.5V2.5H2.5V7H4.6C4.7 7.3 4.87917 7.54167 5.1375 7.725C5.39583 7.90833 5.68333 8 6 8Z"
                          fill="#F68C0F" />
                      </g>
                    </svg>
                    <span>Movimientos de caja</span>
                    <i class="fa-solid fa-angle-down text-[10px] ml-2 mt-px"></i>
                  </p>
                  <template #dropdown>
                    <el-dropdown-menu>
                      <el-dropdown-item v-if="$page.props.auth.user.store.plan == 'Plan Avanzado'"
                        @click="showCashRegisterSelectionModal = true"><i
                          class="fa-solid fa-arrows-rotate text-xs mr-3"></i>Cambiar de caja</el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister"
                        @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4 mr-3">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                        </svg>
                        Ingresar efectivo
                      </el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister"
                        @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4 mr-3">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3 3m3-3 3 3" />
                        </svg>
                        Retirar efectivo</el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister" @click="handleCashCut">
                        <svg width="19" height="20" class="mr-3" viewBox="0 0 120 124" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M118.487 99.2735H2M118.487 99.2735L104.104 56.236M118.487 99.2735V109.594V110.801M2 99.2735L15.5041 56.236M2 99.2735V110.801M15.5041 56.236H104.104M15.5041 56.236V44.9277C15.5041 40.2068 20.2251 37.2424 23.1894 37.2424H27.9103M104.104 56.236V44.9277C104.104 41.0851 101.03 37.2424 96.3092 37.2424H86.9771M2 110.801V111.899M2 110.801L39.6578 110.911C40.4263 110.911 40.5361 111.899 39.6578 111.899H2M118.487 110.801H79.8408C79.1821 110.801 78.9625 111.79 79.8408 111.899H118.487M118.487 110.801V111.021V111.899M24.0677 46.7941C25.4118 46.7941 26.6874 46.7941 27.9103 46.7941M53.7109 46.7941C54.0403 46.7941 51.3199 46.7941 50.1976 46.7941M27.9103 46.7941V16.0531C27.9103 15.2845 28.1299 14.7356 29.4474 14.7356H47.7823C49.4291 14.7356 50.1976 14.9552 50.1976 16.0531V37.2424M27.9103 46.7941C36.1063 46.7941 41.9377 46.7941 50.1976 46.7941M50.1976 46.7941V37.2424M34.0586 22.5306C38.046 22.5306 40.2816 22.5306 44.269 22.5306M34.0586 30.8747C38.046 30.8747 40.2816 30.8747 44.269 30.8747M34.0586 39.3285C38.046 39.3285 40.2816 39.3285 44.269 39.3285M50.1976 37.2424H73.8024M73.8024 37.2424C73.8024 31.9259 73.8024 28.9451 73.8024 23.6285M73.8024 37.2424H86.9771M86.9771 37.2424V23.6285M86.9771 23.6285H64.1409C63.2626 23.6285 62.8387 23.4724 62.9332 22.4209V3.53705C62.9259 2.49762 63.1698 2.14235 64.1409 2H95.9799C97.0778 2 97.53 2.33134 97.6267 2.98811V21.5425C97.6089 22.8782 97.3906 23.4071 96.3092 23.6285H86.9771ZM2 111.899V120.683C2.06415 121.424 2.14221 121.816 3.20769 122H116.291C117.925 122.004 118.487 121.341 118.487 120.134V111.899M14.9552 88.8435C14.7421 89.6661 14.9552 89.9414 15.6139 89.9414H103.116C104.653 89.9414 104.873 89.5023 104.653 88.624C104.653 88.624 97.5169 66.3367 97.5169 66.2269C97.5169 66.1171 97.3705 66.1173 97.2973 66.1171C68.1936 66.0318 51.8083 66.0928 22.6404 66.0073C22.6404 66.0073 22.4209 65.8975 22.3111 66.2269C22.2013 66.5563 14.9552 88.8435 14.9552 88.8435ZM69.85 42.5123H90.161V50.6368H69.85V42.5123ZM71.0576 16.4922H89.0631C90.161 16.4922 90.71 16.4922 90.71 15.2845V10.2342C90.6518 9.36203 90.3806 9.02653 89.2827 9.02653H71.0576C69.9863 8.95894 69.6304 9.24611 69.6304 10.2342V15.2845C69.6304 16.1629 70.0206 16.4298 71.0576 16.4922ZM64.6898 110.911C64.6898 113.579 62.527 115.742 59.8591 115.742C57.1912 115.742 55.0284 113.579 55.0284 110.911C55.0284 108.243 57.1912 106.081 59.8591 106.081C62.527 106.081 64.6898 108.243 64.6898 110.911ZM40.097 85.4401H47.4529C48.3312 85.4401 48.3312 84.452 47.4529 84.3422H40.2068C39.4382 84.452 39.4382 85.3303 40.097 85.4401ZM64.1409 110.801C64.1409 113.166 62.2239 115.083 59.8591 115.083C57.4943 115.083 55.5773 113.166 55.5773 110.801C55.5773 108.437 57.4943 106.52 59.8591 106.52C62.2239 106.52 64.1409 108.437 64.1409 110.801ZM24.0676 85.4401H31.4235C32.3019 85.4401 32.3019 84.452 31.4235 84.3422H24.1774C23.4089 84.452 23.4089 85.3303 24.0676 85.4401ZM25.6047 79.0723H32.9606C33.8389 79.0723 33.8389 78.0842 32.9606 77.9744H25.7145C24.946 78.0842 24.946 78.9625 25.6047 79.0723ZM27.1417 72.4849H34.4976C35.376 72.4849 35.376 71.4968 34.4976 71.387H27.2515C26.483 71.4968 26.483 72.3751 27.1417 72.4849ZM41.8535 72.4849H49.2094C50.0878 72.4849 50.0878 71.4968 49.2094 71.387H41.9633C41.1948 71.4968 41.1948 72.3751 41.8535 72.4849ZM40.9752 79.0723H48.3311C49.2094 79.0723 49.2094 78.0842 48.3311 77.9744H41.085C40.3165 78.0842 40.3165 78.9625 40.9752 79.0723ZM71.936 85.4401H79.2919C80.1702 85.4401 80.1702 84.452 79.2919 84.3422H72.0457C71.2772 84.452 71.2772 85.3303 71.936 85.4401ZM55.9066 85.4401H63.2625C64.1408 85.4401 64.1408 84.452 63.2625 84.3422H56.0164C55.2479 84.452 55.2479 85.3303 55.9066 85.4401ZM55.9066 79.0723H63.2625C64.1408 79.0723 64.1408 78.0842 63.2625 77.9744H56.0164C55.2479 78.0842 55.2479 78.9625 55.9066 79.0723ZM56.1262 72.4849H63.4821C64.3604 72.4849 64.3604 71.4968 63.4821 71.387H56.236C55.4675 71.4968 55.4675 72.3751 56.1262 72.4849ZM69.9597 72.4849H77.3156C78.1939 72.4849 78.1939 71.4968 77.3156 71.387H70.0695C69.3009 71.4968 69.3009 72.3751 69.9597 72.4849ZM71.0576 79.0723H78.4135C79.2918 79.0723 79.2918 78.0842 78.4135 77.9744H71.1674C70.3988 78.0842 70.3988 78.9625 71.0576 79.0723ZM87.9652 85.4401H95.3211C96.1994 85.4401 96.1994 84.452 95.3211 84.3422H88.075C87.3065 84.452 87.3065 85.3303 87.9652 85.4401ZM84.8911 72.4849H92.247C93.1253 72.4849 93.1253 71.4968 92.247 71.387H85.0008C84.2323 71.4968 84.2323 72.3751 84.8911 72.4849ZM85.9889 79.0723H93.3449C94.2232 79.0723 94.2232 78.0842 93.3449 77.9744H86.0987C85.3302 78.0842 85.3302 78.9625 85.9889 79.0723Z"
                            stroke="currentColor" stroke-width="2.19579" />
                        </svg>
                        Hacer corte</el-dropdown-item>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown>
              </el-col>
            </div>
          </article>

        </header>
        <!-- cuerpo de la pagina -->
        <div class="lg:flex lg:space-x-5 my-2">
          <!-- scaner de código  -->
          <div class="lg:w-[70%]">
            <div v-if="isScanOn" class="relative lg:w-1/2 mx-auto mb-4" @keydown="handleKeydownScanInput">
              <input v-model="scannerQuery" :disabled="scanning || syncingIDB" ref="scanInput" class="input w-full pl-9"
                placeholder="Escanea o teclea el código del producto" type="text">
              <i class="fa-solid fa-barcode text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>

            <!-- Pestañas -->
            <div class="lg:mx-7">
              <el-tabs v-model="editableTabsValue" type="card" class="demo-tabs">
                <div v-if="$page.props.auth.user.store.activated_modules.includes('Clientes')"
                  class="m-4 flex justify-between items-center">
                  <div class="flex items-center space-x-3 w-full md:w-1/2">
                    <p class="font-bold">Cliente</p>
                    <el-tooltip
                      content="Si no es necesario agregar un cliente específico, no selecciones ninguna opción"
                      placement="top">
                      <div class="rounded-full border border-primary size-3 flex items-center justify-center px-1">
                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                      </div>
                    </el-tooltip>
                    <el-select v-model="editableTabs[this.editableTabsValue - 1].client_id" clearable filterable
                      placeholder="Seleccione" no-data-text="No hay opciones registradas"
                      no-match-text="No se encontraron coincidencias">
                      <el-option v-for="client in clients" :key="client" :label="client.name" :value="client.id" />
                    </el-select>
                    <button @click="showClientFormModal = true" type="button"
                      class="rounded-full  border-primary size-5 flex items-center justify-center text-primary text-lg">
                      <i class="fa-solid fa-circle-plus"></i>
                    </button>
                  </div>
                </div>
                <el-tab-pane v-for="tab in editableTabs" :key="tab.name" :label="tab.title" :name="tab.name">
                  <div class="flex justify-end">
                    <el-popconfirm v-if="tab.saleProducts.length" confirm-button-text="Si" cancel-button-text="No"
                      icon-color="#C30303" title="Se eliminará todo el registro de productos ¿Deseas continuar?"
                      @confirm="clearTab()">
                      <template #reference>
                        <ThirthButton class="!text-[#F80505] !border-[#F80505] !tracking-normal !py-1 !px-4 mb-2"><i
                            class="fa-regular fa-trash-can mr-1 mt-px text-[10px]"></i> Limpiar lista</ThirthButton>
                      </template>
                    </el-popconfirm>
                  </div>
                  <SaleTable @delete-product="deleteProduct" :saleProducts="tab.saleProducts"
                    :showFull="!showNoCodeProducts" />
                </el-tab-pane>
              </el-tabs>
            </div>
          </div>
          <!-- seccion de desgloce de montos -->
          <div class="lg:w-[30%]">
            <!-- buscador de productos -->
            <div class="relative">
              <el-select @keydown="handleKeydownInputSearch" v-model="productFoundSelectedName"
                @change="handleSelectFoundProduct()" ref="searchInput" :disabled="syncingIDB" filterable remote
                :remote-method="searchProducts" :loading="loading" placeholder="Buscar código o nombre de producto"
                class="w-full">
                <template #prefix>
                  <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </template>
                <el-option v-for="(product, index) in productsFound" :key="index" :label="product.name"
                  :value="product.name">
                  <!-- opciones en tienda de ropa, zapateria y boutique -->
                  <p v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
                    class="w-4/5 flex items-center space-x-2">
                    <i v-if="product.additional?.color.color" class="fa-solid fa-shirt text-xs"
                      :style="{ color: product.additional?.color.color }"></i>
                    <span>{{ product.name }}</span>
                    <span class="text-gray99">
                      ({{ product.additional?.color.name }}-{{ product.additional?.size.name }})
                    </span>
                  </p>
                  <!-- opciones en tienda de abarrotes -->
                  <div v-else>
                    <span style="float: left; font-size: 14px">{{ product.name }}</span>
                    <span style="float: right; color: #8492a6; font-size: 11px">{{ product.code }}</span>
                  </div>
                </el-option>
              </el-select>
              <!-- <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts"
                ref="searchInput" class="input w-full pl-9" placeholder="Buscar código o nombre de producto"
                type="search" :disabled="syncingIDB">
              <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i> -->
              <!-- Resultados de la búsqueda -->
              <!-- <div v-if="searchFocus && searchQuery"
                class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full z-50 max-h-48 overflow-auto">
                <ul v-if="productsFound?.length > 0 && !loading">
                  <li @click="selectProductFromList(product)" v-for="(product, index) in productsFound" :key="index"
                    class="hover:bg-gray-200 cursor-pointer text-xs px-3 py-2 flex space-x-2">
                    <p v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
                      class="w-4/5 flex items-center space-x-2">
                      <i v-if="product.additional?.color.color" class="fa-solid fa-shirt text-xs"
                        :style="{ color: product.additional?.color.color }"></i>
                      <span>{{ product.name }}</span>
                      <span class="text-gray99">
                        ({{ product.additional?.color.name }}-{{ product.additional?.size.name }})
                      </span>
                    </p>
                    <span v-else class="w-4/5">{{ product.name }}</span>
                    <span v-if="product.code" class="w-1/5 text-[10px] text-gray99">
                      {{ product.code }}
                    </span>
                  </li>
                </ul>
                <p v-else-if="!loading" class="text-center text-sm text-gray-600 px-5 py-2">
                  No se encontraron coincidencias
                </p> -->
              <!-- estado de carga -->
              <!-- <div v-if="loading" class="flex justify-center items-center py-10">
                  <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
                </div> -->
              <!-- </div> -->
            </div>
            <!-- Detalle de producto encontrado -->
            <div class="border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
              <div class="relative" v-if="productFoundSelected">
                <i @click="productFoundSelected = null"
                  class="fa-solid fa-xmark cursor-pointer size-5 rounded-full flex items-center justify-center absolute right-3"></i>
                <figure class="h-36">
                  <img v-if="productFoundSelected.imageUrl" :src="productFoundSelected.imageUrl"
                    :alt="productFoundSelected.name" class="object-contain h-36 mx-auto">
                  <p v-else class="text-center text-xs text-gray99 pt-10 px-8">Este producto no tiene imagen registrada
                  </p>
                </figure>
                <div class="flex justify-between items-center mt-2 text-lg">
                  <p class="font-bold">
                    {{ productFoundSelected.name }}
                    <span v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
                      class="text-gray99">
                      ({{ productFoundSelected.additional?.color.name }}-{{ productFoundSelected.additional?.size.name
                      }})
                    </span>
                  </p>
                  <p :class="productFoundSelected.bulk_product ? 'text-gray-900' : 'text-[#5FCB1F]'">${{
                    productFoundSelected.public_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                      ",") }}</p>
                </div>
                <!-- input de cantidad si el producto es a granel -->
                <div v-if="productFoundSelected?.bulk_product" class="flex justify-between items-center mt-4 border border-[#D9D9D9] rounded-xl py-3 px-7">
                  <div>
                    <p class="text-gray-500 mb-1">Cantidad</p>
                    <el-input-number
                      ref="quantitySelector"
                      @keydown="handleKeydownQuantitySelector"
                      v-model="quantity"
                      :min="0"
                      :max="isInventoryOn ? productFoundSelected.current_stock : undefined"
                      :precision="2"
                    >
                      <template #suffix>
                        <span v-if="productFoundSelected.measure_unit?.trim() === 'Kilogramo'">
                          {{ productFoundSelected.measure_unit?.trim() === 'Kilogramo' ? 'Kg' : 
                            productFoundSelected.measure_unit?.trim() === 'Litro' ? 'L' : '' 
                          }}
                        </span>
                      </template>
                    </el-input-number>
                  </div>
                  <div class="text-center">
                    <p class="text-gray-500">Total</p>
                    <p class="text-[#5FCB1F] text-lg font-bold">${{ (quantity * productFoundSelected.public_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</p>
                  </div>
                </div>
                
                <!-- input de cantidad si el producto no es a granel -->
                <div v-else class="flex justify-between items-center mt-4">
                  <p class="text-gray99 mb-1">Cantidad</p>
                  <el-input-number
                    ref="quantitySelector"
                    @keydown="handleKeydownQuantitySelector"
                    v-model="quantity"
                    :min="0"
                    :max="isInventoryOn ? productFoundSelected.current_stock : undefined"
                    :precision="2"
                  >
                    <template #suffix>
                      <span v-if="productFoundSelected.measure_unit?.trim() === 'Kilogramo'">
                        {{ productFoundSelected.measure_unit?.trim() === 'Kilogramo' ? 'Kg' : 
                          productFoundSelected.measure_unit?.trim() === 'Litro' ? 'L' : '' 
                        }}
                      </span>
                    </template>
                  </el-input-number>
                </div>
                <div class="text-center mt-7">
                  <div v-if="productFoundSelected.current_stock == 0 && isInventoryOn" class="text-sm text-gray99 mb-2">
                    No te quedan existencias de este producto.
                    <!-- <p class="text-primary underline cursor-pointer">Clic para dar entrada del producto</p>  -->
                  </div>
                  <div class="flex items-center justify-center space-x-3">
                    <button ref="addButton" @click="addSaleProduct(productFoundSelected); productFoundSelected = null"
                      class="rounded-full !px-24 text-white bg-primary text-sm py-1 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:bg-gray-400 focus:outline-none transition-all ease-linear duration-200"
                      :disabled="quantity == 0">
                      Agregar
                    </button>
                  </div>

                    <el-tooltip v-if="productFoundSelected?.bulk_product" content="Agregar cantidad manualmente" placement="bottom">
                      <button @click="stopReadingScale()" class="rounded-full flex items-center justify-center size-9 bg-gray-300 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                        </svg>
                      </button>
                    </el-tooltip>
                  </div>
                  <figure v-if="isReading" class="my-5 flex items-center justify-center select-none">
                    <img draggable="false" class="w-2/3 md:w-1/2 opacity-70" src="@/../../public/images/EmptyScale.png"
                      alt="Agregar peso">
                  </figure>
                </div>
              </div>
              <div v-else class="text-center text-gray99 text-sm">
                <p>
                  Busca el producto
                  <i class="fa-regular fa-hand-point-up ml-3"></i>
                </p>
                <div
                  v-if="$page.props.auth.user.store.type != 'Boutique / Tienda de Ropa / Zapatería' && $page.props.auth.user.permissions.includes('Crear productos')">
                  <p>ó</p>
                  <button @click="showCreateProductModal = true" type="button"
                    class="text-primary w-full flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-circle-plus text-xs mb-px"></i>
                    <span>Crea uno nuevo</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Total por cobrar -->
            <div v-if="editableTabs[editableTabsValue - 1]?.saleProducts?.length"
              class="border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
              <div
                v-if="!editableTabs[this.editableTabsValue - 1].cash && !editableTabs[this.editableTabsValue - 1].credit">
                <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                  <p>Subtotal</p>
                  <p class="text-gray-99">$ <strong class="ml-3">{{
                    calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</strong></p>
                </div> -->
                <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                  <p class="text-[#999999]">Descuento</p>
                  <el-input v-model="editableTabs[this.editableTabsValue - 1].discount" type="number" class="!w-24 !h-8"
                    placeholder="0.00">
                    <template #prefix>
                      <i class="fa-solid fa-dollar-sign"></i>
                    </template>
                  </el-input>
                </div> -->
                <div class="flex items-center justify-between text-xl mx-5">
                  <p class="font-bold">Total</p>
                  <p v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0"
                    class="text-red-600 text-xs">El descuento es más grande que el total</p>
                  <p v-else class="text-gray-99">$ <strong class="ml-3">{{ (calculateTotal() -
                    editableTabs[this.editableTabsValue
                      - 1].discount)?.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                      }) }}</strong></p>
                </div>

                <!------- botones venta a crédito y al contado ------->
                <div class="text-center mt-7">
                  <p class="text-sm text-gray-400 text-left mb-3">Opciones de pago</p>
                  <div class="flex items-center justify-end space-x-4">
                    <PrimaryButton v-if="$page.props.auth.user.store.activated_modules.includes('Clientes')"
                      @click="creditPayment()"
                      :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0"
                      class="!px-4 !bg-[#baf09b] disabled:!bg-[#999999] !text-black">A crédito</PrimaryButton>
                    <PrimaryButton @click="cashPayment()"
                      :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0"
                      class="!px-4 !bg-[#5FCB1F] disabled:!bg-[#999999]">Al contado</PrimaryButton>
                  </div>
                  <p v-if="!$page.props.auth?.user?.cash_register_id" class="text-sm text-red-600 mt-2">
                    Para cobrar asigna una caja registradora <span @click="showCashRegisterSelectionModal = true"
                      class="underline cursor-pointer text-primary">asignar una</span>
                  </p>
                </div>

                <!------- botones venta normal sin crédito ------->
                <!-- <div class="text-center mt-7">
                  <PrimaryButton @click="cashPayment()"
                    :disabled="editableTabs[this.editableTabsValue - 1]?.saleProducts?.length == 0 || (calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) < 0 || !$page.props.auth?.user?.cash_register_id"
                    class="!rounded-full !px-24 !bg-[#5FCB1F] disabled:!bg-[#999999]">Cobrar</PrimaryButton>
                  <p v-if="!$page.props.auth?.user?.cash_register_id" class="text-sm text-red-600 mt-2">
                    Para cobrar asigna una caja registradora <span @click="showCashRegisterSelectionModal = true"
                      class="underline cursor-pointer text-primary">asignar una</span>
                  </p>
                </div> -->
              </div>

              <!-- cobrando pago al contado -->
              <div v-if="editableTabs[this.editableTabsValue - 1].cash">
                <p class="text-gray-99 text-center mb-3 text-2xl">Total $ <strong>{{ (calculateTotal() -
                  editableTabs[this.editableTabsValue - 1].discount)?.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                  }) }}</strong>
                </p>
                <div class="flex items-center justify-between mx-5 space-x-10 text-lg">
                  <p>Entregado</p>
                  <input v-model="editableTabs[this.editableTabsValue - 1].moneyReceived" @keydown.enter="store"
                    type="number" class="input !rounded-md w-1/3" ref="receivedInput" placeholder="$0.00">
                </div>
                <div class="flex items-center justify-between mx-5 my-2 relative text-lg">
                  <p>Cambio</p>
                  <p
                    v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) <= editableTabs[this.editableTabsValue - 1]?.moneyReceived">
                    ${{
                      (editableTabs[this.editableTabsValue - 1]?.moneyReceived - (calculateTotal() -
                        editableTabs[this.editableTabsValue - 1].discount)).toLocaleString('en-US', {
                          minimumFractionDigits: 2
                        }) }}</p>
                </div>
                <p v-if="((calculateTotal() - editableTabs[this.editableTabsValue - 1].discount) > editableTabs[this.editableTabsValue - 1]?.moneyReceived) && editableTabs[this.editableTabsValue - 1].moneyReceived"
                  class="text-base font-bold text-primary text-center mb-3">
                  La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total de compra.
                </p>
                <div class="flex space-x-2 justify-end">
                  <CancelButton @click="editableTabs[this.editableTabsValue - 1].cash = false">Cancelar</CancelButton>
                  <PrimaryButton :disabled="storeProcessing" @click="store" class="!rounded-full w-1/2">
                    <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Aceptar
                  </PrimaryButton>
                </div>
              </div>

              <!-- Cobrando a crédito  -->
              <div v-if="editableTabs[this.editableTabsValue - 1]?.credit">
                <div class="flex items-center justify-between">
                  <i @click="editableTabs[this.editableTabsValue - 1].credit = false; editableTabs[this.editableTabsValue - 1].deposit = null"
                    class="fa-solid fa-angle-left text-xs p-2 cursor-pointer"></i>
                  <p class="text-gray-99 text-xl">$ <strong class="ml-3">{{
                    calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                      ",") }}</strong></p>
                </div>
                <h3 class="text-lg font-bold">Registrar abono de la venta</h3>
                <div class="flex items-center justify-between space-x-7 my-3">
                  <div class="w-2/3">
                    <InputLabel value="Monto abonado (opcional)" class="!text-base ml-2 !text-gray-400" />
                    <el-input type="number" v-model="editableTabs[this.editableTabsValue - 1].deposit"
                      placeholder="ingresa el abono">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                      </template>
                    </el-input>
                  </div>
                  <div class="w-1/3">
                    <InputLabel value="Saldo restante" class="!text-base !text-gray-400" />
                    <p v-if="(calculateTotal() - editableTabs[this.editableTabsValue - 1].deposit) > 0">${{
                      (calculateTotal() -
                        editableTabs[this.editableTabsValue - 1].deposit)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                          ",")
                    }}</p>
                    <p class="text-red-600 text-xs" v-else>La cantidad abonada debe de ser menor al monto total</p>
                  </div>
                </div>
                <div class="w-2/3 pr-5">
                  <InputLabel value="Fecha de vencimiento (opcional)" class="!text-base !text-gray-400 ml-2" />
                  <el-date-picker v-model="editableTabs[this.editableTabsValue - 1].limit_date" class="!w-full"
                    type="date" placeholder="Seleccione" :disabled-date="disabledDate" />
                </div>
                <!-- <div class="mt-3">
                  <InputLabel value="Notas (opcional)" class="!text-base ml-2 !text-gray-400" />
                  <el-input v-model="editableTabs[this.editableTabsValue - 1].deposit_notes"
                    :autosize="{ minRows: 3, maxRows: 5 }" type="textarea" placeholder="Escribe tus notas"
                    :maxlength="200" show-word-limit clearable />
                </div> -->
                <div class="flex items-center justify-end space-x-3 mt-4">
                  <PrimaryButton @click="editableTabs[this.editableTabsValue - 1].has_credit = true; checkClientExist()"
                    :disabled="(calculateTotal() - editableTabs[this.editableTabsValue - 1].deposit) < 0 || storeProcessing">
                    <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Finalizar venta
                  </PrimaryButton>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- lista de productos -->
      </section>
      <section v-if="isQuickNoCodeSelectionOn && noCodeProducts.length"
        class="border rounded-t-[20px] border-[#D9D9D9] shadow-md bg-[#232323]"
        :class="showNoCodeProducts ? 'h-[40%]' : 'h-[6%]'">
        <div class="mx-4">
          <button @click="showNoCodeProducts = !showNoCodeProducts" type="button"
            class="flex items-center justify-between text-xs text-primary w-full pt-3">
            <h1 class="text-sm font-bold text-gray9A">Selección rápida para productos sin código</h1>
            <p class="flex items-center space-x-2">
              <span>{{ showNoCodeProducts ? 'Ocultar' : 'Mostrar' }}</span>
              <i class="fa-solid" :class="showNoCodeProducts ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
            </p>
          </button>
        </div>
        <div v-if="showNoCodeProducts" class="mt-2 px-3 py-1 overflow-auto h-[82%]">
          <div class="mb-2 relative">
            <input v-model="searchNoCodeProducts" @input="filterNoCodeProducts" placeholder="Buscar producto"
              type="search" class="lg:w-1/3 h-8 pl-8 rounded-md bg-transparent text-gray-400 border border-gray-300 focus:ring-0 focus:border-primary transition-all ease-in-out duration-200 text-sm placeholder:text-sm">
                <i class="absolute left-2 top-2 fa-solid fa-magnifying-glass text-gray-400"></i>
          </div>
          <el-skeleton v-if="syncingIDB"
            class="col-span-full mt-2 px-3 py-1 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2" animated>
            <template #template>
              <el-skeleton-item v-for="item in 7" :key="item" class="!h-36" />
            </template>
          </el-skeleton>
          <div v-show="showNoCodeProducts" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2">
            <button @click="addSaleProduct(item)" type="button" v-for="(item, index) in filteredNoCodeProducts" :key="index"
              class="border border-[#D9D9D9] bg-white px-3 py-2 active:bg-grayF2 rounded-md">
              <h2 class="text-xs text-center">{{ item.name }}</h2>
              <figure class="flex items-center justify-center h-14">
                <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="object-contain h-14 mx-auto">
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-10 text-gray-300">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
              </figure>
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- -------------- Modal selección de caja starts----------------------- -->
    <Modal :show="showCashRegisterSelectionModal" @close="showCashRegisterSelectionModal = false">
      <div class="py-4 px-7 relative">
        <i @click="showCashRegisterSelectionModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <h1 class="font-bold mt-2">Elegir caja</h1>
        <p class="text-gray99">Selecciona la caja de trabajo</p>

        <section class="flex justify-between space-x-7 w-2/3 mx-auto text-sm mt-5">
          <div class="flex flex-col items-center" v-for="(item, index) in cash_registers" :key="item">
            <input :disabled="!item.is_active" v-model="selectedCashRegisterId" :id="'suscription-' + index"
              :aria-describedby="'suscription-text-' + index" type="radio" :value="item.id"
              class="size-3 text-primary focus:ring-0 disabled:cursor-not-allowed">
            <label :class="!item.is_active ? 'text-grayD9 cursor-not-allowed' : 'text-[#373737]'"
              :for="'suscription-' + index">{{ item.name }}</label>
            <label :class="!item.is_active ? 'text-grayD9 cursor-not-allowed' : 'text-[#373737]'"
              :for="'suscription-' + index" class="mt-4">
              <svg width="100" height="66" viewBox="0 0 88 54" fill="none" xmlns="http://www.w3.org/2000/svg"
                stroke="currentColor">
                <path
                  d="M67.8222 13.681L69.8833 27.0754L69.9784 27.6933M67.8222 13.681H71.6168M67.8222 13.681H62.1173M71.9444 40.4698H1M71.9444 40.4698L71.1465 35.2848M71.9444 40.4698L69.2213 49.3273M1 40.4698L2.9526 13.681H30.289H38.5333H58.1012M1 40.4698L4.25433 51.0556H6.42388M71.1465 35.2848L80.6226 35.6911M71.1465 35.2848L69.9784 27.6933M81.9243 35.7469L86.2634 35.9329L87 27.6933M81.9243 35.7469L79.5378 50.4075H68.8893M81.9243 35.7469L80.6226 35.6911M68.8893 50.4075L68.69 51.0556H67.1713M68.8893 50.4075L69.2213 49.3273M69.2213 49.3273H78.453L80.6226 35.6911M6.42388 51.0556L7.07475 53H66.3035L67.1713 51.0556M6.42388 51.0556H67.1713M71.6631 15.6253C71.7742 17.6943 72.0838 19.7969 72.5952 22.3225H81.2734C80.8859 19.771 80.6341 17.66 80.5452 15.6253M71.6631 15.6253H69.8833L71.5104 22.5385H72.5952M71.6631 15.6253C71.7276 17.7194 72.0007 19.2862 72.5952 22.5385M71.6631 15.6253L72.5952 22.5385M71.6631 15.6253C71.6283 14.9779 71.6129 14.3337 71.6168 13.681M72.5952 22.5385H82.3582L80.8395 15.6253H80.5452M80.5452 15.6253C80.416 12.6681 80.6308 9.87213 81.2734 6.11961L80.6226 6.76773L80.1887 6.11961L79.9717 6.55169L79.5378 6.11961L79.1039 6.55169L78.8869 6.11961L78.453 6.55169L78.1055 5.90357L77.5852 6.76773L77.1513 5.90357L76.7174 6.55169L76.5004 5.90357L76.0665 6.55169L75.6326 6.11961L75.1987 6.55169L74.7648 5.90357L74.3309 6.55169L74.1139 5.90357L73.68 6.55169L73.2461 5.90357L72.8122 6.55169L72.3783 5.90357C71.8862 8.95569 71.6307 11.3723 71.6168 13.681M87 27.6933H69.9784M87 27.6933L81.2734 13.681H80.5452M61.9312 10.5057H60.6907H58.1012M61.9312 10.5057H64.7043L64.9213 1L45.8292 1.43208L46.2632 10.5057H58.1012M61.9312 10.5057L62.1173 13.681M62.1173 13.681H58.1012M58.1012 10.5057V13.681M4.90519 14.7612H66.7374L70.4257 39.1735H2.9526L4.90519 14.7612ZM6.42388 16.0574H11.6308L11.1969 20.5942H5.98997L6.42388 16.0574ZM12.2817 16.0574H17.4886L17.2716 20.5942H11.8478L12.2817 16.0574ZM18.1395 16.0574H23.3464L23.1294 20.5942H17.9225L18.1395 16.0574ZM23.9973 16.0574H29.2042V20.5942H23.7803L23.9973 16.0574ZM29.855 16.0574H35.062V20.5942H29.855V16.0574ZM35.7128 16.0574H40.9198V20.5942H35.7128V16.0574ZM4.68824 32.2603H16.4038L16.1869 37.4452H4.25433L4.68824 32.2603ZM29.6381 32.2603H41.5706V37.4452H29.6381V32.2603ZM17.2716 32.2603H22.6955V37.4452H17.0547L17.2716 32.2603ZM23.5633 32.2603H28.9872V37.4452H23.5633V32.2603ZM48.2962 16.0574H53.7201L54.371 20.5942H48.9471L48.2962 16.0574ZM54.371 16.0574L55.0218 20.5942H60.2288L59.5779 16.0574H54.371ZM60.4457 16.0574L61.0966 20.5942H66.3035L65.6527 16.0574H60.4457ZM46.2632 1.86415L46.6971 10.0736H64.2704L64.4874 1.43208L46.2632 1.86415ZM47.3479 2.94435V9.20947H63.6196V2.94435H47.3479ZM5.98997 21.2423H11.1969L10.763 25.7791H5.55606L5.98997 21.2423ZM11.8478 21.2423H17.0547L16.8377 25.7791H11.4139L11.8478 21.2423ZM17.9225 21.2423H23.1294L22.9125 25.7791H17.7056L17.9225 21.2423ZM23.7803 21.2423H28.9872V25.7791H23.5633L23.7803 21.2423ZM29.855 21.2423H35.062V25.7791H29.855V21.2423ZM35.9298 21.2423H41.1367V25.7791H35.9298V21.2423ZM5.55606 26.6433H10.763L10.3291 31.1801H5.12215L5.55606 26.6433ZM11.6308 26.6433H16.8377L16.6208 31.1801H11.1969L11.6308 26.6433ZM17.7056 26.6433H22.9125L22.6955 31.1801H17.4886L17.7056 26.6433ZM23.7803 26.6433H28.9872V31.1801H23.5633L23.7803 26.6433ZM29.855 26.6433H35.062V31.1801H29.855V26.6433ZM36.3637 26.6433H41.5706V31.1801H36.3637V26.6433ZM48.9471 21.4584H54.371L54.8049 26.2112H49.381L48.9471 21.4584ZM55.0218 21.4584H60.2288L60.8796 26.2112H55.4558L55.0218 21.4584ZM61.0966 21.4584H66.3035L67.1713 26.2112H61.7475L61.0966 21.4584ZM49.381 26.8593H54.8049L55.2388 31.8282H49.8149L49.381 26.8593ZM55.6727 26.8593H60.8796L61.5305 31.8282H56.1066L55.6727 26.8593ZM61.7475 26.8593H67.1713L67.8222 31.8282H62.1814L61.7475 26.8593ZM49.8149 32.4763H61.7475L62.3983 37.4452H50.2488L49.8149 32.4763ZM62.3983 32.4763H67.8222L68.69 37.4452H63.0492L62.3983 32.4763Z"
                  stroke="#373737" stroke-width="0.4" />
              </svg>
            </label>
            <p v-if="!item.is_active" class="text-sm text-red-400">Deshabilitada</p>
          </div>
        </section>

        <div class="flex justify-between space-x-1 pt-2 pb-1 py-2 mt-5 col-span-full">

          <p v-if="cash_registers.length == 1 && $page.props.auth.user.store.plan == 'Plan Avanzado'"
            class="text-gray99">
            Por ahora solo tienes una caja. <span @click="$inertia.get(route('cash-registers.create'))"
              class="text-primary cursor-pointer hover:underline ml-1">Crear caja</span></p>

          <span v-else></span>
          <PrimaryButton :disabled="!selectedCashRegisterId" @click="asignCashRegister">Confirmar</PrimaryButton>
        </div>
      </div>
    </Modal>
    <!-- -------------- Modal Ingreso o retiro de dinero en caja starts----------------------- -->
    <Modal :show="cashRegisterModal" @close="cashRegisterModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashRegisterModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <form class="mt-5 mb-2 md:grid grid-cols-2 gap-3" @submit.prevent="storeCashRegisterMovement">
          <h2 v-if="form.cashRegisterMovementType === 'Ingreso'" class="font-bold col-span-full">Ingresar efectivo a
            caja
          </h2>
          <h2 v-if="form.cashRegisterMovementType === 'Retiro'" class="font-bold col-span-full">Retirar efectivo a caja
          </h2>

          <div class="mt-2">
            <InputLabel v-if="form.cashRegisterMovementType === 'Ingreso'" value="Monto a ingresar *"
              class="ml-3 mb-1 text-sm" />
            <InputLabel v-if="form.cashRegisterMovementType === 'Retiro'" value="Monto a retirar *"
              class="ml-3 mb-1 text-sm" />
            <el-input v-model="form.registerAmount" type="text" placeholder="ingresa el monto"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <p class="text-red-500 text-xs"
              v-if="form.cashRegisterMovementType === 'Retiro' && form.registerAmount > localCurrentCash">
              *El monto no debe exceder el dinero actual de tu caja (${{ localCurrentCash }})
            </p>
            <InputError :message="form.errors.registerAmount" />
          </div>

          <div class="col-span-full mt-2">
            <InputLabel value="Motivo (opcional)" class="text-sm ml-2" />
            <el-input v-model="form.registerNotes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe tus notas" :maxlength="255" show-word-limit clearable />
          </div>

          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <CancelButton @click="cashRegisterModal = false">Cancelar</CancelButton>
            <PrimaryButton
              :disabled="!form.registerAmount || form.processing || (form.cashRegisterMovementType === 'Retiro' && (form.registerAmount > localCurrentCash))">
              Confirmar</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
    <!-- -------------- Modal corte de caja starts----------------------- -->
    <Modal :show="cashCutModal" @close="cashCutModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashCutModal = false; cutForm.reset()"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
        <form class="mt-5 mb-2" @submit.prevent="storeCashCut">
          <h2 class="font-bold col-span-full">Hacer corte de caja</h2>
          <p class="col-span-full">Por favor, cuenta el dinero en caja e ingrésalo para proceder con el corte.</p>
          <div class="rounded-full h-3 bg-[#F2F2F2] my-2"></div>

          <section class="w-full flex justify-end space-x-3 text-sm">
            <div class="w-44 space-y-2">
              <p>Efectivo esperado en caja</p>
              <p>Recuento manual</p>
              <p>Diferencia</p>
            </div>
            <div class="w-44 space-y-2">
              <div v-if="cutLoading">
                <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>
              <p v-else>${{ (asignedCashRegister?.started_cash + cutForm.totalStoreSale + cutForm.totalOnlineSale +
                cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              <el-input @input="difference()" v-model="cutForm.counted_cash" type="text" placeholder="0.00"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" class="!w-24 !h-6">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <p v-if="cutForm.counted_cash" :class="{
                'text-green-500': (cutForm.difference) === 0,
                'text-blue-500': (cutForm.difference) < 0,
                'text-red-500': (cutForm.difference) > 0
              }">
                <!-- Se multiplica por -1 para cambiar el signo y si sobra sea positivo y si falta negativo -->
                ${{ (cutForm.difference * -1)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
              </p>
              <p v-if="cutForm.counted_cash" :class="{
                'text-green-500 bg-green-100': (cutForm.difference) === 0,
                'text-blue-500 bg-blue-100': (cutForm.difference) < 0,
                'text-red-500 bg-red-100': (cutForm.difference) > 0
              }" class="rounded-full text-xs inline py-[2px] px-2">
                <!-- Icono de proveedor de verificación si la diferencia es 0 -->
                <i v-if="(cutForm.difference) === 0" class="fa-solid fa-check mr-1"></i>
                <!-- Icono de sobrante en caja si la diferencia es negativa -->
                <i v-else-if="(cutForm.difference) < 0" class="fa-solid fa-plus mr-1"></i>
                <!-- Icono de faltante de efectivo si la diferencia es positiva -->
                <i v-else class="fa-solid fa-xmark mr-1"></i>
                <!-- Muestra el mensaje correspondiente -->
                {{ (cutForm.difference) === 0 ? 'Todo bien' : ((cutForm.difference) < 0 ? 'Sobrante en caja'
                  : 'Faltante de efectivo') }} </p>
            </div>
          </section>

          <div v-if="cutForm.counted_cash" class="flex items-center space-x-3 mt-3">
            <div class="w-full">
              <InputLabel value="Monto a retirar de caja" class="text-sm ml-2" />
              <el-input v-model="cutForm.withdrawn_cash" type="number" step="0.01" class="!w-1/2 !h-6"
                placeholder="0.00">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <InputError :message="cutForm.errors.withdrawn_cash" />
            </div>
            <p v-if="cutForm.withdrawn_cash" class="w-full mt-3 text-sm font-bold">Efectivo que dejarás en caja: ${{
              (cutForm.counted_cash - cutForm.withdrawn_cash)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
          </div>
          <div class="col-span-full mt-2">
            <InputLabel value="Comentarios (opcional)" class="text-sm ml-2" />
            <el-input v-model="cutForm.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe aquí cualquier comentario relacionado al corte" :maxlength="255" show-word-limit
              clearable />
          </div>

          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <CancelButton @click="cashCutModal = false; cutForm.reset()">Cancelar</CancelButton>
            <PrimaryButton
              :disabled="!cutForm.counted_cash || cutForm.processing || (cutForm.totalCashMovements == 0 && cutForm.totalStoreSale == 0)">
              Hacer corte</PrimaryButton>
          </div>
          <p v-if="cutForm.totalCashMovements == 0 && cutForm.totalStoreSale == 0"
            class="text-xs text-red-600 text-right">
            *Para hacer corte es necesario que haya almenos una venta o movimiento de caja registrado</p>
        </form>
      </div>
    </Modal>
    <!------------------- creacion rapida de productos ---------------------->
    <DialogModal :show="showCreateProductModal" @close="showCreateProductModal = false">
      <template #title> Creación rápida de nuevo producto </template>
      <template #content>
        <form v-if="products_quantity < 1500" @submit.prevent="storeProduct" class="lg:grid lg:grid-cols-2 gap-x-3">
          <div class="mt-3 col-span-2">
            <InputLabel value="Nombre del producto*" class="ml-3 mb-1" />
            <el-input v-model="productForm.name" placeholder="Escribe el nombre del producto" :maxlength="100"
              clearable />
            <InputError :message="productForm.errors.name" />
          </div>
          <div class="mt-3">
            <InputLabel value="Precio de venta al público*" class="ml-3 mb-1 text-sm" />
            <el-input v-model="productForm.public_price" placeholder="ingresa el precio"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" class="!self-end !justify-self-end">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <InputError :message="productForm.errors.public_price" />
          </div>
          <div class="mt-3">
            <InputLabel value="Existencia actual (opcional)" class="ml-3 mb-1 text-sm" />
            <el-input v-model="productForm.current_stock" placeholder="ingresa la cantidad actual en stock"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" />
            <InputError :message="productForm.errors.current_stock" />
          </div>
          <div class="mt-3 col-span-2">
            <InputLabel value="Código del producto (en caso de tener)" class="ml-3 mb-1" />
            <el-input v-model="productForm.code" placeholder="Escribe el código del producto" :maxlength="100"
              clearable>
              <template #prefix>
                <i class="fa-solid fa-barcode"></i>
              </template>
            </el-input>
            <InputError :message="productForm.errors.code" />
          </div>
        </form>
        <div v-else class="text-center text-gray37">
          <h1 class="font-bold text-5xl text-center mb-5">¡Cima alcanzada!</h1>
          <p class="text-xl text-center">Has llegado al límite de productos (1,500) de tu plan contratado.</p>
          <p class="text-xl text-center">
            Sigue creciendo tu negocio y descubre nuestros planes haciendo clic en el siguiente botón
          </p>
          <div class="flex justify-center mt-5">
            <PrimaryButton @click="$inertia.get(route('profile.show'))" :disabled="productForm.processing">
              <i v-if="productForm.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Explorar planes
            </PrimaryButton>
          </div>
        </div>
      </template>
      <template #footer>
        <div v-if="products_quantity < 1500" class="flex items-center space-x-2">
          <CancelButton @click="showCreateProductModal = false; resetProductForm()" :disabled="creatingProduct">
            Cancelar</CancelButton>
          <PrimaryButton @click="storeProduct()" :disabled="creatingProduct">Crear</PrimaryButton>
        </div>
      </template>
    </DialogModal>
    <!-- client form -->
    <DialogModal :show="showClientFormModal" @close="showClientFormModal = false; resetClientForm()">
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
            <el-input v-model="clientForm.rfc" placeholder="Escribe el RFC en caso de tenerlo" :maxlength="100"
              clearable />
            <InputError :message="clientForm.errors.rfc" />
          </div>
        </form>
      </template>
      <template #footer>
        <div class="flex items-center space-x-2">
          <CancelButton @click="showClientFormModal = false; resetClientForm()" :disabled="clientForm.processing">
            Cancelar</CancelButton>
          <PrimaryButton @click="storeClient()" :disabled="clientForm.processing">Crear</PrimaryButton>
        </div>
      </template>
    </DialogModal>
    <!-- Modal para advertir que se ha excedido del dinero permitido en caja -->
    <ConfirmationModal :show="showLimitCashModal" @close="showLimitCashModal = false">
      <template #title>
        <h1>Se ha llegado al límite de dinero permitido en caja</h1>
      </template>
      <template #content>
        <p>
          ¿Deseas realizar corte para no exceder el límite de dinero permitido en caja?
        </p>
      </template>
      <template #footer>
        <div class="flex items-center space-x-1">
          <CancelButton @click="showLimitCashModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="showLimitCashModal = false; handleCashCut()">Hacer corte</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
    <!-- Modal de venta a credito sin cliente -->
    <ConfirmationModal :show="showClientConfirmModal" @close="showClientConfirmModal = false">
      <template #title> No seleccionaste un cliente </template>
      <template #content>Para realizar una venta a crédito es necesario que selecciones un cliente.</template>
      <template #footer>
        <div>
          <PrimaryButton @click="showClientConfirmModal = false">De acuerdo</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from "@/Components/DialogModal.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SaleTable from '@/Components/MyComponents/Sale/SaleTable.vue';
import InputError from "@/Components/InputError.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';
import { format } from 'date-fns';
import {
  getItemByPartialAttributes,
  getItemByAttributes,
  getItemsByNullAttribute,
  addOrUpdateBatchOfItems,
  syncIDBProducts,
  addOrUpdateItem
} from '@/dbService.js';

export default {
  data() {
    const form = useForm({
      cashRegisterMovementType: null, //que tipo de movimiento es
      registerAmount: null, //Dinero ingresado o sacado de caja
      registerNotes: null, //notas al entrar o sacar dinero
    });

    const clientForm = useForm({
      name: null,
      rfc: null,
      phone: null,
    });

    const cutForm = useForm({
      counted_cash: null,
      difference: null,
      notes: null,
      totalStoreSale: null, //dinero esperado de ventas hechas para hacer corte
      totalOnlineSale: null, //dinero esperado de ventas en linea para hacer corte
      totalCashMovements: null, //dinero de movimientos de caja para hacer corte
      withdrawn_cash: null, //dinero retirado de caja tras haber hecho el corte
    });

    const productForm = useForm({
      name: null,
      code: null,
      public_price: null,
      cost: null,
      current_stock: null,
      description: null,
      category_id: null,
      brand_id: null,
      min_stock: null,
      max_stock: null,
      imageCover: null,
    });

    const categoryForm = useForm({
      name: null,
    });

    const brandForm = useForm({
      name: null,
    });

    return {
      // formularios
      form,
      cutForm,
      brandForm,
      clientForm,
      productForm,
      categoryForm,

      //báscula
      isConnectedScale: false, // Estado de conexión
      port: null, // Puerto serie de la báscula
      reader: null, // Lector de datos del puerto
      intervalId: null, // Para guardar el ID del intervalo
      isReading: false, // Para controlar el estado de lectura

      // caja
      selectedCashRegisterId: this.$page.props.auth.user.cash_register_id, //id de la caja registradora seleccionada
      asignedCashRegister: this.$page.props.auth.user.cash_register_id, // caja registradora asignada a la venta de el usuario logueado
      showCashRegisterSelectionModal: false, //muestra u oculta el modal de selección de caja

      // conexion a internet
      isOnline: navigator.onLine, // Verificar el estado de conexión al cargar el componente
      syncingData: false,
      syncingIDB: false,

      // modales
      showLimitCashModal: false, //muestra u oculta el modal de excedencia de dinero permitido en caja
      showClientFormModal: false, //muestra u oculta el modal de creación de cliente
      showCashRegisterMoney: true, //muestra u oculta el dinero de caja
      showClientConfirmModal: false, //muestra u oculta el modal de peticion de cliente para venta a crédito
      showCreateProductModal: false,

      // generales
      showNoCodeProducts: false,
      searchNoCodeProducts: null,
      noCodeProducts: [],
      filteredNoCodeProducts: [],
      localCurrentCash: 0, //dinero de caja local
      cashRegisterModal: false, //muestra el modal para ingresar o retirar dinero de la caja
      cashCutModal: false, //muestra el modal para el corte de caja
      // inventario de codigos activado
      isInventoryOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Control de inventario')?.value,
      // mostrar dinero en caja activado
      isShowCahsOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Mostrar dinero en caja')?.value,
      // descuentos activados
      isDiscountOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Hacer descuentos')?.value,
      // escaneo de codigos activado
      isScanOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Escanear productos')?.value,
      // monto maximo en caja activado
      isMaxCashOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Aviso de monto máximo en caja')?.value,
      // mostrar seleccion rapida de productos sin codigo activado
      isQuickNoCodeSelectionOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Selección rápida de productos sin código')?.value,

      // cargas
      storeProcessing: false, //cargando store de venta
      scanning: false, //cargando la busqueda de productos por escaner
      loading: false, //cargando la busqueda de productos
      creatingProduct: false,
      cutLoading: false, //cargando monto total esperado para corte
      scannerQuery: null, //input para scanear el codigo de producto

      //buscador
      searchQuery: null,
      searchFocus: false,
      productsFound: null,
      productSelected: null, //producto escaneado agergado a la lista de compras
      productFoundSelected: null, //producto seleccionado desde barra de busqueda
      productFoundSelectedName: null, //nombre del producto seleccionado desde barra de busqueda
      quantity: 1, //cantidad para agregar del producto escaneado o buscado
      tabIndex: 1, //index del tab - componente de tabs
      editableTabsValue: "1", //tab seleccionado - componente de tabs
      editableTabs: [ //Informacion del tab - componente de tabs
        {
          title: "Lista 1",
          name: "1",
          saleProducts: [],
          has_credit: false,
          deposit: false,
          cash: false, //Pago al contado
          credit: false, //venta a crédito
          discount: 0,
          moneyReceived: null,
          client_id: null,
        },
        {
          title: "Lista 2",
          name: "2",
          saleProducts: [],
          has_credit: false,
          deposit: false,
          cash: false, //Pago al contado
          credit: false, //venta a crédito
          discount: 0,
          moneyReceived: null,
          client_id: null,
        },
        {
          title: "Lista 3",
          name: "3",
          saleProducts: [],
          has_credit: false,
          deposit: false,
          cash: false, //Pago al contado
          credit: false, //venta a crédito
          discount: 0,
          moneyReceived: null,
          client_id: null,
        },
      ],
    }
  },
  components: {
    AppLayout,
    ConfirmationModal,
    PrimaryButton,
    ThirthButton,
    CancelButton,
    DialogModal,
    InputLabel,
    InputError,
    SaleTable,
    Modal,
    InputFilePreview,
  },
  props: {
    products_quantity: Number,
    products: Array,
    cash_registers: Array,
    clients: Array
  },
  methods: {
    filterNoCodeProducts() {
      if (this.searchNoCodeProducts) {
        this.filteredNoCodeProducts = this.noCodeProducts.filter(product =>
          product.name.toLowerCase().includes(this.searchNoCodeProducts.toLowerCase())
        );
      } else {
        this.filteredNoCodeProducts = this.noCodeProducts;
      }
    },
    // Ejecuta un metodo depende de la tecla presionada. atajos de teclado
    handleKeydownScanInput(event) {
      if (event.key === "ArrowRight") {
        this.focusSearchInput();
      } else if (event.key === "Enter") {
        this.getProductByCode();
      } else if (event.key === "Shift" && this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.length) {
        this.cashPayment();
      }
    },
    handleKeydownInputSearch(event) {
      if (event.key === "Control") {
        this.$refs.scanInput.focus();
      } else if (event.key === "Shift" && this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.length) {
        this.cashPayment();
      }
    },
    handleKeydownQuantitySelector(event) {
      if (event.key === "Enter") {
        this.focusAddButton();
      } else if (event.key === "Escape") {
        this.focusSearchInput();
        this.stopReadingScale(); //detiene la lectura de la bascula en caso de que este activa
        this.productFoundSelected = null; //borra el producto seleccionado
      } else if (event.key === "m") {
        this.stopReadingScale(); //detiene la lectura de la bascula en caso de que este activa
      }
    },
    handleSelectFoundProduct() {
      this.focusQuantitySelector(); //enfoca el selector de cantidad
      if (this.productsFound.length > 0) {
        this.productFoundSelected = this.productsFound.find(product => product.name === this.productFoundSelectedName);
      }
    },
    // enfoca el input de cantidad cuando se hace la busqueda por nombre de producto
    focusQuantitySelector() {
      this.$nextTick(() => {
        this.$refs.quantitySelector.focus();
      });
    },
    // enfoca el input de busqueda por nombre de producto
    focusSearchInput() {
      this.$nextTick(() => {
        this.$refs.searchInput.focus();
      });
    },
    // enfoca el boton de agregar producto cuando se hace la busqueda por nombre de producto
    focusAddButton() {
      this.$nextTick(() => {
        this.$refs.addButton.focus();
      });
    },
    checkClientExist() { //revisa si hay cliente seleccionado para venta a crédito
      if (this.editableTabs[this.editableTabsValue - 1]?.client_id == null) {
        this.showClientConfirmModal = true;
      } else {
        this.store();
      }
    },
    async processOnlineSale() {
      try {
        const response = await axios.post(route('sales.store', { cash_register_id: this.asignedCashRegister?.id }), {
          saleProducts: this.editableTabs[this.editableTabsValue - 1]?.saleProducts, //productos vendidos
          has_credit: this.editableTabs[this.editableTabsValue - 1]?.has_credit, //bandera para indicar venta a crédito
          client_id: this.editableTabs[this.editableTabsValue - 1]?.client_id ?? false, //id del cliente al que se vendió
          deposit: this.editableTabs[this.editableTabsValue - 1]?.deposit ?? 0.00, //abono para venta a crédito
          // deposit_notes: this.editableTabs[this.editableTabsValue - 1]?.deposit_notes, //notas de venta a crédito
          limit_date: this.editableTabs[this.editableTabsValue - 1]?.limit_date ?? null, //fecha límite de crédito
        });
        if (response.status === 200) {
          this.updateCurrentStockInIndexedDB();
          this.clearTab();
          this.fetchCashRegister();

          this.$notify({
            title: "Correcto",
            message: "Se ha registrado la venta",
            type: "success",
          });

          localStorage.setItem('pendentProcess', false);

          //se imprime el ticket automáticamente cuando esta la opción activada en config/impresora
          if (this.$page.props.auth.user.printer_config?.automaticPrinting) {
            window.open(route('sales.print-ticket', response.data.folio_stored), '_blank');
          }
        }
      } catch (error) {
        console.error(error);
      }
    },
    processOfflineSale() {
      this.saveToLocalStorage();
      this.updateCurrentStockInIndexedDB();
      this.sumCashForSale();
      this.clearTab();
    },
    async store() { //registra la venta
      if (this.storeProcessing) return;

      this.storeProcessing = true;

      if (this.isOnline) {
        await this.processOnlineSale();
      } else {
        this.processOfflineSale();
      }

      this.storeProcessing = false;
    },
    storeClient() { //registra un cliente
      this.clientForm.post(route('clients.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo cliente",
            type: "success",
          });
          this.showClientFormModal = false;
        },
      });
    },
    storeCashCut() {
      this.cutForm.post(route('cash-cuts.store', { cash_register_id: this.asignedCashRegister?.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha realizado el corte de caja",
            type: "success",
          });
          this.cashCutModal = false;
          this.fetchCashRegister();
          this.cutForm.reset();
          this.inputFocus();
        },
      });
    },
    storeProduct() { //registra un nuevo producto
      this.creatingProduct = true;
      this.productForm.post(route('products.store', { stayInView: true }), {
        onSuccess: async () => {
          const response = await axios.get(route('products.get-all-for-indexedDB'));
          const product = response.data.local_products[0];

          this.showCreateProductModal = false;

          // agregar al carrito de compras
          this.addSaleProduct(product);

          // agregar a indexedDB
          addOrUpdateItem('products', product);

          this.$notify({
            title: "Producto creado y seleccionado para la venta",
            message: "",
            type: "success",
          });

          this.creatingProduct = false;

          this.productForm.reset();
        },
        onError: () => {
          this.creatingProduct = false;
          this.inputFocus();
        }
      });
    },
    resetClientForm() {
      this.clientForm.reset();
      this.inputFocus();
    },
    resetProductForm() {
      this.productForm.reset();
      this.inputFocus();
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    sumCashForSale() {
      this.localCurrentCash += this.editableTabs[this.editableTabsValue - 1]?.saleProducts.reduce((accum, item) => {
        return accum += item.product.public_price * item.quantity;
      }, 0);
    },
    selectProductFromList(product) {
      // crear link virtual de imagen blob si es que tiene imagen el producto
      if (product.image && !product.imageUrl) {
        const imageUrl = URL.createObjectURL(product.image);
        product = { ...product, imageUrl };
      }

      this.productFoundSelected = product;
      this.searchQuery = null;

      // revisar si hay stock del producto para dejar 1 como default a vender
      if ((product.current_stock && this.isInventoryOn) || !this.isInventoryOn) {
        this.quantity = 1;
      } else {
        this.quantity = 0;
      }
    },
    async updateCurrentStockInIndexedDB() {
      // Obtener la pestaña actual y sus productos
      const saleProducts = this.editableTabs[this.editableTabsValue - 1]?.saleProducts;

      // Asegurarse de que existan productos en la pestaña
      if (!saleProducts) {
        return;
      }

      // Mapear los productos para actualizar su stock
      const products = await Promise.all(saleProducts.map(async (item) => {
        // Obtener productos por código
        let foundProducts = await getItemByAttributes('products', { code: item.product.code });

        // Verificar si se encontró el producto
        if (foundProducts.length > 0) {
          // Actualizar el stock
          foundProducts[0].current_stock = item.product.current_stock <= item.quantity ? 0 : item.product.current_stock - item.quantity;
          return foundProducts[0];
        }

        // Manejar el caso donde no se encuentre el producto
        return null;
      }));

      // Filtrar productos que no fueron encontrados
      const validProducts = products.filter(product => product !== null);

      // Actualizar los productos en IndexedDB
      await addOrUpdateBatchOfItems('products', validProducts);
    },
    async asignCashRegister() {
      try {
        const response = await axios.put(route('cash-registers.asign', [this.$page.props.auth?.user.id, this.selectedCashRegisterId]));
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            text: "Se te asignó una caja con éxito!",
            type: "success",
          });
          location.reload();
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeCashRegisterMovement() {
      this.form.post(route('cash-register-movements.store', { cash_register_id: this.asignedCashRegister?.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha registrado el movimiento de caja",
            type: "success",
          });
          this.cashRegisterModal = false;
          this.form.reset();
          this.fetchCashRegister();
          this.inputFocus();
        },
      });
    },
    difference() {
      //  Se hace la resta al reves para cambiar el signo y si sobra sea positivo y si falta negativo
      this.cutForm.difference = (this.cutForm.totalStoreSale + this.cutForm.totalOnlineSale + this.cutForm.totalCashMovements + this.asignedCashRegister?.started_cash) - this.cutForm.counted_cash
    },
    deleteProduct(productId) {
      const indexToDelete = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === productId);
      this.editableTabs[this.editableTabsValue - 1].saleProducts.splice(indexToDelete, 1);
    },
    cashPayment() {
      this.editableTabs[this.editableTabsValue - 1].cash = true;
      this.receivedInputFocus();
    },
    creditPayment() {
      this.editableTabs[this.editableTabsValue - 1].credit = true;
    },
    async fetchCashRegister() {
      try {
        const response = await axios.get(route('cash-registers.fetch-cash-register', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.localCurrentCash = response.data.item.current_cash;
          if ((this.localCurrentCash >= this.asignedCashRegister?.max_cash) && this.isMaxCashOn && this.isOnline) {
            this.showLimitCashModal = true;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async searchProducts(query) {
      this.searchQuery = query;
      try {
        if (this.searchQuery.length < 2) {
          return;
        }
        this.productsFound = await getItemByPartialAttributes('products', { name: this.searchQuery, code: this.searchQuery });
      } catch (error) {
        console.log(error);
      }
    },
    //funciona con el input normal
    // async searchProducts() {
    //   try {
    //     this.productsFound = await getItemByPartialAttributes('products', { name: this.searchQuery, code: this.searchQuery });
    //   } catch (error) {
    //     console.log(error);
    //   }
    // },
    async fetchTotalSaleForCashCut() {
      try {
        const response = await axios.get(route('cash-cuts.fetch-total-sales-for-cash-cut', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.cutForm.totalStoreSale = response.data.store_sales; //ventas en tienda
          this.cutForm.totalOnlineSale = response.data.online_sales; // ventas en linea
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchTotalCashMovements() {
      try {
        this.cutLoading = true;
        const response = await axios.get(route('cash-register-movements.fetch-total-cash-movements', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.cutForm.totalCashMovements = response.data;
          this.cutLoading = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    handleCashCut() {
      this.fetchTotalSaleForCashCut();
      this.fetchTotalCashMovements();
      this.cashCutModal = true;
    },
    handleBlur() {
      // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
      setTimeout(() => {
        this.searchFocus = false;
      }, 100);
    },
    async getProductByCode() {
      // retorna si no hay código de producto escaneado
      if (!this.scannerQuery) {
        return;
      }
      this.scanning = true;
      let foundProducts = await getItemByAttributes('products', { code: this.scannerQuery });
      let productScaned = foundProducts[0];

      // si no se encontró el producto escaneado aparece un mensaje y no busca en la bd para no tardar más
      if (productScaned != null) {
        // agregar la imagen al producto si es que no la tiene
        if (productScaned.image && !productScaned.imageUrl) {
          const imageUrl = URL.createObjectURL(productScaned.image);
          productScaned = { ...productScaned, imageUrl };
        }

        this.addSaleProduct(productScaned);
      } else {
        this.$notify({
          title: "Producto no encontrado",
          message: "El producto escaneado no esta registrado en la base de datos",
          type: "warning"
        });
        this.scannerQuery = null;
        this.scanning = false;
        this.inputFocus();
      }
    },
    addSaleProduct(product) {
      //revisa si el producto a agregar ya esta dentro del arreglo
      const existingIndex = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => {
        return sale.product.id == product.id;
      });
      if (existingIndex !== -1) {
        this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex] = {
          ...this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex],
          quantity: this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex].quantity + (this.isReading ? this.weight : this.quantity)
        };
      } else {
        // Si el producto no existe, agrégalo al array
        this.editableTabs[this.editableTabsValue - 1].saleProducts.push({
          product: product,
          quantity: this.isReading ? this.weight : this.quantity, //si se esta leyendo de la báscula se agrega el peso, si no la cantidad
          originalPrice: null,
        });
      }

      // si esta leyeendo de la báscula se detiene la lectura
      if (this.isReading) {
        this.stopReading();
      }
      this.scannerQuery = null;
      this.quantity = 1;
      this.scanning = false;
      this.productFoundSelectedName = null;
      this.productFoundSelected = null;
      this.inputFocus();

      // indicar al navegador mediante el local storage que hay proceso pendiente
      const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
      if (!pendentProcess) {
        // guardar el valor en el localStorage
        localStorage.setItem('pendentProcess', true);
      }
    },
    clearTab() {
      this.searchQuery = null;
      this.scannerQuery = null;
      this.searchFocus = false;
      this.productsFound = null;
      this.productSelected = null;
      this.editableTabs[this.editableTabsValue - 1].saleProducts = []; //productos
      this.editableTabs[this.editableTabsValue - 1].has_credit = false; //bandera para venta a crédito
      this.editableTabs[this.editableTabsValue - 1].deposit = null; //abono en venta a crédito
      this.editableTabs[this.editableTabsValue - 1].client_id = null; //abono en venta a crédito
      this.editableTabs[this.editableTabsValue - 1].limit_date = null; //fecha límite de crédito
      this.editableTabs[this.editableTabsValue - 1].cash = false; //bandera de venta al contado
      this.editableTabs[this.editableTabsValue - 1].credit = false; //bandera de venta a crédito
      this.editableTabs[this.editableTabsValue - 1].discount = 0; //descuento
      this.editableTabs[this.editableTabsValue - 1].moneyReceived = null; //Dinero recibido para calcular cambio
      this.inputFocus();
    },
    calculateTotal() {
      // Suma de los productos del precio y la cantidad para cada elemento en saleProducts
      const total = this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.reduce((accumulator, sale) => {
        return accumulator + sale.product.public_price * sale.quantity;
      }, 0);

      // Formatear el resultado al final
      // return total?.toLocaleString('en-US', { minimumFractionDigits: 2 }); formatea el total con comas pero me manda a NaN despues de 1000
      return total;
    },
    inputFocus() {
      this.$nextTick(() => {
        if (this.isScanOn) {
          this.$refs.scanInput.focus();
        } else {
          this.$refs.searchInput.focus();
        }
      });
    },
    receivedInputFocus() {
      this.$nextTick(() => {
        this.$refs.receivedInput.focus(); // Enfocar el input de código cuando se abre el modal
      });
    },
    handleOnline() {
      const storedData = JSON.parse(localStorage.getItem('sales')) || [];
      this.isOnline = true;
      if (storedData.length) {
        this.syncData();
      }
    },
    handleOffline() {
      this.isOnline = false;
    },
    saveToLocalStorage() {
      // Obtén los datos actuales almacenados en el Local Storage
      let storedData = JSON.parse(localStorage.getItem('sales')) || [];

      const dataToStore = {
        created_at: format(new Date(), 'yyyy-MM-dd HH:mm'),
        saleProducts: this.editableTabs[this.editableTabsValue - 1]?.saleProducts,
        has_credit: this.editableTabs[this.editableTabsValue - 1]?.has_credit,
        client_id: this.editableTabs[this.editableTabsValue - 1]?.client_id ?? null, //id del cliente al que se vendió
        deposit: this.editableTabs[this.editableTabsValue - 1]?.deposit ?? 0.00, //abono para venta a crédito
        // deposit_notes: this.editableTabs[this.editableTabsValue - 1]?.deposit_notes, //notas de venta a crédito
        limit_date: this.editableTabs[this.editableTabsValue - 1]?.limit_date ?? null, //fecha límite de crédito
      };

      // Agrega el nuevo objeto al arreglo
      storedData.push(dataToStore);

      // Vuelve a guardar el arreglo en el Local Storage
      localStorage.setItem('sales', JSON.stringify(storedData));
      this.form.reset();

      this.$notify({
        title: 'Correcto',
        message: 'Se registró la venta en almacenamiento local. Cuando tengas conexión a internet se guardarán en la nube',
        type: 'success'
      });
    },
    async syncData() {
      this.syncingData = true;
      try {
        const localStorageItems = JSON.parse(localStorage.getItem('sales'));
        const response = await axios.post(route('sales.sync-localstorage'), {
          sales: localStorageItems,
        });

        if (response.status === 200) {
          // eliminar datos en almacenamiento local
          localStorage.removeItem('sales');
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.syncingData = false;
      }
    },
    handleScale() {
      if (this.isConnectedScale) {
        // Si la báscula está sincronizada, empieza a leer
        this.startReading();
      } else {
          // // Si no está sincronizada, muestra la confirmación
          // this.$confirm('No está sincronizada tu báscula. ¿Quieres sincronizarla?', 'Confirmar', {
          //     confirmButtonText: 'Sí',
          //     cancelButtonText: 'No',
          //     type: 'warning'
          // }).then(() => {
          //     // Si el usuario acepta, sincroniza la báscula
          //     this.connectScale();
          // }).catch(() => {
          //     // Si el usuario cancela, no hace nada
          //     console.log('Sincronización cancelada');
          // });
      }
    },
    async connectScale() {
      try {
        if (this.port) {
          await this.port.close(); // Cierra el puerto solo después de liberar el lector
          this.port = null;
        }

        // Solicitar al usuario seleccionar un dispositivo serie
        this.port = await navigator.serial.requestPort();

        // Configurar la conexión con los parámetros adecuados para tu báscula tomados de la base de datos
        await this.port.open({
          baudRate: this.$page.props.auth.user.scale_config?.baudRate ?? 9600,
          dataBits: this.$page.props.auth.user.scale_config?.dataBit ?? 8,
          stopBits: this.$page.props.auth.user.scale_config?.stopBit ?? 1,
          parity: this.$page.props.auth.user.scale_config?.parity ?? "none",
          flowControl: this.$page.props.auth.user.scale_config?.flowControl ?? "none",
        });

        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = this.port.readable.pipeTo(textDecoder.writable);
        this.reader = textDecoder.readable.getReader();

        console.log("Conexión exitosa a la báscula");
        console.log("Puerto:", this.port);

        this.isConnectedScale = true; // Marca la conexión como activa

        this.$notify({
          title: "Correcto",
          message: "Báscula sincronizada",
          type: "success",
        });

      } catch (error) {
        console.error("Error al conectar la báscula:", error);
        // alert("No se conectó la báscula.");
      } finally {
        // si el producto seleccionado es a granel y la báscula está activada se inicia la lectura
        if ( this.productFoundSelected?.bulk_product ) {
          this.startReadingScale();
        }
      }
    },
    async startReading() {
      if (!this.port || !this.port.readable || !this.port.writable) {
        alert("Conecta la báscula primero.");
        return;
      }

      if (this.isReading) {
        alert("La lectura ya está en curso.");
        return;
      }

        // Marcar la lectura de la báscula como activa
        this.isReadingScale = true;

      this.intervalId = setInterval(async () => {
        try {
          // Enviar comando para solicitar datos (si es necesario)
          const textEncoder = new TextEncoder();
          const writer = this.port.writable.getWriter();
          await writer.write(textEncoder.encode("COMANDO_PESO\n")); // Cambia "COMANDO_PESO" al comando requerido por tu báscula
          writer.releaseLock();

          // Leer datos
          if (!this.reader) {
            const textDecoder = new TextDecoder();
            this.reader = this.port.readable.getReader();
          }

          const { value, done } = await this.reader.read();
          if (done) {
            console.log("Lectura finalizada.");
            this.reader.releaseLock();
            this.reader = null;
            this.stopReading(); // Detiene la lectura si es el final
            return;
          }

                console.log("Datos leídos:", value);
                this.quantity = this.parseWeight(value);

            } catch (error) {
                console.error("Error al leer datos:", error);
                this.stopReadingScale(); // Detener lectura en caso de error
            }
        }, 120); // Intervalo de 100 ms
    },
    stopReadingScale() {
        if (this.intervalId) {
            clearInterval(this.intervalId); // Detiene el intervalo
            this.intervalId = null;
            console.log("Intervalo detenido.");
        }
      }, 200); // Intervalo de 500 ms
    },
    stopReading() {
      if (this.intervalId) {
        clearInterval(this.intervalId); // Detiene el intervalo
        this.intervalId = null;
        console.log("Intervalo detenido.");
      }

      this.isReading = false;
      console.log("Lectura detenida.");
    },
    async disconnectScale() {
        try {
            if (this.reader) {
                await this.reader.cancel(); // Cancela cualquier lectura activa
                this.reader.releaseLock(); // Libera el lector
                this.reader = null;
            }

            this.isConnectedScale = false; // Actualiza el estado de conexión
            this.$notify({
                title: "Correcto",
                message: "Báscula desconectada",
                type: "success",
            });
            console.log("Báscula desconectada.");
        } catch (error) {
            console.error("Error al desconectar la báscula:", error);
            alert("Comunicación con la báscula cerrada. Presiona nuevamente para desconectar");
        }

        this.isConnectedScale = false; // Actualiza el estado de conexión
        this.weight = "0.00"; // Reinicia el peso leído
        this.$notify({
          title: "Correcto",
          message: "Báscula desconectada",
          type: "success",
        });
        console.log("Báscula desconectada.");
      } catch (error) {
        console.error("Error al desconectar la báscula:", error);
        alert("Comunicación con la báscula cerrada. Presiona nuevamente para desconectar");
      }
    },
    parseWeight(data) {
      // Ajustar esta lógica según el formato de los datos enviados por la báscula
      const weight = data.trim(); // Quitar espacios en blanco
      return parseFloat(weight) || "0.00";
    },
  },
  async mounted() {
    // redirigir a los tutoriales si no los ha finalizado
    if (!this.$page.props.auth.user.tutorials_seen) {
      this.$inertia.visit(route('tutorials.index'));
    }

    //verificar si el usuario tiene una caja asignada
    if (!this.$page.props.auth?.user?.cash_register_id) {
      this.showCashRegisterSelectionModal = true;
    } else {
      this.asignedCashRegister = this.cash_registers.find(item => item.id == this.$page.props.auth?.user?.cash_register_id);
      this.localCurrentCash = this.asignedCashRegister?.current_cash;
    }

    // sincronizar productos
    // const productsInIDB = await getAll('products');
    // if (!productsInIDB.length) {
    // mostrar carga solo si recien se estan cargando los productos
    this.syncingIDB = true;
    // }
    await syncIDBProducts();
    // obtener productos sin codigo de indexedDB
    this.noCodeProducts = await getItemsByNullAttribute('products', 'code');
    // ordenar alfabeticamente
    this.noCodeProducts = this.noCodeProducts.sort((a, b) => a.name.localeCompare(b.name));
    this.filteredNoCodeProducts = this.noCodeProducts;
    this.syncingIDB = false;

    // resetear variable de local storage a false
    localStorage.setItem('pendentProcess', false);

    // Agregar escuchadores de eventos online/offline
    window.addEventListener('online', this.handleOnline);
    window.addEventListener('offline', this.handleOffline);

    this.inputFocus();
  },
  beforeUnmount() {
    // Eliminar los escuchadores de eventos al desmontar el componente
    window.removeEventListener('online', this.handleOnline);
    window.removeEventListener('offline', this.handleOffline);
  },
}
</script>
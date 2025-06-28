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
    <main class="pt-2 h-[calc(94vh-4px)]"><!--mover altura para productos sin codigo -->
      <section class="overflow-auto px-2 lg:px-6" :class="showNoCodeProducts ? 'h-[70%]' : 'h-[94%]'">
        <!-- header botones -->
        <header class="lg:flex justify-between items-center mt-1 mx-3">
          <h1 class="font-bold text-lg">Registrar venta</h1>
          <article class="flex items-center space-x-2">
            <!-- Indicadores activos como báscula y descuentos -->
            <el-tooltip v-if="$page.props.auth.user.scale_config.is_enabled || true" placement="left">
              <template #content>
                <p v-if="isConnectedScale" class="text-sm">Báscula activada</p>
                <div v-else class="text-sm">
                  <p class="text-[#C5C5C5]">Báscula desctivada</p>
                  <p class="">Verifique la conexión de la báscula. Si la <br>
                    conexión es correcta y no se activa, haga <br>
                    clic en <a class="text-primary underline" href="/settings?tab=4">Ir a configurar</a> </p>
                </div>
              </template>
              <button @click="!isConnectedScale ? connectScale() : ''"
                class="rounded-full size-7 flex items-center justify-center"
                :class="isConnectedScale ? '!text-[#11a709] bg-[#c1febe]' : 'bg-[#EDEDED] text-gray37'">
                <svg width="77" height="73" viewBox="0 0 77 73" fill="none" xmlns="http://www.w3.org/2000/svg"
                  stroke="currentColor" class="size-[18px]">
                  <path
                    d="M5.83797 42.9021L4.29033 64.6428C4.29033 65.6746 4.80621 66.4115 5.46948 66.4115H69.7336C71.2636 66.4232 72.313 65.8956 72.313 64.6428L70.4706 42.9021M5.83797 42.9021H70.4706M5.83797 42.9021C2.0802 42.9021 1.04867 40.6174 2.89008 35.3849L13.871 5.09529C14.3869 3.54765 15.4923 2 17.04 2H58.0894C60.5951 2 62.0691 2.88437 63.0272 5.09529L74.8187 37.7432C75.6294 40.0279 73.6396 42.9021 70.4706 42.9021M52.2685 57.2731L51.7527 57.8626M51.9001 58.3048L52.637 57.4204L52.7107 57.3467L51.8264 58.3785M60.8174 57.2731L61.6281 58.3785M60.8174 50.0507L61.7018 51.1562M51.8264 50.0507L52.8581 51.0825M62.4388 68.6961H68.2609M8.63968 68.7698H14.0933M14.4618 52.2616V56.8309C14.4618 57.2731 14.8303 57.6415 15.2724 57.6415H36.6447C37.6765 57.6415 37.7501 57.2731 37.7501 56.8309V52.2616C37.7501 51.672 37.3817 51.672 36.6447 51.672H15.1987C14.7566 51.672 14.4618 51.7457 14.4618 52.2616ZM69.5874 67.0748C69.5874 69.3541 67.7397 71.2019 65.4604 71.2019C63.1811 71.2019 61.3333 69.3541 61.3333 67.0748H69.5874ZM15.3461 67.0748C15.3461 69.3541 13.4984 71.2019 11.2191 71.2019C8.93978 71.2019 7.09203 69.3541 7.09203 67.0748H15.3461ZM53.0792 57.8626C53.0792 58.3104 52.7163 58.6733 52.2685 58.6733C51.8208 58.6733 51.4579 58.3104 51.4579 57.8626C51.4579 57.4149 51.8208 57.052 52.2685 57.052C52.7163 57.052 53.0792 57.4149 53.0792 57.8626ZM61.9966 57.8626C61.9966 58.3104 61.6336 58.6733 61.1859 58.6733C60.7382 58.6733 60.3753 58.3104 60.3753 57.8626C60.3753 57.4149 60.7382 57.052 61.1859 57.052C61.6336 57.052 61.9966 57.4149 61.9966 57.8626ZM53.0792 50.6403C53.0792 51.088 52.7163 51.451 52.2685 51.451C51.8208 51.451 51.4579 51.088 51.4579 50.6403C51.4579 50.1926 51.8208 49.8296 52.2685 49.8296C52.7163 49.8296 53.0792 50.1926 53.0792 50.6403ZM61.9966 50.6403C61.9966 51.088 61.6336 51.451 61.1859 51.451C60.7382 51.451 60.3753 51.088 60.3753 50.6403C60.3753 50.1926 60.7382 49.8296 61.1859 49.8296C61.6336 49.8296 61.9966 50.1926 61.9966 50.6403ZM16.3042 53.4408V55.6517H35.5392V53.4408H16.3042Z"
                    stroke="currentColor" stroke-width="3.2" />
                </svg>
              </button>
            </el-tooltip>
            <!-- atajos de teclado -->
            <el-dropdown trigger="click">
              <button class="rounded-full size-7 flex items-center justify-center bg-[#EDEDED] text-gray37">
                <svg width="18" height="18" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M3.57457 3.38707L5.38423 4.74432L3.57457 6.10156M6.28906 6.10156H8.09872M2.66974 11.0781H10.8132C11.1732 11.0781 11.5184 10.9351 11.7729 10.6806C12.0275 10.4261 12.1705 10.0808 12.1705 9.72088V2.48224C12.1705 2.12228 12.0275 1.77706 11.7729 1.52253C11.5184 1.268 11.1732 1.125 10.8132 1.125H2.66974C2.30978 1.125 1.96456 1.268 1.71003 1.52253C1.45549 1.77706 1.3125 2.12228 1.3125 2.48224V9.72088C1.3125 10.0808 1.45549 10.4261 1.71003 10.6806C1.96456 10.9351 2.30978 11.0781 2.66974 11.0781Z"
                    stroke="currentColor" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
              <template #dropdown>
                <div class="w-96 px-4 py-2">
                  <p class="text-[#555555] text-sm mt-2">
                    Combina las siguientes teclas para realizar acciones
                    rápidas en punto deventa.
                  </p>
                  <div class="grid grid-cols-3 gap-2 self-start mt-4 text-sm *:self-start">
                    <p class="col-span-2">Buscar por nombre</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2"><i
                        class="fa-solid fa-right-long"></i></button>
                    <p class="col-span-2">Búsqueda de productos sin código</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2"><i
                        class="fa-solid fa-plus"></i></button>
                    <p class="col-span-2">Comenzar a escanear</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2">Crl</button>
                    <p class="col-span-2">Limpiar búsqueda de producto / Cerrar Búsqueda de productos sin código</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2">Esc</button>
                    <p class="col-span-2">Agregar peso manual (productos a granel utilizando báscula)</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2">M</button>
                    <p class="col-span-2">Finalizar venta</p>
                    <button class="cursor-default border border-[#d9d9d9] rounded-md py-[2px] px-2">Shift</button>
                  </div>
                </div>
              </template>
            </el-dropdown>
            <!-- Mas opciones -->
            <div v-if="$page.props.auth.user.permissions.includes('Registrar movimientos de caja')">
              <el-col :span="3">
                <el-dropdown trigger="click">
                  <button class="rounded-full size-7 flex items-center justify-center bg-[#EDEDED] text-gray37">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-[18px]">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                  </button>
                  <template #dropdown>
                    <el-dropdown-menu class="!p-0">
                      <p class="text-gray99 text-xs text-center mt-1">Movimientos de caja</p>
                      <el-dropdown-item v-if="$page.props.auth.user.store.plan == 'Plan Avanzado'"
                        @click="showCashRegisterSelectionModal = true">
                        <i class="fa-solid fa-arrows-rotate text-xs mr-3"></i>
                        Cambiar de caja
                      </el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister"
                        @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'">
                        <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                          class="size-4 mr-2">
                          <path
                            d="M4.13043 1.34783H3.16209C2.93899 1.34787 2.72178 1.41941 2.54233 1.55195C2.36287 1.6845 2.23063 1.87107 2.16499 2.08429L1.04638 5.71977C1.01574 5.81907 1.00011 5.9224 1 6.02632V7.95652C1 8.23327 1.10994 8.49868 1.30563 8.69437C1.50132 8.89006 1.76673 9 2.04348 9H9C9.27675 9 9.54216 8.89006 9.73785 8.69437C9.93354 8.49868 10.0435 8.23327 10.0435 7.95652V6.02632C10.0435 5.92244 10.0277 5.81901 9.9971 5.71977L8.87942 2.08429C8.81378 1.87107 8.68153 1.6845 8.50208 1.55195C8.32262 1.41941 8.10541 1.34787 7.88232 1.34783H6.91304M1 5.86957H2.79014C2.98391 5.86961 3.17383 5.92361 3.33863 6.02551C3.50344 6.12741 3.63661 6.27318 3.72325 6.44649L3.84197 6.68394C3.92864 6.85733 4.06189 7.00315 4.22678 7.10505C4.39168 7.20695 4.5817 7.26091 4.77554 7.26087H6.26794C6.46178 7.26091 6.6518 7.20695 6.81669 7.10505C6.98159 7.00315 7.11484 6.85733 7.20151 6.68394L7.32023 6.44649C7.4069 6.27311 7.54015 6.12729 7.70504 6.02539C7.86994 5.92349 8.05996 5.86953 8.2538 5.86957H10.0435M5.52174 1V4.82609M5.52174 4.82609L4.13043 3.43478M5.52174 4.82609L6.91304 3.43478"
                            stroke="currentColor" stroke-width="0.695652" stroke-linecap="round"
                            stroke-linejoin="round" />
                        </svg>
                        Ingresar efectivo
                      </el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister"
                        @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'">
                        <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                          class="size-4 mr-2">
                          <path
                            d="M4.15797 1H3.1811C2.95605 1.00004 2.73693 1.07221 2.55589 1.20592C2.37486 1.33963 2.24145 1.52784 2.17523 1.74294L1.04678 5.41039C1.01588 5.51057 1.00011 5.6148 1 5.71964V7.66682C1 7.946 1.1109 8.21375 1.30832 8.41116C1.50573 8.60857 1.77347 8.71948 2.05266 8.71948H9.07036C9.34954 8.71948 9.61729 8.60857 9.8147 8.41116C10.0121 8.21375 10.123 7.946 10.123 7.66682V5.71964C10.123 5.61484 10.1071 5.51051 10.0762 5.41039L8.94872 1.74294C8.88251 1.52784 8.74909 1.33963 8.56806 1.20592C8.38703 1.07221 8.16791 1.00004 7.94285 1H6.96505M1 5.56151H2.80589C3.00136 5.56156 3.19295 5.61603 3.3592 5.71882C3.52545 5.82162 3.6598 5.96867 3.7472 6.14351L3.86697 6.38305C3.9544 6.55796 4.08882 6.70506 4.25516 6.80786C4.42151 6.91065 4.6132 6.96509 4.80874 6.96505H6.31428C6.50982 6.96509 6.70151 6.91065 6.86786 6.80786C7.0342 6.70506 7.16862 6.55796 7.25605 6.38305L7.37582 6.14351C7.46325 5.9686 7.59767 5.8215 7.76402 5.7187C7.93036 5.6159 8.12205 5.56147 8.3176 5.56151H10.123M5.56151 5.57898V3.64911L5.52971 1.71948M5.52971 1.71948L6.95619 3.0997M5.52971 1.71948L4.14949 3.14596"
                            stroke="currentColor" stroke-width="0.701771" stroke-linecap="round"
                            stroke-linejoin="round" />
                        </svg>
                        Retirar efectivo
                      </el-dropdown-item>
                      <el-dropdown-item :disabled="!asignedCashRegister" @click="handleCashCut">
                        <svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg"
                          class="size-4 mr-2">
                          <path
                            d="M12.6487 10.7274H1M12.6487 10.7274L11.2104 6.4236M12.6487 10.7274V11.7594V11.8801V11.9021V11.9899V12.8134C12.6487 12.9341 12.5925 13.0004 12.4291 13H1.12077C1.01422 12.9816 1.00641 12.9424 1 12.8683V11.9899V11.8801V10.7274M1 10.7274L2.35041 6.4236M2.35041 6.4236H11.2104M2.35041 6.4236V5.29277C2.35041 4.82068 2.82251 4.52424 3.11894 4.52424H3.59103M11.2104 6.4236V5.29277C11.2104 4.90851 10.903 4.52424 10.4309 4.52424H9.49771M3.20677 5.47941C3.34118 5.47941 3.46874 5.47941 3.59103 5.47941M6.40164 5.47941C6.37793 5.47941 5.93199 5.47941 5.81976 5.47941M3.59103 5.47941V2.40531C3.59103 2.32845 3.61299 2.27356 3.74474 2.27356H5.57822C5.74291 2.27355 5.81976 2.29552 5.81976 2.40531V4.52424M3.59103 5.47941C4.41063 5.47941 4.99377 5.47941 5.81976 5.47941M5.81976 5.47941V4.52424M5.81976 4.52424H8.18024M8.18024 4.52424C8.18024 3.99259 8.18024 3.69451 8.18024 3.16285M8.18024 4.52424H9.49771M9.49771 4.52424V3.16285M9.49771 3.16285H7.21409C7.12626 3.16285 7.08387 3.14724 7.09332 3.04209V1.15371C7.09259 1.04976 7.11698 1.01424 7.21409 1H10.398C10.5078 1 10.553 1.03313 10.5627 1.09881V2.95425C10.5609 3.08782 10.5391 3.14071 10.4309 3.16285H9.49771ZM2.29552 9.68435C2.27421 9.76661 2.29552 9.79414 2.36139 9.79414H11.1116C11.2653 9.79414 11.2873 9.75023 11.2653 9.6624C11.2653 9.6624 10.5517 7.43367 10.5517 7.42269C10.5517 7.41171 10.537 7.41173 10.5297 7.41171C7.61936 7.40317 5.98082 7.40928 3.06404 7.40073C3.06404 7.40073 3.04209 7.38975 3.03111 7.42269C3.02013 7.45563 2.29552 9.68435 2.29552 9.68435ZM7.21409 11.8801C7.21409 12.1166 7.02239 12.3083 6.78591 12.3083C6.54943 12.3083 6.35773 12.1166 6.35773 11.8801C6.35773 11.6437 6.54943 11.452 6.78591 11.452C7.02239 11.452 7.21409 11.6437 7.21409 11.8801Z"
                            stroke="currentColor" stroke-width="0.6" stroke-linecap="round" />
                        </svg>
                        Hacer corte
                      </el-dropdown-item>
                      <div
                        v-if="isShowCahsOn && $page.props.auth.user.permissions.includes('Ver dinero en caja') && $page.props.auth?.user?.cash_register_id"
                        class="text-gray99 text-xs px-2 bg-grayF2 py-1">
                        <span>Efectivo actual en caja: </span>
                        <p class="flex items-center space-x-1"
                          :class="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn ? 'text-[#05688B]' : null">
                          <span>$ {{ localCurrentCash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                          <el-tooltip v-if="(localCurrentCash >= asignedCashRegister?.max_cash) && isMaxCashOn"
                            placement="bottom">
                            <template #content>
                              <p>
                                Se llegó al límite de dinero permitido en caja. <br>
                                Es recomendable hacer corte
                              </p>
                            </template>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                              stroke="currentColor" class="size-4">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                            </svg>
                          </el-tooltip>
                        </p>
                      </div>
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
            <div v-if="isScanOn" class="relative lg:w-1/2 mx-auto mb-4">
              <input v-model="scannerQuery" :disabled="scanning || syncingIDB" ref="scanInput" class="input w-full pl-9"
                placeholder="Escanea o teclea el código del producto" type="text">
              <i class="fa-solid fa-barcode text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>
            <!-- Pestañas -->
            <div class="lg:mx-7">
              <el-tabs v-model="editableTabsValue" type="card" class="demo-tabs">
                <div v-if="$page.props.auth.user.store.activated_modules.includes('Clientes')"
                  class="flex justify-between items-center">
                  <div class="flex items-center space-x-3 w-full md:w-1/2">
                    <p class="flex items-center space-x-1">
                      <svg width="11" height="10" viewBox="0 0 11 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                        class="size-4">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M3.4371 2.5C3.4371 2.00272 3.6544 1.52581 4.04119 1.17417C4.42798 0.822544 4.95259 0.625 5.4996 0.625C6.04661 0.625 6.57121 0.822544 6.958 1.17417C7.3448 1.52581 7.5621 2.00272 7.5621 2.5C7.5621 2.99728 7.3448 3.47419 6.958 3.82583C6.57121 4.17746 6.04661 4.375 5.4996 4.375C4.95259 4.375 4.42798 4.17746 4.04119 3.82583C3.6544 3.47419 3.4371 2.99728 3.4371 2.5ZM1.7188 8.37708C1.73426 7.47478 2.13939 6.61419 2.84675 5.98108C3.5541 5.34796 4.50694 4.9931 5.4996 4.9931C6.49225 4.9931 7.44509 5.34796 8.15245 5.98108C8.8598 6.61419 9.26493 7.47478 9.28039 8.37708C9.28158 8.43783 9.26327 8.49757 9.2277 8.549C9.19212 8.60043 9.14083 8.64132 9.0801 8.66667C7.9568 9.13488 6.73534 9.37652 5.4996 9.375C4.22268 9.375 3.00947 9.12167 1.9191 8.66667C1.85836 8.64132 1.80707 8.60043 1.7715 8.549C1.73592 8.49757 1.71761 8.43783 1.7188 8.37708Z"
                          fill="#373737" />
                      </svg>
                      <span>Cliente</span>
                    </p>
                    <el-select v-model="editableTabs[editableTabsValue - 1].client_id" clearable filterable
                      placeholder="Seleccione si es necesario" no-data-text="No hay opciones registradas"
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
                        <ThirthButton class="!text-[#9C0B0B] !bg-[#FFB8B8] !py-2 mb-2">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                          Limpiar lista
                        </ThirthButton>
                      </template>
                    </el-popconfirm>
                  </div>
                  <SaleTable @delete-product="deleteProduct" @create-gift-product="addGiftProduct"
                    :saleProducts="tab.saleProducts" :showFull="!showNoCodeProducts" />
                </el-tab-pane>
              </el-tabs>
            </div>
          </div>
          <!-- seccion de desgloce de montos -->
          <div class="lg:w-[30%]">
            <!-- buscador de productos -->
            <div>
              <el-autocomplete v-model="productFoundSelectedName" :fetch-suggestions="searchProducts" class="!w-full"
                placeholder="Buscar código o nombre de producto" @select="handleSelectFoundProduct()"
                :value-key="'name'" :loading="loading" :disabled="syncingIDB || scanning" ref="searchInput">
                <template #prefix>
                  <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </template>
                <template #default="{ item }">
                  <!-- Opciones en tienda de ropa, zapatería y boutique -->
                  <p v-if="$page.props.auth.user.store.type == 'Boutique / Tienda de Ropa / Zapatería'"
                    class="w-4/5 flex items-center space-x-2">
                    <i v-if="item.additional?.color.color" class="fa-solid fa-shirt text-xs"
                      :style="{ color: item.additional?.color.color }"></i>
                    <span>{{ item.name }}</span>
                    <span class="text-gray-400">
                      ({{ item.additional?.color.name }}-{{ item.additional?.size.name }})
                    </span>
                  </p>
                  <!-- Opciones en tienda de abarrotes -->
                  <div v-else>
                    <span style="float: left; font-size: 14px">{{ item.name }}</span>
                    <span style="float: right; color: #8492a6; font-size: 11px">{{ item.code }}</span>
                  </div>
                </template>
                <template #loading>
                  cargando...
                </template>
              </el-autocomplete>
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
                      ",") }} <span v-if="productFoundSelected.bulk_product" class="text-gray-800"> /{{
                      productFoundSelected.measure_unit === 'Kilogramo' ? 'Kg' : 'L' }}</span></p>
                </div>
                <!-- input de cantidad si el producto es a granel -->
                <div v-if="productFoundSelected?.bulk_product"
                  class="flex justify-between items-center mt-4 border border-[#D9D9D9] rounded-xl py-3 px-7">
                  <div>
                    <p class="text-gray-500 mb-1">Cantidad</p>
                    <el-input-number ref="quantitySelector" @keydown="handleKeydownQuantitySelector" v-model="quantity"
                      :min="0" :max="isInventoryOn ? productFoundSelected.current_stock : undefined" :precision="2">
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
                    <p class="text-[#5FCB1F] text-lg font-bold">${{ (quantity *
                      productFoundSelected.public_price)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                  </div>
                </div>

                <!-- input de cantidad si el producto no es a granel -->
                <div v-else class="flex justify-between items-center mt-4">
                  <p class="text-gray99 mb-1">Cantidad</p>
                  <el-input-number ref="quantitySelector" @keydown="handleKeydownQuantitySelector" v-model="quantity"
                    :min="0" :max="isInventoryOn ? productFoundSelected.current_stock : undefined" :precision="2">
                    <template #suffix>
                      <span v-if="productFoundSelected.measure_unit?.trim() === 'Kilogramo'">
                        {{ productFoundSelected.measure_unit?.trim() === 'Kilogramo' ? 'Kg' :
                          productFoundSelected.measure_unit?.trim() === 'Litro' ? 'L' : ''
                        }}
                      </span>
                    </template>
                  </el-input-number>
                </div>
                <!-- instrucciones de bascula -->
                <div v-if="isConnectedScale && productFoundSelected.bulk_product">
                  <p class="text-[#7A7A7A] mt-3 text-sm">Agrega la cantidad manual o coloca el producto sobre la bascula
                  </p>
                  <figure class="my-2 flex items-center justify-center select-none">
                    <img draggable="false" class="w-2/3 md:w-[40%] opacity-70"
                      src="@/../../public/images/EmptyScale.png" alt="Agregar peso">
                  </figure>
                </div>
                <div class="text-center mt-7">
                  <div v-if="productFoundSelected.current_stock == 0 && isInventoryOn" class="text-sm text-gray99 mb-2">
                    No te quedan existencias de este producto.
                    <!-- <p class="text-primary underline cursor-pointer">Clic para dar entrada del producto</p>  -->
                  </div>
                  <div class="flex items-center justify-center space-x-4">
                    <button ref="addButton" @click="addSaleProduct(productFoundSelected); productFoundSelected = null"
                      class="rounded-full !px-24 text-white bg-primary text-sm py-1 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:bg-gray-400 focus:outline-none transition-all ease-linear duration-200"
                      :disabled="quantity == 0">
                      Agregar
                    </button>

                    <el-tooltip v-if="productFoundSelected?.bulk_product && isConnectedScale"
                      content="Agregar cantidad manualmente" placement="bottom">
                      <button @click="stopReadingScale(); focusQuantitySelector()"
                        class="rounded-full flex items-center justify-center size-9 bg-gray-300 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-5">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                        </svg>
                      </button>
                    </el-tooltip>
                  </div>
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
            <!-- Total por cobrar escritorio-->
            <div v-if="editableTabs[editableTabsValue - 1]?.saleProducts?.length"
              class="hidden lg:block border border-grayD9 rounded-lg p-4 mt-5 text-xs lg:text-base">
              <div v-if="!editableTabs[editableTabsValue - 1].cash && !editableTabs[editableTabsValue - 1].credit">
                <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                  <p>Subtotal</p>
                  <p class="text-gray-99">$ <strong class="ml-3">{{
                    calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</strong></p>
                </div> -->
                <!-- <div v-if="isDiscountOn" class="flex items-center justify-between text-lg mx-5">
                  <p class="text-[#999999]">Descuento</p>
                  <el-input v-model="editableTabs[editableTabsValue - 1].discount" type="number" class="!w-24 !h-8"
                    placeholder="0.00">
                    <template #prefix>
                      <i class="fa-solid fa-dollar-sign"></i>
                    </template>
                  </el-input>
                </div> -->
                <div class="flex items-center justify-between text-xl mx-5">
                  <p class="font-bold">Total</p>
                  <p v-if="(calculateTotal() - editableTabs[editableTabsValue - 1].discount) < 0"
                    class="text-red-600 text-xs">
                    El descuento es más grande que el total</p>
                  <p v-else class="text-gray-99">
                    $ <strong class="ml-3">
                      {{ (calculateTotal() - editableTabs[editableTabsValue - 1].discount)?.toLocaleString('en-US',
                        {
                          minimumFractionDigits: 2
                        })
                      }}
                    </strong>
                  </p>
                </div>
                <!------- botones venta a crédito y al contado ------->
                <div class="text-center mt-7">
                  <p class="text-sm text-gray-400 text-left mb-3">Opciones de pago</p>
                  <div class="flex items-center justify-end space-x-4">
                    <PrimaryButton v-if="$page.props.auth.user.store.activated_modules.includes('Clientes')"
                      @click="creditPayment()"
                      :disabled="editableTabs[editableTabsValue - 1]?.saleProducts?.length == 0"
                      class="!px-4 !bg-[#baf09b] disabled:!bg-[#999999] !text-black">A crédito</PrimaryButton>
                    <PrimaryButton @click="cashPayment()"
                      :disabled="editableTabs[editableTabsValue - 1]?.saleProducts?.length == 0"
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
                    :disabled="editableTabs[editableTabsValue - 1]?.saleProducts?.length == 0 || (calculateTotal() - editableTabs[editableTabsValue - 1].discount) < 0 || !$page.props.auth?.user?.cash_register_id"
                    class="!rounded-full !px-24 !bg-[#5FCB1F] disabled:!bg-[#999999]">Cobrar</PrimaryButton>
                  <p v-if="!$page.props.auth?.user?.cash_register_id" class="text-sm text-red-600 mt-2">
                    Para cobrar asigna una caja registradora <span @click="showCashRegisterSelectionModal = true"
                      class="underline cursor-pointer text-primary">asignar una</span>
                  </p>
                </div> -->
              </div>
              <!-- cobrando pago al contado -->
              <!-- <div v-if="editableTabs[editableTabsValue - 1].cash">
                <p class="text-gray-99 text-center mb-3 text-2xl">Total $ <strong>{{ (calculateTotal() -
                  editableTabs[editableTabsValue - 1].discount)?.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                  }) }}</strong>
                </p>
                <div class="flex items-center justify-between mx-5 space-x-10 text-lg">
                  <p>Entregado</p>
                  <input v-model="editableTabs[editableTabsValue - 1].moneyReceived" @keydown.enter="store"
                    type="number" class="input !rounded-md w-1/3" ref="receivedInput" placeholder="$0.00">
                </div>
                <div class="flex items-center justify-between mx-5 my-2 relative text-lg">
                  <p>Cambio</p>
                  <p
                    v-if="(calculateTotal() - editableTabs[editableTabsValue - 1].discount) <= editableTabs[editableTabsValue - 1]?.moneyReceived">
                    ${{
                      (editableTabs[editableTabsValue - 1]?.moneyReceived - (calculateTotal() -
                        editableTabs[editableTabsValue - 1].discount)).toLocaleString('en-US', {
                          minimumFractionDigits: 2
                        }) }}</p>
                </div>
                <p v-if="((calculateTotal() - editableTabs[editableTabsValue - 1].discount) > editableTabs[editableTabsValue - 1]?.moneyReceived) && editableTabs[editableTabsValue - 1].moneyReceived"
                  class="text-base font-bold text-primary text-center mb-3">
                  La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total de compra.
                </p>
                <div class="flex space-x-2 justify-end">
                  <CancelButton @click="editableTabs[editableTabsValue - 1].cash = false">Cancelar</CancelButton>
                  <PrimaryButton :disabled="storeProcessing" @click="store" class="!rounded-full w-1/2">
                    <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Aceptar
                  </PrimaryButton>
                </div>
              </div> -->
              <!-- Cobrando a crédito  -->
              <div v-if="editableTabs[editableTabsValue - 1]?.credit">
                <div class="flex items-center justify-between">
                  <i @click="editableTabs[editableTabsValue - 1].credit = false; editableTabs[editableTabsValue - 1].deposit = null"
                    class="fa-solid fa-angle-left text-xs p-2 cursor-pointer"></i>
                  <p class="text-gray-99 text-xl">$ <strong class="ml-3">{{
                    calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                      ",") }}</strong></p>
                </div>
                <h3 class="text-lg font-bold">Registrar abono de la venta</h3>
                <div class="flex items-center justify-between space-x-7 my-3">
                  <div class="w-2/3">
                    <InputLabel value="Monto abonado (opcional)" class="!text-base ml-2 !text-gray-400" />
                    <el-input type="number" v-model="editableTabs[editableTabsValue - 1].deposit"
                      placeholder="ingresa el abono">
                      <template #prefix>
                        <i class="fa-solid fa-dollar-sign"></i>
                      </template>
                    </el-input>
                  </div>
                  <div class="w-1/3">
                    <InputLabel value="Saldo restante" class="!text-base !text-gray-400" />
                    <p v-if="(calculateTotal() - editableTabs[editableTabsValue - 1].deposit) > 0">${{
                      (calculateTotal() -
                        editableTabs[editableTabsValue - 1].deposit)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                          ",")
                    }}</p>
                    <p class="text-red-600 text-xs" v-else>La cantidad abonada debe de ser menor al monto total</p>
                  </div>
                </div>
                <div class="w-2/3 pr-5">
                  <InputLabel value="Fecha de vencimiento (opcional)" class="!text-base !text-gray-400 ml-2" />
                  <el-date-picker v-model="editableTabs[editableTabsValue - 1].limit_date" class="!w-full" type="date"
                    placeholder="Seleccione" :disabled-date="disabledDate" />
                </div>
                <!-- <div class="mt-3">
                  <InputLabel value="Notas (opcional)" class="!text-base ml-2 !text-gray-400" />
                  <el-input v-model="editableTabs[editableTabsValue - 1].deposit_notes"
                    :autosize="{ minRows: 3, maxRows: 5 }" type="textarea" placeholder="Escribe tus notas"
                    :maxlength="200" show-word-limit clearable />
                </div> -->
                <div class="flex items-center justify-end space-x-3 mt-4">
                  <PrimaryButton @click="editableTabs[editableTabsValue - 1].has_credit = true; checkClientExist()"
                    :disabled="(calculateTotal() - editableTabs[editableTabsValue - 1].deposit) < 0 || storeProcessing">
                    <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Finalizar venta
                  </PrimaryButton>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- lista de productos sin codigo -->
      <section v-if="isQuickNoCodeSelectionOn && noCodeProducts.length"
        class="border rounded-md mx-2 border-[#D9D9D9] bg-grayF2" :class="showNoCodeProducts ? 'h-[30%]' : 'h-[6%]'">
        <div class="mx-4">
          <button @click="showNoCodeProducts = !showNoCodeProducts" type="button"
            class="flex items-center justify-between text-primary w-full pt-3 text-xs lg:text-sm">
            <h1 class="text-black">Selección rápida para productos sin código</h1>
            <p class="flex items-center space-x-2 font-semibold">
              <span>{{ showNoCodeProducts ? 'Ocultar' : 'Mostrar' }}</span>
              <i class="fa-solid" :class="showNoCodeProducts ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
            </p>
          </button>
        </div>
        <div v-if="showNoCodeProducts" class="mt-2 px-3 py-1 overflow-auto h-[82%]">
          <div class="mb-2 relative">
            <input v-model="searchNoCodeProducts" @input="filterNoCodeProducts" placeholder="Buscar producto"
              ref="noCodeProductsInput" type="search"
              class="lg:w-1/3 h-8 pl-8 rounded-md bg-transparent text-gray-400 border border-gray-300 focus:ring-0 focus:border-primary transition-all ease-in-out duration-200 text-sm placeholder:text-sm">
            <i class="absolute left-2 top-2 fa-solid fa-magnifying-glass text-gray-400"></i>
          </div>
          <el-skeleton v-if="syncingIDB"
            class="col-span-full mt-2 px-3 py-1 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2" animated>
            <template #template>
              <el-skeleton-item v-for="item in 7" :key="item" class="!h-36" />
            </template>
          </el-skeleton>
          <div v-show="showNoCodeProducts" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2">
            <button @click="handleFastProductSelection(item)" type="button"
              v-for="(item, index) in filteredNoCodeProducts" :key="index"
              class="border border-[#D9D9D9] bg-white px-3 py-2 active:bg-grayF2 rounded-xl">
              <h2 class="text-xs text-center">{{ item.name }}</h2>
              <figure class="flex items-center justify-center h-10 lg:h-14">
                <img v-if="item.image_url" :src="item.image_url" :alt="item.name"
                  class="object-contain h-10 lg:h-14 mx-auto">
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
      <!-- Total por cobrar movil -->
      <section v-if="editableTabs[editableTabsValue - 1]?.saleProducts?.length"
        class="mt-1 mx-2 border border-grayD9 rounded-t-[13px] lg:hidden text-xs px-5 py-3">
        <div v-if="!editableTabs[editableTabsValue - 1].cash && !editableTabs[editableTabsValue - 1].credit">
          <div class="flex items-center justify-between text-base">
            <p class="font-bold">Total</p>
            <p class="text-gray-99">
              $ <strong class="ml-3">
                {{ (calculateTotal() - editableTabs[editableTabsValue - 1].discount)?.toLocaleString('en-US',
                  {
                    minimumFractionDigits: 2
                  })
                }}
              </strong>
            </p>
          </div>
          <!------- botones venta a crédito y al contado ------->
          <div class="text-xs">
            <p class="text-gray37">Opciones de pago</p>
            <div class="flex items-center justify-end space-x-2">
              <PrimaryButton v-if="$page.props.auth.user.store.activated_modules.includes('Clientes')"
                @click="creditPayment()" :disabled="editableTabs[editableTabsValue - 1]?.saleProducts?.length == 0"
                class="!px-6 !py-1 !bg-[#BDF891] disabled:!bg-[#999999] !text-black">A crédito</PrimaryButton>
              <PrimaryButton @click="cashPayment()"
                :disabled="editableTabs[editableTabsValue - 1]?.saleProducts?.length == 0"
                class="!px-6 !py-1 !bg-[#4DE515] text-white disabled:!bg-[#999999]">Al contado</PrimaryButton>
            </div>
            <p v-if="!$page.props.auth?.user?.cash_register_id" class="text-red-600 mt-2">
              Para cobrar asigna una caja registradora <span @click="showCashRegisterSelectionModal = true"
                class="underline cursor-pointer text-primary">asignar una</span>
            </p>
          </div>
        </div>
        <!-- cobrando pago al contado -->
        <!-- Cobrando a crédito  -->
        <div v-if="editableTabs[editableTabsValue - 1]?.credit">
          <div class="flex items-center justify-between">
            <i @click="editableTabs[editableTabsValue - 1].credit = false; editableTabs[editableTabsValue - 1].deposit = null"
              class="fa-solid fa-angle-left text-xs p-2 cursor-pointer"></i>
            <p class="text-gray-99 text-xl">$ <strong class="ml-3">{{
              calculateTotal().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                ",") }}</strong></p>
          </div>
          <h3 class="text-lg font-bold">Registrar abono de la venta</h3>
          <div class="flex items-center justify-between space-x-7 my-3">
            <div class="w-2/3">
              <InputLabel value="Monto abonado (opcional)" class="!text-base ml-2 !text-gray-400" />
              <el-input type="number" v-model="editableTabs[editableTabsValue - 1].deposit"
                placeholder="ingresa el abono">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
            </div>
            <div class="w-1/3">
              <InputLabel value="Saldo restante" class="!text-base !text-gray-400" />
              <p v-if="(calculateTotal() - editableTabs[editableTabsValue - 1].deposit) > 0">${{
                (calculateTotal() -
                  editableTabs[editableTabsValue - 1].deposit)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                    ",")
              }}</p>
              <p class="text-red-600 text-xs" v-else>La cantidad abonada debe de ser menor al monto total</p>
            </div>
          </div>
          <div class="w-2/3 pr-5">
            <InputLabel value="Fecha de vencimiento (opcional)" class="!text-base !text-gray-400 ml-2" />
            <el-date-picker v-model="editableTabs[editableTabsValue - 1].limit_date" class="!w-full" type="date"
              placeholder="Seleccione" :disabled-date="disabledDate" />
          </div>
          <!-- <div class="mt-3">
                  <InputLabel value="Notas (opcional)" class="!text-base ml-2 !text-gray-400" />
                  <el-input v-model="editableTabs[editableTabsValue - 1].deposit_notes"
                    :autosize="{ minRows: 3, maxRows: 5 }" type="textarea" placeholder="Escribe tus notas"
                    :maxlength="200" show-word-limit clearable />
                </div> -->
          <div class="flex items-center justify-end space-x-3 mt-4">
            <PrimaryButton @click="editableTabs[editableTabsValue - 1].has_credit = true; checkClientExist()"
              :disabled="(calculateTotal() - editableTabs[editableTabsValue - 1].deposit) < 0 || storeProcessing">
              <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Finalizar venta
            </PrimaryButton>
          </div>
        </div>
      </section>
    </main>

    <!-- modal de inmpresión -->
    <DialogModal :show="showPrintingModal" @close="showPrintingModal = false" max-width="md">
      <template #title> Impresión de ticket </template>
      <template #content>
        <div class="flex items-start space-x-4">
          <figure class="h-24">
            <img src="@/../../public/images/ticket.png" :draggable="false" class="select-none object-contain h-full"
              alt="Imagen de ticket de venta">
          </figure>
          <p class="w-2/3 text-base text-gray37">¿Desea imprimir el ticket de la venta?</p>
        </div>
      </template>
      <template #footer>
        <div class="flex items-center space-x-2">
          <CancelButton @click="showPrintingModal = false">No</CancelButton>
          <PrimaryButton @click="openPrintingTemplate" :disabled="clientForm.processing">Si, imprimir</PrimaryButton>
        </div>
      </template>
    </DialogModal>
    <!-- -------------- Modal finalizar venta (pago) starts----------------------- -->
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
          <button @click="paymentModalStep = 2; paymentMethod = 'Efectivo'; receivedInputFocus()" type="button"
            class="bg-[#E0FEC5] text-[#37672B] border border-[#D9D9D9] h-60 rounded-3xl p-3 hover:scale-105 transition-all ease-linear duration-200 flex flex-col justify-center items-center space-y-3">
            <p class="text-lg text-center font-bold">EFECTIVO</p>
            <img src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
          </button>
          <button @click="paymentModalStep = 3;; paymentMethod = 'Tarjeta'" type="button"
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
            <p class="font-bold"><span class="mr-4">$</span>{{ (calculateTotal() - editableTabs[editableTabsValue -
              1].discount)?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
          </div>
          <div v-if="!paymentConfirmed" class="mt-5 flex flex-col items-center space-y-3">
            <p class="text-center font-bold">¿Con cuánto paga el cliente?</p>
            <el-input-number ref="receivedInput" @keydown.enter="store"
              v-model="editableTabs[editableTabsValue - 1].moneyReceived" :min="1" :max="999999">
              <template #prefix>
                <span>$</span>
              </template>
            </el-input-number>
            <p class="pt-5 font-bold">Cambio</p>
            <div class="rounded-full border border-[#D9D9D9D] bg-[#E0FEC5] py-2 px-7 flex items-center justify-between">
              <p v-if="(calculateTotal() - editableTabs[editableTabsValue - 1].discount) <= editableTabs[editableTabsValue - 1]?.moneyReceived"
                class="font-bold">
                <span class="mr-5">$</span>
                {{
                  (editableTabs[editableTabsValue - 1]?.moneyReceived - (calculateTotal() -
                    editableTabs[editableTabsValue - 1].discount)).toLocaleString('en-US', {
                      minimumFractionDigits: 2
                    })
                }}
              </p>
            </div>
            <p v-if="((calculateTotal() - editableTabs[editableTabsValue - 1].discount) > editableTabs[editableTabsValue - 1]?.moneyReceived) && editableTabs[editableTabsValue - 1].moneyReceived"
              class="text-base font-bold text-red-600 text-center mb-3">
              La cantidad es insuficiente. Por favor, ingrese una cantidad igual o mayor al total de compra.
            </p>
            <div class="flex justify-center mt-7">
              <PrimaryButton :disabled="storeProcessing" class="!px-20" @click="store">
                <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Confirmar pago
              </PrimaryButton>
            </div>
          </div>

          <!-- Confirmacion de pago -->
          <template v-else>
            <div class="flex flex-col items-center space-y-4 animate-fade-in-up">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
            <p class="my-3 text-sm text-center">El sistema no procesa pagos con tarjeta. Usa tu terminal bancaria
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
              <p class="font-bold"><span class="mr-4">$</span>{{ (calculateTotal() - editableTabs[editableTabsValue
                -
                1].discount)?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
            </div>

            <div class="flex justify-center mt-7">
              <PrimaryButton @click="store" :disabled="storeProcessing" class="!px-20">
                <i v-if="storeProcessing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Confirmar pago
              </PrimaryButton>
            </div>
          </div>

          <!-- Confirmacion de pago -->
          <template v-else>
            <div class="flex flex-col items-center space-y-4 animate-fade-in-up">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <p class="text-green-600 font-bold text-lg">¡Pago realizado con éxito!</p>
            </div>
          </template>
        </section>

      </div>
    </Modal>
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
            <div class="w-52 space-y-2">
              <div class="flex items-center space-x-2">
                <img class="w-5" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
                <p>Total pagado con tarjeta</p>
              </div>
              <div class="flex items-center space-x-2">
                <img class="w-5" src="@/../../public/images/dollar.webp" alt="Pago con tarjeta">
                <p>Efectivo esperado en caja</p>
              </div>
              <p>Recuento manual</p>
              <p>Diferencia</p>
            </div>
            <div class="w-44 space-y-2">
              <div v-if="cutLoading">
                <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>
              <div class="space-y-[8px]" v-else>
                <p>${{ cutForm.totalStoreSale?.card?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <p>${{ (asignedCashRegister?.started_cash + cutForm.totalStoreSale?.cash + cutForm.totalOnlineSale +
                  cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              </div>
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
              :disabled="!cutForm.counted_cash || cutForm.processing || (cutForm.totalCashMovements == 0 && (cutForm.totalStoreSale?.cash + cutForm.totalStoreSale?.card) == 0)">
              Hacer corte</PrimaryButton>
          </div>
          <p v-if="cutForm.totalCashMovements == 0 && (cutForm.totalStoreSale?.cash + cutForm.totalStoreSale?.card) == 0"
            class="text-xs text-red-600 text-right">
            *Para hacer corte es necesario que haya almenos una venta o movimiento de caja registrado</p>
        </form>
      </div>
    </Modal>
    <!------------------- creacion rapida de productos ---------------------->
    <DialogModal :show="showCreateProductModal" @close="showCreateProductModal = false" max-width="lg">
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
        <div v-if="$page.props.auth.user.store.first_user.id != $page.props.auth.user.id">
          <p v-if="!maxCashNotificationSent && !maxCashNotifying">
            Puedes
            <button @click="sendMaxCashNotification" class="text-primary underline">
              notificar/volver a notificar a {{ $page.props.auth.user.store.first_user.email }}
            </button>
            para que realice el corte personalmente.
          </p>
          <p v-else-if="maxCashNotifying" class="flex items-center space-x-2">
            <i class="fa-solid fa-circle-notch fa-spin"></i>
            <span>Enviando notificación...</span>
          </p>
          <p v-else class="flex items-center space-x-2">
            <svg width="18" height="18" viewBox="0 0 54 43" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M12.5263 42.0011C8.73489 31.147 0.492597 22.5137 0.0263141 22.0011C-0.439969 21.4884 5.33881 20.5148 13.5263 29.0011C29.0463 11.4303 44.0918 -0.0470468 52.5263 0.00107837C52.8512 -0.0320255 53.3498 0.705849 53.0263 1.00108C34.3519 9.89275 24.0145 25.6913 15.0263 42.0011C14.9721 42.4953 12.5049 42.397 12.5263 42.0011Z"
                fill="#189203" />
            </svg>
            <span>Se ha enviado la notificación.</span>
          </p>
          <p class="mt-3">
            Si lo prefieres y estas autorizada(o), puedes realizar el corte presionando "Hacer corte"
          </p>
        </div>
        <p v-else>
          Realiza el corte de caja para no volver a mostrar este mensaje cada que registres una venta.
        </p>
      </template>
      <template #footer>
        <div class="flex items-center space-x-1">
          <CancelButton @click="showLimitCashModal = false" :disabled="maxCashNotifying">Ahora no</CancelButton>
          <PrimaryButton @click="showLimitCashModal = false; handleCashCut()" :disabled="maxCashNotifying">Hacer corte
          </PrimaryButton>
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
      maxCashNotificationSent: false,
      maxCashNotifying: false,

      // conexion a internet
      isOnline: navigator.onLine, // Verificar el estado de conexión al cargar el componente
      syncingData: false,
      syncingIDB: false,

      // modales
      showLimitCashModal: false, //muestra u oculta el modal de excedencia de dinero permitido en caja
      showClientFormModal: false, //muestra u oculta el modal de creación de cliente
      showCashRegisterMoney: true, //muestra u oculta el dinero de caja
      showClientConfirmModal: false, //muestra u oculta el modal de peticion de cliente para venta a crédito
      showCreateProductModal: false, //muestra u oculta el modal de creación rápida de producto
      showPaymentModal: false, //muestra u oculta el modal de pago al finalizar la venta
      showPrintingModal: false,

      // generales
      paymentMethod: '', //Método de pago seleccionado
      paymentConfirmed: false, //indica si el pago ha sido confirmado
      paymentModalStep: 1, //paso del modal de pago
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
      // Imprimir automáticamente el ticket de venta despues de finalizarla
      automaticPrinting: this.$page.props.auth.user.store.settings.find(item => item.name == 'Impresión automática de tickets')?.value,

      // cargas
      storeProcessing: false, //cargando store de venta
      scanning: false, //cargando la busqueda de productos por escaner
      loading: false, //cargando la busqueda de productos
      creatingProduct: false,
      cutLoading: false, //cargando monto total esperado para corte
      scannerQuery: null, //input para scanear el codigo de producto

      //impresión
      folioToPrint: null,

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
          deposit: null,
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
          deposit: null,
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
          deposit: null,
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
    handleFastProductSelection(item) {
      if (item.bulk_product) {
        this.productFoundSelected = item; //asigna el producto seleccionado a la variable productFoundSelected
        this.quantity = 1; //asigna la cantidad por defecto a 1
        this.handleScale(); //ejecuta el manejador de bascula si el producto es a granel
      } else {
        item.imageUrl = item.image_url;
        focuseInputAfterAdd = false;
        this.addSaleProduct(item, focuseInputAfterAdd); //agrega el producto a la lista de venta
      }
    },
    filterNoCodeProducts() {
      if (this.searchNoCodeProducts) {
        this.filteredNoCodeProducts = this.noCodeProducts.filter(product =>
          product.name.toLowerCase().includes(this.searchNoCodeProducts.toLowerCase())
        );
      } else {
        this.filteredNoCodeProducts = this.noCodeProducts;
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
      let product = this.productsFound.find(product => product.name === this.productFoundSelectedName);

      // crear link virtual de imagen blob si es que tiene imagen el producto
      if (product.image && !product.imageUrl) {
        const imageUrl = URL.createObjectURL(product.image);
        product = { ...product, imageUrl };
      }

      this.productFoundSelected = product;

      // revisar si hay stock del producto para dejar 1 como default a vender
      if ((product.current_stock && this.isInventoryOn) || !this.isInventoryOn) {
        this.quantity = 1;
      } else {
        this.quantity = 0;
      }

      // si el producto es a granel y en las configuraciones está activada la báscula se ejecuta el manejador de bascula
      if (this.productFoundSelected.bulk_product) {
        //se ejecuta el manejador de bascula si el producto es a granel
        this.handleScale();
      }
      this.focusQuantitySelector(); //enfoca el selector de cantidad
      this.productFoundSelectedName = null; //borra el nombre buscado para que no se muestre la misma opcion
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
          paymentMethod: this.paymentMethod, //método de pago seleccionado
        });
        if (response.status === 200) {

          this.paymentConfirmed = true; //indica que el pago ha sido confirmado
          setTimeout(() => {
            this.paymentConfirmed = false;
            // Aquí cierra el modal como lo manejes normalmente
            this.showPaymentModal = false; // ajusta este método a tu implementación
            this.paymentModalStep = 1; //reinicia el paso del modal de pago
          }, 1000);

          this.updateCurrentStockInIndexedDB();
          this.clearTab();
          this.fetchCashRegister();

          localStorage.setItem('pendentProcess', false);

          //abrir modal de impresión automáticamente cuando esta la opción activada en config/impresora
          if (this.automaticPrinting) {
            this.showPrintingModal = true;
            this.folioToPrint = response.data.folio_stored;
          }
        }
      } catch (error) {
        console.error(error);
      }
    },
    openPrintingTemplate() {
      window.open(route('sales.print-ticket', this.folioToPrint), '_blank');
      this.showPrintingModal = false;
      this.folioToPrint = null;
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
      this.inputFocus(); //enfoca el input de busqueda
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
    async sendMaxCashNotification() {
      try {
        this.maxCashNotifying = true;
        const response = await axios.get(route('cash-registers.max-cash-notify'));
        if (response.status === 200) {
          this.maxCashNotifying = false;
          this.maxCashNotificationSent = true;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.maxCashNotifying = false;
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
      this.cutForm.difference = (this.cutForm.totalStoreSale?.cash + this.cutForm.totalOnlineSale + this.cutForm.totalCashMovements + this.asignedCashRegister?.started_cash) - this.cutForm.counted_cash
    },
    deleteProduct(productId, isGift = false) {
      const indexToDelete = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => sale.product.id === productId);

      if (indexToDelete != -1) {
        // remover regalo si es que el producto es un regalo
        let existingSale = this.editableTabs[this.editableTabsValue - 1].saleProducts[indexToDelete];
        if (isGift && this.editableTabs[this.editableTabsValue - 1].saleProducts[indexToDelete].quantity > existingSale.giftQuantity) {
          // Si hay más de una unidad, simplemente reduce la cantidad regalada
          existingSale.quantity -= existingSale.giftQuantity;
          existingSale.giftQuantity = null; // Resetea la cantidad de regalo a null
          existingSale.product.discounted_price = null; // Resetea la cantidad de regalo a null
        } else {
          this.editableTabs[this.editableTabsValue - 1].saleProducts.splice(indexToDelete, 1);
        }
      }
    },
    cashPayment() {
      this.showPaymentModal = true; //abre el modal de seleccion de pago (efectivo o tarjeta)
    },
    creditPayment() {
      this.editableTabs[this.editableTabsValue - 1].credit = true;
    },
    async fetchCashRegister() {
      try {
        const response = await axios.get(route('cash-registers.fetch-cash-register', this.asignedCashRegister?.id));
        if (response.status === 200) {
          this.localCurrentCash = response.data.item.current_cash;
          // Mostrar limite de caja alcanzado
          if ((this.localCurrentCash >= this.asignedCashRegister?.max_cash) && this.isMaxCashOn && this.isOnline) {
            this.maxCashNotificationSent = false;
            this.showLimitCashModal = true;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async searchProducts(query, cb) {
      this.searchQuery = query;
      this.loading = true; // Activa el estado de carga
      try {
        // Ajustar con cuántas letras comienza a buscar
        if (this.searchQuery.length < 1) {
          cb([]); // Devuelve un arreglo vacío si no hay suficientes caracteres
          return;
        }

        const results = await getItemByPartialAttributes('products', {
          name: this.searchQuery,
          code: this.searchQuery,
        });

        this.productsFound = await getItemByPartialAttributes('products', { name: this.searchQuery, code: this.searchQuery });
        cb(results); // Llama al callback con los resultados

      } catch (error) {
        console.error(error);
        cb([]); // En caso de error, también devuelve un arreglo vacío
      } finally {
        this.loading = false; // Desactiva el estado de carga
      }
    },
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
    async addGiftProduct(product) {
      try {
        const results = await getItemByAttributes('products', {
          name: product.name,
        });

        let giftable = results.length ? results[0] : null;
        if (!giftable) {
          return;
        } else {
          // editar el precio a 0 para que no se cobre el producto
          giftable.discounted_price = 0;
          // agregar prop para ecitar conflicto al mostrar imagen
          giftable.imageUrl = giftable.image_url;
        }

        //revisa si el producto a agregar ya esta dentro del arreglo
        const existingIndex = this.editableTabs[this.editableTabsValue - 1].saleProducts.findIndex(sale => {
          return sale.product.id == giftable.id;
        });
        if (existingIndex !== -1) {
          let existingSale = this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex];
          existingSale.quantity += product.quantity; // Aumenta la cantidad del producto existente
          existingSale.giftQuantity = product.quantity; // guardar la cantidad regalada
          // calcular precio unitario con descuento tomando en cuenta la cantidad regalada
          const pricePerUnit =
            (existingSale.product.public_price * (existingSale.quantity - existingSale.giftQuantity)) / existingSale.quantity;
          existingSale.product.discounted_price = Math.round(pricePerUnit * 100) / 100; // redondear a 2 decimales
        } else {
          // Si el producto no existe, agrégalo al array
          this.editableTabs[this.editableTabsValue - 1].saleProducts.push({
            product: giftable,
            quantity: product.quantity,
            giftQuantity: product.quantity,
            originalPrice: null,
          });
        }

        // indicar al navegador mediante el local storage que hay proceso pendiente
        const pendentProcess = JSON.parse(localStorage.getItem('pendentProcess'));
        if (!pendentProcess) {
          // guardar el valor en el localStorage
          localStorage.setItem('pendentProcess', true);
        }
      } catch (error) {
        console.error(error);
      }
    },
    addSaleProduct(product, focusInputAfterAdd = true) {
      const existingIndex = this.editableTabs[this.editableTabsValue - 1].saleProducts?.findIndex(sale => {
        return sale.product.id == product.id;
      });

      let existingSale = this.editableTabs[this.editableTabsValue - 1].saleProducts[existingIndex];
      //revisa si el producto a agregar ya esta dentro del arreglo
      if (existingIndex !== -1) {
        existingSale.quantity += this.quantity; // Aumenta la cantidad del producto existente
        if (existingSale.giftQuantity) {
          // Si es un regalo, se calcula el precio
          const pricePerUnit =
            (existingSale.product.public_price * (existingSale.quantity - existingSale.giftQuantity)) / existingSale.quantity;
          existingSale.product.discounted_price = Math.round(pricePerUnit * 100) / 100; // redondear a 2 decimales
        } else {
          existingSale.giftQuantity = null; // Resetea la cantidad de regalo si no es un regalo
        }
      } else {
        // Si el producto no existe, agrégalo al array
        this.editableTabs[this.editableTabsValue - 1].saleProducts.push({
          product: product,
          quantity: this.quantity,
          originalPrice: null,
        });
      }

      // si esta leyendo de la báscula se detiene la lectura
      if (this.isReadingScale) {
        this.stopReadingScale();
      }
      this.scannerQuery = null;
      this.quantity = 1;
      this.scanning = false;
      this.productFoundSelected = null;

      if (focusInputAfterAdd) {
        this.inputFocus();
      }

      if (this.isReadingScale) {
        this.stopReadingScale(); //detiene la lectura de la báscula
      }

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
        let priceToUse = sale.product.public_price;
        // Si el producto tiene un precio con descuento, se usa ese precio
        if (sale.product.discounted_price != null) {
          priceToUse = sale.product.discounted_price;
        }

        return accumulator + priceToUse * sale.quantity;
      }, 0);

      // Formatear el resultado al final
      // return total?.toLocaleString('en-US', { minimumFractionDigits: 2 }); formatea el total con comas pero me manda a NaN despues de 1000
      return Math.round(total * 10) / 10; // redondea a un decimal
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
        this.$refs.receivedInput.focus(); // Enfocar el input de recibido cuando se abre el modal
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
        paymentMethod: this.paymentMethod, //método de pago seleccionado
        // deposit_notes: this.editableTabs[this.editableTabsValue - 1]?.deposit_notes, //notas de venta a crédito
        limit_date: this.editableTabs[this.editableTabsValue - 1]?.limit_date ?? null, //fecha límite de crédito
      };

      // Agrega el nuevo objeto al arreglo
      storedData.push(dataToStore);

      this.paymentConfirmed = true; //indica que el pago ha sido confirmado
      setTimeout(() => {
        this.paymentConfirmed = false;
        // Aquí cierra el modal como lo manejes normalmente
        this.showPaymentModal = false; // ajusta este método a tu implementación
        this.paymentModalStep = 1; //reinicia el paso del modal de pago
      }, 1000);


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
        this.startReadingScale();
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
        alert("No se pudo conectar la báscula porque el puerto ya estaba abierto. Intenta nuevamente");
      } finally {
        // si el producto seleccionado es a granel y la báscula está activada se inicia la lectura
        if (this.productFoundSelected?.bulk_product) {
          this.startReadingScale();
        }
      }
    },
    async startReadingScale() {
      if (!this.port || !this.port.readable || !this.port.writable) {
        alert("Conecta la báscula primero.");
        return;
      }

      if (this.isReadingScale) {
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
            this.stopReadingScale(); // Detiene la lectura si es el final
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

      this.isReadingScale = false;
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
    },
    parseWeight(data) {
      // Ajustar esta lógica según el formato de los datos enviados por la báscula
      const weight = data.trim(); // Quitar espacios en blanco
      return parseFloat(weight) || "0.00";
    },
    handleKeydownGlobal(event) {
      if (event.key === "ArrowRight") {
        this.focusSearchInput();
        this.scannerQuery = null; //borra el simbolo que se escribe en el input de escaner
      } else if (event.key === "Enter") {
        this.getProductByCode();
      } else if (event.key === "Shift" && this.editableTabs[this.editableTabsValue - 1]?.saleProducts?.length) {
        this.cashPayment();
      } else if (event.key === "Control") {
        this.$refs.scanInput.focus();
      } else if (event.key === "Escape") {
        this.showNoCodeProducts = false; //cierra el buscador de productos sin código
        this.editableTabs[this.editableTabsValue - 1].cash = false;
      } else if (event.key === "+") {
        this.showNoCodeProducts = true;
        setTimeout(() => {
          this.$nextTick(() => {
            this.$refs.noCodeProductsInput.focus(); // Enfocar el input de código cuando se abre el modal
          });
          this.scannerQuery = null; //borra el simbolo que se escribe en el input de escaner
        }, 100);
      }
    },
  },
  async mounted() {
    //ejecuta un listener para atajos de teclado
    window.addEventListener("keydown", this.handleKeydownGlobal);

    // redirigir a los tutoriales si no los ha finalizado
    if (!this.$page.props.auth.user.tutorials_seen) {
      this.$inertia.visit(route('started-tutorial'));
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
    window.removeEventListener("keydown", this.handleKeydownGlobal);
  },
}
</script>

<style>
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
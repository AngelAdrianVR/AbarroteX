<template>
  <Loading v-if="loading" />
  <div v-else class="min-h-32 mb-6">
    <div v-if="total_cash_registers > 1" class="text-center">
      <p v-if="!cash_register.is_active" class="text-red-500 px-2 bg-red-50 self-start">Caja deshabilitada</p>
      <p v-else class="text-green-500 px-2 bg-green-50 self-start">Caja Habilitada</p>
    </div>
    <section class="flex flex-col md:flex-row justify-between space-y-2 md:space-x-3 mt-2">
      <!-- Boton para activar/desactivar caja registradora -->
      <el-tooltip v-if="canDelete && total_cash_registers > 1" :content="cash_register.is_active
        ? 'Desactivar caja. Si no planeas utilizar esta caja, puedes desactivarla para evitar que los usuarios accedan a ella.'
        : 'Habilitar caja para volver a ponerla en funcionamiento'" placement="right">
        <div class="flex items-center space-x-4 text-sm">
          <button class="flex justify-center items-center rounded-full size-8 bg-grayF2 active:scale-90 self-start">
            <i @click="form.is_active = false; update()" v-if="cash_register.is_active"
              class="fa-solid fa-ban text-primary"></i>
            <i v-else @click="form.is_active = true; update()" class="fa-solid fa-check text-primary"></i>
          </button>
          <p v-if="!cash_register.is_active" class="text-gray99 w-60">
            <i class="fa-solid fa-arrow-left-long mr-3"></i>Esta caja está deshabilitada, por lo que los usuarios no
            pueden utilizarla.
            Para habilitarla, haz clic en el botón
          </p>
        </div>
      </el-tooltip>
      <!-- span vacio para acomodar los botones del lado derecho cuando no hay opcion de habilitar y deshabilitar caja (mala praxis?) -->
      <span v-else></span>

      <div class="flex space-x-3 items-center self-start">
        <!-- Eliminar caja -->
        <el-popconfirm v-if="canDelete && total_cash_registers > 1" confirm-button-text="Si" cancel-button-text="No"
          icon-color="#C30303" title="Se eliminará la caja registradora. ¿Deseas continuar?"
          @confirm="deleteCashRegister()">
          <template #reference>
            <i
              class="fa-regular fa-trash-can mr-3 text-primary text-sm bg-[#F2F2F2] rounded-full py-1 px-[7px] cursor-pointer"></i>
          </template>
        </el-popconfirm>
        <el-dropdown v-if="canRegisterMovements" :disabled="!cash_register.is_active" split-button type="primary"
          @click="handleCashCut">
          Hacer corte de caja
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'"><i
                  class="fa-solid fa-arrow-up mr-3"></i>Ingresar efectivo</el-dropdown-item>
              <el-dropdown-item @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'"><i
                  class="fa-solid fa-arrow-down mr-3"></i>Retirar efectivo</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>
      </div>
    </section>

    <!-- Información de caja -->
    <section class="lg:flex lg:space-x-7 md:w-full xl:w-[90%] mx-auto text-xs md:text-sm mt-7">
      <div class="w-full border border-gray-300 rounded-2xl shadow-lg bg-white p-1 self-start transition-all ease-linear duration-200">
        <div class="p-2 md:p-4 grid grid-cols-4 gap-x-2 gap-y-1">
          
          <!-- Efectivo -->
          <div class="flex items-center space-x-2 col-span-full pb-2">
            <img class="w-7" src="@/../../public/images/dollar.webp" alt="Pago en efectivo">
            <p class="font-bold text-gray-600">Efectivo esperado en caja</p>
          </div>
          <p class="text-[#373737] col-span-3">Efectivo inicial</p>
          <p class="text-[#373737]"><span class="text-[#373737] mr-3 ml-[17px]">$</span>{{
            cash_register.started_cash?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
          <p class="text-[#373737] col-span-3">Ventas en tienda</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="text-[#373737]"><span class="text-[#373737] mr-3 ml-[17px]">$</span>{{
            cutForm.totalStoreSale?.cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          <p v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')" class="text-[#373737] col-span-3">Pedidos en línea</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')" class="text-[#373737]"><span
              class="text-[#373737] mr-3 ml-[17px]">$</span>{{
                cutForm.totalOnlineSale?.cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          <p v-if="$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio')" class="text-[#373737] col-span-3">Ordenes de servicio</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else-if="$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio')" class="text-[#373737]"><span
              class="text-[#373737] mr-3 ml-[17px]">$</span>{{
                cutForm.totalServiceOrders?.cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          
          <p class="text-[#999999] col-span-3 text-right">Subtotal de ventas</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="text-[#999999] pl-4"><span class="mr-3">$</span>{{
            (cash_register.started_cash 
              + cutForm.totalStoreSale?.cash 
              + cutForm.totalOnlineSale?.cash 
              + cutForm.totalServiceOrders?.cash)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>



          <!-- Movimientos de caja -->
          <p v-if="currentMovements?.length" @click="showcashRegisterMovements = !showcashRegisterMovements"
            class="text-primary flex items-center cursor-pointer col-span-full pt-5">Movimientos de caja
            <i :class="showcashRegisterMovements ? 'fa-angle-down' : 'fa-angle-up'" class="fa-solid ml-4"></i>
          </p>
          <div v-if="showcashRegisterMovements" v-for="cashRegisterMovement in currentMovements"
            :key="cashRegisterMovement"
            :title="cashRegisterMovement.type + ' - Motivo: ' + (cashRegisterMovement.notes ?? 'no registrado') + ' • ' + formatDateHour(cashRegisterMovement.created_at)"
            class="text-gray99 col-span-full flex items-center space-x-2">
            <p class="truncate w-3/4">{{ cashRegisterMovement.type + ' - Motivo: ' + (cashRegisterMovement.notes ??
              'no registrado') + ' • ' + formatDateHour(cashRegisterMovement.created_at) }}</p>
            <p class="text-gray99 w-1/4">
              <i :class="cashRegisterMovement.type === 'Ingreso' ? 'ml-[10px]' : 'fa-minus text-gray-500'"
                class="fa-solid text-xs px-1"></i>
              <span class="text-gray99 mr-3">$</span>{{
                cashRegisterMovement.amount?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </p>
          </div>
          <p class="text-[#373737] font-bold col-span-3 text-right py-2">Total en efectivo</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="text-[#373737] font-bold pl-4 py-2"><span class="mr-3">$</span>{{
             (cash_register.started_cash 
             + cutForm.totalStoreSale?.cash 
             + cutForm.totalOnlineSale?.cash 
             + cutForm.totalServiceOrders?.cash 
             + cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>



          <!-- Monto en tarjeta -->
          <div class="flex items-center space-x-2 col-span-full py-2 border-t border-[#D9D9D9]">
            <img class="w-7" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
            <p class="font-bold text-gray-600">Monto esperado en tarjeta</p>
          </div>
          <p class="text-[#373737] col-span-3">Ventas en tienda</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="text-[#373737]"><span class="text-[#373737] mr-3 ml-[17px]">$</span>{{
            cutForm.totalStoreSale?.card?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          <p v-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')" class="text-[#373737] col-span-3">Pedidos en línea</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else-if="$page.props.auth.user.store.activated_modules.includes('Tienda en línea')" class="text-[#373737]"><span
              class="text-[#373737] mr-3 ml-[17px]">$</span>{{
                cutForm.totalOnlineSale?.card?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          <p v-if="$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio')" class="text-[#373737] col-span-3">Ordenes de servicio</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else-if="$page.props.auth.user.store.activated_modules.includes('Ordenes de servicio')" class="text-[#373737]"><span
              class="text-[#373737] mr-3 ml-[17px]">$</span>{{
                cutForm.totalServiceOrders?.card?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
          <p class="font-bold text-[#373737] col-span-3 text-right">Total en tarjeta</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="font-bold text-[#373737] mb-3 pl-4"><span class="mr-3">$</span>{{
            (cutForm.totalStoreSale?.card 
                  + cutForm.totalOnlineSale?.card
                  + cutForm.totalServiceOrders?.card )?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</p>
        </div>

        <footer class="bg-[#F2F2F2] font-bold py-2 rounded-xl flex px-2">
          <p class="w-3/4 text-right pr-7">Total efectivo + tarjeta</p>
          <div v-if="cutLoading">
            <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
          </div>
          <p v-else class="w-1/4 pl-4">
            <span class="mr-3">
              $
            </span>
            <b>{{ (cutForm.totalStoreSale?.card 
                  + cutForm.totalOnlineSale?.card
                  + cutForm.totalServiceOrders?.card
                  + cash_register.started_cash 
                  + cutForm.totalStoreSale?.cash 
                  + cutForm.totalOnlineSale?.cash 
                  + cutForm.totalServiceOrders?.cash 
                  + cutForm.totalCashMovements )?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0.00' }}</b>
          </p>
        </footer>
      </div>

      <!-- Lado derecho -->
      <div v-if="canEdit"
        class="w-full lg:w-[450px] space-y-3 bg-[#F7F7F7] rounded-2xl shadow-lg border border-gray-grayD9 p-3 my-4 lg:mt-0 self-start">
        <p class="font-bold text-center">Ajustes generales</p>
        <!-- Editar cantidad maxima permitida en caja -->
        <div v-if="isMaxCashOn" class="py-3 mx-auto lg:mx-0 border border-grayD9 rounded-lg self-start relative">
          <h2 class="py-2 text-center text-sm font-bold">Efectivo máximo en caja</h2>
          <i v-if="!edit_max_cash" @click="editMaxCash"
            class="fa-solid fa-pen text-xs text-primary cursor-pointer hover:bg-gray-200 rounded-full py-1 px-[5px] absolute top-2 right-2"></i>
          <p v-if="!edit_max_cash" class="text-center mb-1">$ {{
            cash_register.max_cash?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
          <div v-else>
            <el-input v-model="form.max_cash" type="text" placeholder="Ingresa el monto" class="px-10"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\D/g, '')" @keydown.enter="update">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <InputError :message="form.errors.max_cash" />
            <div class="flex justify-center space-x-4 my-2">
              <el-tooltip content="Cancelar" placement="left">
                <button @click="edit_max_cash = false;"
                  class="text-gray-600 text-[11px] bg-gray-100 transition-all rounded-full size-7 duration-150 border border-grayD9">
                  <i class="fa-solid fa-x pr-[1px] pt-[5px]"></i>
                </button>
              </el-tooltip>
              <el-tooltip content="Guardar" placement="right">
                <button @click="update"
                  class="text-green-600 text-[11px] bg-green-100 transition-all size-7 rounded-full duration-150 border border-grayD9"><i
                    class="fa-solid fa-check pr-[1px] pt-[5px]"></i></button>
              </el-tooltip>
            </div>
          </div>
          <p class="text-gray99 text-xs text-center">Se notificará cuando haya alcanzado el máximo permitido para hacer
            corte de caja.</p>
        </div>

        <!-- Editar nombre de caja -->
        <div class="mt-7 py-3 lg:mt-0 mx-auto lg:mx-0 border border-grayD9 rounded-lg self-start relative">
          <h2 class="py-2 text-center text-sm font-bold">Nombre de caja</h2>
          <i v-if="!edit_cash_register_name" @click="editName"
            class="fa-solid fa-pen text-xs text-primary cursor-pointer hover:bg-gray-200 rounded-full py-1 px-[5px] absolute top-2 right-2"></i>
          <p v-if="!edit_cash_register_name" class="text-center mb-1">{{ cash_register.name }}</p>
          <div v-else>
            <el-input v-model="form.name" type="text" placeholder="Ingresa el nuevo nombre" class="px-10"
              @keydown.enter="update">
            </el-input>
            <InputError :message="form.errors.name" />
            <div class="flex justify-center space-x-4 my-2">
              <el-tooltip content="Cancelar" placement="left">
                <button @click="edit_cash_register_name = false;"
                  class="text-gray-600 text-[11px] bg-gray-100 transition-all rounded-full size-7 duration-150 border border-grayD9">
                  <i class="fa-solid fa-x pr-[1px] pt-[5px]"></i>
                </button>
              </el-tooltip>
              <el-tooltip content="Guardar" placement="right">
                <button @click="update"
                  class="text-green-600 text-[11px] bg-green-100 transition-all size-7 rounded-full duration-150 border border-grayD9"><i
                    class="fa-solid fa-check pr-[1px] pt-[5px]"></i></button>
              </el-tooltip>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <!-- -------------- Modal Ingreso o retiro de dinero en caja starts----------------------- -->
  <Modal :show="cashRegisterModal" @close="cashRegisterModal = false">
    <div class="p-4 relative">
      <i @click="cashRegisterModal = false"
        class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

      <form class="mt-5 mb-2 md:grid grid-cols-2 gap-3" @submit.prevent="storeCashRegisterMovement">
        <h2 v-if="form.cashRegisterMovementType === 'Ingreso'" class="font-bold col-span-full">Ingresar efectivo a
          caja
        </h2>
        <h2 v-if="form.cashRegisterMovementType === 'Retiro'" class="font-bold col-span-full">Retirar efectivo a caja
        </h2>

        <div class="mt-2 col-span-full">
          <InputLabel v-if="form.cashRegisterMovementType === 'Ingreso'" value="Monto a ingresar*"
            class="ml-3 mb-1 text-sm" />
          <InputLabel v-if="form.cashRegisterMovementType === 'Retiro'" value="Monto a retirar*"
            class="ml-3 mb-1 text-sm" />
          <el-input @keydown.enter="storeCashRegisterMovement" v-model="form.registerAmount" type="text"
            placeholder="ingresa el monto" :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
            :parser="(value) => value.replace(/[^\d.]/g, '')">
            <template #prefix>
              <i class="fa-solid fa-dollar-sign"></i>
            </template>
          </el-input>
          <p class="text-red-500 text-xs"
            v-if="form.cashRegisterMovementType === 'Retiro' && form.registerAmount > cash_register.current_cash">
            *El monto no debe exceder el dinero actual de tu caja (${{ cash_register.current_cash }})
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
            :disabled="!form.registerAmount || form.processing || (form.cashRegisterMovementType === 'Retiro' && form.registerAmount > cash_register.current_cash)">
            Confirmar</PrimaryButton>
        </div>
      </form>
    </div>
  </Modal>
  <!-- --------------------------- Modal Ingreso o retiro de dinero en caja ends ------------------------------------ -->

  <!-- -------------- Modal corte de caja starts----------------------- -->
  <Modal :show="cashCutModal" @close="cashCutModal = false">
    <div class="p-4 relative">
      <i @click="cashCutModal = false"
        class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

      <form class="mt-5 mb-2" @submit.prevent="storeCashCut">
        <h2 class="font-bold col-span-full">Hacer corte de caja</h2>
        <p class="col-span-full text-sm md:text-base">Por favor, cuenta el efectivo en caja y verifica el monto total en tu tarjeta. Ingresa ambos valores para realizar el corte.</p>
        <div class="rounded-full h-3 bg-[#F2F2F2] my-2"></div>

        <!-- Efectivo -->
        <div class="flex items-center justify-end space-x-2 mb-2">
          <img class="w-5" src="@/../../public/images/dollar.webp" alt="Pago con tarjeta">
          <p class="text-sm md:text-base font-semibold text-[#373737]">Efectivo en caja</p>
        </div>
        <section class="w-full flex justify-end space-x-5 text-xs md:text-sm border-b border-gray-200 pb-3">
          <div class="space-y-2 text-right">
            <p>Efectivo esperado en caja</p>
            <p>Recuento manual de caja</p>
            <p>Diferencia</p>
            <p class="opacity-0">status</p>
            <p v-if="cutForm.counted_cash">Monto a retirar</p>
            <p v-if="cutForm.counted_cash">Efectivo que dejas en caja</p>
          </div>
          <div class="space-y-2 text-right w-36">
            <div v-if="cutLoading">
              <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div class="space-y-[8px]" v-else>
              <div class="flex justify-between">
                <span class="pl-3">$</span>
                <p>{{ (cash_register.started_cash 
                      + cutForm.totalStoreSale?.cash 
                      + cutForm.totalOnlineSale?.cash
                      + cutForm.totalServiceOrders?.cash
                      + cutForm.totalCashMovements)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              </div>
            </div>
            <el-input @input="differenceCash()" v-model="cutForm.counted_cash" type="text" placeholder="0.00"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" class="!w-full !h-6">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <p v-if="cutForm.counted_cash" :class="{
              'text-green-500': (cutForm.difference_cash) === 0,
              'text-blue-500': (cutForm.difference_cash) < 0,
              'text-red-500': (cutForm.difference_cash) > 0
            }">
              <!-- Se multiplica por -1 para cambiar el signo y si sobra sea positivo y si falta negativo -->
              ${{ (cutForm.difference_cash * -1)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </p>
            <p v-if="cutForm.counted_cash" :class="{
              'text-green-500 bg-green-100': (cutForm.difference_cash) === 0,
              'text-blue-500 bg-blue-100': (cutForm.difference_cash) < 0,
              'text-red-500 bg-red-100': (cutForm.difference_cash) > 0
            }" class="rounded-full text-xs inline py-[2px] px-2 w-full">
              <!-- Icono de proveedor de verificación si la diferencia es 0 -->
              <i v-if="(cutForm.difference_cash) === 0" class="fa-solid fa-check mr-1"></i>
              <!-- Icono de sobrante en caja si la diferencia es negativa -->
              <i v-else-if="(cutForm.difference_cash) < 0" class="fa-solid fa-plus mr-1"></i>
              <!-- Icono de faltante de efectivo si la diferencia es positiva -->
              <i v-else class="fa-solid fa-xmark mr-1"></i>
              <!-- Muestra el mensaje correspondiente -->
              {{ (cutForm.difference_cash) === 0 ? 'Todo bien' : ((cutForm.difference_cash) < 0 ? 'Sobrante en caja'
                : 'Faltante de efectivo') }} </p>
              <div v-if="cutForm.counted_cash" class="w-full">
                <el-input v-model="cutForm.withdrawn_cash" type="number" step="0.01" class="!w-full !h-6" placeholder="0.00">
                  <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                  </template>
                </el-input>
                <InputError :message="cutForm.errors.withdrawn_cash" />
              </div>
              <div class="flex justify-between">
                <span class="pl-3">$</span>
                <p>{{ (cutForm.counted_cash - cutForm.withdrawn_cash)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              </div>
          </div>
        </section>

        <!-- Tarjeta -->
        <div class="flex justify-between items-center space-x-2">
          <el-checkbox v-model="cutForm.cutCard_enabled" class="!text-sm !text-gray-700">
            <span class="!text-sm">Corte de tarjeta</span>
          </el-checkbox>
          <div class="flex items-center justify-end space-x-2 my-3">
            <img class="w-5" src="@/../../public/images/card.webp" alt="Pago con tarjeta">
            <p class="text-sm md:text-base font-semibold text-[#373737]" :class="{'text-gray-400' : !cutForm.cutCard_enabled}">Pagos con tarjeta (terminal externa)</p>
          </div>
        </div>
        <section class="w-full flex justify-end space-x-5 text-xs md:text-sm border-b border-gray-200 pb-3">
          <div class="space-y-2 text-right">
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}">Monto esperado en tarjeta</p>
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}">Monto reportado</p>
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}">Diferencia</p>
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}" class="opacity-0">status</p>
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}" v-if="cutForm.counted_card">Monto a retirar</p>
            <p :class="{'text-gray-400' : !cutForm.cutCard_enabled}" v-if="cutForm.counted_card">Dinero que dejas en tarjeta</p>
          </div>
          <div class="space-y-2 text-right w-36" :class="{'text-gray-400' : !cutForm.cutCard_enabled}">
            <div v-if="cutLoading">
              <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
            </div>
            <div class="space-y-[8px]" v-else>
              <div class="flex justify-between">
                <span class="pl-3">$</span>
                <p>{{ (cutForm.totalStoreSale?.card 
                      + cutForm.totalOnlineSale?.card
                      + cutForm.totalServiceOrders?.card)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              </div>
            </div>
            <el-input @input="differenceCard()" v-model="cutForm.counted_card" type="text" placeholder="0.00"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" class="!w-full !h-6"
              :disabled="!cutForm.cutCard_enabled">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <p v-if="cutForm.counted_card" :class="{
              'text-green-500': (cutForm.difference_card) === 0,
              'text-blue-500': (cutForm.difference_card) < 0,
              'text-red-500': (cutForm.difference_card) > 0
            }">
              <!-- Se multiplica por -1 para cambiar el signo y si sobra sea positivo y si falta negativo -->
              ${{ (cutForm.difference_card * -1)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </p>
            <p v-if="cutForm.counted_card" :class="{
              'text-green-500 bg-green-100': (cutForm.difference_card) === 0,
              'text-blue-500 bg-blue-100': (cutForm.difference_card) < 0,
              'text-red-500 bg-red-100': (cutForm.difference_card) > 0
            }" class="rounded-full text-xs inline py-[2px] px-2 w-full">
              <!-- Icono de proveedor de verificación si la diferencia es 0 -->
              <i v-if="(cutForm.difference_card) === 0" class="fa-solid fa-check mr-1"></i>
              <!-- Icono de sobrante en caja si la diferencia es negativa -->
              <i v-else-if="(cutForm.difference_card) < 0" class="fa-solid fa-plus mr-1"></i>
              <!-- Icono de faltante de efectivo si la diferencia es positiva -->
              <i v-else class="fa-solid fa-xmark mr-1"></i>
              <!-- Muestra el mensaje correspondiente -->
              {{ (cutForm.difference_card) === 0 ? 'Todo bien' : ((cutForm.difference_card) < 0 ? 'Sobrante en caja'
                : 'Faltante de efectivo') }} </p>
              <div v-if="cutForm.counted_card" class="w-full">
                <el-input v-model="cutForm.withdrawn_card" type="number" step="0.01" class="!w-full !h-6" placeholder="0.00">
                  <template #prefix>
                    <i class="fa-solid fa-dollar-sign"></i>
                  </template>
                </el-input>
                <InputError :message="cutForm.errors.withdrawn_card" />
              </div>
              <div class="flex justify-between">
                <span class="pl-3">$</span>
                <p>{{ (cutForm.counted_card - cutForm.withdrawn_card)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
              </div>
          </div>
        </section>

        <div class="col-span-full mt-2">
          <InputLabel value="Comentarios (opcional)" class="text-sm ml-2" />
          <el-input v-model="cutForm.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
            placeholder="Escribe aquí cualquier comentario relacionado al corte" :maxlength="255" show-word-limit
            clearable />
        </div>

        <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
          <CancelButton @click="cashCutModal = false">Cancelar</CancelButton>
          <PrimaryButton
            :disabled="!cutForm.counted_cash || cutForm.processing 
            || (!currentMovements.length && (cutForm.totalStoreSale?.cash 
              + cutForm.totalStoreSale?.card 
              + cutForm.totalOnlineSale?.cash 
              + cutForm.totalOnlineSale?.card 
              + cutForm.totalServiceOrders?.cash 
              + cutForm.totalServiceOrders?.card) == 0)">
            Hacer corte</PrimaryButton>
        </div>
        <p v-if="!currentMovements.length && (cutForm.totalStoreSale?.cash 
              + cutForm.totalStoreSale?.card 
              + cutForm.totalOnlineSale?.cash 
              + cutForm.totalOnlineSale?.card 
              + cutForm.totalServiceOrders?.cash 
              + cutForm.totalServiceOrders?.card) == 0" class="text-xs text-red-600 text-right">*Para
          hacer corte es necesario que haya almenos una venta o movimiento de caja registrado</p>
      </form>
    </div>
  </Modal>
  <!-- --------------------------- Modal corte de caja ends ------------------------------------ -->
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      max_cash: null,
      name: null,
      is_active: null,
      cashRegisterMovementType: null,
      registerAmount: null,
      registerNotes: null,
    });

    const cutForm = useForm({
      counted_cash: null, //dinero contado en caja tras haber hecho el corte
      counted_card: null, //dinero contado en tarjeta tras haber hecho el corte
      difference_cash: null, //diferencia de dinero contado en caja y dinero esperado en caja
      difference_card: null, //diferencia de dinero contado en tarjeta y dinero esperado en
      totalStoreSale: null, //dinero esperado de ventas hechas para hacer corte
      totalOnlineSale: null, //dinero esperado de ventas en linea para hacer corte
      totalServiceOrders: null, //dinero esperado de ventas de ordenes de servicio para hacer corte
      totalCashMovements: null, //dinero de movimientos de caja para hacer corte
      withdrawn_cash: null, //dinero retirado de caja tras haber hecho el corte
      withdrawn_card: null, //dinero retirado de tarjeta tras haber hecho el corte
      cutCard_enabled: false, //bandera para contar tarjeta en corte de caja
      notes: null,
    });

    return {
      form,
      cutForm,
      currentMovements: null, //movimientos de caja hechos desde el último corte hasta ahora. Petición axios
      edit_max_cash: false, //bandera para editar el maximo dinero de caja
      edit_cash_register_name: false, //bandera para editar el nombre de caja
      cashRegister: null,
      loading: true,
      cutLoading: false,
      cashRegisterModal: false,
      cashCutModal: false,
      showcashRegisterMovements: true,

      // monto maximo en caja activado
      isMaxCashOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Aviso de monto máximo en caja')?.value,
      // Permisos de rol
      canDelete: this.$page.props.auth.user.permissions.includes('Eliminar cajas'),
      canEdit: this.$page.props.auth.user.permissions.includes('Editar cajas'),
      canRegisterMovements: this.$page.props.auth.user.permissions.includes('Registrar movimientos'),
    }
  },
  components: {
    PrimaryButton,
    ThirthButton,
    CancelButton,
    InputLabel,
    InputError,
    Loading,
    Modal
  },
  props: {
    cash_register: Object,
    total_cash_registers: Number //numero de cajar registradoras para no poder eliminar si solo hay 1
  },
  methods: {
    update() {
      this.form.put(route('cash-registers.update', this.cashRegister.id), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "",
            type: "success",
          });
          this.edit_max_cash = false;
          this.edit_cash_register_name = false;
        }
      });
    },
    storeCashRegisterMovement() {
      this.form.post(route('cash-register-movements.store', { cash_register_id: this.cash_register.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha registrado el movimiento de caja",
            type: "success",
          });
          this.cashRegisterModal = false;
          this.cashRegisterMovementType = null;
          this.registerAmount = null;
          this.fetchTotalCashMovements();
        },
      });
    },
    deleteCashRegister() {
      this.$inertia.delete(route('cash-registers.destroy', this.cash_register.id));
      location.reload();
    },
    editMaxCash() {
      this.edit_max_cash = true;
    },
    editName() {
      this.edit_cash_register_name = true;
    },
    async fetchCashRegister() {
      try {
        this.loading = true;
        const response = await axios.get(route('cash-registers.fetch-cash-register', this.cash_register.id));
        if (response.status === 200) {
          this.cashRegister = response.data.item;
          this.form.max_cash = this.cashRegister.max_cash;
          this.form.name = this.cashRegister.name;
          this.form.is_active = this.cashRegister.is_active;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    handleCashCut() {
      this.fetchTotalSaleForCashCut();
      this.fetchTotalCashMovements();
      this.cashCutModal = true;
    },
    async fetchTotalSaleForCashCut() {
      try {
        const response = await axios.get(route('cash-cuts.fetch-total-sales-for-cash-cut', this.cash_register.id));
        if (response.status === 200) {
          this.cutForm.totalStoreSale = response.data.store_sales; //ventas en tienda
          this.cutForm.totalOnlineSale = response.data.online_sales; // ventas en linea
          this.cutForm.totalServiceOrders = response.data.service_orders; // ventas de ordenes de servicio
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchTotalCashMovements() {
      try {
        this.cutLoading = true;
        const response = await axios.get(route('cash-register-movements.fetch-total-cash-movements', this.cash_register.id));
        if (response.status === 200) {
          this.cutForm.totalCashMovements = response.data;
          this.cutLoading = false;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchCurrentMovements() {
      try {
        const response = await axios.get(route('cash-register-movements.fetch-current-movements', this.cash_register.id));
        if (response.status == 200) {
          this.currentMovements = response.data.items;
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeCashCut() {
      this.cutForm.post(route('cash-cuts.store', { cash_register_id: this.cash_register.id }), {
        onSuccess: () => {
          this.$notify({
            title: "Correcto",
            message: "Se ha realizado el corte de caja",
            type: "success",
          });
          this.cashCutModal = false;
          this.fetchCashRegister();
          this.cutForm.reset();
          location.reload();
        },
      });
    },
    formatDateHour(dateString) {
      return format(parseISO(dateString), 'h:mm a', { locale: es });
    },
    formatDate(dateString) {
      return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
    differenceCash() {
      //  Se hace la resta al reves para cambiar el signo y si sobra sea positivo y si falta negativo
      this.cutForm.difference_cash = (
          this.cutForm.totalStoreSale?.cash + 
          this.cutForm.totalOnlineSale?.cash + 
          this.cutForm.totalServiceOrders?.cash + 
          this.cutForm.totalCashMovements + 
          this.cash_register.started_cash) - 
          this.cutForm.counted_cash
    },
    differenceCard() {
      //  Se hace la resta al reves para cambiar el signo y si sobra sea positivo y si falta negativo
      this.cutForm.difference_card = (
          this.cutForm.totalStoreSale?.card + 
          this.cutForm.totalOnlineSale?.card + 
          this.cutForm.totalServiceOrders?.card ) - 
          this.cutForm.counted_card
    },
  },
  mounted() {
    this.fetchCurrentMovements(); //obtiene los movimientos de caja que hay desde el ultimo corte hasta ahora
    this.fetchCashRegister(); //obtiene el monto actual de dinero que hay en la caja registradora
    this.fetchTotalSaleForCashCut(); //obtiene el monto total de venta que hay desde el ultimo corte hasta ahora
    this.fetchTotalCashMovements(); //obtiene el monto total de movimientos desde el ultimo corte hasta ahora
  }

}
</script>
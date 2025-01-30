<template>
  <div class="flex justify-between">
    <h1 class="font-bold">Configuraciones de tienda en línea</h1>
  </div>

  <!-- Portada -->
  <section class="mt-5">
    <h2 class="my-2 font-bold">Portada y logotipo</h2>
    <p class="text-sm text-gray99 text-justify">
      En esta sección, puedes subir imágenes que representen tu marca, promociones, o
      cualquier mensaje importante que desees comunicar a tus clientes. Puedes agregar
      hasta 3 imágenes que rotarán en la portada de tu tienda en línea, ayudándote a
      captar la atención de tus visitantes desde el primer momento.
    </p>

    <div class="md:flex items-center justify-center md:space-x-9 my-5">
      <div class="flex flex-col items-center justify-center">
        <!-- Banner 1  -->
          <InputFilePreview
              v-show="currentImage == 1"
              @imagen="this.bannerForm.banner1 = $event; clearedBanner1 = false"
              :imageUrl="getMediaUrl('banner1')"
              @cleared="bannerForm.clearedBanner1 = true"
          />

        <!-- Banner 2  -->
          <InputFilePreview
              v-show="currentImage == 2"
              @imagen="this.bannerForm.banner2 = $event; clearedBanner2 = false"
              :imageUrl="getMediaUrl('banner2')"
              @cleared="bannerForm.clearedBanner2 = true"
          />

        <!-- Banner 3  -->
          <InputFilePreview
              v-show="currentImage == 3"
              @imagen="this.bannerForm.banner3 = $event; clearedBanner3 = false"
              :imageUrl="getMediaUrl('banner3')"
              @cleared="bannerForm.clearedBanner3 = true"
          />

        <p class="text-center text-sm mt-3">
            <i
                @click="currentImage = currentImage - 1"
                v-if="currentImage > 1"
                class="fa-solid fa-angle-left text-xs mr-2 cursor-pointer p-1"
            ></i>
            Banner {{ currentImage }} de 3
            <i
                @click="currentImage = currentImage + 1"
                v-if="currentImage < 3"
                class="fa-solid fa-angle-right text-xs ml-2 cursor-pointer p-1"
            ></i>
        </p>
      </div>

      <div class="flex flex-col items-center justify-center mt-7 md:mt-0">
        <!-- Logo  -->
        <InputFilePreview
            @imagen="this.logoForm.logo = $event; clearedLogo = false"
            :imageUrl="logo?.media[0]?.original_url"
            @cleared="logoForm.clearedLogo = true"
        />
        <p class="mt-3 text-sm">Logotipo de tu tienda (opcional)</p>
      </div>

    </div>

    <div class="text-right">
        <PrimaryButton :disabled="logoForm.processing || bannerForm.processing" @click="update">
          <i v-if="logoForm.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
          Guardar
        </PrimaryButton>
    </div>
  </section>
  <!-- Divisor -->
  <div class="border-b border-grayD9 my-4"></div>


  <!-- Productos -->
  <!-- -------------------------------------------------->
  <section class="my-7">
    <h2 class="my-2 font-bold">Productos</h2>
    <p class="text-sm mx-7 text-justify">
      Todos los productos disponibles en tu inventario serán reflejados en la tienda en línea. 
      Si no deseas que algún producto aparezca en la tienda en línea, deberás eliminarlo directamente desde tu inventario. 
      Esto asegura que todos los productos que tienes disponibles se muestren automáticamente en la tienda en línea sin necesidad de configuración adicional.
      <span @click="$inertia.get(route('products.index'))" class="text-primary ml-1 cursor-pointer">Ir a mis productos</span>
    </p>
  </section>

  <!-- Divisor -->
  <div class="border-b border-grayD9 my-4"></div>

  <!-- información de tienda -->
  <section class="my-5 lg:grid grid-cols-2 gap-3 *:mt-2">

    <!-- Numero de whatsapp ----------------------------------------->
    <!-- --------------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <i v-if="!editWhatsapp" @click="editWhatsapp = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
      <h2 class="font-bold">Contacto de WhatsApp <i class="fa-brands fa-whatsapp ml-3"></i></h2>
      <p class="text-sm text-gray99 mb-4">El número registrado será el destinatario de los pedidos realizados en la tienda en línea</p>
      <div class="flex items-center text-sm">

        <p class="mx-7">Número de teléfono:</p>
        <p v-if="!editWhatsapp" class="mx-7">{{ onlineStoreForm.online_store_properties?.whatsapp ?? 'Sin registro' }}</p>

        <div v-else class="flex items-center space-x-2 mx-7">
          <el-input v-model="onlineStoreForm.online_store_properties.whatsapp"
          :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
          :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
          placeholder="Escribe el número de teléfono" />
          <InputError :message="onlineStoreForm.errors.whatsapp" />
          <button v-if="editWhatsapp">
            <i @click="editWhatsapp = false; updateOnlineSalesInfo()" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
          </button>
          <button @click="editWhatsapp = false;">
              <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
            </button>
        </div>
      </div>
    </article>

    <!-- Gestion de pago ----------------------->
    <!-- ------------------------------------ -->
    <article class="border border-grayD9 rounded-md p-4">
      <h2 class="font-bold">Gestión de pago <i class="fa-regular fa-credit-card ml-3"></i></h2>
      <p class="text-sm text-gray99 mb-4">Activa o desactiva las opciones de pago que manejas para tu tienda en línea.</p>
      
      <div class="flex items-center text-sm">
        <div class="w-36 mx-7 space-y-2">
          <p>Efectivo</p>
          <p>Tarjeta de crédito</p>
          <p>Tarjeta de débito</p>
          <p>Mercado pago</p>
        </div>

        <div class="flex flex-col w-14 md:w-36 mx-7 space-y-1">
          <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.cash_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.credit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.debit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.mercado_pago" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        </div>
      </div>
    </article>

    <!-- Envío a domicilio ---------------------------------->
    <!-- ------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <i v-if="!editDelivery" @click="editDelivery = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
      <h2 class="font-bold mb-4 flex items-center space-x-2">
        <span>Envío a domicilio </span>
        <svg width="20" height="17" viewBox="0 0 22 19" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path d="M6.72524 15.6484C6.77183 19.0724 1.91988 18.9401 1.89108 15.6484M14.1595 15.6484C12.0402 15.6484 10.852 15.6484 8.73279 15.6484C9.55513 10.2173 4.95498 10.1689 2.48591 12.4581M6.87279 15.6484H1.89108M1.89108 15.6484H1C0.999873 14.3808 1.60826 13.2718 2.48591 12.4581M2.48591 12.4581H1C1.10321 11.5627 1.36537 11.1234 2.41156 10.5173C2.4526 10.4935 3.19201 10.5458 3.22945 10.5173C3.60693 9.96558 3.81857 9.65626 4.19605 9.10456M11.9288 9.10456H1.14755V8.99303M11.9288 9.10456V8.99303M11.9288 9.10456C11.9954 9.03243 12.0533 8.95782 12.1029 8.8815M11.9288 9.10456C10.0014 11.2309 9.84694 12.8222 11.2597 13.3427C12.334 13.3427 12.9364 13.3427 14.0107 13.3427C14.7543 13.3427 15.5585 12.1549 15.2748 10.5916C15.0049 9.10456 12.5237 4.34592 14.903 4.12286H15.1261M11.9288 8.8815V8.99303M11.9288 8.8815H8.43421M11.9288 8.8815C11.9968 8.8815 12.0349 8.8815 12.1029 8.8815M1.14755 8.8815V8.99303M1.14755 8.8815H8.43421M1.14755 8.8815V3.23061M1.14755 8.99303H11.9288M8.43421 8.8815V6.72524M1.14755 3.23061V1.9666C1.14755 1.37177 1.59367 1 2.11414 1H7.83938C8.21115 1 8.43421 1.22306 8.43421 1.59483V6.72524M1.14755 3.23061C2.80265 3.23061 3.7306 3.23061 5.38571 3.23061M8.43421 6.72524H10.9622C12.0631 6.72524 12.7183 7.93499 12.1029 8.8815M13.1929 2.71014C14.1065 1.81999 14.6056 1.29742 15.4235 1.29742H16.7618C18.695 1.29742 18.6207 4.12286 16.7618 4.12286H16.3157M16.3157 4.12286C16.3157 3.71634 16.3157 3.48842 16.3157 3.08191M16.3157 4.12286H15.1261M15.1261 4.12286L18.2489 10.8891C19.4446 10.9319 20.0583 11.0519 20.8513 11.8556M13.1185 3.00755C13.1185 2.84329 12.9853 2.71014 12.8211 2.71014M13.1185 3.00755C13.1185 3.04556 13.1114 3.0819 13.0984 3.11531M13.1185 3.00755L12.8211 2.71014M12.5237 3.00755C12.5237 2.9675 12.5316 2.92929 12.5459 2.89441M12.5237 3.00755C12.5237 3.16428 12.6449 3.29269 12.7987 3.30414M12.5237 3.00755L12.7987 3.30414M12.8211 2.71014C12.7457 2.71014 12.6768 2.73822 12.6244 2.78449M12.6244 2.78449L13.0178 3.23061M12.6244 2.78449C12.5905 2.81441 12.5634 2.85194 12.5459 2.89441M13.0178 3.23061C13.0531 3.19943 13.081 3.15999 13.0984 3.11531M13.0178 3.23061C12.9832 3.26114 12.9415 3.28374 12.8954 3.2956M12.7467 2.71014L13.0984 3.11531M12.8954 3.2956C12.8717 3.30171 12.8468 3.30497 12.8211 3.30497C12.8136 3.30497 12.8061 3.30469 12.7987 3.30414M12.8954 3.2956L12.5459 2.89441M21 15.722C21 17.1593 19.8349 18.3244 18.3976 18.3244C16.9604 18.3244 15.7952 17.1593 15.7952 15.722C15.7952 14.2848 16.9604 13.1197 18.3976 13.1197C19.8349 13.1197 21 14.2848 21 15.722ZM20.8513 15.722C20.8513 17.0772 19.7527 18.1757 18.3976 18.1757C17.0425 18.1757 15.9439 17.0772 15.9439 15.722C15.9439 14.3669 17.0425 13.2684 18.3976 13.2684C19.7527 13.2684 20.8513 14.3669 20.8513 15.722ZM13.2672 3.00755C13.2672 3.25394 13.0675 3.45367 12.8211 3.45367C12.5747 3.45367 12.375 3.25394 12.375 3.00755C12.375 2.76116 12.5747 2.56143 12.8211 2.56143C13.0675 2.56143 13.2672 2.76116 13.2672 3.00755Z" stroke="currentColor" stroke-width="0.371769" stroke-linecap="round"/>
        </svg>
      </h2>

      <!-- costo de envío -->
      <div class="flex items-center text-sm">

        <p class="mx-7">Costo de envío:</p>
        <p v-if="!editDelivery" class="ml-12">${{ onlineStoreForm.online_store_properties?.delivery_price ?? 'Sin registro' }}</p>

        <div v-else class="flex items-center space-x-2 mx-2">
          <el-input v-model="onlineStoreForm.online_store_properties.delivery_price" type="text" placeholder="Ingresa el monto" class="pl-10"
            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
            :parser="(value) => value.replace(/\D/g, '')">
            <template #prefix>
              <i class="fa-solid fa-dollar-sign"></i>
            </template>
          </el-input>
          <button v-if="editDelivery">
            <i @click="editDelivery = false; updateOnlineSalesInfo()" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
          </button>
          <button @click="editDelivery = false">
              <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
            </button>
        </div>
      </div>

      <!-- condiciones de envío -->
      <div class="flex items-center text-sm mt-3">

        <p class="mx-7">Condiciones de envío:</p>
        <p v-if="!editDelivery" class="ml-2">{{ onlineStoreForm.online_store_properties?.delivery_conditions ?? 'Sin registro' }}</p>

        <div v-else class="flex items-center space-x-2 mx-2">
          <el-input v-model="onlineStoreForm.online_store_properties.delivery_conditions" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe las condiciones de envío" :maxlength="500" show-word-limit class="!w-48"
              clearable />
          <button v-if="editDelivery">
            <i @click="editDelivery = false; updateOnlineSalesInfo()" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
          </button>
          <button @click="editDelivery = false;">
              <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
            </button>
        </div>
      </div>
    </article>



    <!-- Mínimo de compra para envío gratis ---------------------------------->
    <!-- ------------------------------------------------------------------ -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <i v-if="!editMinFreeDelivery" @click="editMinFreeDelivery = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
      <h2 class="font-bold mb-4">Mínimo de compra para envío gratis <i class="fa-solid fa-percent ml-3"></i></h2>

      <!-- Mínimo de compra para envío gratis -->
      <div class="flex items-center justify-between text-sm mt-3">

        <div class="flex items-center space-x-7">
          <p class="mx-7">Compra mínima para envío gratis:</p>
          <p v-if="!editMinFreeDelivery" class="mx-7">${{ onlineStoreForm.online_store_properties?.min_free_delivery ?? 'Sin registro' }}</p>
          <div v-else class="flex items-center space-x-2 mx-2">
            <el-input v-model="onlineStoreForm.online_store_properties.min_free_delivery" type="text" placeholder="Ingresa el monto"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\D/g, '')">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <button v-if="editMinFreeDelivery">
              <i @click="editMinFreeDelivery = false; updateOnlineSalesInfo()" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
            </button>
            <button @click="editMinFreeDelivery = false">
                <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
              </button>
          </div>
        </div>

        <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.enabled_free_delivery" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
      </div>
    </article>

    <!-- Gestión de cajas ---------------------------------->
    <!-- ------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <h2 class="font-bold">Gestión de cajas <i class="fa-solid fa-cash-register ml-3"></i></h2>
      <p class="text-sm text-gray99 mb-4">Selecciona una caja específica para agregar todos los pedidos a domicilio.</p>

      <p class="mx-7 text-sm">Selecciona una caja específica para agregar todos los pedidos a domicilio.</p>
      <!-- Gestión de cajas -->
      <div class="flex items-center text-sm mt-3">
        <p class="mx-7">Caja para pedidos a domicilio:</p>
        <el-select @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.online_sales_cash_register" class="!w-40" filterable required clearable placeholder="Seleccione"
            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in cash_registers" :key="item" :value="item.id" :label="item.name" />
            <el-option :value="null" label="Ninguna caja" />
        </el-select>
      </div>
    </article>
    
    <!-- Inventario ---------------------------------------->
    <!-- ------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <h2 class="font-bold mb-4">Inventario <i class="fa-solid fa-boxes-stacked ml-3"></i></h2>
      
      <div class="flex items-center justify-between space-x-2">
        <p class="mx-7 text-sm">Actualiza el inventario automáticamente al hacer una venta y lo toma en cuenta para no permitir ventas
          de productos agotados.
        </p>
        <el-switch @change="updateOnlineSalesInfo()" v-model="onlineStoreForm.online_store_properties.inventory" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
      </div>
    </article>

    <div class="text-right pt-10 md:mx-7 col-span-full">
      <!-- <PrimaryButton :disabled="onlineStoreForm.processing" @click="updateOnlineSalesInfo">
        <i v-if="onlineStoreForm.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
        Guadar cambios
      </PrimaryButton> -->
    </div>

  </section>
</template>

<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";

export default {
  data() {
    const bannerForm = useForm({
      banner1: null,
      banner2: null,
      banner3: null,
      clearedBanner1: false,
      clearedBanner2: false,
      clearedBanner3: false,
    });

    const logoForm = useForm({
      logo: null,
      clearedLogo: false,
    });

    const onlineStoreForm = useForm({
      online_store_properties: {
          whatsapp : this.$page.props.auth.user.store.online_store_properties?.whatsapp ?? null,
          cash_payment: !! this.$page.props.auth.user.store.online_store_properties?.cash_payment,
          credit_payment: !! this.$page.props.auth.user.store.online_store_properties?.credit_payment,
          debit_payment: !! this.$page.props.auth.user.store.online_store_properties?.debit_payment,
          mercado_pago: !! this.$page.props.auth.user.store.online_store_properties?.mercado_pago,
          delivery_price: this.$page.props.auth.user.store.online_store_properties?.delivery_price ?? null,
          delivery_conditions: this.$page.props.auth.user.store.online_store_properties?.delivery_conditions ?? null,
          min_free_delivery: this.$page.props.auth.user.store.online_store_properties?.min_free_delivery ?? null,
          enabled_free_delivery: this.$page.props.auth.user.store.online_store_properties?.enabled_free_delivery ?? null,
          online_sales_cash_register: this.$page.props.auth.user.store.online_store_properties?.online_sales_cash_register ?? null,
          inventory: !! this.$page.props.auth.user.store.online_store_properties?.inventory,
      }
    });

    return {
      onlineStoreForm,
      bannerForm,
      logoForm,
      currentImage: 1,

      editWhatsapp: false, //edita el numero de whatsapp
      editDelivery: false, //edita el costo de envío y condiciones de envío
      editMinFreeDelivery: false, //edita el minimo para envío gratis
    };
  },
  components: {
    InputFilePreview,
    PrimaryButton,
    InputError,
  },
  props: {
    cash_registers: Array,
    banners: Object,
    logo: Object,
  },
  methods: {
    update() {
      //Se agrega un timer para que termine de cargar las imagenes de banners y prosiga con el logo
      setTimeout(() => {
        this.updateBanners();
      }, 1000);
      this.updateLogo();
    },
    updateOnlineSalesInfo() {
      this.onlineStoreForm.put(route('stores.update-online-sales-info', this.$page.props.auth.user.store.id), {
          onSuccess: () => {
            // this.$notify({
            //     title: "Correcto",
            //     message: "¡Configuraciones actualizadas!",
            //     type: "success",
            // });
            this.editWhatsapp = false;
            this.editDeliveryPrice = false;
            this.editDeliveryConditions = false;
            this.editMinFreeDelivery = false;
          },
      });
    },
    updateBanners() {
        this.bannerForm.post(route("banners.update-with-media", this.banners.id), {
          method: '_put',
        });
    },
    updateLogo() {
        this.logoForm.post(route("logos.update-with-media", this.logo.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
                title: "Correcto",
                message: "¡Banners y Logo actualizados!",
                type: "success",
            });
          }
        });
    },
    getMediaUrl(collectionName) {
        const media = this.banners?.media.find(img => img.collection_name === collectionName);
        return media ? media.original_url : null;
    },
  },
};
</script>

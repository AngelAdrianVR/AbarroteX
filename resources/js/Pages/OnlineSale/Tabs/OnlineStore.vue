<template>
  <div class="flex justify-between">
    <h1 class="font-bold">Configuraciones de tienda en línea</h1>
  </div>

  <!-- Portada -->
  <section class="mt-5">
    <h2 class="my-2 font-bold">Portada y logotipo</h2>
    <p class="text-sm text-gray99">
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
    <p class="text-sm mx-7">
      Todos los productos disponibles en tu inventario serán reflejados en la tienda en línea. 
      Si no deseas que algún producto aparezca en la tienda en línea, deberás eliminarlo directamente desde tu inventario. 
      Esto asegura que todos los productos que tienes disponibles se muestren automáticamente en la tienda en línea sin necesidad de configuración adicional.
      <span @click="$inertia.get(route('products.index'))" class="text-primary ml-1 cursor-pointer">Ir a mis productos</span>
    </p>
  </section>

  <!-- Divisor -->
  <div class="border-b border-grayD9 my-4"></div>

  <!-- información de tienda -->
  <section class="my-5 lg:grid grid-cols-2 gap-3">

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
            <i @click="editWhatsapp = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
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
          <el-switch v-model="onlineStoreForm.online_store_properties.cash_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch v-model="onlineStoreForm.online_store_properties.credit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch v-model="onlineStoreForm.online_store_properties.debit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
          <el-switch v-model="onlineStoreForm.online_store_properties.mercado_pago" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        </div>
      </div>
    </article>

    <!-- Envío a domicilio ---------------------------------->
    <!-- ------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <i v-if="!editDelivery" @click="editDelivery = true" class="fa-solid fa-pen absolute top-3 right-3 text-xs text-primary cursor-pointer bg-gray-100 rounded-full py-1 px-[5px]"></i>
      <h2 class="font-bold mb-4">Envío a domicilio <i class="fa-solid fa-motorcycle ml-3"></i></h2>

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
            <i @click="editDelivery = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
          </button>
          <button @click="editDelivery = false;">
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
            <i @click="editDelivery = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
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
              <i @click="editMinFreeDelivery = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
            </button>
            <button @click="editMinFreeDelivery = false;">
                <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
              </button>
          </div>
        </div>

        <el-switch v-model="onlineStoreForm.online_store_properties.enabled_free_delivery" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
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
        <el-select v-model="onlineStoreForm.online_store_properties.online_sales_cash_register" class="!w-40" filterable required clearable placeholder="Seleccione"
            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in cash_registers" :key="item" :value="item.id" 
            :label="item.name" />
        </el-select>
      </div>
    </article>
    
    <!-- Inventario ---------------------------------------->
    <!-- ------------------------------------------------- -->
    <article class="border border-grayD9 rounded-md p-4 relative">
      <h2 class="font-bold mb-4">Inventario <i class="fa-solid fa-boxes-stacked ml-3"></i></h2>
      
      <div class="flex items-center justify-between space-x-2">
        <p class="mx-7 text-sm">Actualiza el inventario automáticamente al hacer una venta y toma en cuenta el inventario</p>
        <el-switch v-model="onlineStoreForm.online_store_properties.inventory" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
      </div>
    </article>

    <div class="text-right pt-10 md:mx-7 col-span-full">
      <PrimaryButton :disabled="onlineStoreForm.processing" @click="updateOnlineSalesInfo">
        <i v-if="onlineStoreForm.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
        Guadar cambios
      </PrimaryButton>
    </div>

  </section>
</template>

<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
    PrimaryButton
  },
  props: {
    cash_registers: Array,
    banners: Object,
    logo: Object,
  },
  methods: {
    update() {
      this.updateBanners();
      this.updateLogo();
    },
    updateOnlineSalesInfo() {
      this.onlineStoreForm.put(route('stores.update-online-sales-info', this.$page.props.auth.user.store.id), {
          onSuccess: () => {
            this.$notify({
                title: "Correcto",
                message: "¡Configuraciones actualizadas!",
                type: "success",
            });
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

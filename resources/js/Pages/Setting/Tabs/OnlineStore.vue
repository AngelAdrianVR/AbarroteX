<template>
  <div class="flex justify-between">
    <h1 class="font-bold">Configuraciones de tienda en línea</h1>
    <Link :href="route('online-sales.client-index', $page.props.auth.user.store_id)">
      <ThirthButton>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-4 mr-1"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
          />
        </svg>
        Ver tienda en línea
      </ThirthButton>
    </Link>
  </div>

  <!-- Portada -->
  <section class="mt-5">
    <h2 class="my-2 font-bold">Portada</h2>
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

        <p class="text-center mt-3">
            <i
                @click="currentImage = currentImage - 1"
                v-if="currentImage > 1"
                class="fa-solid fa-angle-left text-xs mr-2 cursor-pointer p-1"
            ></i>
            Imagen {{ currentImage }} de 3
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
        <p class="mt-3">Logotipo de tu tienda (opcional)</p>
      </div>

    </div>

        <div class="text-right">
            <PrimaryButton :disabled="logoForm.processing || bannerForm.processing" @click="update">Guardar</PrimaryButton>
        </div>
      
  </section>

  <!-- información de tienda -->
  <section class="my-5">

    <!-- Numero de whatsapp ----------------------------------------->
    <!-- --------------------------------------------------------- -->
    <h2 class="font-bold mb-5">Contacto de WhatsApp <i class="fa-brands fa-whatsapp"></i></h2>
    <div class="flex items-center text-sm">
      <p class="ml-7">Número de teléfono:</p>

      <div v-if="!editWhatsapp" class="flex items-center space-x-5">
        <p class="ml-7">{{ onlineStoreForm.online_store_properties?.whatsapp ?? 'Sin registro' }}</p>
        <i v-if="!editWhatsapp" @click="editWhatsapp = true" class="fa-solid fa-pen text-xs text-primary cursor-pointer bg-gray-100  rounded-full py-1 px-[5px]"></i>
      </div>

      <div v-else class="flex items-center space-x-2 ml-7">
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

    <!-- Gestion de pago ----------------------->
    <!-- ------------------------------------ -->
    <h2 class="font-bold mb-5 mt-8">Gestión de pago</h2>
    
    <div class="flex items-center text-sm">
      <div class="w-36 ml-7 space-y-2">
        <p>Efectivo</p>
        <p>Tarjeta de crédito</p>
        <p>Tarjeta de débito</p>
        <p>Mercado pago</p>
      </div>

      <div class="flex flex-col w-36 ml-7 space-y-1">
        <el-switch v-model="onlineStoreForm.online_store_properties.cash_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        <el-switch v-model="onlineStoreForm.online_store_properties.credit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        <el-switch v-model="onlineStoreForm.online_store_properties.debit_payment" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
        <el-switch v-model="onlineStoreForm.online_store_properties.mercado_pago" class="ml-2" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
      </div>
    </div>

    <!-- Envío a domicilio ---------------------------------->
    <!-- ------------------------------------------------- -->
    <h2 class="font-bold mb-5 mt-8">Envío a domicilio</h2>

    <!-- costo de envío -->
    <div class="flex items-center text-sm">
      <p class="ml-7">Costo de envío:</p>

      <div v-if="!editDeliveryPrice" class="flex items-center space-x-5">
        <p class="ml-7">${{ onlineStoreForm.online_store_properties?.delivery_price ?? 'Sin registro' }}</p>
        <i v-if="!editDeliveryPrice" @click="editDeliveryPrice = true" class="fa-solid fa-pen text-xs text-primary cursor-pointer bg-gray-100  rounded-full py-1 px-[5px]"></i>
      </div>

      <div v-else class="flex items-center space-x-2 ml-7">
        <el-input v-model="onlineStoreForm.online_store_properties.delivery_price" type="text" placeholder="Ingresa el monto" class="px-10"
          :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
          :parser="(value) => value.replace(/\D/g, '')" @keydown.enter="update">
          <template #prefix>
            <i class="fa-solid fa-dollar-sign"></i>
          </template>
        </el-input>
        <button v-if="editDeliveryPrice">
          <i @click="editDeliveryPrice = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
        </button>
        <button @click="editDeliveryPrice = false;">
            <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
          </button>
      </div>
    </div>

    <!-- condiciones de envío -->
    <div class="flex items-center text-sm mt-3">
      <p class="ml-7">Condiciones de envío:</p>

      <div v-if="!editDeliveryConditions" class="flex items-center space-x-5">
        <p class="ml-7">{{ onlineStoreForm.online_store_properties?.delivery_conditions ?? 'Sin registro' }}</p>
        <i v-if="!editDeliveryConditions" @click="editDeliveryConditions = true" class="fa-solid fa-pen text-xs text-primary cursor-pointer bg-gray-100  rounded-full py-1 px-[5px]"></i>
      </div>

      <div v-else class="flex items-center space-x-2 ml-7">
        <el-input v-model="onlineStoreForm.online_store_properties.delivery_conditions" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
            placeholder="Escribe las condiciones de envío" :maxlength="500" show-word-limit
            clearable />
        <button v-if="editDeliveryConditions">
          <i @click="editDeliveryConditions = false" class="fa-solid fa-check text-xs text-white cursor-pointer bg-primary rounded-full py-1 px-[7px]"></i>
        </button>
        <button @click="editDeliveryConditions = false;">
            <i class="fa-solid fa-x text-xs text-gray-600 cursor-pointer bg-gray-100 rounded-full py-1 px-[7px]"></i>
          </button>
      </div>
    </div>


    <!-- Mínimo de compra para envío gratis ---------------------------------->
    <!-- ------------------------------------------------- -->
    <h2 class="font-bold mb-5 mt-8">Mínimo de compra para envío gratis</h2>

    <!-- Mínimo de compra para envío gratis -->
    <div class="flex items-center text-sm mt-3">
      <p class="ml-7">Compra mínima para envío gratis:</p>

      <div v-if="!editMinFreeDelivery" class="flex items-center space-x-5">
        <p class="ml-7">${{ onlineStoreForm.online_store_properties?.min_free_delivery ?? 'Sin registro' }}</p>
        <i v-if="!editMinFreeDelivery" @click="editMinFreeDelivery = true" class="fa-solid fa-pen text-xs text-primary cursor-pointer bg-gray-100  rounded-full py-1 px-[5px]"></i>
      </div>

      <div v-else class="flex items-center space-x-2 ml-7">
        <el-input v-model="onlineStoreForm.online_store_properties.min_free_delivery" type="text" placeholder="Ingresa el monto" class="px-10"
          :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
          :parser="(value) => value.replace(/\D/g, '')" @keydown.enter="update">
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
      <el-switch v-model="onlineStoreForm.online_store_properties.enabled_free_delivery" class="ml-4" size="small" style="--el-switch-on-color: #F68C0F; --el-switch-off-color: #D9D9D9"/>
    </div>

    <!-- Gestión de cajas ---------------------------------->
    <!-- ------------------------------------------------- -->
    <!-- <h2 class="font-bold mb-5 mt-8">Gestión de cajas</h2>

    <p class="ml-7 text-sm">Selecciona una caja específica para agregar todos los pedidos a domicilio.</p>
    Gestión de cajas
    <div class="flex items-center text-sm mt-3">
      <p class="ml-7">Caja para pedidos a domicilio:</p>
    </div> -->

    <div class="text-right mt-4">
      <PrimaryButton @click="updateOnlineSalesInfo">Guadar cambios</PrimaryButton>
    </div>

  </section>
</template>

<script>
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import { Link, useForm } from "@inertiajs/vue3";

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
      }
    });

    return {
      onlineStoreForm,
      bannerForm,
      logoForm,
      currentImage: 1,

      editWhatsapp: false, //edita el numero de whatsapp
      editDeliveryPrice: false, //edita el costo de envío
      editDeliveryConditions: false, //edita condiciones de envío
      editMinFreeDelivery: false, //edita el minimo para envío gratis
    };
  },
  components: {
    InputFilePreview,
    PrimaryButton,
    ThirthButton,
    Link,
  },
  props: {
    banners: Object,
    logo: Object,
  },
  methods: {
    update() {
      this.updateBanners();
      this.updateLogo();
      this.$notify({
          title: "Correcto",
          message: "Se han actualizado la media",
          type: "success",
      });
      // window.location.reload();
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
        this.logoForm.post(route("logos.update-with-media", 1), {
          method: '_put',
        });
    },
    getMediaUrl(collectionName) {
        const media = this.banners?.media.find(img => img.collection_name === collectionName);
        return media ? media.original_url : null;
    },
  },
};
</script>

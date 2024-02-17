<template>
    <AppLayout :title="product.data.name">
        <div class="px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Productos</h1>
                <div class="flex items-center space-x-3">
                    <PrimaryButton @click="openEntryModal" class="!rounded-full !bg-[#5FCB1F]">Entrada de producto</PrimaryButton>
                    <PrimaryButton @click="$inertia.get(route('products.edit', product.data.id))" class="!rounded-full">Editar</PrimaryButton>
                </div>
            </div>
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @focus="searchFocus = true" @blur="handleBlur" @input="searchProducts" class="input w-full pl-9" placeholder="Buscar producto" type="text">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
                 <!-- Resultados de la búsqueda -->
                <div v-if="searchFocus && searchQuery" class="absolute mt-1 bg-white border border-gray-300 rounded shadow-lg w-full">
                    <ul v-if="productsFound?.length > 0">
                        <li @click.stop="$inertia.get(route('products.show', product.id))" v-for="(product, index) in productsFound" :key="index" class="hover:bg-gray-200 cursor-default text-sm px-5 py-2">{{ product.name }}</li>
                    </ul>
                    <p v-else class="text-center text-sm text-gray-600 px-5 py-2">No se encontraron coincidencias</p>
                </div>
            </div>
            <div class="mt-5">
                <Back :route="'products.index'" />
            </div>

            <!-- Info de producto -->
            <div class="grid grid-cols-3 gap-x-12 mx-10">
                <!-- fotografia de producto -->
                <section class="mt-7">
                    <figure class="border border-grayD9 rounded-lg">
                        <img class="w-96 mx-auto" :src="product.data.imageCover[0]?.original_url" alt="">
                    </figure>
                </section>

                <!-- informacion de producto -->
                <section class="col-span-2">
                    <!-- Pestañas -->
                    <div class="lg:w-3/4 w-full flex items-center space-x-7 text-sm border-b border-gray4 lg:mx-16 mx-2 mb-5 contenedor transition-colors ease-linear duration-200">
                        <div @click="currentTab = 1" :class="currentTab == 1 ? 'text-primary border-b-2 border-primary pb-1 px-3 font-bold' : 'px-3 pb-1 text-gray66 font-semibold' " class="flex items-center space-x-2 cursor-pointer text-base">
                            <i class="fa-regular fa-file-lines"></i>
                            <p>Información del producto</p> 
                        </div>
                        <div @click="currentTab = 2" :class="currentTab == 2 ? 'text-primary border-b-2 border-primary pb-1 px-3 font-bold' : 'px-3 pb-1 text-gray66 font-semibold'" class="flex items-center space-x-2 cursor-pointer text-base">
                            <i class="fa-regular fa-calendar-check"></i>
                            <p>Historial de movimientos</p> 
                        </div>
                    </div>

                    <!-- pestaña 1 Informacion de producto -->
                    <div v-if="currentTab == 1" class="mt-7 mx-16">
                        <div class="flex justify-between items-center">
                            <p class="text-[#373737]">ID.{{ product.data.id }}</p>
                            <p class="text-[#373737]">Fecha de alta: <strong class="ml-5">{{ product.data.created_at }}</strong></p>
                        </div>
                        <h1 class="font-bold text-xl my-4">{{ product.data.name }}</h1>

                        <div class="w-1/2 mt-10 -ml-7 space-y-2">
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Precio de compra:</p>
                                <p class="text-right font-bold">${{ product.data.cost }}</p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Precio de venta: </p>
                                <p class="text-right font-bold">${{ product.data.public_price }}</p>
                            </div>
                            <div v-if="product.data.current_stock >= product.data.min_stock" class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Existencias: </p>
                                <p class="text-right font-bold text-[#5FCB1F]">{{ product.data.current_stock }}</p>
                            </div>
                            <div v-else class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1 relative">
                                <p class="text-[#373737]">Existencias: </p>
                                <p class="text-right font-bold text-primary">{{ product.data.current_stock }}<i class="fa-solid fa-arrow-down text-xs ml-2"></i></p>
                                <p class="absolute top-2 -right-16 text-xs font-bold text-primary">Bajo stock</p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Código: </p>
                                <p class="text-right font-bold">{{ product.data.code }}</p>
                            </div>

                            <h2 class="pt-5 ml-5 font-bold text-lg">Cantidades de stock permitidas</h2>

                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Cantidad mínima:</p>
                                <p class="text-right font-bold">{{ product.data.min_stock }}</p>
                            </div>
                            <div class="grid grid-cols-2 border border-grayD9 rounded-full px-5 py-1">
                                <p class="text-[#373737]">Cantidad máxima:</p>
                                <p class="text-right font-bold">{{ product.data.max_stock }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------- -->


                    <!-- pestaña 2 historial de producto -->
                    <div v-if="currentTab == 2" class="mt-7 mx-16">
                        <!-- estado de carga -->
                        <div v-if="loading" class="flex justify-center items-center py-10">
                            <i class="fa-solid fa-square fa-spin text-4xl text-primary"></i>
                        </div>
                        <div v-else>
                            <div v-for="(history, index) in productHistory" :key="history">
                                <h2 class="rounded-full text-sm bg-gray99 font-bold px-3 py-1 my-4 w-36">{{ translateMonth(index) }}</h2>
                                <p class="mt-1 ml-4 text-sm" v-for="activity in history" :key="activity"><span class="mr-2" v-html="getIcon(activity.type)"></span>{{ activity.description + ' ' + activity.created_at }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- ---------------------------------- -->
                </section>
            </div>
        </div>


        <!-- -------------- Modal starts----------------------- -->
      <Modal :show="entryProductModal" @close="entryProductModal = false">
        <div class="p-4 relative">
          <i @click="entryProductModal = false"
            class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
        
        <h1 class="font-bold my-4">Ingresar producto a almacén</h1>
          <section class="text-center mt-5 mb-2 mx-5">
                <div class="mt-3">
                    <InputLabel value="Cantidad" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.quantity" ref="quantityInput" @keydown.enter="entryProduct" placeholder="Catidad que entra a almacén"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\$\s?|(,*)/g, '')" >
                      <template #prefix>
                        <i class="fa-solid fa-hashtag"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.quantity" />
                </div>              

            <div class="flex justify-end space-x-3 pt-7 pb-1 py-2">
              <PrimaryButton @click="entryProduct" class="!rounded-full">Ingresar producto</PrimaryButton>
              <CancelButton @click="entryProductModal = false">Cancelar</CancelButton>
            </div>
          </section>
        </div>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
      quantity: null,
    });
    return {
        form,
        currentTab: 1,
        searchQuery: null,
        searchFocus: false,
        productsFound: null,
        entryProductModal: false,
        productHistory: null,
        loading: null,
    };
},
components:{
AppLayout,
PrimaryButton,
CancelButton,
InputError,
Modal,
Back
},
props:{
product: Object
},
methods:{
    async searchProducts() {
        try {
            const response = await axios.get(route('products.search'), { params: { query: this.searchQuery } });
            if (response.status == 200) {
                this.productsFound = response.data.items;
            }    
            
        } catch (error) {
            console.log(error);
        }
    },
     handleBlur() {
      // Introducir un retraso para dar tiempo al evento click de ejecutarse antes del blur
      setTimeout(() => {
        this.searchFocus = false;
      }, 100);
    },
    openEntryModal() {
      this.entryProductModal = true;
      this.$nextTick(() => {
        this.$refs.quantityInput.focus(); // Enfocar el input de código cuando se abre el modal
      });
    },
    entryProduct() {
       this.form.put(route('products.entry', this.product.data?.id), {
        onSuccess: () => {
            this.form.reset();
            this,this.entryProductModal = false;
            this.$notify({
                title: 'Correcto',
                text: 'Se ha ingresado ' + this.form.quantity + ' unidades de ',
                type: 'success',
            });
        },
      }); 
    },
    async fetchHistory() {
        this.loading = true;
        try {
          const response = await axios.get(route("products.fetch-history", this.product.data.id));
          if (response.status === 200) {
            this.productHistory = response.data.items;            
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
        }
    },
    getIcon(type) {
        if (type === 'Precio') {
            return '<i class="fa-solid fa-dollar-sign"></i>';
        } else if (type === 'Entrada') {
            return '<i class="fa-regular fa-square-plus"></i>';
        } else if (type === 'Venta') {
            return '<i class="fa-solid fa-hand-holding-dollar"></i>';
        }
    },
    translateMonth(dateString) {
      const [month, year] = dateString.split(' ');

      const monthsTranslation = {
        'January': 'Enero',
        'February': 'Febrero',
        'March': 'Marzo',
        'April': 'Abril',
        'May': 'Mayo',
        'June': 'Junio',
        'July': 'Julio',
        'August': 'Agosto',
        'September': 'Septiembre',
        'October': 'Octubre',
        'November': 'Noviembre',
        'December': 'Diciembre',
      };

      const translatedMonth = monthsTranslation[month] || month;

      return `${translatedMonth} ${year}`;
    },
},
mounted() {
    this.fetchHistory();
}   
}
</script>

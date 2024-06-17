<template>
    <AppLayout title="Crear cotización">
        <div class="px-3 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear cotización</h1>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <InputLabel value="Cliente (en caso de tenerlo registrado)" class="ml-3 mb-1" />
                        <button
                            @click="showClientFormModal = true" type="button"
                            class="rounded-full border border-primary size-4 flex items-center justify-center mb-1">
                            <i class="fa-solid fa-plus text-primary text-[9px] pl-[1px]"></i>
                        </button>
                    </div>
                    <el-select class="w-1/2" filterable v-model="form.client_id" clearable placeholder="Seleccione"
                        no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="client in clients" :key="client" :label="client.name"
                            :value="client.id" />
                    </el-select>
                    <InputError :message="form.errors.client_id" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Nombre del contacto*" class="ml-3 mb-1" />
                    <el-input v-model="form.contact_name" placeholder="Escribe el nombre del contacto" :maxlength="100" clearable />
                    <InputError :message="form.errors.contact_name" />
                </div>

                <div class="mt-3">
                    <InputLabel class="mb-1 ml-2" value="Teléfono*" />
                    <el-input v-model="form.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Correo electrónico (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="form.email" placeholder="Escribe el correo electrónico del contacto" :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>
                
                <div class="mt-3 col-span-full">
                    <InputLabel value="Dirección (opcional)" class="ml-3 mb-1" />
                    <el-input v-model="form.address" placeholder="Escribe la dirección del cliente" :maxlength="100" clearable />
                    <InputError :message="form.errors.address" />
                </div>

                <!-- productos -->
                <!-- ------------------------------------------------------------------- -->
                <h2 class="font-bold ml-3 mt-3 mb-1 col-span-full">Productos</h2>

                <section class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ProductInput :products="products" v-for="(item, index) in form.products" :key="item.id" :id="item.id"
                        @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.products?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                    productos </p>
                </section>
                <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                        <i class="fa-solid fa-plus"></i>
                        Agregar producto
                    </button>
                </div>
                <!-- -------------------------------------------------------------------- -->


                <!-- Servicios -->
                <!-- ------------------------------------------------------------------- -->
                <h2 class="font-bold ml-3 mt-3 mb-1 col-span-full">Servicios</h2>

                <section class="max-h-72 overflow-auto col-span-full">
                    <div class="space-y-3">
                        <ServiceInput :services="services" v-for="(item, index) in form.services" :key="item.id" :id="item.id"
                        @deleteItem="deleteItem(index)" @syncItem="syncItems(index, $event)" class="mb-1" />
                    </div>
                    <p v-if="!form.services?.length" class="text-sm text-gray-600"> Click al botón de "+" para empezar a agregar
                    servicios </p>
                </section>
                <div class="mt-4 mb-6 text-left flex justify-between border-t border-grayD9 pt-2 text-sm col-span-full">
                    <button class="text-primary text-sm self-start" type="button" @click="addNewItem">
                        <i class="fa-solid fa-plus"></i>
                        Agregar Servicio
                    </button>
                </div>
                <!-- -------------------------------------------------------------------- -->

                <div class="mt-3 col-span-full">
                    <InputLabel value="Notas adicionales (opcional)" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Escribe aquí tus comentarios que consideres relevantes para el cliente" :maxlength="800" show-word-limit
                        clearable />
                    <InputError :message="form.errors.notes" />
                </div>

                <div class="col-span-2 text-right mt-5">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Crear cotización
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProductInput from "@/Components/MyComponents/ProductInput.vue";
import ServiceInput from "@/Components/MyComponents/ServiceInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        client_id: null,
        contact_name: null,
        phone: null,
        email: null,
        address: null,
        category: null,
        description: null,
        price: null,
        notes: null,
        products: [
            {
                id: 1,
                name: null,
                product_id: null,
                price: 1,
                isLocal: null,
                quantity: 1,
                image_url: null,
            }
        ],
        services: [
            {
                id: 1,
                name: null,
                price: 1,
                quantity: 1,
            }
        ],
    });

    return {
        form,
        products: null, //se obtienne todos los productos de la tienda
        services: null, //se obtienne todos los servicios de la tienda
        next_item_id: 2, //para el index de productos creados como venta en linea
    }
},
components:{
    AppLayout,
    PrimaryButton,
    ProductInput,
    ServiceInput,
    InputLabel,
    InputError,
    Back
},
props:{
    clients: Array,
},
methods:{
    store() {
        this.form.post(route("quotes.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado una cotización",
                    type: "success",
                });
            },
        });
    },
    addNewItem() {
      this.form.products.push({ id: this.next_item_id++, price: null, product_id: null, isLocal: null, quantity: null });
    },
    deleteItem(index) {
      if (this.form.products.length > 1) {
        this.form.products.splice(index, 1);
        //actualiza el total y precio de envío cuando se elimina algun producto
        // this.syncItems();
      }
    },
    syncItems(index, product_obj) {
        this.form.products[index] = product_obj;
    },
    async fetchAllProducts() {
        try {
          const response = await axios.get(route('online-sales.fetch-all-products'));
          if ( response.status === 200 ) {
            this.products = response.data.products;
          }  
        } catch (error) {
            console.log(error);
        }
    },
    async fetchAllServices() {
        try {
          const response = await axios.get(route('services.fetch-all-services'));
          if ( response.status === 200 ) {
            this.services = response.data.services;
          }  
        } catch (error) {
            console.log(error);
        }
    },
},
mounted() {
    this.fetchAllProducts();
    this.fetchAllServices();
}
}
</script>

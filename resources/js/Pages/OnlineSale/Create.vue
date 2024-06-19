<template>
    <OnlineStoreLayout title="Mi carrito">
        <div class="p-3 lg:p-9">
            <Back />

            <section class="md:grid grid-cols-2 gap-x-9 my-8 lg:mx-16">
                <!-- Formulario -->
                <form @submit.prevent="store">
                    <h1 class="font-bold ml-2">Contacto</h1>

                    <div class="mt-3">
                        <InputLabel value="Nombre*" class="ml-3 mb-1" />
                        <el-input v-model="form.name" placeholder="Escribe tu nombre" :maxlength="255" clearable />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="mt-3">
                        <InputLabel class="mb-1 ml-2" value="Teléfono *" />
                        <el-input v-model="form.phone"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                        placeholder="Escribe el número de teléfono" />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <!-- <div class="mt-3">
                        <InputLabel value="Correo electrónico (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="form.email" placeholder="Escribe tu correo electrónico" :maxlength="255" clearable />
                        <InputError :message="form.errors.email" />
                    </div> -->

                    <h1 class="font-bold my-3 ml-3">Dirección</h1>

                    <div class="mt-3">
                        <InputLabel value="Calle*" class="ml-3 mb-1" />
                        <el-input v-model="form.street" placeholder="Escribe tu calle" :maxlength="255" clearable />
                        <InputError :message="form.errors.street" />
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Colonia*" class="ml-3 mb-1" />
                        <el-input v-model="form.suburb" placeholder="Escribe tu colonia" :maxlength="255" clearable />
                        <InputError :message="form.errors.suburb" />
                    </div>

                    <div class="grid grid-cols-2 gap-x-7 mt-3">
                        <div>
                            <InputLabel class="mb-1 ml-2" value="Código postal" />
                            <el-input v-model="form.postal_code"
                            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                            :parser="(value) => value.replace(/\D/g, '')" maxlength="6" clearable
                            placeholder="Escribe el código postal" />
                            <InputError :message="form.errors.postal_code" />
                        </div>

                        <div>
                            <InputLabel value="Estado*" class="ml-3 mb-1" />
                            <el-input v-model="form.polity_state" placeholder="Ej. Jalisco, Monterrey, Michoacan" :maxlength="255" clearable />
                            <InputError :message="form.errors.polity_state" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-7 mt-3">
                        <div>
                            <InputLabel value="Número exterior*" class="ml-3 mb-1" />
                            <el-input v-model="form.ext_number" placeholder="Número de tu casa" :maxlength="255" clearable />
                            <InputError :message="form.errors.ext_number" />
                        </div>

                        <div>
                            <InputLabel value="Número Interior" class="ml-3 mb-1" />
                            <el-input v-model="form.int_number" placeholder="Número de edificio, coto, fraccionamiento" :maxlength="255" clearable />
                            <InputError :message="form.errors.int_number" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Referencias (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="form.address_references" placeholder="Escribe referencias que nos ayuden a encontrar tu domicilio. Ej. Oxxo en la esquina" :maxlength="255" clearable />
                        <InputError :message="form.errors.address_references" />
                    </div>

                    <div class="mt-5 border border-primary rounded-lg p-4">
                        <p class="font-bold mb-3">Importante</p>
                        <p class="text-sm">
                            Una vez que confirmes tu pedido, nos pondremos en contacto contigo a través de WhatsApp <i class="fa-brands fa-whatsapp text-sm text-green-500"></i> al número proporcionado para coordinar
                            todos los detalles de la entrega y el pago.
                        </p>
                        <p v-if="store?.online_store_properties?.delivery_conditions" class="mt-4 text-primary text-sm">Condiciones de envío: <strong>{{ store?.online_store_properties?.delivery_conditions }}</strong></p>
                    </div>
                </form>

                <!-- Resumen de pedido -->
                <article class="border border-grayD9 rounded-lg mt-12 md:mt-0 self-start">
                    <div class="flex items-center justify-between border-b border-grayD9 py-2 px-7">
                        <p class="font-bold text-sm">Resumen del pedido</p>
                        <p @click="$inertia.get(route('online-sales.cart'))" class="text-primary cursor-pointer text-sm">Editar</p>
                    </div>
                    
                    <!-- body -->
                    <div class="py-3 px-7 space-y-3 h-[450px] overflow-auto">
                        <CartProductCard :cartProduct="product" v-for="product in cart" :key="product" :actions="false" />
                    </div>
                    
                    <!-- Total  -->
                    <div class="flex justify-between px-10 py-4 text-sm border">
                        <div class="w-48">
                            <p>Subtotal:</p>
                            <p>Costo de envío:</p>
                            <p>Total:</p>
                        </div>
                        <div>
                            <p><span class="mr-4">$</span>{{ form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            <!-- Costo de envio si esta activado el minimo para envio gratis -->
                            <div v-if="store?.online_store_properties?.enabled_free_delivery">
                                <p v-if="(form.total < store?.online_store_properties?.min_free_delivery)"><span class="mr-4">$</span>{{ parseFloat(store?.online_store_properties?.delivery_price || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                                <p class="text-green-500" v-else><span class="mr-4">$</span>0</p>
                            </div>
                            <!-- Costo de envío si esta desactivado el envio gratis -->
                            <p v-else><span class="mr-4">$</span>{{ parseFloat(store?.online_store_properties?.delivery_price || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            <!-- Total a pagar -->
                            <p v-if="form.total < store?.online_store_properties?.min_free_delivery" class="font-bold"><span class="mr-4">$</span>{{ (form.total + parseFloat(store?.online_store_properties?.delivery_price || 0)).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                            <p v-else class="font-bold"><span class="mr-4">$</span>{{ form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                        </div>
                    </div>

                    <!-- Aviso de restante para envio gratis -->
                    <p v-if="(form.total < store?.online_store_properties?.min_free_delivery) && store?.online_store_properties?.enabled_free_delivery" 
                        class="text-sm text-center py-1 border-b border-grayD9">
                        Agregar 
                        <span>
                            ${{ (parseFloat(store?.online_store_properties?.min_free_delivery || 0) - form.total)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                        </span>
                        más para conseguir envió gratis. 
                        <span @click="$inertia.get(route('online-sales.client-index', form.store_id ?? 0))" class="text-primary cursor-pointer ml-1">Seguir comprando</span>
                    </p>
                    
                    <label for="confirm" class="text-xs items-center flex">
                        <el-checkbox id="confirm" class="px-2" name="confirm" v-model="confirmSale"></el-checkbox>
                        Confirmo que mi pedido es correcto y deseo continuar
                    </label>

                    <div class="col-span-2 text-center py-3">
                        <PrimaryButton @click="storeOrder" class="!px-16" :disabled="form.processing || !confirmSale">
                            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            Relizar pedido
                        </PrimaryButton>
                    </div>
                </article>
            </section>
        </div>
    </OnlineStoreLayout>
</template>

<script>
import OnlineStoreLayout from '@/Layouts/OnlineStoreLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CartProductCard from '@/Components/MyComponents/OnlineSale/CartProductCard.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    const form = useForm({
        // Contacto -------------
        name: null,
        phone: null,
        email: null,
        // Domicilio --------------
        suburb: null,
        street: null,
        ext_number: null,
        int_number: null,
        postal_code: null,
        polity_state: null,
        // Envío -----------------------
        products: null, //productos
        delivery_price: 0, //precio de envío
        total: null, //cantidad total de venta $
        address_references: null, //referencias para dar con el lugar
        store_id: null,
        store_inventory: null //bandera para saber si la configuración de inventario está activa y descontar productos
    });

    return {
        form,
        confirmSale: false,
        cart: [],
        store: {} //guarda la información de la tienda
    }
},
components:{
OnlineStoreLayout,
CartProductCard,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{
},
methods:{
    storeOrder() {
        // si no se alcanza el monto mínimo calcula el envio.
        const storeProperties = this.store?.online_store_properties;
        this.form.delivery_price = storeProperties?.enabled_free_delivery && this.form.total >= storeProperties?.min_free_delivery 
        ? 0 
        : parseFloat(storeProperties?.delivery_price);

        this.form.post(route("online-sales.store"), {
            onSuccess: () => {
                // Mandar informacion de actualizacion de stock para IDB del admin de la tienda
                // Aqui

                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado tu pedido correctamente. Nos comunicaremos contigo",
                    type: "success",
                });
                localStorage.removeItem('Ezycart');
                location.reload();
            },
        });
    },
    async fetchStoreInfo() {
        try {
            const response = await axios.get(route('stores.fetch-store-info', this.form.store_id));
            if ( response.status === 200 ) {
                this.store = response.data.store;
                this.form.store_inventory = this.store.online_store_properties?.inventory;
            }
        } catch (error) {
            console.log(error);
        }
    }
},
mounted() {
    // Obtener el carrito actual desde localStorage
    this.cart = JSON.parse(localStorage.getItem('Ezycart')) || [];

    //guarda el carrito en variable products del formulario para guardarlo en la base de datos
    this.form.products = this.cart;

    //calcula el total de la venta en el carrito
    this.form.total = this.cart.reduce((sum, item) => {
        return sum + (item.price * item.quantity);
    }, 0);

},
created() {
    // recupera el store_id del localStorage
    this.form.store_id = localStorage.getItem('storeId');

    // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
    this.fetchStoreInfo();
}
}
</script>

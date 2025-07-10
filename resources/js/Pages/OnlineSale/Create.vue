<template>
    <OnlineStoreLayout title="Terminar pedido">
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
                            <el-select v-model="form.polity_state" class="!w-full" filterable required clearable
                                placeholder="Selecciona" no-data-text="No hay opciones registradas"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="(state, index) in polityStates" :key="index" :value="state"
                                    :label="state" />
                            </el-select>
                            <InputError :message="form.errors.polity_state" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-7 mt-3">
                        <div>
                            <InputLabel value="Número exterior*" class="ml-3 mb-1" />
                            <el-input v-model="form.ext_number" placeholder="Número de tu casa" :maxlength="255"
                                clearable />
                            <InputError :message="form.errors.ext_number" />
                        </div>

                        <div>
                            <InputLabel value="Número Interior" class="ml-3 mb-1" />
                            <el-input v-model="form.int_number" placeholder="Número de edificio, coto, fraccionamiento"
                                :maxlength="255" clearable />
                            <InputError :message="form.errors.int_number" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <InputLabel value="Referencias (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="form.address_references"
                            placeholder="Escribe referencias que nos ayuden a encontrar tu domicilio. Ej. Oxxo en la esquina"
                            :maxlength="255" clearable />
                        <InputError :message="form.errors.address_references" />
                    </div>

                    <div class="mt-5 border border-primary rounded-lg p-4">
                        <p class="font-bold mb-3">Importante</p>
                        <p class="text-sm">
                            Una vez que confirmes tu pedido, nos pondremos en contacto contigo a través de WhatsApp <i
                                class="fa-brands fa-whatsapp text-sm text-green-500"></i> al número proporcionado para
                            coordinar
                            todos los detalles de la entrega y el pago.
                        </p>
                        <p v-if="store?.online_store_properties?.delivery_conditions"
                            class="flex space-x-2 mt-4 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 md:size-5||||||||||||||||||||||||">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>
                            <span>
                                Condiciones de envío: <b>{{ store?.online_store_properties?.delivery_conditions }}</b>
                            </span>
                        </p>
                    </div>
                </form>

                <!-- Resumen de pedido -->
                <article class="border border-grayD9 rounded-lg mt-12 md:mt-0 self-start">
                    <div class="flex items-center justify-between border-b border-grayD9 py-2 px-7">
                        <p class="font-bold text-sm">Resumen del pedido</p>
                        <p @click="$inertia.get(route('online-sales.cart', store?.slug))"
                            class="text-primary cursor-pointer text-sm">Editar</p>
                    </div>

                    <!-- body -->
                    <div class="py-3 px-7 space-y-3 h-[450px] overflow-auto">
                        <CartProductCard :cartProduct="product" v-for="product in cart" :key="product"
                            :actions="false" />
                    </div>

                    <!-- Total  -->
                    <div class="flex justify-between px-10 py-4 text-sm border">
                        <div class="w-48">
                            <p>Subtotal:</p>
                            <p>Costo de envío:</p>
                            <strong>Total:</strong>
                        </div>
                        <div class="w-20">
                            <p class="flex items-center justify-between">
                                <span>$</span>
                                <span>
                                    {{ form.total?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}
                                </span>
                            </p>
                            <p class="flex items-center justify-between">
                                <span>$</span>
                                <span>{{ deliveryPrice.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            </p>
                            <p class="font-bold flex items-center justify-between">
                                <span>$</span>
                                <span>{{ (form.total + deliveryPrice).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }}</span>
                            </p>
                        </div>
                    </div>
                    <div v-if="deliveryPrice" class="text-center py-2 border-b">
                        <p class="text-sm">Agregar ${{ remainingToFreeDelivery }} más para conseguir envió gratis.
                            <span @click="$inertia.get(route('online-sales.client-index', encodedIdStore ?? 0))"
                                class="text-primary cursor-pointer ml-1">Seguir comprando</span>
                        </p>
                    </div>
                    <label for="confirm" class="text-xs items-center flex">
                        <el-checkbox id="confirm" class="px-2" name="confirm" v-model="confirmSale"></el-checkbox>
                        Confirmo que mi pedido es correcto y deseo continuar
                    </label>

                    <div class="col-span-2 text-center py-3">
                        <PrimaryButton @click="storeOrder" class="!px-16" :disabled="form.processing || !confirmSale">
                            <i v-if="form.processing"
                                class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
import emitter from '@/eventBus.js';

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
            store_inventory: null, //bandera para saber si la configuración de inventario está activa y descontar productos
        });

        return {
            form,
            confirmSale: false,
            cart: [],
            encodedIdStore: null, //id codificado de la tienda
            store: {}, //guarda la información de la tienda
            polityStates: [
                "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas", "Chihuahua",
                "Ciudad de México", "Coahuila", "Colima", "Durango", "Estado de México", "Guanajuato", "Guerrero",
                "Hidalgo", "Jalisco", "Michoacán", "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla", "Querétaro",
                "Quintana Roo", "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz",
                "Yucatán", "Zacatecas"
            ],
        }
    },
    components: {
        OnlineStoreLayout,
        CartProductCard,
        PrimaryButton,
        InputLabel,
        InputError,
        Back
    },
    props: {
    },
    computed: {
        deliveryPrice() {
            const delivery = this.store.online_store_properties?.delivery_price ?? 0;
            const minFreeDelivery = this.store.online_store_properties?.min_free_delivery ?? 0;

            if (minFreeDelivery === 0 || this.form.total < minFreeDelivery) {
                return parseFloat(delivery);
            }

            return 0;
        },
        remainingToFreeDelivery() {
            const minFreeDelivery = this.store.online_store_properties?.min_free_delivery ?? 0;
            return minFreeDelivery - this.form.total;
        }
    },
    methods: {
        storeOrder() {
            this.form.delivery_price = this.deliveryPrice;
            this.form.post(route("online-sales.store"), {
                onSuccess: () => {
                    // limpiar carro de compras
                    localStorage.removeItem('Ezycart');
                    emitter.emit('update-cart');
                },
            });
        },
        encodeStoreId() {
            const encodedId = btoa(this.form.store_id?.toString());
            this.encodedIdStore = encodedId;
        },
        async fetchStoreInfo() {
            try {
                const response = await axios.get(route('stores.fetch-store-info', this.form.store_id));
                if (response.status === 200) {
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

        this.encodeStoreId(); //codifica el id de la tienda para mandarlo como parametro e ir al index
    },
    created() {
        // recupera el store_id del localStorage
        this.form.store_id = localStorage.getItem('storeId');

        // recupera la información de la tienda para tomar las configuraciones de la tienda en linea.
        this.fetchStoreInfo();
    }
}
</script>

<template>
    <OnlineStoreLayout title="Mi carrito">
        <div class="p-3 lg:p-9">
            <Back />

            <section class="md:grid grid-cols-2 gap-x-9 my-8 lg:mx-16">
                <!-- Formulario -->
                <form @submit.prevent="store">
                    <h1 class="font-bold ml-2 col-span-full">Contacto</h1>

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

                    <div class="mt-3">
                        <InputLabel value="Correo electrónico (opcional)" class="ml-3 mb-1" />
                        <el-input v-model="form.email" placeholder="Escribe tu correo electrónico" :maxlength="255" clearable />
                        <InputError :message="form.errors.email" />
                    </div>

                    <h1 class="font-bold my-3 ml-3 col-span-full">Dirección</h1>

                    <div>
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

                    <div class="mt-5 border border-grayD9 rounded-lg p-4">
                        <p class="font-bold mb-3">Importante</p>
                        <p class="text-sm">
                            Una vez que confirmes tu pedido, nos pondremos en contacto contigo a través de WhatsApp al número proporcionado para coordinar
                            todos los detalles de la entrega y el pago.
                        </p>
                    </div>
                </form>

                <!-- Resumen de pedido -->
                <div class="border border-grayD9 rounded-lg mt-12 md:mt-0 self-start">
                    <div class="flex items-center justify-between border-b border-grayD9 py-2 px-7">
                        <p class="font-bold text-sm">Resumen del pedido</p>
                        <p @click="$inertia.get(route('online-sales.cart'))" class="text-primary cursor-pointer text-sm">Editar</p>
                    </div>
                    
                    <!-- body -->
                    <div class="py-3 px-7 space-y-3 h-[450px] overflow-auto">
                        <CartProductCard :cartProduct="product" v-for="product in cart" :key="product" :actions="false" />
                    </div>

                    <el-checkbox class="mt-5 ml-3" v-model="confirmSale">Confirmo que mi pedido es correcto y deseo continuar</el-checkbox>

                    <div class="col-span-2 text-center py-3">
                        <PrimaryButton @click="store" class="!px-16" :disabled="form.processing || !confirmSale">Relizar pedido</PrimaryButton>
                    </div>
                </div>
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

export default {
data() {
    const form = useForm({
        name: null, //contacto
        phone: null, //contacto
        email: null, //contacto
        suburb: null, //Dirección / colonia
        street: null, //Dirección / calle
        ext_number: null, //Dirección
        int_number: null, //Dirección
    });
    return {
        form,
        confirmSale: false,
        cart: []
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
    store() {
        this.form.post(route("online-sales.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha creado tu pedido correctamente. Nos comunicaremos contigo",
                    type: "success",
                });
            },
        });
    },
},
mounted() {
    // Obtener el carrito actual desde localStorage
    this.cart = JSON.parse(localStorage.getItem('Ezycart')) || [];
}
}
</script>

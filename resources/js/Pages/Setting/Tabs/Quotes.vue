<template>
    <div>
        <section class="my-5 divide-y-[1px] border border-grayD9 rounded-[5px]">
            <article class="p-4 lg:flex justify-between text-sm">
                <div class="lg:w-1/2">
                    <h2>Pie de página </h2>
                    <p class="text-[#575757] text-justify">
                        Texto opcional que aparecerá al final de todas tus cotizaciones. <br>
                        P.ej. Precios sujetos a cambio.
                    </p>
                </div>
                <div class="lg:w-1/2 grid grid-cols-2 gap-3 items-start *:text-end mt-5 lg:mt-0">
                    <p>Texto:</p>
                    <el-input v-model="form.quote_config.footer" @blur="updateFooter"
                        :autosize="{ minRows: 3, maxRows: 6 }" type="textarea"
                        placeholder="Escribe aquí el pie de página." :maxlength="800" show-word-limit clearable />
                    <p v-if="loadingFooter" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
                </div>
            </article>
            <article class="p-4 lg:flex justify-between text-sm">
                <div class="lg:w-1/2">
                    <h2>Datos de contacto</h2>
                    <p class="text-[#575757] text-justify">
                        Son tus datos que se mostrarán al final de la cotización como contacto
                    </p>
                </div>
                <div class="lg:w-1/2 grid grid-cols-2 gap-3 items-center *:text-end mt-5 lg:mt-0">
                    <p>Nombre:</p>
                    <el-input v-model="form.quote_config.contact.name" @blur="updateContactInfo"
                        @keyup.enter="updateContactInfo" maxlength="255" clearable placeholder="Ej. David Peralta" />
                    <p>Whatsapp:</p>
                    <el-input v-model="form.quote_config.contact.whatsapp" @blur="updateContactInfo"
                        @keyup.enter="updateContactInfo" placeholder="Ej. 3333123456"
                        :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                        :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable>
                        <template #prepend>
                            <img src="@/../../public/images/mxFlag.png" alt="Bandera de mexico en miniatura"
                                :draggable="false" class="select-none" />
                            <span class="ml-1">+52</span>
                        </template>
                    </el-input>
                    <p>Correo electrónico:</p>
                    <el-input v-model="form.quote_config.contact.email" @blur="updateContactInfo"
                        @keyup.enter="updateContactInfo" maxlength="255" clearable
                        placeholder="Escribe tu correo electrónico" />
                    <p v-if="loadingContactInfo" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
                </div>
            </article>
            <!-- <article class="p-4 lg:flex justify-between text-sm">
                <div class="lg:w-1/2">
                    <h2>Mostrar información del cliente</h2>
                    <p class="text-[#575757] text-justify">
                        Activa las opciones si deseas que parezcan los datos del cliente al que va dirigida la
                        cotización
                    </p>
                </div>
                <div class="lg:w-1/4 flex flex-col mt-5 lg:mt-0">
                    <el-checkbox v-model="form.quote_config.show_company" label="Nombre de la empresa" size="small" />
                    <el-checkbox v-model="form.quote_config.show_contact" label="Nombre de cliente" size="small" />
                    <el-checkbox v-model="form.quote_config.show_phone" label="Teléfono" size="small" />
                    <el-checkbox v-model="form.quote_config.show_email" label="Correo electrónico" size="small" />
                    <el-checkbox v-model="form.quote_config.show_address" label="Dirección" size="small" />
                    <p v-if="loadingContactInfo" class="text-gray-400 text-end text-xs col-span-full">Guardando...</p>
                </div>
            </article> -->
        </section>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            quote_config: {
                footer: this.$page.props.auth.user.quote_config?.footer ?? null,
                contact: {
                    name: this.$page.props.auth.user.quote_config?.contact?.name ?? null,
                    whatsapp: this.$page.props.auth.user.quote_config?.contact?.whatsapp ?? null,
                    email: this.$page.props.auth.user.quote_config?.contact?.email ?? null,
                }
            }
        });

        return {
            form,
            // cargas
            loadingFooter: false,
            loadingContactInfo: false,
        }
    },
    components: {
        PrimaryButton,
    },
    props: {
    },
    methods: {
        updateFooter() {
            if (this.form.quote_config.footer !== this.$page.props.auth.user.quote_config?.footer) {
                this.loadingFooter = true;
                this.form.put(route('users.update-quote-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingFooter = false;
                    },
                });
            }
        },
        updateContactInfo() {
            if (this.form.quote_config.contact.name !== this.$page.props.auth.user.quote_config?.contact.name ||
                this.form.quote_config.contact.whatsapp !== this.$page.props.auth.user.quote_config?.contact.whatsapp ||
                this.form.quote_config.contact.email !== this.$page.props.auth.user.quote_config?.contact.email
            ) {
                this.loadingContactInfo = true;
                this.form.put(route('users.update-quote-config', this.$page.props.auth.user.id), {
                    onFinish: () => {
                        this.loadingContactInfo = false;
                    },
                });
            }
        },
    },
}
</script>

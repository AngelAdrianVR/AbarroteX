<template>
    <section>
        <h1 class="ml-3">Información básica</h1>
        <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
            <article>
                <h2 v-if="canSeeStoreInfo">Información de la Tienda</h2>
                <div class="flex items-start space-x-3 justify-between">
                    <div class="grid grid-cols-2 gap-2 mt-2 w-5/6">
                        <p v-if="canSeeStoreInfo" class="flex flex-col">
                            <b>Nombre de la Tienda </b>
                            <el-input v-if="edit" v-model="form.store_name" placeholder="Nombre de tienda *"
                                :disabled="form.processing" maxlength="255" />
                            <span v-else>{{ form.store_name }}</span>
                            <InputError :message="form.errors.store_name" />
                        </p>
                        <p v-if="canSeeStoreInfo" class="flex flex-col">
                            <b>Dirección</b>
                            <el-input v-if="edit" v-model="form.store_address" placeholder="Dirección de tienda"
                                :disabled="form.processing" maxlength="255" />
                            <span v-else>{{ form.store_address ?? '-' }}</span>
                            <InputError :message="form.errors.store_address" />
                        </p>
                        <div v-if="canSeeStoreInfo" class="flex flex-col">
                            <b>Logo</b>
                            <div>
                                <InputFilePreview @imagen="storeLogo($event)" width="w-32" height="h-24"
                                    :imageUrl="storeLogoUrl" @cleared="storeLogo()" />
                                <p v-if="logoForm.processing" class="text-gray-400 text-xs col-span-full">
                                    Guardando...
                                </p>
                            </div>
                        </div>
                        <el-divider content-position="left" class="col-span-full">Información del usuario</el-divider>
                        <p class="flex flex-col">
                            <b> Nombre</b>
                            <el-input v-if="edit" v-model="form.name" placeholder="Nombre *" :disabled="form.processing"
                                maxlength="255" />
                            <span v-else>{{ form.name }}</span>
                            <InputError :message="form.errors.name" />
                        </p>
                        <p class="flex flex-col">
                            <b>Teléfono</b>
                            <el-input v-if="edit" v-model="form.contact_phone" placeholder="Teléfono"
                                :disabled="form.processing"
                                :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                                :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable />
                            <span v-else>{{ form.contact_phone ?? '-' }}</span>
                            <InputError :message="form.errors.contact_phone" />
                        </p>
                        <p class="flex flex-col">
                            <b>Correo electrónico</b>
                            <el-input v-if="edit" v-model="form.email" placeholder="Correo *"
                                :disabled="form.processing" maxlength="255" />
                            <span v-else>{{ form.email }}</span>
                            <InputError :message="form.errors.email" />
                        </p>
                    </div>
                    <div v-if="!form.processing">
                        <div v-if="edit"
                            class="flex items-center space-x-1 *:size-5 *:rounded-full *:flex *:items-center *:justify-center *:border">
                            <el-tooltip content="Cancelar" placement="left">
                                <button @click="edit = false; form.reset(); form.clearErrors()"
                                    class="text-gray-600 text-[9px] hover:bg-gray-100 transition-all duration-150"><i
                                        class="fa-solid fa-x"></i></button>
                            </el-tooltip>
                            <el-tooltip content="Guardar" placement="right">
                                <button @click="store"
                                    class="text-green-600 text-[9px] hover:bg-green-100 transition-all duration-150"><i
                                        class="fa-solid fa-check"></i></button>
                            </el-tooltip>
                        </div>
                        <button @click="edit = true" v-else class="text-primary text-xs">Modificar</button>
                    </div>
                    <p v-else class="text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-primary mr-px"></i>
                        Cargando...
                    </p>
                </div>
            </article>
        </div>
    </section>
</template>
<script>
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputFilePreview from '@/Components/MyComponents/InputFilePreview.vue';

export default {
    data() {
        const form = useForm({
            store_name: this.$page.props.auth.user.store.name,
            store_address: this.$page.props.auth.user.store.address,
            name: this.$page.props.auth.user.name,
            contact_phone: this.$page.props.auth.user.phone,
            email: this.$page.props.auth.user.email,
        });

        const logoForm = useForm({
            img: null,
        });

        return {
            logoForm,
            form,
            edit: false,
            // Permisos de rol actual
            canSeeStoreInfo: ['Administrador'].includes(this.$page.props.auth.user.rol),
        };
    },
    components: {
        InputError,
        InputFilePreview,
    },
    props: {
        user: Object,
    },
    computed: {
        storeLogoUrl() {
            return this.$page.props.auth.user.store.media?.find(media => media.collection_name === 'logo')?.original_url;
        },
    },
    methods: {
        storeLogo(img = null) {
            this.logoForm.img = img;
            this.logoForm.post(route("stores.store-logo"));
        },
        store() {
            this.form.put(route('ezy-profile.update-basic'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        message: '',
                        type: 'success',
                    });
                    this.edit = false;
                },
            });
        },
    }
}
</script>
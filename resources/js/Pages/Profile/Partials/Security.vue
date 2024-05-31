<template>
    <section>
        <h1 class="ml-3">Seguridad y privacidad</h1>
        <div class="rounded-[5px] border border-grayD9 px-4 py-4 mt-3">
            <article class="border-b pb-5 border-grayD9">
                <div v-if="!editPassword" class="grid grid-cols-3 gap-2">
                    <p>Contraseña</p>
                    <p>*******</p>
                    <button @click="editPassword = true" class="text-primary text-xs justify-self-end">
                        Cambiar
                    </button>
                </div>
                <div v-else>
                    <button @click="editPassword = false" type="button" class="my-px text-[9px] self-start">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <form @submit.prevent="updatePassword">
                        <div>
                            <InputLabel value="Contraseña actual" />
                            <el-input ref="currentPasswordInput" type="password" v-model="formPassword.current_password"
                                class="md:!w-1/3" autocomplete="current-password" :disabled="formPassword.processing"
                                maxlength="255" />
                            <InputError :message="formPassword.errors.current_password" />
                        </div>
                        <div class="mt-3">
                            <InputLabel value="Contraseña nueva" />
                            <el-input ref="passwordInput" type="password" v-model="formPassword.password"
                                autocomplete="new-password" class="md:!w-1/3" :disabled="formPassword.processing"
                                maxlength="255" />
                            <InputError :message="formPassword.errors.password" />
                        </div>
                        <div class="mt-3">
                            <InputLabel value="Confirmar contraseña" />
                            <el-input v-model="formPassword.password_confirmation" type="password"
                                autocomplete="new-password" class="md:!w-1/3" :disabled="formPassword.processing"
                                maxlength="255" />
                            <InputError :message="formPassword.errors.password_confirmation" />
                        </div>
                        <div class="flex items-center space-x-2 mt-5">
                            <PrimaryButton :class="{ 'opacity-25': formPassword.processing }"
                                :disabled="formPassword.processing">
                                Guardar
                            </PrimaryButton>
                            <ActionMessage :on="formPassword.recentlySuccessful" class="me-3">
                                <div class="flex items-center space-x-2 text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Guardado</span>
                                </div>
                            </ActionMessage>
                        </div>
                    </form>
                </div>
            </article>
            <article class="mt-5">
                <div v-if="!editSessions" class="grid grid-cols-3 gap-2">
                    <p>Sesiones</p>
                    <p>{{ sessions.length }} {{ sessions.length > 1 ? 'sesiones' : 'sesión' }}</p>
                    <button @click="editSessions = true" class="text-primary text-xs justify-self-end">
                        Ver detalles
                    </button>
                </div>
                <div v-else>
                    <button @click="editSessions = false" type="button" class="my-px text-[9px] self-start">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <div>
                        <p class="text-gray99 text-justify text-xs">
                            Si es necesario, puede cerrar sesión en todos sus
                            dispositivos. Algunas de sus sesiones recientes se enumeran a continuación. Si cree que su
                            cuenta está comprometida o que alguien pueda ingresar sin su autorización,
                            también debe actualizar su contraseña.
                        </p>
                        <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                            <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                                <div>
                                    <svg v-if="session.agent.is_desktop" class="w-8 h-8 text-gray-500"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                    </svg>

                                    <svg v-else class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                </div>

                                <div class="ms-3">
                                    <div class="text-sm text-gray-600">
                                        {{ session.agent.platform ? session.agent.platform : 'desconocido' }} - {{
                                            session.agent.browser ? session.agent.browser : 'desconocido' }}
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500">
                                            {{ session.ip_address }},

                                            <span v-if="session.is_current_device"
                                                class="text-green-500 font-semibold">Este
                                                dispositivo</span>
                                            <span v-else>Última vez activo {{ session.last_active }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center mt-5">
                        <PrimaryButton v-if="sessions.length > 1" @click="confirmLogout">
                            Cerrar sesión en otros dispositivos
                        </PrimaryButton>
                        <ActionMessage :on="formLogout.recentlySuccessful" class="ms-3">
                            <div class="flex items-center space-x-2 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>Hecho</span>
                            </div>
                        </ActionMessage>
                    </div>

                    <!-- Log Out Other Devices Confirmation Modal -->
                    <DialogModal :show="showConfirmingLogout" @close="closeLogoutModal">
                        <template #title>
                            Cerrar sesión en otros dispositivos
                        </template>

                        <template #content>
                            Ingrese su contraseña para confirmar que desea cerrar sesión en todos sus dispositivos.
                            <div class="mt-4">
                                <el-input ref="passwordInput" v-model="formLogout.password" type="password" class="mt-1"
                                    placeholder="Contraseña" autocomplete="current-password"
                                    @keyup.enter="logoutOtherBrowserSessions" />

                                <InputError :message="formLogout.errors.password" class="mt-2" />
                            </div>
                        </template>

                        <template #footer>
                            <CancelButton @click="closeLogoutModal">
                                Cancelar
                            </CancelButton>
                            <PrimaryButton class="ms-3" :class="{ 'opacity-25': formLogout.processing }"
                                :disabled="formLogout.processing" @click="logoutOtherBrowserSessions">
                                Cerrar sesión en otros dispositivos
                            </PrimaryButton>
                        </template>
                    </DialogModal>
                </div>
            </article>
        </div>
    </section>
</template>
<script>
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ActionMessage from '@/Components/ActionMessage.vue';

export default {
    data() {
        const formLogout = useForm({
            password: '',
        });

        const formPassword = useForm({
            current_password: '',
            password: '',
            password_confirmation: '',
        });

        return {
            formLogout,
            formPassword,
            editSessions: false,
            editPassword: false,
            showConfirmingLogout: false,
        };
    },
    components: {
        InputError,
        InputLabel,
        DialogModal,
        PrimaryButton,
        CancelButton,
        ActionMessage,
    },
    props: {
        sessions: Array,
    },
    methods: {
        confirmLogout() {
            this.showConfirmingLogout = true;
            setTimeout(() => this.$refs.passwordInput.focus(), 250);
        },
        logoutOtherBrowserSessions() {
            this.formLogout.delete(route('other-browser-sessions.destroy'), {
                preserveScroll: true,
                onSuccess: () => this.closeLogoutModal(),
                onError: () => this.$refs.passwordInput.focus(),
                onFinish: () => this.formLogout.reset(),
            });
        },
        closeLogoutModal() {
            this.showConfirmingLogout = false;
            this.formLogout.reset();
        },
        updatePassword() {
            this.formPassword.put(route('user-password.update'), {
                errorBag: 'updatePassword',
                preserveScroll: true,
                onSuccess: () => this.formPassword.reset(),
                onError: () => {
                    if (this.formPassword.errors.password) {
                        this.formPassword.reset('password', 'password_confirmation');
                        passwordInput.value.focus();
                    }

                    if (this.formPassword.errors.current_password) {
                        this.formPassword.reset('current_password');
                        currentPasswordInput.value.focus();
                    }
                },
            });
        },
    }
}
</script>
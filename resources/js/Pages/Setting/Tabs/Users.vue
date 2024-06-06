<template>
    <section>
        <h1 class="rounded-full pl-10 bg-[#F2F2F2] py-1 text-sm">Usuarios</h1>

        <!-- Botones -->
        <div class="text-right mt-3">
            <PrimaryButton v-if="users.length < 1" @click="$inertia.get(route('users.create'))">Agregar usuario</PrimaryButton>
        </div>

        <div class="mt-3 mx-3">
            <div class="overflow-auto mt-9">
                <table v-if="users?.length" class="w-full">
                    <thead>
                        <tr class="*:text-left *:px-3 *:pb-2 *:text-sm border-b border-primary">
                            <th class="w-[10%]">ID</th>
                            <th class="w-[20%]">Nombre</th>
                            <th class="w-[20%]">Correo electrónico</th>
                            <th class="w-[20%]">Rol</th>
                            <th class="w-[20%]">Caja asignada</th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in users" :key="user"
                            class="*:text-xs *:px-3 *:py-2 hover:bg-primarylight">
                            <td class="rounded-s-full w-[20%]">{{ user.id }}</td>
                            <td class="w-[10%]">{{ user.name }}</td>
                            <td class="w-[20%]">{{ user.email }}</td>
                            <td class="w-[20%]">{{ user.rol ?? '--' }}</td>
                            <td class="w-[20%]">{{ user.cash_register?.name ?? 'No asignado' }}</td>
                            <td class="rounded-e-full text-end">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <button @click.stop
                                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'reset|' + user.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="text-[#777777] size-[14px] mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                <span class="text-xs">Restablecer contraseña</span>
                                            </el-dropdown-item>
                                            <!-- <el-dropdown-item :command="'see|' + user.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span class="text-xs">Ver</span>
                                            </el-dropdown-item> -->
                                            <el-dropdown-item :command="'edit|' + user.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="text-[#777777] size-[14px] mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                </svg>
                                                <span class="text-xs">Editar</span>
                                            </el-dropdown-item>
                                            <!-- Solo se puede eliminar si no es el usuario administrador de la tienda -->
                                            <el-dropdown-item v-if="index != 0" :command="'delete|' + user.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="size-[14px] mr-2 text-red-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                <span class="text-xs text-red-600">Eliminar</span>
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <el-empty v-else description="No hay usuarios registrados" />
            </div>
        </div>
    </section>

    <ConfirmationModal :show="showDeleteConfirm || showResetConfirm"
        @close="showDeleteConfirm = false; showResetConfirm = false">
        <template #title>
            <h1 v-if="showDeleteConfirm">Eliminar usuario</h1>
            <h1 v-else>Reetablecer contraseña</h1>
        </template>
        <template #content>
            <p v-if="showDeleteConfirm">
                Se eliminará al usuario seleccionado, esto es un proceso irreversible. ¿Continuar
                de todas formas?
            </p>
            <p v-else>
                Se restablecerá la contraseña al usuario seleccionado, esto es un proceso irreversible. ¿Continuar
                de todas formas?
            </p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false; showResetConfirm = false">Cancelar</CancelButton>
                <DangerButton v-if="showDeleteConfirm" @click="deleteItem">Eliminar</DangerButton>
                <DangerButton v-else @click="resetPassword" class="!bg-green-500">Restablecer</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

export default {
    data() {
        return {
            showDeleteConfirm: false,
            showResetConfirm: false,
            itemIdToDelete: null,
            itemIdToReset: null
        }
    },
    components: {
        PrimaryButton,
        DangerButton,
        CancelButton,
        ConfirmationModal
    },
    props: {
        users: Array
    },
    methods: {
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const data = command.split('|')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('users.show', data));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('users.edit', data));
            } else if (commandName == 'reset') {
                this.showResetConfirm = true;
                this.itemIdToReset = data;
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = data;
            }
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('users.destroy', this.itemIdToDelete));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha eliminado al usuario',
                        type: 'success',
                    });
                    location.reload();
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Error',
                    message: 'No se pudo eliminar al usuario. Intente más tarde',
                    type: 'error',
                });
            }
        },
        async resetPassword() {
            try {
                const response = await axios.put(route('users.reset-password', this.itemIdToReset));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha reseteado el password a: ezyventas',
                        type: 'success',
                    });
                    this.showResetConfirm = false;
                }
            } catch (error) {
                console.log(error);
            }
        }
    }
}
</script>
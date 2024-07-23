<template>
    <Dropdown @click="readNotifications()" align="right" width="notifications" class="mt-2" :closeInClick="false">
        <template #trigger>
            <button
                class="ml-6 relative text-gray-500 bg-white hover:text-gray-700 focus:outline-none rounded-[5px] p-2 mb-2 focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                :class="getUnreadMessages.length ? 'text-primary' : 'text-[#97989A]'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
            </button>
            <span v-if="getUnreadMessages.length"
                class="size-4 bg-primary text-white text-[9px] rounded-full absolute top-0 -right-1 flex items-center justify-center border border-white">
                {{ getUnreadMessages.length }}
            </span>
        </template>
        <template #content>
            <h1 class="text-gray66 text-center text-sm my-2">
                Notificaciones tienda en línea
                <span class="text-primary">({{ notifications.length }})</span>
            </h1>
            <header v-if="notifications.length" class="py-1 px-4 flex justify-between items-center border-b mt-3">
                <label class="text-gray99 text-xs has-[:checked]:text-primary">
                    <Checkbox v-model:checked="allItems" @change="handleChange" class="size-3 mr-2" />
                    <span>Seleccionar todos</span>
                </label>
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                    :title="'¿Desea eliminar las notificaciones seleccionadas (' + selectedItems.length + ')?'"
                    @confirm="deleteNotifications()">
                    <template #reference>
                        <button
                            class="flex justify-center items-center size-6 text-xs rounded-[5px] bg-primarylight text-primary disabled:cursor-not-allowed disabled:bg-grayF2 disabled:text-gray66"
                            :disabled="!selectedItems.length">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </template>
                </el-popconfirm>
            </header>
            <main class="h-[220px] overflow-y-auto">
                <el-empty v-if="!notifications.length" description="No hay notificaciones" :image-size="90" />
                <DropdownLink v-for="item in notifications" :key="item.id" :href="item.data.url"
                    :class="{ 'bg-primarylight': item.read_at === null }">
                    <div class="relative">
                        <div v-if="item.read_at === null"
                            class="size-1 bg-primary rounded-full absolute top-[6px] -right-2"></div>
                        <div class="flex">
                            <label class="w-[8%]">
                                <input type="checkbox" @change="handleItemChecked()" @click.stop v-model="selectedItems"
                                    :value="item.id"
                                    class="size-3 rounded-sm border-[#999999] text-primary shadow-sm focus:ring-primary bg-transparent disabled:border-gray-300 disabled:bg-gray-200 disabled:cursor-not-allowed" />
                            </label>
                            <section class="w-[78%] text-xs">
                                <p :class="{ 'text-primary': item.read_at === null }" class="flex items-start space-x-1">
                                    <!-- iconos de notificacion -->
                                    <svg v-if="item.data.type === 'new'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.5 -0.5 22 22"
                                        height="26" width="26" id="New-Label--Streamline-Rounded----Material-Symbols">
                                        <path fill="currentColor"
                                            d="M13.125 16.625H10.28125C10.0953125 16.625 9.939475000000002 16.56169375 9.81378125 16.435125C9.6879125 16.3086875 9.625 16.15193125 9.625 15.9648125C9.625 15.77786875 9.6879125 15.62238125 9.81378125 15.498437499999998C9.939475000000002 15.37449375 10.0953125 15.3125 10.28125 15.3125H13.365625L16.734375 10.5L13.365625 5.6875H3.9375V8.96875C3.9375 9.1546875 3.874206875 9.310524999999998 3.747625 9.43621875C3.6211874999999996 9.5620875 3.464418125 9.625 3.2773125 9.625C3.090355625 9.625 2.934894375 9.5620875 2.8109375 9.43621875C2.6869806250000003 9.310524999999998 2.625 9.1546875 2.625 8.96875V5.6875C2.625 5.3265625000000005 2.753550625 5.01755625 3.01065625 4.7604375C3.267613125 4.50349375 3.5765625000000005 4.375 3.9375 4.375H13.125C13.44581875 4.375 13.737499999999999 4.458868750000001 14 4.6265624999999995C14.262500000000001 4.79425625 14.48855625 5.0093749999999995 14.678124999999998 5.2718750000000005L17.849999999999998 9.734375C18.01043125 9.96725625 18.090625 10.22240625 18.090625 10.49978125C18.090625 10.777025 18.01043125 11.03230625 17.849999999999998 11.265625L14.678124999999998 15.728125000000002C14.48855625 15.990624999999998 14.262500000000001 16.20574375 14 16.373437499999998C13.737499999999999 16.54113125 13.44581875 16.625 13.125 16.625ZM4.375 14.875H2.40625C2.2203125 14.875 2.064488125 14.81169375 1.93878125 14.685125000000001C1.812925625 14.558687500000001 1.75 14.40193125 1.75 14.2148125C1.75 14.02786875 1.812925625 13.87238125 1.93878125 13.7484375C2.064488125 13.62449375 2.2203125 13.5625 2.40625 13.5625H4.375V11.59375C4.375 11.4078125 4.43830625 11.251931249999998 4.564875 11.1260625C4.6913125 11.00036875 4.84806875 10.9375 5.0351875 10.9375C5.2221312499999994 10.9375 5.37761875 11.00036875 5.5015624999999995 11.1260625C5.62550625 11.251931249999998 5.6875 11.4078125 5.6875 11.59375V13.5625H7.65625C7.8421875000000005 13.5625 7.998068750000001 13.62580625 8.1239375 13.752375C8.24963125 13.878812499999999 8.3125 14.03556875 8.3125 14.2226875C8.3125 14.40963125 8.24963125 14.56511875 8.1239375 14.689062500000002C7.998068750000001 14.81300625 7.8421875000000005 14.875 7.65625 14.875H5.6875V16.84375C5.6875 17.029687499999998 5.62419375 17.185525 5.497625 17.311218750000002C5.3711875 17.4370875 5.21443125 17.5 5.0273125 17.5C4.8403687500000006 17.5 4.68488125 17.4370875 4.5609375000000005 17.311218750000002C4.43699375 17.185525 4.375 17.029687499999998 4.375 16.84375V14.875Z"
                                            stroke-width="1"></path>
                                    </svg>
                                    <span v-html="item.data.description"></span>
                                </p>
                            </section>
                        </div>
                        <p class="text-right text-gray99 text-[10px]">{{ item.created_at_for_humans }}</p>
                    </div>
                </DropdownLink>
            </main>
        </template>
    </Dropdown>
</template>

<script>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

export default {
    data() {
        return {
            // showNotificationPopup: false,
            allItems: false,
            notifications: [],
            selectedItems: [],
        };
    },
    components: {
        Dropdown,
        DropdownLink,
        PrimaryButton,
        Checkbox,
    },
    methods: {
        // toggleNotificationPopup() {
        //     this.showNotificationPopup = !this.showNotificationPopup;
        // },
        handleChange() {
            if (this.allItems) {
                this.selectedItems = this.notifications.map(notification => notification.id);
            } else {
                this.selectedItems = [];
            }
        },
        handleItemChecked() {
            if (this.selectedItems.length == this.notifications.length) {
                this.allItems = true;
            } else if (this.selectedItems.length < this.notifications.length && this.allItems) {
                this.allItems = false;
            }
        },
        async deleteNotifications() {
            try {
                const response = await axios.post(route('users.delete-user-notifications'), { notifications_ids: this.selectedItems });

                if (response.status === 200) {
                    // Filtrar el arreglo excluyendo los elementos con IDs en 'selectedItems'
                    this.notifications = this.notifications.filter(item => !this.selectedItems.includes(item.id));
                    this.$notify({
                        title: "Éxito",
                        message: response.data.message,
                        type: "success",
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "No se pudo completar la solicitud",
                    message: "El servidor no pudo procesar la petición, inténtalo más tarde",
                    type: "error",
                });
            }
        },
        async fetchOnlineSalesNotifications() {
            try {
                const response = await axios.get(route('users.get-online-sales-notifications'));

                if (response.status === 200) {
                    this.notifications = response.data.items;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "No se pudo completar la solicitud",
                    message: "El servidor no pudo procesar la petición para obtener las notificaciones de tienda en linea. Inténtalo más tarde",
                    type: "error",
                });
            }
        },
        async readNotifications() {
            try {
                const response = await axios.post(route("users.read-user-online-sales-notifications"));

                if (response.data.unread) {
                    this.fetchOnlineSalesNotifications();
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
    computed: {
        getUnreadMessages() {
            return this.notifications?.filter(item => item.read_at === null);
        }
    },
    mounted() {
        this.fetchOnlineSalesNotifications();
    },

};
</script>
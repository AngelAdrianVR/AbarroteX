<template>
    <AppLayout :title="'Gastos del día'">
        <section class="mx-2 lg:mx-10 mt-7">
            <Back :to="route('expenses.index')" />
            <div class="flex items-center justify-end space-x-1">
                <!-- ** descomentar cuando se haga una plantilla para imprimir todos los gastos del día **  -->
                <!-- <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?"
                    @confirm="print(expenses[0].id)">
                    <template #reference>
                        <i @click.stop
                            class="fa-solid fa-print text-primary hover:bg-gray-200 cursor-pointer bg-grayED rounded-full p-[6px]"></i>
                    </template>
</el-popconfirm> -->
                <!-- <el-popconfirm v-if="canDelete" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303"
                    title="¿Continuar?" @confirm="deleteDayExpenses(expenses[0].id)">
                    <template #reference>
                        <i @click.stop
                            class="fa-regular fa-trash-can text-primary cursor-pointer hover:bg-gray-200 rounded-full p-2"></i>
                    </template>
                </el-popconfirm> -->
            </div>
        </section>
        <header class="flex items-center justify-between font-bold mx-2 lg:mx-36 text-sm mt-4">
            <h1>Detalle de gastos </h1>
            <h2>
                <span class="text-gray77">Fecha: </span>
                {{ formatDate(expenses[0]?.created_at) }}
            </h2>
            <span></span>
        </header>
        <main class="mx-2 lg:mx-32 text-xs md:text-sm mt-4 mb-6">
            <section class="border border-gray9A rounded-[5px] p-4 grid grid-cols-4 gap-4">
                <p class="text-gray77 font-semibold">Monto:</p>
                <b class="col-span-3">${{ totalExpenses().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</b>
                <p class="text-gray77 font-semibold pt-1">Desgloce:</p>
                <table class="col-span-full md:col-span-3 w-full">
                    <thead>
                        <tr
                            class="*:py-1 *:px-2 *:md:px-4 *:text-xs *:md:text-sm text-gray37 bg-primarylight text-start">
                            <th class="w-[40%] rounded-s-full text-start">Concepto</th>
                            <th class="w-[15%] text-end">Cantidad</th>
                            <th class="w-[20%] text-end">Costo</th>
                            <th class="w-[20%] text-end">$ tomado de caja</th>
                            <th class="w-[5%] rounded-e-full"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-[1px]">
                        <tr v-for="expense in expenses" :key="expense"
                            class="*:pb-px *:px-2 *:md:px-4 *:text-xs *:md:text-sm *:py-2 *:align-top border-grayD9">
                            <td>{{ expense.concept }}</td>
                            <td class="text-end">{{ expense.quantity }}</td>
                            <td class="text-end">
                                ${{ (expense.quantity *
                                    expense.current_price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                        ",") }}
                            </td>
                            <td class="text-end">
                                ${{ expense.amount_from_cash_register?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                    ?? 0.00
                                }}
                            </td>
                            <td class="text-end">
                                <el-dropdown v-if="canEdit && canDelete" trigger="click" @command="handleCommand">
                                    <button @click.stop
                                        class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item v-if="canEdit" :command="'edit|' + expense.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                </svg>
                                                <span class="text-xs">Editar</span>
                                            </el-dropdown-item>
                                            <el-dropdown-item v-if="canDelete && expenses.length > 1" :command="'delete|' + expense.id">
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
            </section>
        </main>
        <DialogModal :show="showEditModal" @close="showEditModal = false">
            <template #title>
                <h1>Editar gasto</h1>
            </template>
            <template #content>
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p class="text-gray37">
                        Si se tomó dinero de caja para este gasto, los cambios que se hagan no se verán
                        reflejados en el dinero de caja actual. Para esto debes de registrar un "Ingreso"
                        o "Retiro" de caja.
                    </p>
                </div>
                <form @submit.prevent="update" class="mt-3">
                    <div>
                        <InputLabel value="Concept" ref="conceptInput" />
                        <el-input v-model="form.concept" placeholder="No olvides llenar este campo">
                        </el-input>
                        <InputError :message="form.errors.concept" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Cantidad" />
                        <el-input v-model="form.quantity" placeholder="No olvides llenar este campo"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')">
                        </el-input>
                        <InputError :message="form.errors.quantity" />
                    </div>
                    <div class="mt-2">
                        <InputLabel value="Costo por unidad" />
                        <el-input v-model="form.current_price" placeholder="No olvides llenar este campo"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')">
                        </el-input>
                        <InputError :message="form.errors.current_price" />
                    </div>
                </form>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showEditModal = false" :disabled="form.processing">Cancelar</CancelButton>
                    <PrimaryButton @click="update" :disabled="form.processing">Guardar cambios
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
        <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
            <template #title>
                <h1>Eliminar gasto</h1>
            </template>
            <template #content>
                <p>
                    Se eliminará el gasto seleccionado, esto es un proceso irreversible. ¿Continuar
                    de todas formas?
                </p>
            </template>
            <template #footer>
                <div class="flex items-center space-x-1">
                    <CancelButton @click="showDeleteConfirm = false" :disabled="deleting">Cancelar</CancelButton>
                    <PrimaryButton @click="deleteItem" :disabled="deleting">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import { useForm } from "@inertiajs/vue3";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
    data() {
        const form = useForm({
            concept: null,
            quantity: null,
            current_price: null,
        });

        return {
            form,
            showEditModal: false,
            showDeleteConfirm: false,
            itemIdToDelete: null,
            itemIdToEdit: null,
            // Permisos de rol actual
            canDelete: this.$page.props.auth.user.permissions.includes('Eliminar gastos'),
            canEdit: this.$page.props.auth.user.permissions.includes('Editar gastos'),
            // cargas
            deleting: false,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        InputError,
        Back,
        ConfirmationModal,
        DialogModal,
    },
    props: {
        expenses: Object,
    },
    methods: {
        handleCommand(command) {
            const commandName = command.split('|')[0];
            const itemId = command.split('|')[1];

            if (commandName == 'edit') {
                const expense = this.expenses.find(item => item.id == itemId);
                this.form.concept = expense.concept;
                this.form.quantity = expense.quantity;
                this.form.current_price = expense.current_price;
                this.itemIdToEdit = itemId;
                this.showEditModal = true;
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = itemId;
            }
        },
        update() {
            this.form.put(route('expenses.update', this.itemIdToEdit), {
                onSuccess: () => {
                    this.itemIdToEdit = null;
                    this.showEditModal = false;
                    this.$notify({
                        title: 'Gasto actualizado',
                        message: '',
                        type: 'success',
                    });
                }
            });
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
        print(expenseId) {
            window.open(route('expenses.print-expenses', expenseId), '_blank');
        },
        totalExpenses() {
            let total = 0;
            this.expenses.forEach(expense => {
                total += expense.quantity * expense.current_price;
            });
            return total;
        },
        async deleteItem() {
            this.deleting = true;
            try {
                const response = await axios.delete(route('expenses.destroy', {expense: this.itemIdToDelete, dayExpenses: this.expenses.length}));
                if (response.status == 200) {
                    this.showDeleteConfirm = false;

                    // eliminar el gasto de la lista
                    const index = this.expenses.findIndex(item => item.id == this.itemIdToDelete);
                    this.expenses.splice(index, 1);

                    this.$notify({
                        title: 'Gasto eliminado',
                        message: '',
                        type: 'success',
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la petición',
                    message: 'No se pudo eliminar el gasto. Inténte más tarde',
                    type: 'error',
                });
            } finally {
                this.deleting = false;
            }
        },
        async deleteDayExpenses(expenseId) {
            try {
                const response = await axios.delete(route('expenses.delete-day', expenseId));
                if (response.status == 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se han eliminado todos los gastos del día',
                        type: 'success',
                    });

                    this.$inertia.get(route('expenses.index'));
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'No se pudo procesar la petición',
                    message: 'No se pudo eliminar el registro de gastos. Inténte más tarde',
                    type: 'error',
                });
            }
        },
    },
}
</script>
<template>
    <Loading v-if="loading" />
    <div v-else class="min-h-32">
        <section class="flex justify-end space-x-3 mt-2">
            <ThirthButton @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Ingreso'" class="!rounded-md"><i class="fa-solid fa-arrow-down mr-3"></i>Ingresar efectivo
            </ThirthButton>
            <ThirthButton @click="cashRegisterModal = true; form.cashRegisterMovementType = 'Retiro'" class="!rounded-md"><i class="fa-solid fa-arrow-up mr-3"></i>Retirar efectivo</ThirthButton>
        </section>
        <section v-if="isMaxCashOn" class="mt-5">
            <form @submit.prevent="update">
                <div>
                    <InputLabel value="Máxima cantidad en caja permitida *" class="ml-3 mb-1 text-sm" />
                    <el-input v-model="form.max_cash" type="text" placeholder="Ingresa el monto"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\D/g, '')">
                        <template #prefix>
                            <i class="fa-solid fa-dollar-sign"></i>
                        </template>
                    </el-input>
                    <InputError :message="form.errors.max_cash" />
                </div>
                <div class="mt-2">
                    <PrimaryButton type="submit" :disabled="form.processing">Guardar</PrimaryButton>
                </div>
            </form>
        </section>
    </div>

    <!-- -------------- Modal Ingreso o retiro de dinero en caja starts----------------------- -->
    <Modal :show="cashRegisterModal" @close="cashRegisterModal = false; form.reset">
      <div class="p-4 relative">
        <i @click="cashRegisterModal = false"
          class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>

        <form class="mt-5 mb-2 md:grid grid-cols-2 gap-3" @submit.prevent="storeCashRegisterMovement">
          <h2 v-if="form.cashRegisterMovementType === 'Ingreso'" class="font-bold col-span-full">Ingresar efectivo a
            caja
          </h2>
          <h2 v-if="form.cashRegisterMovementType === 'Retiro'" class="font-bold col-span-full">Retirar efectivo a caja
          </h2>

          <div class="mt-2">
            <InputLabel v-if="form.cashRegisterMovementType === 'Ingreso'" value="Monto a ingresar*"
              class="ml-3 mb-1 text-sm" />
            <InputLabel v-if="form.cashRegisterMovementType === 'Retiro'" value="Monto a retirar*"
              class="ml-3 mb-1 text-sm" />
            <el-input v-model="form.registerAmount" type="text" placeholder="ingresa el monto"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')">
              <template #prefix>
                <i class="fa-solid fa-dollar-sign"></i>
              </template>
            </el-input>
            <InputError :message="form.errors.registerAmount" />
          </div>

          <div class="col-span-full mt-2">
            <InputLabel value="Motivo (opcional)" class="text-sm ml-2" />
            <el-input v-model="form.registerNotes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe tus notas" :maxlength="255" show-word-limit clearable />
          </div>

          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <CancelButton @click="cashRegisterModal = false">Cancelar</CancelButton>
            <PrimaryButton :disabled="!form.registerAmount || form.processing">Confirmar</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
    <!-- --------------------------- Modal Ingreso o retiro de dinero en caja ends ------------------------------------ -->
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            max_cash: null,
            cashRegisterMovementType: null,
            registerAmount: null,
        });

        return {
            form,
            cashRegister: null,
            loading: true,
            cashRegisterModal: false,

            // monto maximo en caja activado
            isMaxCashOn: this.$page.props.auth.user.store.settings.find(item => item.name == 'Aviso de monto máximo en caja')?.value,
        }
    },
    components: {
        PrimaryButton,
        ThirthButton,
        CancelButton,
        InputLabel,
        InputError,
        Loading,
        Modal
    },
    methods: {
        update() {
            this.form.put(route('cash-registers.update', this.cashRegister.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Correcto",
                        message: "",
                        type: "success",
                    });
                }
            });
        },
        storeCashRegisterMovement() {
            this.form.post(route('cash-register-movements.store'), {
                onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha registrado el movimiento de caja",
                    type: "success",
                });
                this.cashRegisterModal = false;
                this.cashRegisterMovementType = null;
                this.registerAmount = null;
                },
            });
        },
        async fetchCashRegister() {
            try {
                this.loading = true;
                const response = await axios.get(route('cash-registers.fetch-cash-register'));
                if (response.status === 200) {
                    this.cashRegister = response.data.item;
                    this.form.max_cash = this.cashRegister.max_cash;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchCashRegister();
    }

}
</script>
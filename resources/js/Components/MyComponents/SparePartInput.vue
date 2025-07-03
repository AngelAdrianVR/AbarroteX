<template>
  <main class="py-3 pl-3 border rounded-md">
    <section class="max-h-60 overflow-auto">
        <div v-if="parts.length"
          class="grid grid-cols-9 gap-4 items-center mb-4 relative overflow-hidden"
          v-for="(part, index) in parts"
          :key="index"
          >
            <div class="col-span-3">
                <div class="flex items-center justify-between">
                  <InputLabel value="Nombre de la refacción" />
                  <p v-if="index === (parts.length - 1)" @click="changeTypeOfSelection" class="cursor-pointer text-primary underline text-sm">Cambiar a {{ typeOfSelection === 'texto' ? 'lista' : 'texto' }}</p>
                </div>
                <SmallLoading v-if="loadingSpareParts" class="ml-12" />
                <div v-else>
                  <template v-if="index === parts.length - 1">
                    <!-- Mostrar input o select solo en el último -->
                    <el-input
                      v-if="typeOfSelection === 'texto'"
                      v-model="part.name"
                      placeholder="Nombre de la refacción"
                    />
                    <el-select
                      v-else
                      v-model="part.name"
                      @change="handleSparePartSelected(index, $event)"
                      clearable
                      placeholder="Selecciona la refacción"
                      no-data-text="No hay opciones registradas"
                      no-match-text="No se encontraron coincidencias"
                    >
                      <el-option
                        v-for="option in spareParts"
                        :key="option.id"
                        :label="option.name"
                        :value="option.name"
                      />
                    </el-select>
                    <p
                      v-if="index === parts.length - 1 && nameError"
                      class="text-xs text-red-500"
                    >
                      Agrega una refacción
                    </p>
                  </template>

                  <template v-else>
                    <!-- Mostrar nombre de la refacción como texto en los demás -->
                    <p class="text-gray-600 text-sm ml-4">{{ part.name }}</p>
                  </template>
                </div>
            </div>

            <div class="col-span-2">
                <InputLabel value="Precio unitario" />
                <el-input
                  v-model.number="part.unitPrice"
                  @input="syncItems()"
                >
                <template #prepend>$</template>
                </el-input>
                <p v-if="index === (parts.length - 1) && priceError" class="text-xs text-red-500">El precio no puede ser 0</p>
            </div>

            <div class="col-span-2">
                <InputLabel value="Cantidad" />
                <div class="flex items-center gap-1">
                  <el-button size="" @click="decreaseQuantity(index)" :disabled="part.quantity <= 1">-</el-button>
                  <el-input-number
                      v-model="part.quantity"
                      @change="syncItems()"
                      :min="1"
                      :controls="false"
                      class="w-16"
                  />
                  <el-button size="" @click="increaseQuantity(index)">+</el-button>
                </div>
            </div>

            <div class="col-span-2">
                <InputLabel value="Total" />
                <div class="font-medium">
                $ {{ getTotal(part).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                </div>
            </div>

            <div v-if="index !== 0" class="absolute top-5 right-3 text-right">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                    :title="'¿Continuar?'" @confirm="removePart(index)">
                    <template #reference>
                        <button type="button" class="text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
                    </template>
                </el-popconfirm>
            </div>
        </div>
        <p class="text-center" v-else>Click al botón de “+” para agregar refacciones</p>
    </section>

    <el-link type="warning" @click="addPart" class="mt-2 inline-block">
      + Agregar refacción
    </el-link>
  </main>
</template>

<script>
import InputLabel from "@/Components/InputLabel.vue";
import SmallLoading from "@/Components/MyComponents/SmallLoading.vue";
import axios from 'axios';

export default {
  name: 'PartsForm',

  components: {
    InputLabel,
    SmallLoading
  },

  props:{
    initialData: Array
  },

  emits:['syncItems'],

  data() {
    return {
      nameError: false, // indica si hay un error en el nombre de la refacción
      priceError: false, // indica si hay un error en el precio de la refacción
      loadingSpareParts: false, // indica si se están cargando las refacciones
      typeOfSelection: 'texto',
      spareParts: [], // refacciones disponibles para seleccionar en lista
      parts: [
        {
          name: '',
          unitPrice: 0,
          quantity: 1,
        },
      ],
    };
  },

  methods: {
    increaseQuantity(index) {
      this.parts[index].quantity++;
    },
    decreaseQuantity(index) {
      if (this.parts[index].quantity > 1) {
        this.parts[index].quantity--;
      }
    },
    syncItems() {
      this.$emit('syncItems', this.parts);
    },

    getTotal(part) {
      return part.unitPrice * part.quantity;
    },
    addPart() {
      const lastPart = this.parts[this.parts.length - 1];

      // Validar si no hay partes o si el nombre está vacío
      if (!this.parts.length || lastPart.name.trim() === '') {
        this.nameError = true;
        this.priceError = false;
        return;
      }

      // Validar si el precio es 0
      if (lastPart.unitPrice === 0) {
        this.priceError = true;
        this.nameError = false;
        return;
      }

      // Si todo está bien
      this.nameError = false;
      this.priceError = false;

      this.parts.push({
        name: '',
        unitPrice: 0,
        quantity: 1,
      });
    },
    removePart(index) {
      this.parts.splice(index, 1);
    },
    changeTypeOfSelection() {
      // Cambia entre modo de texto y lista
      this.typeOfSelection = this.typeOfSelection === 'texto' ? 'lista' : 'texto';

      // Si cambia a lista, verifica si ya se han cargado las refacciones
      if ( !this.spareParts.length ) {
        this.fetchSpareParts();
      } 
    },
    handleSparePartSelected(index, selectedName) {
      const selectedPart = this.spareParts.find(p => p.name === selectedName);
      if (selectedPart) {
        this.parts[index].unitPrice = selectedPart.public_price;
        // Puedes también guardar el id si es necesario:
        // this.parts[index].partId = selectedPart.id;
      }
    },
    async fetchSpareParts() {
      this.loadingSpareParts = true;
      try {
        const response = await axios.get(route('service-reports.fetch-spare-parts'));

        if ( response.status === 200 ) {
            this.spareParts = response.data.spare_parts;
        }
      } catch (error) {
        console.error('Error fetching spare parts:', error);
      } finally {
        this.loadingSpareParts = false;
      }
    }
  },
  mounted() {
    if ( this.initialData ) {
      this.parts = [... this.initialData]
    }
  }
};
</script>

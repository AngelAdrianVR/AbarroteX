<template>
  <div class="rounded-lg rounded-b-lg border-x border-b border-[#D9D9D9]">
    <p class="font-semibold mb-4 bg-[#EDEDED] py-2 px-4 rounded-t-lg border border-[#D9D9D9]">Estado de la orden</p>
    <div class="flex items-center justify-between pb-5">
      <template v-for="(step, index) in steps" :key="index">
        <div class="flex-1 flex-col items-center">
          <div class="flex flex-col md:flex-row justify-center items-center space-x-2">
            <div :class="'mb-1'">
              <slot :name="'icon-' + index" />
            </div>
            <span :class="'text-[#373737] text-xs text-center mb-1 font-medium'">
              <slot :name="'label-' + index" />
            </span>
          </div>
          <!-- Line connector -->
          <div v-if="index < steps.length" class="flex-1 h-[5px] mx-2 rounded-full"
            :class="index < activeStep ? 'bg-gradient-to-r from-[#751F8B] via-[#56345f] to-[#1F0825]' : 'bg-gray-300'">
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatusStep',
  props: {
    activeStep: {
      type: Number,
      required: true,
    },
    steps: {
      type: Array,
      required: true, // Array vacío con la cantidad de pasos, solo para generar el for
    },
  },
};
</script>

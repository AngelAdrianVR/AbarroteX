<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'flex space-x-2 items-center w-full ps-3 pe-4 py-1 border-l-4 border-primary text-start text-sm font-medium text-primary bg-[#393939] focus:outline-none focus:text-primary focus:bg-primarylight focus:border-primary transition duration-150 ease-in-out'
        : 'flex space-x-2 items-center w-full ps-3 pe-4 py-1 border-l-4 border-transparent text-start text-sm font-medium text-gray9A hover:text-white hover:bg-[#393939] hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-start">
            <slot />
        </button>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>

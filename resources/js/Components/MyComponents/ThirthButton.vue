<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-primarylight rounded-full text-xs text-primary focus:bg-primarylight active:bg-primarylight focus:outline-none focus:ring-0 active:scale-95 disabled:active:scale-100 transition ease-in-out duration-200 disabled:opacity-25 disabled:cursor-not-allowed">
        <slot />
    </button>
</template>

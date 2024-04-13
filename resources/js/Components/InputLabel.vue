<script setup>
defineProps({
    value: String,
});
</script>

<template>
    <label class="block font-medium text-xs text-gray-700 ml-2 mb-1">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

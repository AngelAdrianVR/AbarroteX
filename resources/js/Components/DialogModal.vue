<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '3xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 pt-4 pb-2">
            <div class="font-medium text-gray-900 flex items-center justify-between">
                <slot name="title" />
                <button @click="close">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="mt-4 text-sm text-gray37">
                <slot name="content" />
            </div>
        </div>

        <div class="flex flex-row justify-end px-6 pb-4 text-end">
            <slot name="footer" />
        </div>
    </Modal>
</template>

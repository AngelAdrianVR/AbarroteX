<template>
    <button type="button" class="flex bg-gray37 rounded-full px-[2px] text-sm relative overflow-hidden" @click="toggle">
        <span class="absolute w-[calc(50%-6px)] h-[calc(100%-6px)] rounded-full bg-primary transition-all duration-200 ease-in-out bottom-[3px]" :class="{'left-[3px]': active == labels[0], 'left-[calc(50%+3px)]': active == labels[1]}"></span>
        <span class="relative w-1/2 text-center py-[5px] z-10" :class="active == labels[0] ? 'text-white' : 'text-gray9A'">{{ labels[0] }}</span>
        <span class="relative w-1/2 text-center py-[5px] z-10" :class="active == labels[1] ? 'text-white' : 'text-gray9A'">{{ labels[1] }}</span>
    </button>
</template>

<script>
export default {
    props: {
        labels: Array,
    },
    emits: ['update'],
    data() {
        return {
            active: this.labels[0],
        };
    },
    methods: {
        toggle() {
            this.active = this.active === this.labels[0] ? this.labels[1] : this.labels[0];
            this.$emit('update', this.active);
        }
    }
};
</script>


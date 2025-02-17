<template>
    <div class="rounded-lg shadow-md text-center p-4">
        <section class="flex space-x-3">
            <span class="flex items-center justify-center text-primary text-xs bg-primarylight size-7 rounded-full">
                <i :class="icon"></i>
            </span>
            <div class="flex flex-col w-4/5">
                <h2 class="text-xs text-gray66">{{ title }}</h2>
                <span class="text-lg font-semibold">{{ value }}</span>
            </div>
        </section>
    </div>
</template>
<script>
export default {
    data() {
        return {
            
        }
    },
    props: {
        icon: String,
        title: String,
        value: String,
    }
}
</script>
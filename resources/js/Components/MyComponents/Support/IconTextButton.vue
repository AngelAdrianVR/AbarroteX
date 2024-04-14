<template>
    <Link :href="route(routeName)"
        class="w-28 h-32 bg-white border border-grayD9 hover:border-2 hover:border-primary rounded-[10px] shadow-lg text-gray37 text-lg flex flex-col items-center justify-center space-y-5 p-1">
        <span v-html="icon"></span>
        <b>{{ title }}</b>
    </Link>
</template>
<script>
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        title: String,
        icon: String,
        routeName: String,
    },
    components: {
        Link,
    }
}
</script>
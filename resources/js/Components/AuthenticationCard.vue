<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0  bg-white relative lg:-mt-16">
        <figure class="absolute top-16 left-0 hidden lg:block">
            <img class="h-[570px]" src="@/../../public/images/triangle_login.png" alt="logo">
        </figure>
        <figure class="absolute top-16 right-56 hidden lg:block">
            <img class="h-screen" src="@/../../public/images/line_login.png" alt="logo">
        </figure>
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md px-6 py-4 bg-white overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

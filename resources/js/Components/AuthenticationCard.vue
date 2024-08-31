<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0  bg-black1 relative">
        <img class="absolute top-7 left-0" src="@/../../public/images/autentication_decoration_1.png" alt="">
        <img class="hidden md:block absolute top-7 right-0" src="@/../../public/images/autentication_decoration_2.png" alt="">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md px-6 py-4 bg-black1 overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

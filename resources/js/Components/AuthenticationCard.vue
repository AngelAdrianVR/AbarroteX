<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0  bg-white relative lg:-mt-16">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md px-6 py-4 bg-white overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

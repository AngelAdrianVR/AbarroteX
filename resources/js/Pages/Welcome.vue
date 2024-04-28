<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Ezy Ventas" />

    <nav class="bg-white border-b border-gray-100 shadow-md">
        <div class="max-w-8xl mx-auto px-4 md:px-7 py-3">
            <div class="flex justify-between items-center h-12">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <ApplicationMark class="block h-14 w-auto" />
                    </div>
                </div>

                <p>¡15 días de prueba gratuitos!</p>

                <div class="hidden sm:flex sm:items-center space-x-2 sm:ms-6">
                    <Link :href="$page.props.auth.user ? route('dashboard') : route('login')"><PrimaryButton>Ingresar</PrimaryButton></Link>
                    <Link :href="route('register')"><ThirthButton>Pruébalo ahora</ThirthButton></Link>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <!-- notifications -->
                    <NotificationsCenter />
                    <button
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        @click="showingNavigationDropdown = !showingNavigationDropdown">
                        <svg class="size-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path
                                :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Responsive Navigation Menu -->
        <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
            class="sm:hidden">
            <!-- <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink :href="route('sales.point')" :active="route().current('sales.point')">
                    Punto de venta
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Reportes
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('sales.index')"
                    :active="route().current('sales.index') || route().current('sales.show')">
                    Ventas registradas
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('expenses.index')" :active="route().current('expenses.*')">
                    Gastos
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.*') || route().current('global-product-store.*')">
                    Productos
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('cash-cuts.index')" :active="route().current('cash-cuts.*')">
                    Caja
                </ResponsiveNavLink>
                <ResponsiveNavLink :href="route('settings.index')" :active="route().current('settings.*')">
                    Configuraciones
                </ResponsiveNavLink>
            </div> -->

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover"
                            :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.store.name">
                    </div>

                    <div>
                        <div class="font-medium text-base text-gray-800">
                            {{ $page.props.auth.user.store.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.show')"
                        :active="route().current('profile.show')">
                        Perfil
                    </ResponsiveNavLink>

                    <!-- Authentication -->
                    <form method="POST" @submit.prevent="logout">
                        <ResponsiveNavLink as="button">
                            Cerrar sesión
                        </ResponsiveNavLink>
                    </form>

                </div>
            </div>
        </div>
    </nav>

    <main class="relative bg-center selection:bg-primary selection:text-white">
        <section class="lg:flex">
            <div class="self-start mt-28 ml-10">
                <h1 class="text-4xl font-bold"><strong class="text-primary">Ezy Ventas </strong>El mejor punto de venta del mundo</h1> <br>
                <p class="text-xl">Potencia tu negocio con nuestro Punto de Venta, donde cada transacción es el comienzo de una historia exitosa.</p>
            </div>
            <figure class="flex justify-end">
                <img class="" src="@/../../public/images/landing-01.png" alt="">
            </figure>
        </section>
    </main>
</template>


<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link  class="flex" href="/">
                <img  class="h-20 w-20 fill-current text-gray-500" src="/public/images/logo.png" alt="">
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white/90 px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>

        <Link  class="flex" href="/">
            <h4 class="text-white text-lg pt-5 hover:text-gray-200" >Anasayfa Git</h4>
        </Link>
    </div>
</template>

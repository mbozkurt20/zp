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
                <img  class="h-20 w-20 fill-current text-gray-500" src="/public/images/zp.png" alt="">
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white/90  shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useCartStore } from '../Stores/cartStore'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import { Link } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
const products = ref([])
const cartStore = useCartStore()

const fetchProducts = async () => {
    const response = await axios.get('/products');
    console.log({response:response})
    products.value = response.data.data
}

onMounted(fetchProducts)
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex gap-6">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800"
                >
                    Sepet
                </h2>

                <div class="flex text-sm text-gray-800 gap-5 mt-1">
                    <div><span class="font-semibold">Toplam Ürün:</span> <strong class="text-green-600">{{ cartStore.totalProduct }}</strong></div>
                    <div><span class="font-semibold">Toplam Tutar:</span> <strong class="text-green-600">{{ cartStore.totalAmount }}₺</strong></div>

                    <div class=" ml-auto">
                        <Link class=" text-purple-600 border border-purple-600 rounded-md py-2 px-3 hover:bg-purple-500 hover:text-white" :href="route('dashboard')">
                           Siparişe Devam Et
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-4">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

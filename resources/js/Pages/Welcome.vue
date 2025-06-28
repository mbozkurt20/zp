<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import {useCartStore} from '../Stores/cartStore'

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}


const products = ref([])
const categories = ref([])
const cartStore = useCartStore()

const searchQueries = reactive({})

const fetchProducts = async () => {
    const response = await axios.get('/products');
    products.value = response.data.data
}

const fetchCategories = async () => {
    const response = await axios.get('/categories');
    categories.value = response.data.data
}

onMounted(() => {
    fetchCategories();
    fetchProducts();
});

const filteredProducts = (category) => {
    const query = searchQueries[category.id]?.toLowerCase() || ''
    return products.value.filter(p =>
        p.category_id === category.id &&
        (p.name.toLowerCase().includes(query) || p.description.toLowerCase().includes(query))
    )
}
</script>

<template>
    <Head title="Hoşgeldiniz" />

    <header
        class="sticky top-0 z-50 bg-white/80 backdrop-blur-md shadow-md flex flex-col gap-4 py-4 sm:flex-row sm:justify-between sm:items-center px-4 "
    >
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-black text-gray-600">
                🛒 Emisoft Menü
            </h1>
        </div>

        <nav
            v-if="canLogin"
            class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto justify-end"
        >
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="text-lg px-4 py-2 text-gray-700 transition "
            >
              Satın Almaya Git
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="w-full sm:w-auto text-center rounded-md px-4 py-2 text-lg text-black bg-orange-400 hover:bg-orange-500 transition dark:text-white"
                >
                    Giriş Yap
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="w-full sm:w-auto text-center rounded-md px-4 py-2 text-lg text-black bg-orange-400 hover:bg-orange-500 transition dark:text-white"
                >
                    Hesap Aç
                </Link>
            </template>
        </nav>
    </header>


    <div class="bg-gray-500 text-black/50 dark:bg-gray-900 dark:text-white/50 ">

        <div class="relative flex min-h-screen flex-col w-full max-w-2xl lg:max-w-7xl">
            <div class="relative ">
                <main class="mt-6 py-12">
                    <div class="mx-auto max-w-7xl px-6 sm:px-6 lg:px-8">
                        <div v-for="category in categories" :key="category.id"   class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm ">
                            <a href="#">
                                <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 ">
                                   {{category.name}} Kategorisi
                                </h5>
                            </a>
                            <p class="mb-3 font-normal text-gray-700">
                              {{category.description}}
                            </p>
                            <div class="py-4">
                                <Link
                                    :href="route('products', { id: category.id })"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 ">
                                    Ürünlere Git
                                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

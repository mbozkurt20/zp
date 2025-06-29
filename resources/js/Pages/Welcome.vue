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
    categories.value = response.data.data ?? []
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
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 sticky top-0 z-50 bg-white/90 backdrop-blur shadow flex flex-col sm:flex-row sm:items-center sm:justify-between py-3"
    >
        <div class="flex items-center gap-2">
            <img class="h-10" src="/public/images/logo.png" alt="">
            <h1 class="text-xl font-semibold text-gray-800">Emisoft Menü</h1>
        </div>

        <nav
            v-if="canLogin"
            class="flex flex-col sm:flex-row "
        >
            <div v-if="$page.props.auth.user" class="bg-white/80 border-2 border-blue-900 rounded-2xl hover:bg-blue-950 hover:text-white ml-5">
                <Link

                    :href="route('dashboard')"
                    class="px-4 py-2 rounded-md font-bold     transition text-center"
                >
                    Alışverişe Başla ->
                </Link>
            </div>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="px-4 py-2 rounded-md  text-blue-950 hover:text-blue-700  transition text-center"
                >
                    Giriş Yap
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="px-4 py-2 rounded-md text-blue-950 hover:text-blue-700 transition text-center"
                >
                    Hesap Aç
                </Link>
            </template>
        </nav>
    </header>

    <div class="bg-blue-950 text-black/50  dark:text-white/50 ">

        <div class="relative flex min-h-screen flex-col w-full  mx-auto max-w-7xl">
            <div class="relative w-full">
                <main class="mt-6 py-12">
                    <div v-if="categories.length" class="px-6 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <div v-for="category in categories" :key="category.id"
                             class="p-6 bg-white/90 border border-gray-200 rounded-lg shadow h-52">
                            <a class="bg-white" href="#">
                                <h5 class="mb-2 text-2xl bg-white/80 font-bold tracking-tight py-1 rounded-lg text-center text-blue-950 ">
                                   {{category.name}}
                                </h5>
                            </a>
                            <p class="mb-3 font-normal text-gray-700">
                              {{category.description}}
                            </p>
                            <div class="py-4 pt-14">
                                <Link
                                    :href="route('products', { id: category.id })"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-blue-950 bg-white/80 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 ">
                                    Ürünlere Git
                                    <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <h4>Kategori Bulunmuyor...</h4>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

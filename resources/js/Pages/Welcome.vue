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
                    <div v-if="categories.length" class="px-6 sm:px-6 lg:px-8  gap-10">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4 backdrop-blur-md">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                class="bg-white/10 border border-white/30 rounded-2xl shadow-xl backdrop-blur-lg p-4 relative overflow-hidden transition-all duration-300 hover:scale-105"
                            >
                                <!-- Soft glow effect -->
                                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-blue-100/10 pointer-events-none"></div>

                                <!-- Image -->
                                <div class="h-72 w-full rounded-xl overflow-hidden border border-white/20">
                                    <img
                                        class="w-full h-full object-cover object-center"
                                        :src="category.image ? `/storage/${category.image}` : '/images/images.jpeg'"
                                        alt="Kategori Görseli"
                                    />
                                </div>

                                <!-- Content -->
                                <div class="relative z-10 mt-4 flex flex-col justify-between h-[160px]">
                                    <h3 class="text-xl font-semibold text-white text-center drop-shadow-sm">
                                        {{ category.name }}
                                    </h3>
                                    <p class="text-sm text-white/80 text-center mt-2 line-clamp-3">
                                        {{ category.description }}
                                    </p>

                                    <div class="mt-4 text-center">
                                        <Link
                                            :href="route('products', { id: category.id })"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-white/10 border border-white/30 rounded-full hover:bg-white/20 backdrop-blur-sm transition"
                                        >
                                            Ürünlere Git
                                            <svg class="w-4 h-4 rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                <path
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M1 5h12m0 0L9 1m4 4L9 9"
                                                />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
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

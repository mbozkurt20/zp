<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import {useCartStore} from '../Stores/cartStore'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img
            id="background"
            class="absolute -left-20 top-0 max-w-[877px]"
            src="https://laravel.com/assets/img/welcome/background.svg"
        />
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

                <header class="flex flex-col gap-4 py-6 sm:flex-row sm:justify-between sm:items-center">
                    <div class="flex-1">
                        <!-- Logonuz veya başlık buraya gelebilir -->
                    </div>

                    <nav v-if="canLogin" class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="w-full sm:w-auto text-center rounded-md text-xl px-4 text-lg py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            → Satın Almaya Git
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="w-full sm:w-auto text-center rounded-md px-4  text-lg py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Giriş Yap
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="w-full sm:w-auto text-center rounded-md px-4 py-2 text-lg text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Hesap Aç
                            </Link>
                        </template>
                    </nav>
                </header>


                <main class="mt-6">
                    <div class="mx-auto max-w-7xl px-6 sm:px-6 lg:px-8">
                        <div v-for="category in categories" :key="category.id"
                             class="mb-10 overflow-hidden bg-orange-500 shadow-xl rounded-2xl">
                            <div class="grid sm:grid-cols-2 grid-cols-1  px-3 py-8">
                                <h4 class=" text-2xl text-gray-100 pl-8 font-bold ">
                                    {{ category.name }} Reyonu
                                </h4>

                                <!-- Arama inputu -->
                                <div class="px-5 pb-3 py-4 sm:py-0 w-full">
                                    <input
                                        v-model="searchQueries[category.id]"
                                        type="text"
                                        placeholder="Ürün ara..."
                                        class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400"
                                    />
                                </div>
                            </div>

                            <div class="p-10 text-gray-900">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                                    <div
                                        v-for="product in filteredProducts(category)"
                                        :key="product.id"
                                        class="bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 p-6 flex flex-col items-center text-center"
                                    >
                                        <img
                                            :src="`/storage/${product.image}`"
                                            alt=""
                                            class="w-36 h-36 object-cover rounded-xl mb-4 shadow-inner"
                                        />
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ product.name }}</h3>

                                        <div class="text-xl font-extrabold text-orange-500 mb-4">
                                            {{ product.price }}₺
                                        </div>

                                        <div class="font-semibold text-green-500 text-sm  mb-4">
                                            Stok  {{ product.quantity > 0 ? 'Mevcut' : 'Gelince Haber Ver'}}
                                        </div>

                                        <div class="py-5">
                                            <img :src="`https://barcode.tec-it.com/barcode.ashx?data=${product.barcode}&code=Code128&translate-esc=false&text=false`" />
                                        </div>

                                        <div class="flex items-center justify-center gap-4 mt-auto">
                                            <button
                                                @click="cartStore.decreaseFromCart(product)"
                                                class="w-8 h-8 flex items-center justify-center bg-orange-500 hover:bg-orange-400 text-white text-lg font-bold rounded-full shadow"
                                            >-</button>

                                            <span class="font-semibold text-lg min-w-[24px] text-center">
              {{ cartStore.cart[product.id]?.quantity || 0 }}
            </span>

                                            <button
                                                @click="cartStore.addToCart(product)"
                                                class="w-8 h-8 flex items-center justify-center bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-full shadow"
                                            >+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>


                <footer
                    class="py-16 text-center text-sm text-black dark:text-white/70"
                >
                    Copyright (c) Tüm Haklara Saklıdır 2025
                </footer>
            </div>
        </div>
    </div>
</template>

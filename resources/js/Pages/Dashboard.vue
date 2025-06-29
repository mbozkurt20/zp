<script setup>
import {ref, onMounted, computed,reactive} from 'vue'
import axios from 'axios'
import {useCartStore} from '../Stores/cartStore'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

import {Link} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

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
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex gap-6">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800"
                >
                    Sepet:
                </h2>

                <div class="flex flex-col md:flex-row text-sm text-gray-800 gap-5 mt-1">
                    <div><span class="font-semibold">Toplam Ürün:</span> <strong
                        class="text-green-600">{{ cartStore.totalProduct }}</strong>
                    </div>
                    <div>
                        <span class="font-semibold">Toplam Tutar:</span> <strong
                        class="text-green-600">{{ cartStore.totalAmount }}₺</strong>
                    </div>

                    <div class="ml-auto gap-4 space-x-4">
                        <Link
                            class="  text-blue-950 font-bold   border-2 border-blue-950 py-1 px-2 rounded-xl hover:bg-blue-950 hover:text-white"
                            :href="route('basket')">
                            Sepete Git
                        </Link>

                        <a v-if="cartStore.cart"  class=" cursor-pointer text-red-600  ont-bold   border-2 border-red-500 py-1 px-2 rounded-xl hover:bg-red-500 hover:text-white"  @click="cartStore.clearCart()">Sepeti Temizle</a>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 ">
            <div class="mx-auto max-w-7xl px-6 sm:px-6 lg:px-8">
                <div v-if="categories.length" v-for="category in categories" :key="category.id"
                     class="mb-10 overflow-hidden bg-white/90 shadow-xl rounded-2xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 px-3 py-8">
                        <h4 class="mb-5 sm:mb-0 text-2xl text-blue-950 pl-8 font-bold ">
                            {{ category.name }} Reyonu
                        </h4>

                        <!-- Arama inputu -->
                        <div class="px-5 pb-3 w-full">
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
                                v-if="filteredProducts(category).length"
                                class="bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 p-6 flex flex-col items-center text-center"
                            >
                                <img
                                    :src="`/storage/${product.image}`"
                                    alt=""
                                    class="w-36 h-36 object-cover rounded-xl mb-4 shadow-inner"
                                />
                                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ product.name }} -   <span class="font-bold text-gray-600 mb-1">{{ product.sales_quantity }}</span> </h3>


                                <div class="text-xl font-extrabold text-orange-500 mb-4">
                                    {{ product.price }}₺
                                </div>

                                <div class="font-semibold text-green-500 text-sm  mb-4">
                                    Stok  {{ product.quantity > 0 ? 'Mevcut' : 'Gelince Haber Ver'}}
                                </div>

                                <div v-if="product.quantity" class="flex items-center justify-center gap-4 mt-auto">
                                    <button
                                        @click="cartStore.decreaseFromCart(product)"
                                        class="w-8 h-8 flex items-center justify-center bg-orange-500 hover:bg-orange-400 text-white text-lg font-bold rounded-full shadow"
                                    >-</button>

                                    <span class="font-semibold text-lg min-w-[24px] text-center">
              {{ cartStore.cart&&cartStore.cart[product.id]?.quantity || 0 }}
            </span>

                                    <button
                                        @click="cartStore.addToCart(product)"

                                        class="w-8 h-8 flex items-center justify-center bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-full shadow"
                                    >+</button>
                                </div>
                            </div>
                            <div class="bg-gray-100 px-4 rounded-lg py-4" v-else>
                                <h4>Ürün Bulunmuyor...</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="mt-20">
                    <div  class="bg-white/90 h-32 rounded-lg py-4" >
                        <h5 class="text-blue-950 text-3xl font-bold mx-auto text-center py-5">Ürünler Bulunmamaktadır...</h5>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

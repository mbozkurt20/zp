<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";

import {useCartStore} from '../Stores/cartStore'

import { Link } from '@inertiajs/vue3';


defineProps({
    products: Array,

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
    isLogin: {
        type: Boolean,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const cartStore = useCartStore()
</script>

<template>
    <header
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 sticky top-0 z-50 bg-white/90 backdrop-blur  flex flex-col sm:flex-row sm:items-center sm:justify-between py-3"
    >
        <div class="flex items-center gap-2">
            <span class="text-3xl">🛒</span>
            <h1 class="text-xl font-semibold text-gray-800">Emisoft Menü</h1>

            <div class="bg-white/80 border-2 border-blue-900 rounded-2xl hover:bg-blue-950 hover:text-white ml-5">
                <Link
                    :href="route('welcome')"
                    class="px-4 py-2 rounded-md font-bold     transition text-center"
                >
                    Kategoriler Listesi
                </Link>
            </div>
        </div>

        <nav v-if="canLogin" class="flex flex-col sm:flex-row ">
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

    <div class="flex py-4  border-t border-blue-950 max-w-7xl mx-auto gap-10 px-6 sm:px-6 ">
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

    <div class="min-h-screen bg-blue-950 shadow py-4">
        <div class="py-10 max-w-7xl mx-auto text-gray-900 ">
            <div class="grid grid-cols-1 px-6 sm:px-0 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <div
                    v-for="product in products"
                    :key="product.id"
                    v-if="products.length"
                    class="bg-white border border-gray-200 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 p-6 flex flex-col items-center text-center"
                >
                    <img
                        :src="`/storage/${product.image}`"
                        alt=""
                        class="w-36 h-36 object-cover rounded-xl mb-4 shadow-inner"
                    />
                    <h3 class="text-lg font-bold text-blue-950 mb-1">{{ product.name }} -  <span class=" font-bold text-gray-600 mb-1">{{ product.sales_quantity }}</span></h3>


                    <div class="text-xl font-extrabold text-orange-500 mb-4">
                        {{ product.price }}₺
                    </div>

                    <div class="font-semibold text-green-500 text-sm  mb-4">
                        Stok  {{ product.quantity > 0 ? 'Mevcut' : 'Gelince Haber Ver'}}
                    </div>

                    <Link v-if="!isLogin"
                        :href="route('login')"
                        class="px-4 py-2 rounded-md  bg-blue-950 hover:bg-blue-900 text-white  transition text-center"
                    >
                        Giriş Yap
                    </Link>

                    <div v-if="product.quantity&&isLogin" class="flex items-center justify-center gap-4 mt-auto">
                        <button
                            @click="cartStore.decreaseFromCart(product)"
                            class="w-8 h-8 flex items-center justify-center bg-orange-500 hover:bg-orange-400 text-white text-lg font-bold rounded-full shadow"
                        >-</button>

                        <span class="font-semibold text-lg min-w-[24px] text-center">
              {{ cartStore.cart && cartStore.cart[product.id]?.quantity || 0 }}
            </span>

                        <button
                            @click="cartStore.addToCart(product)"
                            class="w-8 h-8 flex items-center justify-center bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-full shadow"
                        >+</button>
                    </div>
                </div>

                <div class="mx-auto text-center bg-gray-100 py-4 px-4 rounded" v-else>
                    <h3> Bu Kategoriye Ait Ürün Bulunmuyor...</h3>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>

</style>

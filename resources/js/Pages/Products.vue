<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";

import {useCartStore} from '../Stores/cartStore'

import { Link } from '@inertiajs/vue3';


const props = defineProps({
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

onMounted(() => {
    props.products.forEach(product => {
        if (!product.variantId && product.variants.length > 0) {
            product.variantId = product.variants[0].id;
        }
    });
});
</script>

<template>
    <header
        class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 sticky top-0 z-50 bg-white/90 backdrop-blur  flex flex-col sm:flex-row sm:items-center sm:justify-between py-3"
    >
        <div class="flex items-center gap-2">
            <img class="h-10" src="/public/images/logo.png" alt="">
            <h1 class="text-xl font-semibold text-gray-800">Emisoft Menü</h1>

            <div class="bg-white/80 border-2 border-blue-900 rounded-2xl hover:bg-blue-950 hover:text-white ml-12">
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
            <!--div><span class="font-semibold">Toplam Ürün:</span> <strong
                class="text-green-600">{{ cartStore.totalProduct }}</strong>
            </div-->

            <!--div>
                <span class="font-semibold">Toplam Tutar:</span> <strong
                class="text-green-600">{{ cartStore.totalAmount }}₺</strong>
            </div-->

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
                    class="bg-white/10 border border-white/30 rounded-2xl shadow-xl backdrop-blur-lg  hover:shadow-xl transition-shadow duration-300 p-6 flex flex-col items-center text-center"
                >

                    <img
                        :src="`${product.image ? '/storage/'+product.image : '/images/images.jpeg'}`"
                        alt=""
                        class="w-36 h-36 object-cover rounded-xl mb-4 shadow-inner"
                    />
                    <h3 class="text-lg font-bold text-white mb-1">{{ product.name }}</h3>

                    <div class="space-y-2 mt-4  mb-4">
                        <label
                            v-for="variant in product.variants"
                            :key="variant.id"
                            :for="variant.id"
                            class="cursor-pointer border rounded-lg px-4 py-2 transition-all duration-200 text-sm
           flex items-center gap-1
           hover:border-orange-400
           "
                            :class="{
      'bg-orange-500 text-white border-orange-500 shadow-md': product.variantId === variant.id,
      'bg-white text-gray-800': product.variantId !== variant.id
    }"
                        >
                            <input
                                type="radio"
                                class="hidden"
                                :id="variant.id"
                                :value="variant.id"
                                v-model="product.variantId"
                            />
                            <span class="font-medium">{{ variant.quantity }} {{ variant.type }}</span>
                            <span class="text-xs opacity-70">|</span>
                            <span class="font-semibold">{{ variant.price }}₺</span>
                        </label>
                    </div>

                    <Link v-if="!isLogin"
                        :href="route('login')"
                        class="px-4 py-2 rounded-md  bg-blue-950 hover:bg-blue-900 text-white  transition text-center"
                    >
                        Giriş Yap
                    </Link>

                    <div v-if="isLogin&&product.quantity" class="flex items-center justify-center pt-6 gap-4 mt-auto">
                        <button
                            @click="cartStore.addToCart(product)"
                            class="py-0.5 px-3 flex items-center justify-center bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-lg shadow"
                        >Sepete Ekle</button>
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

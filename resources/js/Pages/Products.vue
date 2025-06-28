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
    phpVersion: {
        type: String,
        required: true,
    },
});

const cartStore = useCartStore()
</script>

<template>
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md shadow-md flex flex-col gap-4 py-4 sm:flex-row sm:justify-between sm:items-center px-4 "
    >
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-black text-gray-600">
                <Link   :href="route('welcome')">
                    🛒 Emisoft Menü
                </Link>

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

    <div class=" bg-gray-50 shadow py-4">
      <div class="flex max-w-7xl  px-6 sm:px-0 mx-auto gap-10">
          <h2
              class="text-xl font-semibold leading-tight text-gray-800"
          >
              Sepet:
          </h2>

          <div class="flex text-sm text-gray-800 gap-5 mt-1 ">
              <div><span class="font-semibold">Toplam Ürün:</span> <strong
                  class="text-green-600">{{ cartStore.totalProduct }}</strong>
              </div>
              <div>
                  <span class="font-semibold">Toplam Tutar:</span> <strong
                  class="text-green-600">{{ cartStore.totalAmount }}₺</strong>
              </div>

              <div class="ml-auto float-right justify-end">
                  <Link
                      class=" text-gray-500 border border-gray-900 rounded-md py-2 px-3 hover:bg-gray-700 hover:text-white"
                      :href="route('basket')">
                      Sepet
                  </Link>
              </div>
          </div>
      </div>
    </div>

    <div class="py-10 max-w-7xl mx-auto text-gray-900">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
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
                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ product.name }} -  <span class=" font-bold text-gray-600 mb-1">{{ product.sales_quantity }}</span></h3>


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
              {{ cartStore.cart[product.id]?.quantity || 0 }}
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
</template>

<style scoped>

</style>

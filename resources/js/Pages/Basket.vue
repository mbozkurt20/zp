<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '../Stores/cartStore'

import {toast} from "vue3-toastify";
const products = ref([])
const cartStore = useCartStore()

const fetchProducts = async () => {
    const response = await axios.get('/products');
    console.log({response:response})
    products.value = response.data.data
}

const props = defineProps(['activeBasket'])

const isCheckout = (id) => {
    axios.get(`/checkout/basket/${id}`).then(res => {
        console.log({sa:res})
        props.activeBasket.is_checkout = res.data.data.is_checkout;
        toast(res.data.message, {
            "theme": "dark",
            "type": "success",
            "dangerouslyHTMLString": true,
        })
    }).catch(err => {
        console.log({err:err})
    })
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
                    Sepet:
                </h2>

                <div class="flex text-sm  gap-5 mt-1">
                    <div><span class="font-semibold">Toplam Ürün:</span> <strong class="text-green-600">{{ cartStore.totalProduct }}</strong></div>
                    <div><span class="font-semibold">Toplam Tutar:</span> <strong class="text-green-600">{{ cartStore.totalAmount }}₺</strong></div>

                    <a v-if="cartStore.cart"  class=" cursor-pointer text-red-600  hover:text-red-500"  @click="cartStore.clearCart()">Sepeti Temizle</a>
                </div>
            </div>
        </template>

        <div class="py-12 px-5 sm:px-8 rounded-lg">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="container mx-auto mt-10">
                    <div class="sm:flex shadow-md my-10">
                        <div class="  w-full  sm:w-3/4 bg-white px-10 py-10">
                            <div class="flex justify-between border-b pb-8">
                                <h1 class="font-semibold text-3xl">Alışveriş Sepeti</h1>
                                <h2 class="font-semibold text-xl">Sepetinizde <span class="text-orange-600">{{cartStore.totalProduct}} Ürün</span> Bulunuyor.</h2>
                            </div>
                            <div v-for="item in cartStore.cart" class="border-b border-orange-200 md:flex items-strech py-8 md:py-10 lg:py-8 border-t">

                                <div class="md:w-4/12 2xl:w-1/4 w-full">
                                    <img :src="`/storage/${item.image}`" alt="Black Leather Purse" class=" h-full object-center object-cover md:block hidden" />
                                    <img :src="`/storage/${item.image}`" alt="Black Leather Purse" class="md:hidden w-full h-full object-center object-cover" />
                                </div>
                                <div class="md:pl-3 md:w-8/12 2xl:w-3/4 flex flex-col justify-center">
                                    <!--p class="text-xs leading-3 text-gray-800 md:pt-0 pt-4">RF293</p-->
                                    <div class="mx-auto text-center w-full">
                                        <p class="text-base font-black leading-none text-blue-950 py-4">{{item.name}} - <span class=" font-bold text-gray-600 mb-1">{{ item.sales_quantity }}</span></p>

                                        <div class="text-xl font-extrabold text-orange-500 mb-4">
                                            {{ item.price }}₺
                                        </div>

                                        <p class="text-sm font-black leading-none text-gray-500 pb-4">{{item.description}}</p>

                                        <div class="font-semibold text-green-500 text-sm  mb-4">
                                            Stok  {{ item.quantity > 0 ? 'Mevcut' : 'Gelince Haber Ver'}}
                                        </div>

                                        <div class="flex mx-auto justify-center gap-4 mt-4">
                                            <button
                                                @click="cartStore.decreaseFromCart(item)"
                                                class="px-3 py-1 bg-orange-500 text-white hover:bg-orange-400 rounded-full shadow"
                                            >-</button>
                                            <span class="font-semibold text-lg">
                                                {{ cartStore.cart[item.id]?.quantity || 0 }}
                                            </span>
                                            <button
                                                @click="cartStore.addToCart(item)"
                                                class="w-8 h-8 flex items-center justify-center bg-green-500 hover:bg-green-400 text-white text-lg font-bold rounded-full shadow"
                                            >+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 py-5 w-auto" v-if="!cartStore.totalProduct">
                                <p class="text-center mx-auto text-gray-500">Sepette Ürün Bulunmuyor...</p>
                            </div>

                            <Link class=" flex font-semibold text-indigo-600 text-sm mt-10" :href="route('dashboard')">
                                <svg class="fill-current mr-2 text-indigo-600 w-4" viewBox="0 0 448 512">
                                    <path
                                        d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z" />
                                </svg>
                                Alışverişe Devam Et
                            </Link>
                        </div>
                        <div id="summary" class=" w-full   sm:w-1/4   md:w-1/2     px-8 py-10 bg-white/90">
                            <h1 class="font-semibold text-3xl border-b pb-8">Sepet Özeti</h1>
                            <div class=" justify-between mt-10 mb-5">
                                <div class="" v-for="item in cartStore.cart">
                                    <div class="mb-2 gap-2 flex border-b border-gray-300">
                                        <span>{{item.name}} <strong>x {{item.quantity}}</strong></span>
                                        <span class="ml-auto font-semibold">{{item.price}}₺ </span>
                                    </div>
                                </div>
                            </div>
                            <!--div>
                                <label class="font-medium inline-block mb-3 text-sm uppercase">
                                    Shipping
                                </label>
                                <select class="block p-2 text-gray-600 w-full text-sm">
                                    <option>Standard shipping - $10.00</option>
                                </select>
                            </div-->

                            <div class="border-t mt-8">
                                <div class="flex font-semibold justify-between py-6 text-sm uppercase">
                                    <span>Toplam Tutar</span>
                                    <span class="text-lg">{{cartStore.totalAmount}}₺</span>
                                </div>


                                <div v-if="cartStore.totalProduct">
                                    <div v-if="activeBasket && !activeBasket.is_checkout" class="pt-24">
                                        <p class="text-orange-600 badge"><strong>Bilgi:</strong> Alışverişi Tamamla onaylandıktan sonra siparişleriniz hazırlanmaya başlanıcaktır.</p>
                                        <h5 class="text-white bg-green-500 rounded-lg py-2 cursor-pointer hover:bg-green-400 text-xl mx-auto text-center mt-5">
                                            <a @click="isCheckout(activeBasket.id)"> Alışverişi Tamamla</a>
                                        </h5>
                                    </div>

                                    <div v-else disabled="" class="pt-24">
                                        <p class="text-gray-700 text-lg">Siparişleriniz Hazırlanıyor...</p>
                                        <h5 class="text-white bg-blue-950 hover:bg-blue-900 rounded-xl py-2 cursor-pointer text-2xl mx-auto text-center mt-5">
                                            <a @click="isCheckout(activeBasket.id)"> Alışverişe Devam Et </a>
                                        </h5>
                                    </div>
                                </div>


                                <!--button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full">
                                    Checkout
                                </button-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

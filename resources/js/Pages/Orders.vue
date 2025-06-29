<script setup>
import {ref, onMounted, computed,reactive} from 'vue'
import axios from 'axios'
import {useCartStore} from '../Stores/cartStore'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';

import {Link} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Pusher from "pusher-js";
import {toast} from "vue3-toastify";

const orders = ref([])
const cartStore = useCartStore()

const searchQueries = reactive({})
const fetchOrders = async () => {
    const response = await axios.get('/order');
    console.log({orders: response})
    orders.value = response.data.data
}
const pusher = new Pusher('ac293c727687682a5b63', {
    cluster: 'eu',
})

const channel = pusher.subscribe('orders-channel') // kanal ismi
channel.bind(`orders-event`, (data) => {
    console.log({gelen: data})

    orders.value = data;
});

onMounted(() => {
    fetchOrders();
});

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex gap-6">
                <h2
                    class="text-xl font-semibold leading-tight text-gray-800"
                >
                    Siparişler
                </h2>

                <div class="flex text-sm text-gray-800 gap-5 mt-1">

                </div>
            </div>
        </template>

        <div class="py-12 ">
            <div class="mx-auto max-w-7xl px-6 sm:px-6 lg:px-8">
                <table  v-if="orders.length"  class="min-w-full table-auto border border-gray-300 rounded-lg shadow-md">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-lg font-semibold text-gray-700">Sipariş No</th>
                        <th class="px-6 py-3 text-left text-lg font-semibold text-gray-700">Müşteri</th>
                        <th class="px-6 py-3 text-left text-lg font-semibold text-gray-700">Sipariş Tarihi</th>
                        <th class="px-6 py-3 text-left text-lg font-semibold text-gray-700">Hazır Edildi</th>
                        <th class="px-6 py-3 text-left text-lg font-semibold text-gray-700">Sipariş Durumu</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        :class="[{'bg-yellow-500': !order.is_ready},{'bg-green-500': order.is_ready}]"
                        class="border-t border-gray-200 hover:bg-gray-500 cursor-pointer"
                    >

                        <td class="px-6 py-4 text-gray-100 font-semibold text-lg">{{ order.id }}</td>
                        <td class="px-6 py-4 text-gray-100 font-semibold text-lg">{{ order.user }}</td>
                        <td class="px-6 py-4 text-gray-100 font-semibold text-lg">
                            {{ order.created_at }}
                        </td>
                        <td class="px-6 py-4 text-gray-100 font-semibold text-lg">
                            {{ order.ready_date }}
                        </td>
                        <td
                            class="px-6 py-4 font-semibold text-lg text-gray-100"
                        >
                            {{ order.is_ready ? 'Siparişiniz Hazır.' : 'Hazırlanıyor...' }}
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div v-else class="pt-32">
                    <div class="bg-white/90 text-blue-950 h-32 py-5 rounded-lg">
                        <h5 class=" text-3xl font-bold mx-auto text-center py-5">Sipariş Bulunmamaktadır...</h5>
                    </div>
                </div>
            </div>


        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import {Head, Link} from '@inertiajs/vue3';
import axios from 'axios';
import {ref} from 'vue';

const props = defineProps(['category', 'products']);
const productList = ref(props.products);

// Günün şarkısı yap
async function setDay(id) {
    try {
        await axios.post(`/songs/set-day/${id}`);
        productList.value = productList.value.map(p => ({
            ...p,
            is_day: p.id === id,
        }));
    } catch (e) {
        alert('Günün Şarkısı yapma başarısız!');
    }
}

// Favori toggle
async function toggleFavorite(id) {
    try {
        await axios.post(`/songs/favorite/${id}`);
        productList.value = productList.value.map(p => p.id === id ? {...p, is_favorite: !p.is_favorite} : p);
    } catch (e) {
        alert('Favori toggle başarısız!');
    }
}

// Ürünü sil
async function removeProduct(id) {
    try {
        await axios.post(`/songs/remove/${id}`);
        productList.value = productList.value.filter(p => p.id !== id);
    } catch (e) {
        alert('Ürün silme başarısız!');
    }
}

// Görsel güncelle
async function handleImageChange(event, productId) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post(`/songs/update-image/${productId}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        const updatedProduct = productList.value.find(p => p.id === productId);
        if (updatedProduct) {
            updatedProduct.image = response.data.image;
        }

        alert('Görsel başarıyla güncellendi');
    } catch (error) {
        alert('Görsel güncelleme başarısız!');
    }
}
</script>

<template>
    <div class="min-h-screen bg-pink-50 dark:bg-gray-900 py-10 pb-24 px-4">
        <h1 class="text-3xl font-bold text-center text-pink-600 mb-6">{{ category.name }}</h1>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div
                v-for="product in productList"
                :key="product.id"
                :class="[
                    'relative bg-white dark:bg-gray-800 p-5 rounded-xl shadow-md flex flex-col items-center transition-transform hover:scale-[1.03] cursor-pointer',
                    product.is_day ? 'border-4 border-pink-500' : 'border border-transparent'
                ]"
            >
                <!-- Sil butonu -->
                <button
                    @click.stop="removeProduct(product.id)"
                    class="absolute top-3 right-3 text-red-600 hover:text-red-800 transition-colors"
                    title="Sil"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Resim ve değiştirme ikonu -->
                <div class="relative">
                    <img
                        :src="`/storage/${product.image}`"
                        alt="Şarkı resmi"
                        class="w-28 h-28 object-cover rounded-full mb-3 shadow-lg"
                    />
                    <label class="absolute bottom-0 right-0 bg-white rounded-full p-1 shadow cursor-pointer hover:bg-pink-100">
                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleImageChange($event, product.id)"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V8.414a2 2 0 00-.586-1.414l-4.414-4.414A2 2 0 0012.586 2H4z" />
                            <path d="M8 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </label>
                </div>

                <h4 class="text-lg font-semibold text-center" :class="product.is_day ? 'text-pink-600' : 'text-gray-900 dark:text-gray-100'">
                    {{ product.name }}
                </h4>
                <p class="text-sm text-center text-gray-600 dark:text-gray-300 mt-2 line-clamp-3">
                    {{ product.description }}
                </p>

                <div class="mt-4 flex space-x-4">
                    <!-- Günün Şarkısı -->
                    <button
                        @click="setDay(product.id)"
                        :class="[
                            'flex items-center space-x-1 px-4 py-2 rounded-full text-sm font-semibold transition-colors',
                            product.is_day
                                ? 'bg-pink-600 text-white shadow-md'
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-pink-600 hover:text-white'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="none"
                             :class="product.is_day ? 'text-white' : 'text-pink-600'">
                            <path
                                d="M12 2a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.22a1 1 0 011.41 1.42l-1.42 1.41a1 1 0 11-1.41-1.41l1.42-1.42zm4.78 7.78a1 1 0 110 2h-2a1 1 0 110-2h2zm-3.21 6.79a1 1 0 011.41 0l1.42 1.42a1 1 0 01-1.41 1.41l-1.42-1.42a1 1 0 010-1.41zM12 18a6 6 0 100-12 6 6 0 000 12z"
                            />
                        </svg>
                        <span>Günün Şarkısı</span>
                    </button>

                    <!-- Favori Toggle -->
                    <button
                        @click="toggleFavorite(product.id)"
                        :class="[
                            'flex items-center space-x-1 px-4 py-2 rounded-full text-sm font-semibold transition-colors',
                            product.is_favorite
                                ? 'bg-yellow-400 text-white shadow-md'
                                : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-yellow-400 hover:text-white'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" stroke="none"
                             :class="product.is_favorite ? 'text-white' : 'text-yellow-400'">
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
                            />
                        </svg>
                        <span>Favori</span>
                    </button>
                </div>

                <!-- Badge -->
                <div v-if="product.is_day" class="absolute top-2 left-2 bg-pink-600 text-white px-2 py-1 rounded-full text-xs font-bold shadow">
                    🌞 Günün Şarkısı
                </div>
            </div>
        </div>

        <!-- Nav bar -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 dark:bg-gray-900 dark:border-gray-700 flex justify-around py-2 z-50">
            <Link :href="route('day')" class="flex flex-col items-center text-sm" :class="$page.url === '/day' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                🎵<span>Günün Şarkısı</span>
            </Link>
            <Link :href="route('add')" class="flex flex-col items-center text-sm" :class="$page.url === '/add' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                ➕<span>Yeni Ekle</span>
            </Link>
            <Link :href="route('favorites')" class="flex flex-col items-center text-sm" :class="$page.url === '/favorites' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                ⭐<span>Favoriler</span>
            </Link>
            <Link :href="route('categories.all')" class="flex flex-col items-center text-sm" :class="$page.url === '/categories-all' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                📚<span>Kataloglar</span>
            </Link>
        </div>
    </div>
</template>

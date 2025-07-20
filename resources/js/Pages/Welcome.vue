<script setup>
import {Head,} from '@inertiajs/vue3';
import {onMounted, reactive, ref, computed} from "vue";
import axios from "axios";
import {toast} from "vue3-toastify";

const props = defineProps(['categories']);
const tab = ref('list');


const form = ref({
    name: '',
    slug: '',
    description: '',
    image: null,
    file: null,
    category_id: 1,
});

const currentSong = ref(null)
const audioRefs = ref({})

const setAudioRef = (id, el) => {
    if (el) {
        audioRefs.value[id] = el;
    }
};

const playSong = (id) => {
    if (currentSong.value && currentSong.value !== id) {
        pauseSong(currentSong.value);
    }

    const audio = audioRefs.value[id];
    if (audio) {
        audio.play();
        currentSong.value = id;
    }
};

const pauseSong = (id) => {
    const audio = audioRefs.value[id];
    if (audio) {
        audio.pause();
        audio.currentTime = 0;
    }

    if (currentSong.value === id) {
        currentSong.value = null;
    }
};
const handleFileUpload = (e) => {
    form.value.file = e.target.files[0]
}
const submitForm = async () => {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('slug', form.value.slug)
    formData.append('description', form.value.description)
    formData.append('category_id', form.value.category_id)
    if (form.value.image) formData.append('image', form.value.image)
    if (form.value.file) formData.append('file', form.value.file)

    try {
        await axios.post('/songs', formData, {
            headers: {'Content-Type': 'multipart/form-data'},
        }).finally(() => {
            form.value = {
                name: '',
                slug: '',
                description: '',
                image: null,
                file: null,
                category_id: 1,
            }
        })

        toast.success('Müzik başarıyla yüklendi!')

        await fetchProducts();
    } catch (error) {
        console.error(error)
        toast.warning('Müzik Yüklenemedi')
    }
}
const handleImageUpload = (e) => {
    form.value.image = e.target.files[0]
}

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

const products = ref([])
const categories = ref(props.categories)

const searchQuery = ref('');

const fetchProducts = async () => {
    const response = await axios.get('/products');
    products.value = response.data.data
}

const fetchCategories = async () => {
    const response = await axios.get('/categories');
    categories.value = response.data.data ?? []
}
const audioRef = ref(null)

const playAudio = (file) => {
    if (!file) return toast.warning("Müzik dosyası mevcut değil.");

    const url = typeof file === 'string' ? `/storage/${file}` : URL.createObjectURL(file);
    if (audioRef.value) {
        audioRef.value.src = url;
        audioRef.value.load(); // önemli!
        audioRef.value.play();
    }
}
onMounted(() => {
    fetchCategories();
    fetchProducts();
});

const filteredProductsByCategory = (category) => {
    const query = searchQuery.value.toLowerCase().trim();

    return products.value.filter(p =>
        p.category_id === category.id &&
        (!query ||
            (typeof p.description === 'string' && p.description.toLowerCase().includes(query)) ||
            (typeof p.name === 'string' && p.name.toLowerCase().includes(query)))
    );
};

const messages = [
    "Seninle her şey daha güzel.",
    "Kalbim hep sana ait.",
    "Bir gülüşün tüm karanlığı aydınlatır.",
    "Seninle geçen her an bir ömre bedel.",
    "Aşk, senin adınla başlar.",
];

let index = 0;
let marquee = null;

onMounted(() => {
    marquee = document.getElementById('marquee-container');

    function updateMarquee() {
        if (!marquee) return;
        marquee.classList.remove('animate-slide');
        void marquee.offsetWidth;
        marquee.textContent = `"${messages[index]}"`;
        marquee.classList.add('animate-slide');
        index = (index + 1) % messages.length;
    }

    updateMarquee();
    setInterval(updateMarquee, 8000);
});

const setAsDay = async (id) => {
    try {
        await axios.post(`/songs/set-day/${id}`);
        toast.success("Günün şarkısı başarıyla güncellendi!");
        await fetchProducts();
    } catch (error) {
        console.error(error);
        toast.warning("Günün şarkısı güncellenemedi.");
    }
}

const setAsRemove = async (id) => {
    try {
        await axios.post(`/songs/remove/${id}`);
        toast.success("Günün şarkısı başarıyla silindi!");
        await fetchProducts();
    } catch (error) {
        console.error(error);
        toast.warning("Günün şarkısı silinemedi.");
    }
}


const daySong = computed(() => products.value.find(p => p.is_day))
</script>

<template>
    <Head title="Hoşgeldiniz"/>
    <header
        style="background: #ec49c5" class=" flex mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 sticky top-0 z-50  backdrop-blur shadow flex-col sm:flex-row sm:items-center sm:justify-between py-2"
    >
        <div class="flex items-center gap-2">
            <img class="h-10" src="/public/images/ZM2014Logo.png" alt="">
            <div class="relative w-full sm:w-[400px] overflow-hidden mt-2 sm:mt-0 h-6">
                <div id="marquee-container"
                     class="absolute whitespace-nowrap text-white font-medium text-sm animate-slide">
                    "Seninle her şey daha güzel."
                </div>
            </div>
        </div>

    </header>

    <div class="py-5 dark:text-white/50 " style="background: #f116bd">
        <div class="relative flex min-h-screen flex-col w-full  mx-auto max-w-7xl">
            <div class="relative w-full">
                <main class="px-3 sm:px-8">
                    <h1 class="py-4 text-3xl text-white font-bold text-center mx-auto">Z&M Malikanesi</h1>

                    <div class="flex justify-center mx-auto text-center gap-6 py-8">
                        <button class="border border-white text-xl px-5 rounded-full hover:bg-white hover:text-pink-900 font-bold text-white" @click="tab = 'list'">Listemiz</button>
                        <button class="border border-white text-xl px-5 rounded-full hover:bg-white hover:text-pink-900 font-bold text-white" @click="tab = 'add'">Yeni Ekle</button>
                    </div>

                    <div v-if="tab === 'list'">
                        <section v-if="products.find(p => p.is_day)"
                                 class="my-10 grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div
                                class="lg:col-span-2 bg-gradient-to-b bg-white/80 text-pink-500 p-6 rounded-xl shadow-lg">
                                <h2 class="text-xl font-bold mb-4">🎵 Günün Şarkısı</h2>
                                <div v-if="daySong" class="flex flex-col items-center">
                                    <img :src="`/storage/${daySong.image}`" alt="Günün Şarkısı"
                                         class="w-32 h-32 object-cover rounded-full mb-4"
                                         :class="{ 'spin-animation': currentSong === daySong.id }"/>
                                    <p class="font-semibold mb-2">{{ daySong.name }}</p>
                                    <p class="text-sm mb-4 text-center">{{ daySong.description }}</p>

                                    <audio :ref="el => setAudioRef(daySong.id, el)" :src="`/storage/${daySong.file}`" preload="none" />


                                    <div class="flex gap-2 mt-3">
                                        <button
                                            @click="playSong(daySong.id)"
                                            class="px-4 py-1 bg-pink-500 text-white rounded hover:bg-pink-600"
                                            v-if="currentSong !== daySong.id"
                                        >
                                            Oynat
                                        </button>

                                        <button
                                            @click="pauseSong(daySong.id)"
                                            class="px-4 py-1 bg-pink-500 text-white rounded"
                                            v-else
                                        >
                                            Durdur
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="py-8">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Şarkı açıklamasında ara..."
                                class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            />
                        </div>
                        <div v-for="category in categories" :key="category.id" class="mb-2">
                            <h2 v-if="filteredProductsByCategory(category).length" class="mx-auto text-center text-3xl font-bold text-white mb-4">{{category.name}}</h2>
                            <div class="lg:col-span-3 grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                <div v-for="product in filteredProductsByCategory(category)"
                                     :key="product.id"
                                     class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-md flex flex-col items-center"
                                >
                                    <img :src="`/storage/${product.image}`" alt="Şarkı"
                                         class="w-28 h-28 object-cover rounded-full mb-3"
                                         :class="{ 'spin-animation': currentSong === product.id }"
                                    />
                                    <h4 class="text-md font-semibold text-center text-gray-800 dark:text-white">
                                        {{ product.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 text-center mt-1 mb-3">
                                        {{ product.description }}</p>

                                    <audio :ref="el => setAudioRef(product.id, el)" :src="`/storage/${product.file}`" preload="none" />


                                    <div class="flex gap-2 mt-3">
                                        <button
                                            @click="playSong(product.id)"
                                            class="px-4 py-1 bg-pink-500 text-white rounded hover:bg-pink-600"
                                            v-if="currentSong !== product.id"
                                        >
                                            Oynat
                                        </button>

                                        <button
                                            @click="pauseSong(product.id)"
                                            class="px-4 py-1 bg-pink-500 text-white rounded"
                                            v-else
                                        >
                                            Durdur
                                        </button>
                                        <button @click="setAsDay(product.id)"
                                                class="px-4 py-1 bg-white text-pink-500 border border-pink-500 rounded hover:bg-pink-100">
                                            Günün Şarkısı Yap
                                        </button>

                                        <button @click="setAsRemove(product.id)"
                                                class="px-4 py-1 bg-white text-pink-500 border border-pink-500 rounded hover:bg-pink-100">
                                            Sil
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 mt-10  bg-white dark:bg-gray-900 rounded-2xl shadow-md max-w-2xl mx-auto">
                        <h1 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Müzik Ekle</h1>

                        <form @submit.prevent="submitForm" class="space-y-4">
                            <div class="py-3">
                                <label class="block mb-1 text-sm text-gray-700 dark:text-white">Kategori</label>
                                <select v-model="form.category_id"
                                        class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    <option v-for="category in categories" :value="category.id">{{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="py-2">
                                <label class="block mb-1 text-sm text-gray-700 dark:text-white">Görsel Yükle
                                    (Opsiyonel)</label>
                                <input type="file" @change="handleImageUpload"
                                       class="w-full text-sm text-gray-600 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-pink-500 file:text-white"/>
                            </div>

                            <div class="py-2">
                                <label class="block mb-1 text-sm text-gray-700 dark:text-white">Müzik Dosyası</label>
                                <input type="file" @change="handleFileUpload"
                                       class="w-full text-sm text-gray-600 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:bg-pink-500 file:text-white"/>
                            </div>

                            <div class="py-1 pb-2">
                                <label class="block mb-1 text-sm text-gray-700 dark:text-white">Açıklama</label>
                                <textarea rows="3" placeholder="Özel bir anlamı varsa yazabiliriz..." v-model="form.description"
                                          class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"/>
                            </div>

                            <button type="submit"
                                    class="px-6 w-full py-2 bg-pink-500 text-white rounded hover:bg-pink-600 transition duration-200">
                                Kaydet
                            </button>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes slide {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-100%);
    }
}

.animate-slide {
    animation: slide 6s linear infinite;
}

@keyframes spin-slow {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.spin-animation {
    animation: spin 4s linear infinite;
}


</style>

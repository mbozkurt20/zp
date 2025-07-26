<script setup>
import {Head, Link,} from '@inertiajs/vue3';
import {onMounted, reactive, ref, computed} from "vue";
import axios from "axios";
import {toast} from "vue3-toastify";

const props = defineProps(['categories']);
const tab = ref('day');
const otpLength = 4
const otp = reactive(Array(otpLength).fill(''))
const inputRefs = ref([])
const password = '2105';
const enterPassword = ref('');

const onInput = (e, index) => {
    const value = e.target.value

    // Sadece rakam girilmesine izin ver
    if (!/^\d$/.test(value)) {
        otp[index] = ''
        return
    }

    otp[index] = value

    // Son kutuda değilsek, bir sonrakine geç
    if (index < otpLength - 1) {
        inputRefs.value[index + 1]?.focus()
    } else {
        // Son kutuya girildiyse submit fonksiyonu tetikle
        submitOtp()
    }
}

const onKeyDown = (e, index) => {
    if (e.key === 'Backspace') {
        if (otp[index] === '') {
            if (index > 0) {
                otp[index - 1] = ''
                inputRefs.value[index - 1]?.focus()
            }
        }
    }
}

const submitOtp = () => {
    const code = otp.join('')

    if (code !== password) {
        return toast("Hatalı Bilgiler", {
            "theme": "auto",
            "type": "default",
            "dangerouslyHTMLString": true
        })
    }

    enterPassword.value = code;

    return toast("Hoşgelginiz...", {
        "theme": "auto",
        "type": "success",
        "dangerouslyHTMLString": true
    })
}

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
const isLoading = ref(false);
const submitForm = async () => {

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('slug', form.value.slug)
    formData.append('description', form.value.description)
    formData.append('category_id', form.value.category_id)
    if (form.value.image) formData.append('image', form.value.image)
    if (form.value.file) formData.append('file', form.value.file)

    try {
        isLoading.value = true;

        await axios.post('/songs', formData, {
            headers: {'Content-Type': 'multipart/form-data'},
        }).finally(() => {
            isLoading.value = false;
            form.value = {
                name: '',
                slug: '',
                description: '',
                image: null,
                file: null,
                category_id: 1,
            }

            tab.value = 'list'
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

const updateImage = async (id, event) => {
    const file = event.target.files[0];
    if (!file) return toast.warning("Bir dosya seçiniz");

    const formData = new FormData();
    formData.append('image', file);

    try {
        const res = await axios.post(`/songs/update-image/${id}`, formData, {
            headers: {'Content-Type': 'multipart/form-data'},
        });

        toast.success("Görsel güncellendi!");
        await fetchProducts();
    } catch (error) {
        console.error(error);
        toast.warning("Görsel güncellenemedi.");
    }
};

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
    inputRefs.value[0]?.focus()

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

onMounted(() => {
    const marquee = document.getElementById('marquee-container');


    function updateMarquee() {
        if (!marquee) return;
        marquee.classList.remove('opacity-100');
        marquee.classList.add('opacity-0');

        setTimeout(() => {
            marquee.textContent = `"${messages[index]}"`;
            marquee.classList.remove('opacity-0');
            marquee.classList.add('opacity-100');
            index = (index + 1) % messages.length;
        }, 500);
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
        toast.success("Şarkı başarıyla silindi!");
        await fetchProducts();
    } catch (error) {
        console.error(error);
        toast.warning("Şarkı silinemedi.");
    }
}
const daysPassed = computed(() => {
    const startDate = new Date('2025-05-21');
    const today = new Date();
    const diff = today - startDate;
    console.log(Math.floor(diff / (1000 * 60 * 60 * 24)))
    return Math.floor(diff / (1000 * 60 * 60 * 24));
});

const daySong = computed(() => products.value.find(p => p.is_day))
</script>

<template>
        <Head title="Hoşgeldiniz"/>
        <header
            style="background: #ec49c5"
            class=" flex mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 sticky top-0 z-50  backdrop-blur shadow flex-col sm:flex-row sm:items-center sm:justify-between py-2"
        >
            <div class="flex items-center gap-2">
                <img class="h-10" src="/public/images/ZM2014Logo.png" alt="">
                <h1 class="py-4 text-xl text-white font-bold text-center mx-auto">Z&M Malikanesi</h1>
            </div>
        </header>

        <div class="py-5 dark:text-white/50 bg-gray-900">
            <div class="relative flex min-h-screen flex-col w-full  mx-auto max-w-7xl">
                <div class="relative w-full">
                    <main class="px-4 sm:px-8">
                        <div class="p-6 mt-10 bg-white dark:bg-gray-900 rounded-2xl shadow-md max-w-2xl mx-auto">
                            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">Kategori</label>
                                    <select
                                        v-model="form.category_id"
                                        class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-pink-500 focus:border-pink-500 transition"
                                    >
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">Görsel Yükle (Opsiyonel)</label>
                                    <input
                                        type="file"
                                        @change="handleImageUpload"
                                        class="w-full text-sm text-gray-600 dark:text-white
                 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0
                 file:text-sm file:bg-pink-500 file:text-white
                 hover:file:bg-pink-600 cursor-pointer
                 transition"
                                    />
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">Müzik Dosyası</label>
                                    <input
                                        type="file"
                                        @change="handleFileUpload"
                                        class="w-full text-sm text-gray-600 dark:text-white
                 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0
                 file:text-sm file:bg-pink-500 file:text-white
                 hover:file:bg-pink-600 cursor-pointer
                 transition"
                                    />
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-white">Açıklama</label>
                                    <textarea
                                        rows="4"
                                        placeholder="Özel bir anlamı varsa yazabiliriz..."
                                        v-model="form.description"
                                        class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none
                 focus:ring-pink-500 focus:border-pink-500 transition"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="isLoading"
                                    class="w-full py-3 bg-pink-500 text-white rounded hover:bg-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 font-semibold"
                                >
                                    {{ isLoading ? 'Kaydediliyor...' : 'Kaydet' }}
                                </button>
                            </form>
                        </div>
                    </main>

                </div>
            </div>
        </div>

    <div
        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 dark:bg-gray-900 dark:border-gray-700 flex justify-around py-2 z-50"
    >
        <Link
            :href="route('day')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/day' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            🎵<span>Günün Şarkısı</span>
        </Link>
        <Link
            :href="route('add')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/add' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            ➕<span>Yeni Ekle</span>
        </Link>
        <Link
            :href="route('favorites')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/favorites' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            ⭐<span>Favoriler</span>
        </Link>
        <Link
            :href="route('categories.all')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/categories-all' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            📚<span>Kataloglar</span>
        </Link>
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

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>

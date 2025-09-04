<template>
    <div class="gallery-wrapper">
        <!-- Başlık & Logo -->
        <div class="gallery-header">
            <img src="/public/images/zp.png" alt="Z&P Logo" class="logo" />
            <h1>Dünyasına Hoşgeldiniz!</h1>
        </div>

        <!-- Galeri -->
        <div class="gallery-container">
            <div
                v-for="(item, index) in sortedImages"
                :key="item.id"
                class="gallery-item"
                :style="{ backgroundImage: `url('/storage/${item.image}')` }"
                @mouseover="hoverIndex = index"
                @mouseleave="hoverIndex = null"
                @click="openLightbox(index)"
                :class="{ hovered: hoverIndex === index }"
            >
                <div class="info-overlay">
                    <div class="title">{{ item.description }}</div>
                    <div class="date">{{ new Date(item.created_at).toLocaleDateString('tr-TR', { year:'numeric', month:'long', day:'numeric' }) }}</div>
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <div v-if="lightboxOpen" class="lightbox" @click.self="closeLightbox">
            <span class="close-btn" @click="closeLightbox">&times;</span>

            <!-- Random şekiller -->
            <div class="shapes">
        <span
            v-for="n in 30"
            :key="n"
            class="shape"
            :style="{
            '--randX': Math.random() * 100,
            '--randY': Math.random() * 100,
            'animation-delay': `${Math.random() * 10}s`,
            'animation-duration': `${10 + Math.random() * 15}s`
          }"
        />
            </div>

            <img
                :src="`/storage/${sortedImages[currentIndex].image}`"
                class="lightbox-img animate-in"
            />
            <div class="lightbox-controls">
                <button @click.stop="prevImage">&#10094;</button>
                <button @click.stop="nextImage">&#10095;</button>
            </div>
        </div>
    </div>

    <div
        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 dark:bg-gray-900 dark:border-gray-700 flex justify-around py-2 z-50"
    >
        <Link
            :href="route('welcome')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            <span>Galeri</span>
        </Link>
        <Link
            :href="route('add')"
            class="flex flex-col items-center text-sm"
            :class="$page.url === '/add' ? 'text-pink-600 font-bold' : 'text-gray-600'"
        >
            <span>Yeni Ekle</span>
        </Link>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import {Link} from "@inertiajs/vue3";

const hoverIndex = ref(null);
const lightboxOpen = ref(false);
const currentIndex = ref(0);
const images = ref([]);

// Backend'den ürünleri çek
const fetchProducts = async () => {
    const response = await axios.get('/products');
    images.value = response.data.data;
    console.log({ images: images.value });
};

onMounted(() => {
    fetchProducts();
});

// Lightbox fonksiyonları
function openLightbox(index) {
    currentIndex.value = index;
    lightboxOpen.value = true;
}

function closeLightbox() {
    lightboxOpen.value = false;
}

function prevImage() {
    currentIndex.value = (currentIndex.value - 1 + images.value.length) % images.value.length;
}

function nextImage() {
    currentIndex.value = (currentIndex.value + 1) % images.value.length;
}

// Tarihe göre sıralama: en yeni en üstte
const sortedImages = computed(() => {
    return [...images.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});
</script>

<style scoped>
/* GENEL WRAPPER */
.gallery-wrapper {
    min-height: 100vh;
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at 20% 30%, rgba(255,110,196,0.2), transparent 70%),
    radial-gradient(circle at 80% 70%, rgba(120,115,245,0.2), transparent 70%),
    linear-gradient(135deg, #1a001f, #2a002a, #1a001f);
    background-size: cover;
}

/* BAŞLIK & LOGO */
.gallery-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}
.logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
}
.gallery-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #ff6ec4;
    text-shadow: 0 0 15px rgba(255,110,196,0.8);
}

/* GALERİ GRID */
.gallery-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    padding: 10px;
}

/* GALERİ KARTLARI */
.gallery-item {
    position: relative;
    width: 100%;
    padding-top: 100%;
    border-radius: 20px;
    background-size: cover;
    background-position: center;
    cursor: pointer;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    transition: transform 0.4s ease, box-shadow 0.4s ease, border-radius 0.4s ease;
}
.gallery-item:hover {
    transform: scale(1.05);
    border-radius: 25px;
    box-shadow: 0 20px 40px rgba(255, 105, 180, 0.5),
    0 0 25px rgba(120, 115, 245, 0.5);
}
.gallery-item.hovered {
    transform: scale(1.1) rotate(-5deg);
    border-radius: 50%;
    box-shadow: 0 20px 40px rgba(255, 105, 180, 0.6),
    0 0 30px rgba(120, 115, 245, 0.5);
    border-image: conic-gradient(from 0deg, #ff6ec4, #ffafbd, #7873f5, #ff6ec4) 1;
    animation: rotate-border 3s linear infinite;
}
@keyframes rotate-border {
    from { border-image: conic-gradient(from 0deg, #ff6ec4, #ffafbd, #7873f5, #ff6ec4) 1; }
    to   { border-image: conic-gradient(from 360deg, #ff6ec4, #ffafbd, #7873f5, #ff6ec4) 1; }
}
.gallery-item::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(to top, rgba(0,0,0,0.6), transparent 60%);
    transition: opacity 0.3s ease;
    opacity: 0.5;
}
.gallery-item:hover::after { opacity: 0.8; }

/* Overlay: açıklama ve tarih */
.info-overlay {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.6);
    color: #fff;
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 14px;
    text-align: left;
}
.info-overlay .title {
    font-weight: 600;
    margin-bottom: 4px;
}
.info-overlay .date {
    font-weight: 400;
    font-size: 12px;
    color: #ffafbd;
}

/* LIGHTBOX */
.lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255,110,196,0.25), rgba(10,0,30,0.95));
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
    flex-direction: column;
    overflow: hidden;
}

/* RANDOM SHAPES */
.shapes {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    overflow: hidden;
    z-index: 0;
    pointer-events: none;
}
.shape {
    position: absolute;
    display: block;
    width: 8px;
    height: 8px;
    background: #ff6ec4;
    border-radius: 50%;
    opacity: 0.6;
    animation: floatShape linear infinite;
}
.shape:nth-child(3n) { background: #ffafbd; width: 12px; height: 12px; border-radius: 50%; }
.shape:nth-child(4n) { background: #7873f5; width: 10px; height: 10px; border-radius: 0; }
.shape:nth-child(5n) {
    background: #ffffff; width: 14px; height: 14px;
    clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
}
.shape:nth-child(n) {
    top: calc(var(--randY, 50) * 1%);
    left: calc(var(--randX, 50) * 1%);
}
@keyframes floatShape {
    0% { transform: translateY(0) scale(0.8); opacity: 0.7; }
    50% { opacity: 1; }
    100% { transform: translateY(-200vh) scale(1.2); opacity: 0; }
}

.lightbox-img {
    max-width: 90%;
    max-height: 80%;
    border-radius: 20px;
    box-shadow: 0 0 30px rgba(255,110,196,0.7);
    z-index: 1;
}
.animate-in {
    animation: zoomIn 0.6s ease forwards;
}
@keyframes zoomIn {
    from { transform: scale(0.7) rotate(-3deg); opacity: 0; }
    to   { transform: scale(1) rotate(0deg); opacity: 1; }
}
.lightbox-controls {
    display: flex;
    justify-content: space-between;
    width: 120px;
    margin-top: 20px;
    z-index: 1;
}
.lightbox-controls button {
    font-size: 30px;
    background: none;
    border: none;
    color: #ffafbd;
    cursor: pointer;
    transition: transform 0.2s ease, color 0.3s ease;
}
.lightbox-controls button:hover {
    transform: scale(1.2);
    color: #fff;
}
.close-btn {
    position: absolute;
    top: 30px;
    right: 50px;
    font-size: 50px;
    color: #ff6ec4;
    cursor: pointer;
    text-shadow: 0 0 15px rgba(255,110,196,0.9);
    z-index: 2;
}
</style>

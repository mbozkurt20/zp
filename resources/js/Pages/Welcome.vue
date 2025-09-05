<template>
    <div class="gallery-wrapper">

        <!-- Başlık -->
        <header class="gallery-header">
            <img src="/public/images/zp.png" alt="Z&P Logo" class="logo" />
            <h1>Dünyasına Hoşgeldiniz!</h1>
        </header>

        <!-- Galeri -->
        <ul class="gallery-container">
            <li v-for="item in sortedImages" :key="item.id" class="gallery-item">
                <div
                    class="image"
                    :style="{ backgroundImage: `url('/storage/${item.image}')` }"
                >
                    <!-- Info Overlay -->
                    <div class="info-overlay">
                        <div class="title">{{ item.description }}</div>
                        <div class="date">{{ formatDate(item.created_at) }}</div>
                    </div>

                    <!-- Like / Beğen -->
                    <div class="like-wrapper">
                        <button class="like-btn" @click.stop="likeItem(item, $event)">
                            ❤️ {{ item.liked }}
                        </button>

                        <span
                            v-for="(heart, index) in item.hearts"
                            :key="index"
                            class="flying-heart"
                            :style="{ left: heart.x + 'px', bottom: heart.y + 'px', animationDuration: heart.duration + 's' }"
                        >❤️</span>
                    </div>

                    <!-- Lightbox -->
                    <div class="full-click-area" @click="openLightbox(item)"></div>
                </div>
            </li>
        </ul>

        <!-- Lightbox -->
        <div v-if="lightboxOpen" class="lightbox" @click.self="closeLightbox">
            <span class="close-btn" @click="closeLightbox">&times;</span>

            <!-- Arka Plan Hologram Efekti -->
            <div class="holo-bg">
        <span
            v-for="n in 50"
            :key="n"
            class="holo-shape"
            :class="['shape-'+(n%4)]"
            :style="{
            top: Math.random()*100+'%',
            left: Math.random()*100+'%',
            animationDelay: Math.random()*5+'s',
            animationDuration: (5+Math.random()*10)+'s'
          }"
        ></span>
            </div>

            <!-- Açılan Resim Animasyonlu -->
            <div class="lightbox-inner">
                <img
                    :src="`/storage/${currentItem.image}`"
                    class="lightbox-img animate-in"
                />
            </div>
        </div>

        <!-- Alt Navigasyon -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 dark:bg-gray-900 dark:border-gray-700 flex justify-between items-center py-2 px-8 z-50">
            <Link :href="route('welcome')" class="flex flex-col items-center text-sm" :class="$page.url === '/' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                <span>Galeri</span>
            </Link>

            <button @click="togglePlay" class="bg-pink-600 hover:bg-pink-500 text-white rounded-full w-16 h-16 flex justify-center items-center shadow-lg">
                <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-6.518-3.759A1 1 0 007 8.22v7.56a1 1 0 001.234.97l6.518-1.873a1 1 0 000-1.82z" />
                </svg>

                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6" />
                </svg>
            </button>
            <Link :href="route('add')" class="flex flex-col items-center text-sm" :class="$page.url === '/add' ? 'text-pink-600 font-bold' : 'text-gray-600'">
                <span>Yeni Anı Ekle</span>
            </Link>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps(['imagess']);

const lightboxOpen = ref(false);
const currentItem = ref(null);

const images = ref(props.imagess.map(img => ({ ...img, hearts: [] })));
const isPlaying = ref(false);
const audio = ref(new Audio('/Zuhal.mp3'));

function togglePlay() {
    if (isPlaying.value) audio.value.pause();
    else audio.value.play();
    isPlaying.value = !isPlaying.value;
}

const sortedImages = computed(() =>
    [...images.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
);

function openLightbox(item) { currentItem.value = item; lightboxOpen.value = true; }
function closeLightbox() { lightboxOpen.value = false; }
function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', { year:'numeric', month:'long', day:'numeric' });
}

function likeItem(item, event) {
    fetch(`/songs/liked/${item.id}`, { method: 'get' })
        .then(() => {
            item.liked++;
            const heart = { x: event.offsetX, y: 0, duration: 1 + Math.random() * 1.5 };
            item.hearts.push(heart);
            setTimeout(() => item.hearts.shift(), heart.duration * 1000);
        })
        .catch(err => console.error(err));
}
</script>

<style scoped>
/* Wrapper */
.gallery-wrapper { min-height:100vh; padding:25px; font-family:'Segoe UI',sans-serif; background:linear-gradient(135deg,#1a001f,#2a002a,#1a001f); }

/* Başlık */
.gallery-header { display:flex; align-items:center; gap:15px; margin-bottom:25px; }
.logo { width:50px; height:50px; object-fit:contain; }
.gallery-header h1 { font-size:24px; font-weight:700; color:#ff6ec4; text-shadow:0 0 12px rgba(255,110,196,0.8); }

/* Grid */
.gallery-container { display:grid; grid-template-columns:repeat(2,1fr); gap:25px; list-style:none; margin:0; padding:0; }
.gallery-item { position:relative; width:100%; padding-top:100%; border-radius:15px; overflow:hidden; cursor:pointer; }
.image { position:absolute; inset:0; background-size:cover; background-position:center; border-radius:15px; border:2px solid transparent; box-shadow:0 5px 15px rgba(0,0,0,0.4); transition: transform 0.3s, border-color 0.3s; }
.image:hover { transform:scale(1.05); border-color:#ff6ec4; }

/* Info overlay */
.info-overlay { position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.65); color:#fff; padding:6px 10px; border-radius:12px; font-size:12px; }
.info-overlay .title { font-weight:600; margin-bottom:2px; }
.info-overlay .date { font-weight:400; font-size:10px; color:#ffafbd; }

/* Like */
.like-wrapper { position:absolute; top:8px; right:8px; z-index:2; }
.like-btn { background:rgba(255,105,180,0.9); color:#fff; border:none; padding:6px 12px; border-radius:14px; cursor:pointer; font-size:16px; transition: transform 0.2s ease; }
.like-btn:hover { transform:scale(1.4); }

.flying-heart { position:absolute; animation-name:flyUp; animation-timing-function:ease-out; animation-fill-mode:forwards; font-size:20px; }
@keyframes flyUp { 0% { transform: translateY(0) scale(1); opacity:1; } 50% { transform: translateY(-50px) scale(1.6); opacity:0.8; } 100% { transform: translateY(-120px) scale(0.8); opacity:0; } }

.full-click-area { position:absolute; inset:0; z-index:1; }

/* Lightbox */
.lightbox { position:fixed; inset:0; display:flex; justify-content:center; align-items:center; z-index:999; overflow:hidden; background:rgba(0,0,0,0.95); }
.lightbox-inner { display:flex; justify-content:center; align-items:center; animation:popIn 0.6s ease forwards; }
@keyframes popIn { 0% { transform: scale(0) rotate(0deg); opacity:0; } 50% { transform: scale(1.2) rotate(180deg); opacity:1; } 100% { transform: scale(1) rotate(360deg); opacity:1; } }

.lightbox-img { max-width:80%; max-height:80%; border-radius:25%; box-shadow:0 0 30px #ff6ec4; transition: transform 0.4s ease; }

/* Close Button */
.close-btn { position:absolute; top:20px; right:30px; font-size:45px; color:#ff6ec4; cursor:pointer; z-index:10; }

/* Hologram Arka Plan */
.holo-bg { position:absolute; inset:0; overflow:hidden; z-index:0; }
.holo-shape { position:absolute; display:block; width:12px; height:12px; border-radius:50%; background:linear-gradient(45deg,#ff6ec4,#7873f5,#ffafbd); opacity:0.5; animation:floatHolo linear infinite; }
.shape-0 { border-radius:50%; }
.shape-1 { clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); } /* star */
.shape-2 { clip-path: polygon(50% 0%, 0% 100%, 100% 100%); } /* triangle */
.shape-3 { border-radius:0%; } /* square */
@keyframes floatHolo { 0% { transform:translateY(0) scale(0.8); opacity:0.5; } 100% { transform:translateY(-200vh) scale(1.2); opacity:0; } }
</style>

<template>
    <div class="gallery-wrapper">

        <!-- Başlık -->
        <header class="gallery-header">
            <img src="/public/images/zp.png" alt="Logo" class="logo"/>
            <h1>Dünyasına Hoşgeldiniz!</h1>
        </header>

        <!-- Galeri -->
        <ul class="gallery-container">
            <li v-for="item in sortedImages" :key="item.id" class="gallery-item">
                <div class="media-wrapper" @dblclick="likeItem(item)">
                    <!-- ✅ type kontrolü -->
                    <template v-if="item.type === 'video'">
                        <video
                            class="media"
                            :src="getVideoUrl(item.image)"
                            controls
                            @click.stop="openLightbox(item)"
                        ></video>
                    </template>
                    <template v-else>
                        <img
                            class="media"
                            :src="getImageUrl(item.image)"
                            alt="Z&P "
                            @click="openLightbox(item)"
                        />
                    </template>

                    <transition-group name="heart" tag="div">
                        <span v-for="h in item.hearts" :key="h.id" class="floating-heart">❤️</span>
                    </transition-group>
                </div>

                <div class="post-footer">
                    <p class="desc-text font-bold">
                        {{ truncatedText(item) }}
                        <button
                            v-if="item.description && item.description.length > 100"
                            @click.stop="toggleExpand(item)"
                            class="more-btn"
                        >
                            {{ item.expanded ? 'Daha Az' : 'Daha Fazla' }}
                        </button>
                    </p>
                    <div class="meta font-bold">
                        <span class="likes" @click.stop="likeItem(item)">
                            {{ item.liked }} ❤️
                        </span>
                        <span class="date">{{ formatDate(item.created_at) }}</span>
                    </div>

                    <p v-if="!item.editing" class="desc-text font-bold">
                        {{ truncatedText(item) }}
                        <button
                            v-if="item.description && item.description.length > 100"
                            @click.stop="toggleExpand(item)"
                            class="more-btn"
                        >
                            {{ item.expanded ? 'Daha Az' : 'Daha Fazla' }}
                        </button>
                        <button @click.stop="enableEdit(item)" class="more-btn ml-2">✏️ Düzenle</button>
                    </p>

                    <!-- Edit Mode -->
                    <div v-else class="desc-edit">
                        <textarea v-model="item.description" class="desc-textarea"></textarea>
                        <div class="flex gap-2 mt-1">
                            <button @click.stop="saveDescription(item)" class="save-btn">Kaydet</button>
                            <button @click.stop="cancelEdit(item)" class="cancel-btn">İptal</button>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <!-- Lightbox -->
        <div v-if="lightboxOpen" class="lightbox" @click.self="closeLightbox">
            <span class="close-btn" @click="closeLightbox">&times;</span>
            <div class="holo-bg">
                <span v-for="n in 50" :key="n" class="holo-shape"
                      :style="{
                        top: Math.random()*100+'%',
                        left: Math.random()*100+'%',
                        animationDelay: Math.random()*5+'s',
                        animationDuration: (5+Math.random()*10)+'s'
                      }"></span>
            </div>
            <div class="lightbox-inner">
                <!-- ✅ Lightbox type kontrolü -->
                <template v-if="currentItem?.type === 'video'">
                    <video
                        :src="getVideoUrl(currentItem.image)"
                        class="lightbox-media animate-in"
                        controls
                        autoplay
                    ></video>
                </template>
                <template v-else>
                    <img
                        :src="getImageUrl(currentItem.image)"
                        class="lightbox-media animate-in"
                    />
                </template>
            </div>
        </div>

        <!-- Alt Navigasyon -->
        <div class="fixed bottom-0 left-0 right-0 flex justify-around py-3 z-50 nav-bg">
            <Link :href="route('welcome')" class="flex flex-col items-center text-sm"
                  :class="$page.url === '/' ? 'text-white font-bold' : 'text-gray-200'">
                <span>Galeri</span>
            </Link>

            <button @click="togglePlay" class="nav-center-btn">
                <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M14.752 11.168l-6.518-3.759A1 1 0 007 8.22v7.56a1 1 0 001.234.97l6.518-1.873a1 1 0 000-1.82z"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                </svg>
            </button>

            <Link :href="route('add')" class="flex flex-col items-center text-sm"
                  :class="$page.url === '/add' ? 'text-white font-bold' : 'text-gray-200'">
                <span>Yeni Anı Ekle</span>
            </Link>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import {toast} from "vue3-toastify";

const props = defineProps(["imagess"]);

const lightboxOpen = ref(false);
const currentItem = ref(null);

const isPlaying = ref(false);
const audio = ref(new Audio('/Zuhal.mp3'));
function togglePlay() {
    if (isPlaying.value) audio.value.pause();
    else audio.value.play();
    isPlaying.value = !isPlaying.value;
}

const images = ref(
    props.imagess.map(img => ({
        ...img,
        liked: parseInt(img.liked) || 0,
        expanded: false,
        hearts: []
    }))
);

const sortedImages = computed(() =>
    [...images.value].sort((a,b) => new Date(b.created_at) - new Date(a.created_at))
);
function enableEdit(item) {
    item.oldDescription = item.description;
    item.editing = true;
}
function cancelEdit(item) {
    item.description = item.oldDescription;
    item.editing = false;
}
function saveDescription(item) {
    axios.post(`/songs/update-description/${item.id}`, {
        description: item.description
    })
        .then(res => {
            console.log("Description updated:", res.data);
            item.editing = false;

            toast.success('Anı Güncellendi')
        })
        .catch(err => {
            console.error("Güncellenemedi:", err);
            item.description = item.oldDescription;
            item.editing = false;
        });
}

function truncatedText(item) {
    const text = item.description || '';
    if (item.expanded || text.length <= 100) return text;
    return text.substring(0, 100) + '...';
}
function toggleExpand(item) { item.expanded = !item.expanded; }

function openLightbox(item) { currentItem.value = item; lightboxOpen.value = true; }
function closeLightbox() { lightboxOpen.value = false; }

function likeItem(item) {
    const id = Date.now();
    item.hearts.push({ id });
    setTimeout(() => { item.hearts = item.hearts.filter(h => h.id !== id); }, 1200);

    item.liked = parseInt(item.liked) + 1;
    axios.get(`/songs/liked/${item.id}`)
        .then(res => console.log("Backend OK:", res.data))
        .catch(err => { console.error("Beğeni gönderilemedi:", err); item.liked -= 1; });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString("tr-TR", { year:"numeric", month:"long", day:"numeric" });
}

function getImageUrl(path) {
    if(!path) return '/public/images/placeholder.png';
    return `/storage/${path}`;
}
function getVideoUrl(path) {
    if(!path) return '/public/images/placeholder.png';
    return `/storage/${path}`;
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap');

.gallery-wrapper {
    min-height:100vh;
    padding:25px;
    font-family: 'Quicksand', sans-serif;
    background: linear-gradient(135deg, #2b0d3e, #6f2fa0, #b742c2); /* örnek tonlar */
}

.gallery-header { display:flex; align-items:center; gap:15px; margin-bottom:25px; }
.logo{width:50px;height:50px;object-fit:contain;}
.gallery-header h1{
    font-size:26px;
    font-weight:700;
    color:#fff;
    text-shadow:0 0 15px rgba(255,255,255,0.4);
}
.desc-edit { display:flex; flex-direction:column; }
.desc-textarea {
    width:100%;
    min-height:60px;
    border-radius:8px;
    border:1px solid #ff87c9;
    padding:6px 8px;
    font-size:13px;
    background:rgba(255,255,255,0.1);
    color:#fff;
}
.save-btn, .cancel-btn {
    padding:4px 10px;
    border-radius:6px;
    font-size:12px;
    cursor:pointer;
}
.save-btn { background:#ff49d1; color:#fff; }
.cancel-btn { background:#aaa; color:#fff; }

.gallery-container {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:18px;
    list-style:none;
    margin:0;
    padding:0;
}
.gallery-item {
    display:flex;
    flex-direction:column;
    border-radius:16px;
    overflow:hidden;
    cursor:pointer;
    background: rgba(255,255,255,0.05); /* daha koyu */
    border: 2px solid rgba(255,73,209,0.4); /* neon kenar */
    backdrop-filter:blur(6px);
    transition:0.3s;
}
.gallery-item:hover { transform: scale(1.04); filter: brightness(1.15); }

.media-wrapper { position:relative; overflow:hidden; border-radius:16px 16px 0 0; }
.media {
    width:100%;
    display:block;
    object-fit:cover;
    transition: all 0.5s ease;
    border-radius:16px 16px 0 0;
}
.media-wrapper:hover .media { transform:scale(1.05); filter:brightness(1.1); }

.floating-heart {
    position:absolute;
    left:50%;
    top:50%;
    transform:translate(-50%,-50%) scale(1);
    font-size:28px;
    text-shadow:0 0 12px #ff9ce3,0 0 18px #d761f7;
    animation:heart-float 1.2s ease-out forwards;
    pointer-events:none;
}
@keyframes heart-float {
    0%{transform:translate(-50%,-50%) scale(1);opacity:1;}
    50%{transform:translate(-50%,-160%) scale(1.4);opacity:1;}
    100%{transform:translate(-50%,-280%) scale(1.8);opacity:0;}
}

.post-footer {
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    padding:8px 10px;
    border-radius:0 0 16px 16px;
    font-size:13px;
    color:#fff;
}
.desc-text{margin-bottom:4px; line-height:1.4;}
.more-btn{background:none; border:none; color:#ff49d1; cursor:pointer; font-size:12px; font-weight:bold;}
.meta{display:flex; justify-content:space-between; font-size:12px; color:#eee;}
.likes{font-weight:bold; cursor:pointer; color:#ff87c9; transition: all 0.3s ease;}
.likes:hover{transform:scale(1.2);}

.lightbox {
    position:fixed; inset:0;
    display:flex; justify-content:center; align-items:center;
    z-index:999; overflow:auto;
    background:rgba(0,0,0,0.92);
    padding:20px;
}
.lightbox-inner { display:flex; justify-content:center; align-items:center; }
.lightbox-media {
    max-width:95vw;
    max-height:95vh;
    border-radius:24px;
    border:3px solid #f78acb;
    box-shadow:0 0 60px rgba(247,138,203,0.8);
    transition: transform 0.4s ease;
    animation:popIn 0.6s ease forwards;
}
@keyframes popIn {
    0%{transform:scale(0) rotate(0deg);opacity:0;}
    50%{transform:scale(1.2) rotate(180deg);opacity:1;}
    100%{transform:scale(1) rotate(360deg);opacity:1;}
}
.close-btn {
    position:absolute;
    top:20px;
    right:30px;
    font-size:50px;
    color:#f78acb;
    cursor:pointer;
    z-index:10;
}

.holo-bg { position:absolute; inset:0; overflow:hidden; z-index:0; }
.holo-shape {
    position:absolute;
    display:block;
    width:12px;
    height:12px;
    border-radius:50%;
    background: radial-gradient(circle, #ff9ce3, #d761f7, #a261d9);
    opacity:0.6;
    animation:floatHolo linear infinite;
}
@keyframes floatHolo {
    0%{transform:translateY(0) scale(0.8);opacity:0.6;}
    100%{transform:translateY(-200vh) scale(1.2);opacity:0;}
}

.nav-bg { background: linear-gradient(90deg,#f78acb,#d761f7); }
.nav-center-btn {
    background: #ff49d1;
    color: #fff;
    border:none;
    width:56px;
    height:56px;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    box-shadow: 0 8px 20px rgba(255, 73, 209, 0.5);
    transition:0.3s;
}
.nav-center-btn:hover { transform:scale(1.1); }
.nav-center-btn svg { width:24px; height:24px; }
</style>

<template>
    <div>
        <div class="gallery-wrapper">
            <!-- Başlık & Logo -->
            <div class="gallery-header">
                <img src="/public/images/zp.png" alt="Z&P Logo" class="logo"/>
                <h1>Dünyasına Hoşgeldiniz!</h1>
            </div>

            <!-- Galeri -->
            <div class="gallery-container">
                <div
                    v-for="(item, index) in sortedImages"
                    :key="item.id"
                    class="gallery-item"
                    :style="{ backgroundImage: item.type === 'image' ? `url('/storage/${item.file}')` : 'none' }"
                    @mouseover="hoverIndex = index"
                    @mouseleave="hoverIndex = null"
                    @click="openLightbox(index)"
                    :class="{ hovered: hoverIndex === index }"
                >
                    <!-- Eğer video ise küçük video göster -->
                    <video
                        v-if="item.type === 'video'"
                        class="video-thumb"
                        :src="`/storage/${item.file}`"
                        muted
                        loop
                        playsinline
                    ></video>

                    <div class="info-overlay">
                        <div class="title">{{ item.description }}</div>
                        <div class="date">
                            {{
                                new Date(item.created_at).toLocaleDateString('tr-TR', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })
                            }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Alanı -->
            <div class="upload-wrapper">
                <div class="upload-header">
                    <h1>Yeni Anı Ekle</h1>
                </div>

                <div class="upload-box" @click="$refs.fileInput.click()">
                    <input
                        type="file"
                        accept="image/*,video/*"
                        class="hidden"
                        ref="fileInput"
                        @change="handleFileUpload"
                    />

                    <div v-if="!previewUrl" class="upload-placeholder">
                        <p>📷 Görsel veya 🎥 Video seç / sürükle</p>
                        <span class="hint">PNG, JPG, MP4, WebM (max 20MB)</span>
                    </div>

                    <div v-else class="preview">
                        <!-- Video önizleme -->
                        <video
                            v-if="isVideo"
                            :src="previewUrl"
                            class="preview-img"
                            controls
                        ></video>

                        <!-- Resim önizleme -->
                        <img
                            v-else
                            :src="previewUrl"
                            class="preview-img"
                        />

                        <button type="button" @click.stop="removeFile" class="remove-btn">✕</button>
                    </div>
                </div>

                <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Bu anının hikayesini yaz..."
                    class="description"
                ></textarea>

                <button @click="submitForm" :disabled="isLoading" class="submit-btn">
                    {{ isLoading ? "Kaydediliyor..." : "Galeriye Ekle" }}
                </button>
            </div>
        </div>

        <!-- Alt Menü -->
        <div
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 dark:bg-gray-900 dark:border-gray-700 flex justify-around py-8 z-50"
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
                <span>Yeni Anı Ekle</span>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { Link } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";

/* ----- STATE ----- */
const form = ref({ description: "", file: null });
const previewUrl = ref(null);
const isVideo = ref(false);
const isLoading = ref(false);

const hoverIndex = ref(null);
// sortedImages ve openLightbox veritabanı/props'tan geliyor varsayımı
const sortedImages = ref([]); // backend'den doldurulmalı

/* ----- FILE UPLOAD ----- */
const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const type = file.type;

    if (!type.startsWith("image/") && !type.startsWith("video/")) {
        toast("Sadece resim veya video yükleyebilirsiniz.", { type: "error" });
        return;
    }

    if (file.size > 20 * 1024 * 1024) {
        toast("Dosya boyutu 20MB'den büyük olamaz.", { type: "error" });
        return;
    }

    form.value.file = file;
    isVideo.value = type.startsWith("video/");
    previewUrl.value = URL.createObjectURL(file);
};

const removeFile = () => {
    form.value.file = null;
    previewUrl.value = null;
    isVideo.value = false;
};

/* ----- FORM SUBMIT ----- */
const submitForm = async () => {
    if (!form.value.file) {
        toast("Lütfen bir görsel veya video seçin.", { type: "warning" });
        return;
    }

    const fd = new FormData();
    fd.append("description", form.value.description);
    fd.append("file", form.value.file);

    isLoading.value = true;
    try {
        await axios.post("/create-photo", fd, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        toast("Dosya başarıyla yüklendi 🎉", { theme: "auto", type: "success" });

        // Formu temizle
        form.value = { description: "", file: null };
        previewUrl.value = null;
        isVideo.value = false;
    } catch (err) {
        console.error(err);
        toast("Yükleme sırasında bir hata oluştu.", { type: "error" });
    } finally {
        isLoading.value = false;
    }
};

/* ----- LIGHTBOX (Varsayımsal) ----- */
const openLightbox = (index) => {
    // Burada lightbox açma işlemini gerçekleştirebilirsiniz
};
</script>

<style scoped>
/* GENEL WRAPPER */
.gallery-wrapper {
    min-height: 100vh;
    padding: 20px;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at 20% 30%, rgba(255, 110, 196, 0.2), transparent 70%),
    radial-gradient(circle at 80% 70%, rgba(120, 115, 245, 0.2), transparent 70%),
    linear-gradient(135deg, #1a001f, #2a002a, #1a001f);
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
    text-shadow: 0 0 15px rgba(255, 110, 196, 0.8);
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

.video-thumb {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Overlay */
.info-overlay {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 14px;
}

.info-overlay .title {
    font-weight: 600;
    margin-bottom: 4px;
}

.info-overlay .date {
    font-size: 12px;
    color: #ffafbd;
}

/* UPLOAD FORM */
.upload-wrapper {
    max-width: 600px;
    margin: 50px auto;
    padding: 25px;
    border-radius: 20px;
    background: radial-gradient(circle at 20% 30%, rgba(255, 110, 196, 0.1), transparent 70%),
    radial-gradient(circle at 80% 70%, rgba(120, 115, 245, 0.1), transparent 70%),
    linear-gradient(135deg, #1a001f, #2a002a, #1a001f);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.upload-header h1 {
    font-size: 22px;
    font-weight: 700;
    color: #ff6ec4;
    text-shadow: 0 0 12px rgba(255, 110, 196, 0.8);
}

.upload-box {
    position: relative;
    border: 2px dashed #ff6ec4;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.upload-box:hover {
    box-shadow: 0 0 20px rgba(255, 110, 196, 0.5), 0 0 40px rgba(120, 115, 245, 0.4);
}

.upload-placeholder p {
    color: #fff;
    font-weight: 500;
}

.upload-placeholder .hint {
    font-size: 13px;
    color: #bbb;
}

.preview {
    position: relative;
}

.preview-img {
    max-height: 200px;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(255, 110, 196, 0.7);
    width: 100%;
    object-fit: contain;
}

.remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff6ec4;
    color: #fff;
    border: none;
    border-radius: 50%;
    padding: 5px 9px;
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 0 10px rgba(255, 110, 196, 0.8);
}

.description {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid #ff6ec4;
    background: rgba(0, 0, 0, 0.4);
    color: #fff;
    resize: none;
}

.submit-btn {
    margin-top: 20px;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(45deg, #ff6ec4, #7873f5);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.3s ease;
}

.submit-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255, 110, 196, 0.7),
    0 0 25px rgba(120, 115, 245, 0.5);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

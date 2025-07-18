<x-filament::page>
    <style>
        .fi-main {
            padding: 0 !important;
            max-width: 100% !important;
        }

        @media (min-width: 768px) {
            .category-scroll {
                padding-bottom: 1rem;
                height: 4.5rem;
            }

            .category-scroll > div {
                align-items: center;
            }

            .product-grid-container {
                max-height: 600px;
                overflow-y: auto;
            }
        }
    </style>
    <div x-data="saleApp()" x-init="init()" class="flex flex-col h-screen">

        <!-- Kategoriler üstte yatay scroll -->
        <div class="bg-white dark:bg-gray-800 border-b  overflow-x-auto category-scroll">
            <div class="flex space-x-4 whitespace-nowrap">
                <template x-for="category in categories" :key="category.id">
                    <button
                        @click="selectedCategory = category.id"
                        :class="selectedCategory === category.id
                            ? 'bg-primary-600 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'"
                        class="rounded px-4 py-4 hover:bg-primary-100 border border-gray-100">
                        <span x-text="category.name"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Alt kısım: sol ve sağ panel -->
        <div class="flex flex-1 overflow-hidden">

            <!-- Sol: Ürünler ve diğer -->
            <div class="w-full lg:w-2/3 flex flex-col p-6 space-y-6 overflow-y-auto bg-gray-50 dark:bg-gray-900">

                <!-- Ürün Arama -->
                <input type="text" placeholder="Ürün ara..." x-model="sidebarSearch"
                       class="w-full px-4 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white" />

                <!-- Ürün Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 product-grid-container">
                    <template x-for="product in selectedProducts" :key="product.id">
                        <div @click="$dispatch('scan', { barcode: product.barcode })"
                             class="border rounded shadow-sm p-3 bg-white dark:bg-gray-800 hover:bg-gray-100 cursor-pointer">
                            <img :src="'/storage/' + product.image" class="w-full h-32 object-cover rounded mb-2" />
                            <div class="text-center font-semibold text-sm" x-text="product.name"></div>
                        </div>
                    </template>
                </div>

                <!-- Barkod Girişi -->
                <div class="flex flex-wrap space-x-4 items-end gap-4 mt-6">
                    <div class="flex-1">
                        <x-filament::input
                            placeholder="Barkod Giriniz"
                            label="Barkod"
                            class="bg-white text-black placeholder:text-gray-500 border border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            x-model="code"
                            x-on:keydown.enter.prevent="scanBarcode()"
                            x-on:paste="setTimeout(() => { if(code.trim()) scanBarcode(); }, 0);"
                        />
                    </div>
                </div>

                <!-- Ödeme Türü -->
                <x-filament::section>
                    <x-slot name="heading">Ödeme Türü</x-slot>
                    <div class="flex space-x-2 gap-4">
                        <template x-for="option in ['Nakit', 'Kredi Kart', 'EFT/Havale']" :key="option">
                            <button type="button"
                                    @click="payment = option"
                                    :class="payment === option
                                    ? 'bg-primary-600 text-white border-primary-600'
                                    : 'bg-white text-gray-700 border-gray-300'"
                                    class="border rounded-lg px-4 py-2 font-semibold hover:bg-primary-50">
                                <span x-text="option"></span>
                            </button>
                        </template>
                    </div>
                </x-filament::section>

                <div class="flex">
                    <x-filament::button class="py-4 w-full flex ml-auto justify-end" color="success" @click="submitCart">
                        Satışı Tamamla
                    </x-filament::button>
                </div>
            </div>

            <!-- Sağ: Sepet ve Varyantlar -->
            <div class="w-full lg:w-1/3 border-l bg-white dark:bg-gray-800 p-6 overflow-y-auto">

                <!-- Sepet -->
                <x-filament::section>
                    <x-slot name="heading">Sepet</x-slot>

                    <template x-if="cart.length === 0">
                        <div class="text-center text-gray-500 py-8 text-lg">Sepette Ürün Bulunmuyor...</div>
                    </template>

                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex items-center border-b py-4 space-x-4 gap-4">
                            <img :src="'/storage/' + item.image" class="w-16 h-16 object-cover rounded-md" />
                            <div class="flex-1">
                                <div class="font-bold text-lg" x-text="item.name"></div>
                                <div class="flex gap-2 text-sm text-gray-400">
                                    <span x-text="item.variantQuantity"></span>
                                    <span x-text="item.variantType"></span>
                                </div>
                                <div class="text-green-400 font-bold" x-text="`₺${item.price}`"></div>
                            </div>
                            <input disabled style="background: #0000cc" type="number" min="1" x-model.number="item.quantity"
                                   class="w-16 border rounded px-2 py-1 text-center text-black bg-white"
                                   @change="updateQuantity(index, item.quantity)" />
                            <button class="text-red-500 ml-2" @click="removeItem(index)">Sil</button>
                        </div>
                    </template>

                    <div class="text-right mt-5 text-lg font-bold py-4">
                        Toplam: ₺<span x-text="totalPrice().toFixed(2)"></span>
                    </div>
                </x-filament::section>

                <!-- Varyant Paneli (Sağda Sepet Altında) -->
                <template x-if="variants.length > 0">
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold" x-text="product.name"></h2>
                            <button @click="variants = []" class="text-red-500 font-bold text-lg">×</button>
                        </div>

                        <div class="font-bold py-1">Varyant Seçiniz</div>
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="variant in variants" :key="variant.id">
                                <button
                                    @click="selectVariant(variant)"
                                    class="cursor-pointer border rounded p-3 hover:bg-primary-50 flex justify-between">
                                    <div class="flex gap-2">
                                        <div class="text-sm font-bold text-gray-100" x-text="variant.quantity"></div>
                                        <div class="font-semibold" x-text="variant.type"></div>

                                    </div>
                                    <div class="text-green-600 font-bold" x-text="`₺${variant.price}`"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- Bildirim -->
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-on:notify.window="
                message = $event.detail.message;
                type = $event.detail.type;
                show = true;
                setTimeout(() => show = false, 400);
             "
             x-show="show"
             x-transition
             class="fixed inset-0 flex items-center justify-center p-6 rounded-lg shadow-lg text-white z-50 bg-black bg-opacity-60"
             style="display: none;">
            <span class="text-2xl font-bold" x-text="message"></span>
        </div>

        <audio id="addSound" src="/Beep_Once.mp3" preload="auto"></audio>

        <!-- SCRIPT (aynı senin verdiğin gibi) -->
        <script>
            function saleApp() {
                return {
                    code: '',
                    quantity: 1,
                    cart: [],
                    payment: 'Nakit',
                    variants: [],
                    product: {},
                    sidebarSearch: '',
                    selectedCategory: null,
                    categories: @json($this->categories),

                    init() {
                        if (this.categories.length) {
                            this.selectedCategory = this.categories[0].id;
                        }

                        window.addEventListener('scan', e => {
                            this.scanBarcode(e.detail.barcode);
                        });
                    },

                    get selectedProducts() {
                        const category = this.categories.find(c => c.id === this.selectedCategory);
                        if (!category) return [];
                        const query = this.sidebarSearch?.toLowerCase() || '';
                        return category.products.filter(p =>
                            p.name.toLowerCase().includes(query)
                        );
                    },

                    scanBarcode(passedBarcode = null) {
                        let scanned = passedBarcode || (this.code || '').trim();
                        if (!scanned) return;
                        this.code = '';

                        fetch(`/scan-product?barcode=${encodeURIComponent(scanned)}`)
                            .then(res => res.json())
                            .then(data => {
                                this.product = data.product;
                                this.variants = data.product.variants;

                                if (this.variants.length === 0) {
                                    this.addToCart({
                                        id: this.product.id,
                                        name: this.product.name,
                                        image: this.product.image,
                                        price: 0,
                                        quantity: this.quantity,
                                        variantId: 0,
                                        variantType: '',
                                        variantQuantity: '',
                                    });
                                } else {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { type: 'success', message: 'Varyant seçiniz' }
                                    }));
                                }
                            })
                            .catch(() => {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { type: 'error', message: 'Sunucu hatası!' }
                                }));
                            });
                    },

                    selectVariant(variant) {
                        this.addToCart({
                            id: this.product.id,
                            name: this.product.name,
                            image: this.product.image,
                            price: variant.price,
                            quantity: this.quantity,
                            variantId: variant.id,
                            variantType: variant.type,
                            variantQuantity: variant.quantity
                        });
                        this.variants = [];
                        this.product = {};
                    },

                    addToCart(item) {
                        const existingIndex = this.cart.findIndex(c =>
                            c.id === item.id && c.variantId === item.variantId
                        );

                        if (existingIndex !== -1) {
                            this.cart[existingIndex].quantity += item.quantity;
                        } else {
                            this.cart.push(item);
                        }

                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { type: 'success', message: 'Ürün Sepete Eklendi' }
                        }));

                        const sound = document.getElementById('addSound');
                        if (sound) {
                            sound.currentTime = 0;
                            sound.play();
                        }
                    },

                    updateQuantity(index, qty) {
                        this.cart[index].quantity = qty;
                    },

                    removeItem(index) {
                        this.cart.splice(index, 1);
                    },

                    submitCart() {
                        fetch('/checkout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                payment: this.payment,
                            }),
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { type: 'success', message: 'Satış başarılı!' }
                                    }));
                                    this.cart = [];
                                } else {
                                    window.dispatchEvent(new CustomEvent('notify', {
                                        detail: { type: 'error', message: data.message || 'Bir hata oluştu!' }
                                    }));
                                }
                            })
                            .catch(() => {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { type: 'error', message: 'Sunucu hatası!' }
                                }));
                            });
                    },

                };
            }
        </script>
    </div>
</x-filament::page>

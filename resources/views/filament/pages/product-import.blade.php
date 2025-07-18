<x-filament::page>
    <div x-data="saleApp()" x-init="init()" class="flex w-full h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-80 bg-white dark:bg-gray-800 border-r border-gray-300 dark:border-gray-700 overflow-y-auto p-4">
            <div class="font-bold text-lg mb-4 text-gray-900 dark:text-white">Ürünler</div>

            <!-- Search -->
            <input type="text" placeholder="Ürün ara..." x-model="sidebarSearch"
                   class="w-full mb-4 px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-black dark:text-white" />

            <!-- Kategoriler ve Ürünler -->
            <template x-for="category in categories" :key="category.id">
                <div class="mb-4">
                    <div class="font-semibold text-md text-primary-600 mb-2" x-text="category.name"></div>
                    <template x-for="product in filteredCategoryProducts(category)" :key="`${category.id}-${product.id}`">
                        <div @click="$dispatch('scan', { barcode: product.barcode })" class="flex justify-between items-center border-b text-sm hover:bg-gray-100 cursor-pointer   hover:text-gray-700 py-3">
                            <span class="px-2" x-text="product.name"></span>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50 dark:bg-gray-900 text-black dark:text-white">
            <!-- Barcode + Quantity -->
            <div class="flex flex-wrap space-x-4 items-end gap-4">
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


            <!-- Varyantlar -->
            <template x-if="variants.length > 0">
                <div class=" p-4 rounded space-y-2 bg-white dark:bg-gray-800">
                    <h1 class="mb-5 text-2xl font-bold " x-text="product.name"></h1>
                    <div class="font-bold py-1">Varyant Seçiniz</div>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="variant in variants" :key="variant.id">
                            <button
                                @click="selectVariant(variant)"
                                class="cursor-pointer border rounded p-2 flex flex-col text-left hover:bg-primary-50"
                            >
                                <div class="flex">
                                    <div class="font-bold text-white pr-2" x-text="` ${variant.quantity} `"></div>
                                    <div class="px-1"></div>
                                    <div class="font-semibold" x-text="variant.type"></div>
                                </div>
                                <div class="text-green-600 font-bold" x-text="`₺${variant.price}`"></div>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Cart -->
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
                            <div class="flex gap-2">
                                <div class="text-sm text-gray-100" x-text="item.variantQuantity"></div>
                                <div class="text-sm text-gray-100" x-text="item.variantType"></div>
                            </div>
                            <div class="text-green-400 font-bold" x-text="`₺${item.price}`"></div>
                        </div>
                        <input style="background: #000077" type="number" min="1" x-model.number="item.quantity"
                               class="w-16 border rounded px-2 py-1 text-center text-black bg-white"
                               @change="updateQuantity(index, item.quantity)" />
                        <button class="text-red-500 ml-2" @click="removeItem(index)">Sil</button>
                    </div>
                </template>

                <div class="text-right mt-5 text-lg font-bold py-4">
                    Toplam: ₺<span x-text="totalPrice().toFixed(2)"></span>
                </div>
            </x-filament::section>

            <!-- Payment -->
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
                <x-filament::button class="py-4 w-full  flex ml-auto justify-end" color="success" @click="submitCart">
                    Satışı Tamamla
                </x-filament::button>
            </div>
        </div>

        <!-- Notification -->
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-on:notify.window="
                message = $event.detail.message;
                type = $event.detail.type;
                show = true;
                setTimeout(() => show = false, 200);
             "
             x-show="show"
             x-transition
             class="fixed inset-0 flex items-center justify-center p-6 rounded-lg shadow-lg text-white z-50 bg-black bg-opacity-60"
             style="display: none;">
            <span class="text-2xl font-bold" x-text="message"></span>
        </div>

        <audio id="addSound" src="/Beep_Once.mp3" preload="auto"></audio>

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
                    categories: @json($this->categories),

                    init() {
                        window.addEventListener('scan', e => {
                            this.scanBarcode(e.detail.barcode);
                        });
                    },

                    addFromSidebar(barcode) {
                        // Inputa değeri set et
                        this.code = barcode;

                        // Inputun gerçek enter basılmış gibi çalışmasını simüle et
                        setTimeout(() => {
                            if ((this.code || '').trim()) {
                                this.scanBarcode();
                            }
                        }, 0);
                    },

                    scanBarcode(passedBarcode = null) {
                        let scanned = passedBarcode;

                        if (!scanned) {
                            scanned = (this.code || '').trim();
                        }

                        if (!scanned) return; // hala boşsa çık

                        this.code = '';

                        console.log("Barkod taranıyor:", scanned);

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
                    }
                    ,

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
                                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: data.message } }));
                                this.cart = [];
                                this.payment = 'Nakit';
                                window.open('/receipt/print/' + data.order.id, '_blank');
                            });
                    },

                    totalPrice() {
                        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    },

                    filteredCategoryProducts(category) {
                        const query = this.sidebarSearch?.toLowerCase() || '';
                        return category.products.filter(p =>
                            p.name.toLowerCase().includes(query)
                        );
                    },
                }
            }
        </script>
    </div>
</x-filament::page>

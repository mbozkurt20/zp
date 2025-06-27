<x-filament::page>
    <div x-data="saleApp()" x-init="init()" class="space-y-6">

        <!-- Barkod ve Adet Input + Buton -->
        <div class="flex space-x-4 items-end gap-8">
            <div class="flex-1">
                <x-filament::input
                    placeholder="Barkod Giriniz"
                    label="Barkod"
                    class="bg-white text-black placeholder:text-gray-500 border border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                    x-model="code"
                    x-on:keydown.enter.prevent="scanBarcode()"
                    x-on:paste="
        setTimeout(() => {
            if (code.trim()) {
                scanBarcode();
            }
        }, 0);
    "
                />

            </div>
            <div class="w-32">
                <x-filament::input
                    style="background: #0b52ea"
                    label="Adet"
                    co
                    type="number"
                    min="1"
                    class="bg-white text-black placeholder:text-gray-500 border border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                    x-model.number="quantity"
                />
            </div>
            <x-filament::button color="primary" @click="scanBarcode">Ürünü Ekle</x-filament::button>
        </div>

        <!-- Sepet -->
        <x-filament::section>
            <x-slot name="heading">Sepet</x-slot>

            <template x-if="cart.length === 0">
                <div class="text-center text-gray-500 py-8 text-lg">
                    Sepette Ürün Bulunmuyor...
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center border-b py-4 space-x-4 gap-4">
                    <img :src="'/storage/'+item.image" class="w-16 h-16 object-cover rounded-md" />
                    <div class="flex-1">
                        <div class="font-bold text-lg" x-text="item.name"></div>
                        <div class="text-green-400 font-bold" x-text="`₺${item.price}`"></div>
                    </div>
                    <input type="number" min="1" x-model.number="item.quantity" style="background: #0b52ea"
                           class="w-16 border rounded px-2 py-1 text-center text-black bg-white placeholder:text-white"
                           @change="updateQuantity(index, item.quantity)" />
                    <button class="text-red-500 ml-2" @click="removeItem(index)">Sil</button>
                </div>
            </template>

            <div class="text-right mt-5 text-lg font-bold py-4">
                Toplam: ₺<span x-text="totalPrice().toFixed(2)"></span>
            </div>
        </x-filament::section>

        <!-- Ödeme Tipi -->
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

        <!-- Tamamla -->
        <x-filament::button color="success" @click="submitCart">Satışı Tamamla</x-filament::button>
    </div>

    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="
        message = $event.detail.message;
        type = $event.detail.type;
        show = true;
        setTimeout(() => show = false, 3000);
    "
        x-show="show"
        x-transition
        class="fixed inset-0 flex items-center justify-center p-6 rounded-lg shadow-lg text-white z-50 bg-black bg-opacity-60"
        style="display: none;"
    >
        <span class="text-2xl font-bold" x-text="message"></span>
    </div>

    <audio id="addSound" src="/Beep_Once.mp3" preload="auto"></audio>

    <script>

        function saleApp() {
            return {
                code: '',
                quantity: 1,
                cart: @json(session('cart', [])),
                payment: 'Nakit',

                init() {},

                scanBarcode() {
                    if (!this.code.trim() || this.quantity < 1) return;
                    let scanned = this.code;
                    let qty = this.quantity;
                    this.code = '';
                    this.quantity = 1;

                    fetch('/scan-product', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ barcode: scanned, quantity: qty }),
                    })
                        .then(async res => {
                            if (!res.ok) {
                                const error = await res.json();
                                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: error.message || 'Bir hata oluştu' } }));
                                return;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data) {
                                this.cart = data.cart;
                                window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', message: 'Ürün Sepete Eklendi' } }));

                                const sound = document.getElementById('addSound');
                                if (sound) {
                                    sound.currentTime = 0; // başa sar
                                    sound.play();
                                }
                            }
                        })
                        .catch(err => {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', message: 'Sunucu hatası!' } }));
                            console.error(err);
                        });
                },

                updateQuantity(index, qty) {
                    fetch('/update-cart-quantity', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ index: index, quantity: qty }),
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.cart = data.cart;
                        });
                },

                removeItem(index) {
                    fetch('/remove-from-cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ index: index }),
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.cart = data.cart;
                        });
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
                            this.payment = '';
                        });
                },

                totalPrice() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                }
            };
        }
    </script>
</x-filament::page>

<x-filament::page>
    <style>
        @media print {
            .print-hidden {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .space-y-10 > * {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
            }

            .shadow, .shadow-sm, .shadow-md {
                box-shadow: none !important;
            }

            .bg-white, .bg-gray-50 {
                background-color: white !important;
            }

            .text-right {
                display: none !important;
            }

            .avoid-break {
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="space-y-10">

        {{-- 🔍 FİLTRELER --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 print-hidden">
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-200">Başlangıç Tarihi</label>
                <input wire:model="dateFrom" type="date" id="dateFrom" class="w-full border rounded px-3 py-2 shadow-sm placeholder:text-gray-700  text-gray-700" />
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-200">Bitiş Tarihi</label>
                <input wire:model="dateTo" type="date" id="dateTo" class="w-full border rounded px-3 py-2 shadow-sm placeholder:text-gray-700 text-gray-700" />
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-200">Ürün Ara</label>
                <input wire:model.debounce.500ms="search"  type="text" id="search" placeholder="Ürün adına göre ara..." class="placeholder:text-gray-700 w-full text-gray-700 border rounded px-3 py-2 shadow-sm" />
            </div>
            <div class="flex items-end">
                <button wire:click="generateReport"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white rounded px-4 py-2">
                    Ara
                </button>
            </div>
        </div>
        <br>
        <br>
        {{-- GENEL ÖZET --}}
        <div>
            <h2 class="text-xl font-bold ">Genel Sipariş Özeti</h2>
            <p class="mb-5">Bu raporlar günlüktür gün sonu otomatik yeni gün raporları gelicektir. </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                    <div class="text-sm font-bold text-gray-700 mb-2">Toplam Sipariş Sayısı</div>
                    <div class="text-2xl font-semibold text-gray-700">{{ $data['totalOrders'] }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                    <div class="text-sm font-bold text-gray-700 mb-2">Toplam Ciro</div>
                    <div class="text-2xl font-semibold text-gray-700">₺{{ number_format($data['totalRevenue'], 2,',','.') }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                    <div class="text-sm font-bold text-gray-700 mb-2">Toplam Kar</div>
                    <div class="text-2xl font-semibold text-gray-700">₺{{ number_format($data['realizedProfit'], 2,',','.') }}</div>
                </div>

                @foreach($data['paymentTypeReport'] as $p)
                    <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                        <div class="text-sm font-bold text-gray-700 mb-2">{{ $p['payment_type'] }}</div>
                        <div class="text-sm font-bold text-gray-700 mb-2"><strong>İşlem Sayısı: </strong>{{ $p['basket_count'] }}</div>
                        <div class="text-2xl font-semibold text-gray-700">₺{{ number_format($p['total_revenue'], 2, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ÜRÜN BAZLI RAPOR --}}
        <div class="py-12">
            <h2 class="text-xl font-bold mb-5">Ürün Bazlı Sipariş Raporu</h2>

            @foreach ($data['reportItems'] as $item)
                @php $product = $item['product']; @endphp
                <div class="border border-gray-200 rounded-lg p-3 mb-4 shadow-sm bg-white avoid-break">
                    <div class="text-xl font-semibold text-gray-700">{{ $product->name }}</div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3 text-sm text-gray-600">
                        @foreach($product->variants as $i => $variant)
                            <div class="grid grid-cols-2 rounded px-3 p-2" style="background: #0522e1;color: white;padding: 3px">
                                <p><strong>Variant-{{$i+1}} | </strong>
                                    <span class="font-medium">{{ $variant->type }} Fiyatı:</span>
                                    <strong>₺{{ number_format($variant->price, 2,',','.') }}</strong></p>
                            </div>
                        @endforeach
                        <div><span class="font-medium">Sepete Eklenme:</span> <strong>{{ $item['added_count'] }} kez</strong></div>
                        <div><span class="font-medium">Satış Adedi:</span> <strong>{{ $item['sold_count'] }} adet</strong></div>
                        <div><span class="font-medium">Toplam Satış Tutarı:</span> <strong>₺{{ number_format($item['soldTotal'], 2,',','.') }}</strong></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- YAZDIR BUTONU --}}
        <div class="text-right print-hidden">
            <button onclick="window.print()" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow">
                Yazdır
            </button>
        </div>
    </div>
</x-filament::page>

<x-filament::page>
    <style>
        @media print {
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

        {{-- GENEL ÖZET --}}
        <div>
            <h2 class="text-xl font-bold mb-4">Genel Sipariş Özeti</h2>
            <div class="grid grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                    <div class="text-sm text-gray-700 mb-2">Toplam Sipariş Sayısı</div>
                    <div class="text-2xl font-semibold text-gray-700">{{ $data['totalOrders'] }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded shadow-sm avoid-break">
                    <div class="text-sm text-gray-700 mb-2">Toplam Ciro</div>
                    <div class="text-2xl font-semibold text-gray-700">₺{{ number_format($data['totalRevenue'], 2) }}</div>
                </div>
            </div>
        </div>

        {{-- ÜRÜN BAZLI RAPOR --}}
        <div class="py-12">
            <h2 class="text-xl font-bold mb-5">Ürün Bazlı Sipariş Raporu</h2>

            @foreach ($data['reportItems'] as $item)
                @php
                    $product = $item['product'];
                @endphp

                <div class="border border-gray-200 rounded-lg p-3 mb-4 shadow-sm bg-white avoid-break">
                    <div class="text-xl font-semibold text-gray-700">{{ $product->name }}</div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-3 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">{{ $product->stock_type }} Fiyatı:</span>
                            ₺{{ number_format($product->price, 2) }}
                        </div>
                        <div>
                            <span class="font-medium">Sepete Eklenme:</span>
                            {{ $item['added_count'] }} kez
                        </div>
                        <div>
                            <span class="font-medium">Satış Adedi:</span>
                            {{ $item['sold_count'] }} adet
                        </div>
                        <div>
                            <span class="font-medium">Toplam Satış Tutarı:</span>
                            ₺{{ number_format($item['sold_count'] * $product->price, 2) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- YAZDIR BUTONU --}}
        <div class="text-right">
            <button
                onclick="window.print()"
                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow">
                Yazdır
            </button>
        </div>
    </div>
</x-filament::page>

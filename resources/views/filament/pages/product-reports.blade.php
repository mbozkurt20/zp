<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <x-filament::card>
            <div class="text-gray-200 mb-2">Toplam Sermaye (Alış Ücreti)</div>
            <div class="text-lg font-bold">{{ number_format($totalPurchase, 2) }}₺</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-gray-200 mb-2">Toplam Satış Değeri</div>
            <div class="text-lg font-bold">{{ number_format($totalSales, 2) }}₺</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-gray-200 mb-2">Uyarı Miktarını Aşan Ürün Sayısı</div>
            <div class="text-lg font-bold">{{ $warningCount }}</div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-gray-200 mb-2"> Toplam Ürün Sayısı</div>
            <div class="text-lg font-bold">{{ $totalCount }}</div>
        </x-filament::card>
    </div>

    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold"></h1>
        <x-filament::button color="primary" onclick="window.print()">
            Rapor Yazdır
        </x-filament::button>
    </div>

    <div class="overflow-x-auto">
        <table id="report-table" class="min-w-full border border-gray-300">
            <thead class="bg-gray-400">
            <tr>
                <th class="border px-4 py-2 text-left">Barkod</th>
                <th class="border px-4 py-2 text-left">Ürün Adı</th>
                <th class="border px-4 py-2 text-left">Alış Fiyatı</th>
                <th class="border px-4 py-2 text-left">Satış Fiyatı</th>
                <th class="border px-4 py-2 text-left">Kar Payı</th>
                <th class="border px-4 py-2 text-left">Mevcut Miktar</th>
                <th class="border px-4 py-2 text-left">Miktar Türü</th>
                <th class="border px-4 py-2 text-left">Uyarı Miktarı</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($products as $product)
                <tr class="">
                    <td class="border px-4 py-2 bg-white">

                      <div class="py-5 ">
                          <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&includetext=false&scaleX=3&scaleY=0.8" alt="Barcode">
                      </div>
                    </td>
                    <td class="border px-4 py-2">{{ $product->name }}</td>
                    <td class="border px-4 py-2">{{ number_format($product->purchase_price, 2) }}₺</td>
                    <td class="border px-4 py-2">{{ number_format($product->price, 2) }}₺</td>
                    <td class="border px-4 py-2">{{ number_format($product->price - $product->purchase_price, 2) }}₺</td>
                    <td class="border px-4 py-2">{{ $product->quantity }}</td>
                    <td class="border px-4 py-2">{{ $product->stock_type }}</td>
                    <td class="border px-4 py-2">{{ $product->warning_quantity ?? 'Bulunmuyor' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-4 py-2 text-center">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #report-table, #report-table * {
                visibility: visible;
            }
            #report-table {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</x-filament::page>

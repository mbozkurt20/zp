<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_NAME') }} - Ürün Fişi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 28px; /* Başlığı büyüttüm */
            font-weight: bold;
        }

        .product-list {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: left;
            font-size: 16px; /* Ürün metinleri için genel font büyüklüğü */
        }

        .product-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .product-item div:first-child {
            font-size: 17px;
            font-weight: 500;
        }

        .product-item small {
            font-size: 14px;
        }

        .product-item strong {
            font-size: 16px;
        }

        .footer {
            margin-top: 40px;
        }

        .footer img {
            width: 250px;
            height: auto;
        }

        @media print {
            body {
                padding: 0;
            }

            .product-item {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print();">

<h2 style="margin-top: 3vh">Sipariş Fişi</h2>

<div class="product-list">
    @php $total = 0; @endphp
    @foreach($order->basket->basketItems as $item)
        @php
            $lineTotal = $item->productVariant->price * $item->quantity;
            $total += $lineTotal;
        @endphp
        <div class="d-flex justify-content-between align-items-center product-item">
            <div style="flex: 1;">
                {{ $item->product->name }} x {{ $item->productVariant->quantity }} {{ $item->productVariant->type }}
            </div>
            <div style="min-width: 80px; text-align: right;">
                <small>{{ number_format($item->productVariant->price, 2) }}₺ x {{ $item->quantity }}</small><br>
                <strong>{{ number_format($lineTotal, 2) }}₺</strong>
            </div>
        </div>
    @endforeach
    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
        <strong>Toplam:</strong>
        <strong>{{ number_format($total, 2) }}₺</strong>
    </div>
</div>

<div class="footer">
    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $order->barcode }}&includetext=false&scaleX=3&scaleY=0.8" alt="Barcode">
</div>

</body>
</html>

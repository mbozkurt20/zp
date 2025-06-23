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
        }

        .product-list {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: left;
        }

        .product-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
            margin-bottom: 10px;
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
            $lineTotal = $item->product->price * $item->quantity;
            $total += $lineTotal;
        @endphp
        <div class="d-flex justify-content-between align-items-center product-item">
            <div style="flex: 1;">
                {{ $item->product->name }} x {{ $item->quantity }} {{ $item->product->stock_type }}
            </div>
            <div style="min-width: 80px; text-align: right;">
                <small>{{ number_format($item->product->price, 2) }}₺ x {{ $item->quantity }}</small><br>
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
    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $order->barcode }}&code=Code128&translate-esc=false&text=false" alt="Barkod" />
</div>

</body>
</html>

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
            padding: 60px 30px;
            text-align: center;
        }

        h2 {
            margin-bottom: 40px;
            font-size: 90px;
            font-weight: bold;
        }

        .product-list {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
            font-size: 54px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .product-item div:first-child {
            font-size: 60px;
            font-weight: 600;
        }

        .product-item small {
            font-size: 48px;
        }

        .product-item strong {
            font-size: 54px;
        }

        .total {
            border-top: 4px solid #000;
            padding-top: 30px;
            margin-top: 50px;
            font-size: 64px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }

        .footer {
            margin-top: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 60px;
        }

        .footer img {
            width: 750px;
            height: auto;
        }

        @media print {
            body {
                padding: 0;
            }

            .product-item {
                page-break-inside: avoid;
            }

            h2 {
                margin-top: 0;
            }
        }
    </style>
</head>
<body onload="window.print();">

<h2>Sipariş Fişi</h2>
<br>
<br>
<div class="product-list">
    @php $total = 0; @endphp
    @foreach($order->basket->basketItems as $item)
        @php
            $lineTotal = $item->productVariant->price * $item->quantity;
            $total += $lineTotal;
        @endphp
        <div class="product-item">
            <div>
                {{ $item->product->name }} x {{ $item->productVariant->quantity }} {{ $item->productVariant->type }}
            </div>
            <div style="text-align: right;">
                <small>{{ number_format($item->productVariant->price, 2) }}₺ x {{ $item->quantity }}</small><br>
                <strong>{{ number_format($lineTotal, 2) }}₺</strong>
            </div>
        </div>
    @endforeach

    <div class="total">
        <span>Toplam:</span>
        <span>{{ number_format($total, 2) }}₺</span>
    </div>
</div>

<div class="footer">
    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $order->barcode }}&includetext=false&scaleX=5&scaleY=2" alt="Barcode">
</div>

</body>
</html>

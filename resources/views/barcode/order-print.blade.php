<!DOCTYPE html>
<html>
<head>
    <title>{{ env('APP_NAME') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 50px;
            text-align: center;
        }

        .title {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 30px;
        }

        .title h5, .title h4 {
            margin: 0;
        }

        .title h5 {
            text-align: left;
        }

        .title h4 {
            text-align: right;
        }

        .body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }

        .footer {
            margin-top: auto;
        }

        img {
            width: 300px;
            height: auto;
        }
    </style>
</head>
<body onload="window.print();">

<div class="title">
    <h4>Sipariş No: {{ $order->id }}</h4>
    <h4>Müşteri: {{ $order->user->name }}</h4>
</div>

<div class="body">
    <h2>Sipariş İçerikleri</h2>
    <br>
    @foreach($order->basket->basketItems as $item)
        <h4>{{ $item->product->name }} * {{ $item->quantity }} {{$item->product->stock_type}}</h4>
    @endforeach
</div>

<div class="footer">
    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $order->barcode }}&code=Code128&translate-esc=false&text=false" alt="Barcode" />
</div>

</body>
</html>

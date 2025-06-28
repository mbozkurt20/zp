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
            width: 100%;
            margin-bottom: 30px;
        }

        .title h4 {
            margin-bottom: 5px;
        }

        .body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-top: 15px;
            gap: 10px;
        }

        .footer {
            margin-top: auto;
            text-align: center;
        }

        img {
            width: 200px;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body onload="window.print();">

<div class="title">
    <h4>Müşteri: {{ $order->user->name }} {{ $order->user->surname }}</h4>
    <h4>Telefon: {{ $order->user->phone }}</h4>
    <h4>Sipariş No: {{ $order->id }}</h4>
</div>

<div class="body">
    <h2>Sipariş İçerikleri</h2>
    <br>
    @foreach($order->basket->basketItems as $item)
        <h4>{{ $item->product->name }} * {{ $item->quantity }} {{ $item->product->stock_type }}</h4>
    @endforeach
</div>

<div class="footer">
    <img style="height: 5vh;width: 5vw"  src="/images/logo.png" alt="Logo">
    <br>
    <br>
    <p class="font-bold">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
    <p>emisof.com.tr</p>

</div>
<script>
    window.onload = function() {
        window.print();
        window.onafterprint = function() {
            window.close();
        }
    };
</script>
</body>
</html>

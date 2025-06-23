<!DOCTYPE html>
<html>
<head>
    <title>Toplu Barkod Yazdır</title>
    <style>
        body {
            padding: 40px;
            font-family: sans-serif;
        }
        .barcode {
            display: inline-block;
            text-align: center;
            margin: 20px;
            page-break-inside: avoid;
        }
        img {
            width: 250px;
            height: auto;
        }
    </style>
</head>
<body onload="window.print(); window.onafterprint = () => window.close();">

@foreach ($products as $product)
    <div class="barcode">
        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $product->barcode }}&code=Code128&translate-esc=false&text=false" alt="Barcode" />
    </div>
@endforeach

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Barkod Yazdır</title>
    <style>
        body {
            text-align: center;
            padding: 50px;
        }
        img {
            width: 300px;
            height: auto;
        }
    </style>
</head>
<body onload="window.print();">


<img src="https://barcode.tec-it.com/barcode.ashx?data={{ $product->barcode }}&code=Code128&translate-esc=false&text=false" alt="Barcode" />

</body>
</html>

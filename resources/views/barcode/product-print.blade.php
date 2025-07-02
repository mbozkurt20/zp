<!DOCTYPE html>
<html>
<head>
    <title>Barkod Yazdır</title>
    <style>
        @page {
            size: 60mm 40mm;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 60mm;
            height: 40mm;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4mm;
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
            max-height: 70%;
            object-fit: contain;
        }

        .product-name {
            width: 100%;
            text-align: center;
            font-size: 6pt; /* Küçük font */
            margin-top: 4px;
            word-wrap: break-word;
        }
    </style>
</head>
<body onload="window.print();">

<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&includetext=false&scaleX=3&scaleY=1.5" alt="Barcode">

<div class="product-name">{{ $product->name }}</div>

</body>
</html>

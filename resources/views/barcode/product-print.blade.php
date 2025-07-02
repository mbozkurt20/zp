<!DOCTYPE html>
<html>
<head>
    <title>Barkod Yazdır</title>
    <style>
        @page {
            size: 50mm 30mm;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 50mm;
            height: 30mm;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.2mm;
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body onload="window.print();">

<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&includetext=false&scaleX=3&scaleY=0.8" alt="Barcode">

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Barkod Yazdır</title>
    <style>
        @page {
            size: 50mm 30mm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            text-align: center;
            /* Sayfa ortalama için */
            width: 50mm;
            height: 30mm;
        }
        img {
            width: 50mm;
            height: 30mm;
            object-fit: contain; /* Barkodun bozulmaması için */
        }
    </style>
</head>
<body onload="window.print();">

<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&includetext=false&scaleX=3&scaleY=0.8" alt="Barcode">

</body>
</html>

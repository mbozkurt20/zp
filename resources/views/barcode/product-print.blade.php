<!DOCTYPE html>
<html>
<head>
    <title>Barkod Yazdır</title>
    <style>
        @page {
            size: 60mm 40mm; /* En 60mm, boy 40mm */
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 60mm;  /* En 60mm */
            height: 40mm; /* Boy 40mm */
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4mm; /* Kenarlardan biraz daha boşluk */
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

<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&includetext=false&scaleX=3&scaleY=1.5" alt="Barcode">

</body>
</html>

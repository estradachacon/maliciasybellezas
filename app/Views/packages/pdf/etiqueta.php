<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 0;
        }

@page {
    margin: 0;
}

body {
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: Arial, sans-serif;
    font-size: 9px;
}

.etiqueta {
    width: 278pt;
    border: 1px solid #000;
    padding: 5pt;
    page-break-inside: avoid;
}
body {
    overflow: hidden;
}
        /* tabla segura */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        .logo {
            height: 25px;
        }

        .codigo {
            text-align: right;
            font-weight: bold;
            font-size: 9px;
        }

        .cliente {
            font-weight: bold;
            font-size: 10px;
        }

        .info {
            font-size: 8px;
        }

        .destino {
            font-weight: bold;
            font-size: 10px;
        }

        .footer td {
            font-size: 9px;
        }

        .total {
            font-weight: bold;
        }

        .codigo {
            text-align: right;
            font-weight: bold;
            font-size: 8px;

            /* 🔥 CLAVE */
            word-wrap: break-word;
            overflow: hidden;
            max-width: 100px;
        }

        .destino {
            font-weight: bold;
            font-size: 9px;

            /* 🔥 CLAVE */
            word-wrap: break-word;
        }
        
    </style>
</head>

<body>

    <div class="etiqueta">

        <table>
            <tr>
                <td width="65%">
                    <img src="<?= $logo ?>" class="logo">
                </td>
                <td width="35%" class="codigo">
                    <?= $codigo ?>
                </td>
            </tr>
        </table>

        <div class="cliente"><?= $cliente ?></div>

        <div class="info">Tel: <?= $telefono ?></div>
        <div class="destino"><?= $destino ?></div>

        <div class="info">Fecha: <?= $fecha ?></div>
        <div class="info">Hora: <?= $hora ?></div>

        <table class="footer">
            <tr>
                <td>$<?= $precio ?></td>
                <td>$<?= $envio ?></td>
                <td class="total">TOTAL $<?= $total ?></td>
            </tr>
        </table>

    </div>

</body>

</html>
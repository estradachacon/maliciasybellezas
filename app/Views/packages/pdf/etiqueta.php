<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 0;
        }

        html,
        body {
            width: 278pt;
            height: 150pt;
            margin: 3;
            padding: 0;
            overflow: hidden;
            font-family: sans-serif;
            font-size: 9pt;
        }

        .etiqueta {
            /* Bajamos un poco las dimensiones para dejar un margen físico real */
            width: 262pt;
            height: 110pt;
            margin: 4pt auto;
            border-radius: 4%;
            /* Centramos la viñeta en el papel */
            padding: 5pt;
            border: 2px solid #000;
            overflow: hidden;
            display: block;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ayuda a que no se estire más de la cuenta */
        }

        /* TÍTULO */
        .titulo {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4pt;
        }

        /* FILAS */
        .fila {
            font-size: 4pt;
            margin-bottom: 2pt;
        }

        /* DOS COLUMNAS */
        .row {
            width: 100%;
        }

        .col-izq {
            width: 50%;
            display: inline-block;
        }

        .col-der {
            width: 50%;
            display: inline-block;
        }

        /* FOOTER */
        .footer {
            margin-top: 4pt;
            font-size: 7pt;
        }

        .total {
            font-weight: bold;
        }

        .col-1 {
            width: 5%;
        }

        .col-2 {
            width: 10%;
        }

        .col-3 {
            width: 15%;
        }

        .col-4 {
            width: 20%;
        }

        .col-5 {
            width: 25%;
        }

        .col-6 {
            width: 30%;
        }

        .col-7 {
            width: 35%;
        }

        .col-8 {
            width: 40%;
        }

        .col-9 {
            width: 45%;
        }

        .col-10 {
            width: 50%;
        }

        .col-11 {
            width: 55%;
        }

        .col-12 {
            width: 60%;
        }

        .col-13 {
            width: 65%;
        }

        .col-14 {
            width: 70%;
        }

        .col-15 {
            width: 75%;
        }

        .col-16 {
            width: 80%;
        }

        .col-17 {
            width: 85%;
        }

        .col-18 {
            width: 90%;
        }

        .col-19 {
            width: 95%;
        }

        .col-20 {
            width: 100%;
        }

        .value {
            min-height: 16pt;
        }

        .label {
            white-space: nowrap;
        }


        .bloque {
            border-bottom: 1px dotted #999;
            padding-bottom: 2pt;
            margin-bottom: 2pt;
        }

        .multi-line {
            height: 18pt;
            line-height: 8pt;
            overflow: hidden;

            word-wrap: break-word;
            word-break: normal;
            /* 🔥 CAMBIAR ESTO */
        }
    </style>
</head>

<body>
    <table style="height:110pt;">

        <!-- 🔹 CONTENIDO -->
        <tr>
            <td style="vertical-align:top;">

                <!-- LOGO + CONTENIDO -->
                <table>
                    <tr>

                        <!-- LOGO -->
                        <td class="col-5" style="text-align:center; vertical-align:top;">
                            <img src="<?= $logo ?>" style="width:100%; max-height:80pt;">
                        </td>

                        <!-- CONTENIDO -->
                        <td class="col-15">

                            <!-- HEADER -->
                            <table style="border-bottom:1px solid #000; margin-bottom:2pt;">
                                <tr>
                                    <td class="col-17" style="font-size:11pt; font-weight:bold; font-family: 'Trebuchet MS', sans-serif;">
                                        MALICIAS Y BELLEZAS
                                    </td>
                                    <td class="col-3" style="text-align:right; font-size:6pt;">
                                        #<?= $codigo ?>
                                    </td>
                                </tr>
                            </table>
                            <!-- CLIENTE FULL -->
                            <table class="bloque">
                                <tr>
                                    <td class="col-20">
                                        <table>
                                            <tr>
                                                <td class="col-4 label">Cliente:</td>
                                                <td class="col-16 value"><?= $cliente ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- TEL + FECHA -->
                            <table class="bloque">
                                <tr>
                                    <td class="col-10">
                                        <table>
                                            <tr>
                                                <td class="col-6 label">Tel:</td>
                                                <td class="col-14 value"><?= $telefono ?></td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td class="col-10">
                                        <table>
                                            <tr>
                                                <td class="col-6 label">Fecha:</td>
                                                <td class="col-14 value">
                                                    <?php
                                                    Locale::setDefault('es_SV');
                                                    $fmt = new IntlDateFormatter('es_SV', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                                    $fmt->setPattern('EEE d');
                                                    echo ucfirst($fmt->format(new DateTime($fecha)));
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- ENCOMENDISTA FULL -->
                            <table class="bloque">
                                <tr>
                                    <td class="col-20">
                                        <table>
                                            <tr>
                                                <td class="col-6 label">Encomendista:</td>
                                                <td class="col-14 value">
                                                    <?= wordwrap($encomendista, 30, true) ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>

                    </tr>
                </table>

                <!-- DESTINO (full width) -->
                <table class="bloque">
                    <tr>
                        <td class="col-2 label">Destino:</td>
                        <td class="col-10 value">
                            <?= $destino ?>
                            <?php if (!empty($hora)): ?>
                                (<?= $hora ?>)
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <!-- ESPACIADOR QUE CRECE -->
        <tr>
            <td></td>
        </tr>
        <!-- FOOTER FIJO ABAJO -->
        <tr>
            <td style="vertical-align:bottom;">

                <table>
                    <tr>
                        <td class="col-20" style="text-align:right; font-weight:bold; font-size:9pt;">
                            Total: $<?= $total ?>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

    </table>
</body>

</html>
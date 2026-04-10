<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
@media print {}

@page {
margin: 0;
}

/* 🔥 BASE GENERAL */
html,
body {
width: 278pt;
height: 155pt;
margin: 2;
padding: 0;
overflow: hidden;
font-family: DejaVu Sans, sans-serif;
font-size: 9pt;
}

/* CONTENEDOR PRINCIPAL */
.etiqueta {
width: 262pt;
height: 120pt;
margin: 4pt auto;
border-radius: 4%;
padding: 2pt;
border: 1px solid #000;
overflow: hidden;
display: block;
position: relative;
}

/* TABLAS */
table {
width: 100%;
border-collapse: collapse;
table-layout: fixed;
}

/* 🔥 TÍTULO */
.titulo {
font-size: 12pt;
font-weight: bold;
text-align: center;
margin-bottom: 2pt;
}

/* TEXTO GENERAL */
.fila {
font-size: 7pt;
margin-bottom: 1pt;
}

/* COLUMNAS */
.row {
width: 100%;
}

.col-izq,
.col-der {
width: 50%;
display: inline-block;
}

/* FOOTER */
.footer {
margin-top: 2pt;
font-size: 8pt;
}

/* 🔥 TOTAL */
.total {
font-weight: bold;
font-size: 12pt;
}

/* GRID DE COLUMNAS */
.col-1 { width: 5%; }
.col-2 { width: 10%; }
.col-3 { width: 15%; }
.col-4 { width: 20%; }
.col-5 { width: 25%; }
.col-6 { width: 30%; }
.col-7 { width: 35%; }
.col-8 { width: 40%; }
.col-9 { width: 45%; }
.col-10 { width: 50%; }
.col-11 { width: 55%; }
.col-12 { width: 60%; }
.col-13 { width: 65%; }
.col-14 { width: 70%; }
.col-15 { width: 75%; }
.col-16 { width: 80%; }
.col-17 { width: 85%; }
.col-18 { width: 90%; }
.col-19 { width: 95%; }
.col-20 { width: 100%; }

/* 🔥 LABELS */
.label {
white-space: nowrap;
font-weight: bold;
font-size: 7pt;
}

/* CELDAS */
td,
tr {
padding: 0;
margin: 0;
line-height: 1.1;
vertical-align: middle;
}

/* BLOQUES */
.bloque {
border-bottom: 1px dotted #999;
padding-bottom: 1pt;
margin-bottom: 1pt;
}

/* TEXTO MULTILÍNEA */
.multi-line {
height: 17pt;
line-height: 8pt;
overflow: hidden;
word-wrap: break-word;
word-break: normal;
}

    </style>
</head>

<body>

    <div class="etiqueta">

        <table style="width:100%; height:100%;">

            <tr>
                <!-- COLUMNA IZQUIERDA -->
                <td style="width:30%; vertical-align:top; padding:0;">

                    <!-- LOGO -->
                    <?php if (!empty($logo)): ?>
                        <img src="<?= $logo ?>" style="width:100%; max-height:57pt; display:block; margin-top:-7pt;">
                    <?php endif; ?>

                    <!-- MENSAJE -->
                    <img src="<?= base_url('img/gracias.png') ?>"
                        style="width:100%; max-height:20pt; display:block; margin-top:-5pt;">

                    <!-- QR -->
                    <?php if (!empty($qr)): ?>
                        <div style="text-align:center; margin-top:1pt;">
                            <img src="<?= $qr ?>" style="width: 60px;pt; height:38pt;">
                        </div>
                    <?php endif; ?>

                </td>

                <!-- COLUMNA DERECHA -->
                <td style="width:70%; padding-left:4pt; vertical-align:top;">

                    <!-- HEADER -->
                    <table style="border-bottom:1px solid #000;">
                        <tr>
                            <td>
                                <img src="<?= $titulo_img ?>" style="height:26pt;">
                            </td>
                            <td style="text-align:right; font-size:6pt; padding-top:18pt;" >
                                #<?= $codigo ?>
                            </td>
                        </tr>
                    </table>

                    <!-- CLIENTE -->
                    <table class="bloque">
                        <tr>
                            <td class="label" style="width:20%;">Cliente:</td>
                            <td><?= $cliente ?></td>
                        </tr>
                    </table>

                    <!-- TEL + FECHA -->
                    <table class="bloque">
                        <tr>
                            <td style="width:50%;">
                                <span class="label">Tel:</span> <?= $telefono ?>
                            </td>
                            <td style="width:50%;">
                                <span class="label">Fecha:</span>
                                <?php
                                Locale::setDefault('es_SV');
                                $fmt = new IntlDateFormatter('es_SV', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                $fmt->setPattern('EEE d');
                                echo ucfirst($fmt->format(new DateTime($fecha)));
                                ?>
                            </td>
                        </tr>
                    </table>

                    <!-- ENCOMENDISTA -->
                    <table class="bloque">
                        <tr>
                            <td class="label" style="width:33%;">Encomendista:</td>
                            <td><?= $encomendista ?></td>
                        </tr>
                    </table>

                    <!-- 🔥 DESTINO AHORA DENTRO DE LA COLUMNA -->
                    <table class="bloque">
                        <tr>
                            <td class="label" style="width:20%;">Destino:</td>
                            <td>
                                <?= $destino ?>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

            <!-- FOOTER -->
            <tr>
                <td colspan="2" style="vertical-align:bottom;">
                    <table>
                        <tr>
                            <td style="text-align:right; font-weight:bold; font-size:9pt;"> 
                                <?php if (!empty($hora)): ?>
                                    (<?= $hora ?>)
                                <?php endif; ?>
                                Total: $<?= $total ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>

    </div>
</body>

</html>
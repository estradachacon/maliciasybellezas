<?php

if (!function_exists('factura_builder')) {
    function factura_builder($data)
    {
        return [
            "identificacion" => [
                "tipoDte" => "01",
                "fecEmi" => date('Y-m-d'),
                "horEmi" => date('H:i:s'),
            ],
            "emisor" => [
                "nit" => $data['emisor']['nit'] ?? null,
                "nombre" => $data['emisor']['nombre'] ?? null,
            ],
            "receptor" => [
                "nit" => $data['receptor']['nit'] ?? null,
                "nombre" => $data['receptor']['nombre'] ?? null,
            ],
            "detalle" => build_detalle($data['items'] ?? []),
            "resumen" => [
                "totalPagar" => $data['total'] ?? 0
            ]
        ];
    }
}

if (!function_exists('ccf_builder')) {
    function ccf_builder($data)
    {
        return [
            "identificacion" => [
                "tipoDte" => "03",
                "fecEmi" => date('Y-m-d'),
                "horEmi" => date('H:i:s'),
            ],
            "emisor" => [
                "nit" => $data['emisor']['nit'] ?? null,
                "nombre" => $data['emisor']['nombre'] ?? null,
            ],
            "receptor" => [
                "nit" => $data['receptor']['nit'] ?? null,
                "nombre" => $data['receptor']['nombre'] ?? null,
                "nrc" => $data['receptor']['nrc'] ?? null,
            ],
            "detalle" => build_detalle($data['items'] ?? []),
            "resumen" => [
                "totalPagar" => $data['total'] ?? 0
            ]
        ];
    }
}

if (!function_exists('build_detalle')) {
    function build_detalle($items)
    {
        $detalle = [];

        foreach ($items as $i => $item) {
            $detalle[] = [
                "numItem" => $i + 1,
                "descripcion" => $item['descripcion'] ?? '',
                "cantidad" => $item['cantidad'] ?? 1,
                "precioUni" => $item['precio'] ?? 0,
                "ventaTotal" => ($item['cantidad'] ?? 1) * ($item['precio'] ?? 0),
            ];
        }

        return $detalle;
    }
}
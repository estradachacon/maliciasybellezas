<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class MockHaciendaController extends ResourceController
{
    public function recepcionDTE()
    {
        $request = service('request')->getJSON(true);

        // 🔥 Validación básica
        $validacion = $this->validarDTE($request);

        if ($validacion !== true) {
            return $this->response->setJSON([
                "estado" => "RECHAZADO",
                "codigoMsg" => "002",
                "descripcionMsg" => "Documento inválido",
                "observaciones" => $validacion
            ])->setStatusCode(400);
        }

        // 🎲 Simulación de error aleatorio (opcional)
        if (rand(1, 10) == 1) {
            return $this->response->setJSON([
                "estado" => "RECHAZADO",
                "codigoMsg" => "099",
                "descripcionMsg" => "Error interno simulado",
                "observaciones" => ["Fallo aleatorio de prueba"]
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            "estado" => "PROCESADO",
            "codigoGeneracion" => strtoupper(uniqid()),
            "selloRecibido" => strtoupper(bin2hex(random_bytes(8))),
            "fhProcesamiento" => date('c'),
            "clasificaMsg" => "OK",
            "codigoMsg" => "001",
            "descripcionMsg" => "Documento recibido correctamente",
            "observaciones" => []
        ]);
    }

    private function validarDTE($data)
    {
        $errores = [];

        // 🔍 Validaciones básicas (ajústalas a tu estructura)
        if (empty($data['documento'])) {
            $errores[] = "Falta el objeto documento";
        }

        if (empty($data['documento']['receptor']['nit'])) {
            $errores[] = "El NIT del receptor es obligatorio";
        }

        if (empty($data['documento']['emisor']['nit'])) {
            $errores[] = "El NIT del emisor es obligatorio";
        }

        if (empty($data['documento']['totalPagar'])) {
            $errores[] = "El total a pagar es obligatorio";
        }

        if (!empty($data['documento']['totalPagar']) && $data['documento']['totalPagar'] <= 0) {
            $errores[] = "El total debe ser mayor a 0";
        }

        return empty($errores) ? true : $errores;
    }
}

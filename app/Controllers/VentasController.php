<?php

namespace App\Controllers;

class VentasController extends BaseController
{
    public function index()
    {
        // 🔥 Por ahora vacío (luego metemos modelo)
        $data['ventas'] = [];

        return view('ventas/index', $data);
    }

    public function nueva()
    {
        return view('ventas/nueva');
    }
}

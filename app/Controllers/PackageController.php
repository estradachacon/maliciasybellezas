<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\SellerModel;
use App\Models\SettledPointModel;
use Dompdf\Dompdf;
use Dompdf\Options;


class PackageController extends BaseController
{
    protected $packageModel;
    protected $sellerModel;
    protected $settledPointModel;
    protected $packages;
    public function __construct()
    {
        $this->packageModel = new PackageModel();
        $this->settledPointModel = new SettledPointModel();
        $this->sellerModel = new SellerModel();
        $this->packages = new PackageModel();
    }

    public function index() {}

    public function show($id = null) {}

    public function new()
    {
        return view('packages/new');
    }
    public function generarEtiqueta()
    {
        $data = [
            'logo' => base_url('favicon.ico'), // dompdf sí soporta esto
            'codigo' => session('codigo_vendedor') ?? 'N/A',
            'cliente' => $this->request->getGet('cliente_nombre'),
            'telefono' => $this->request->getGet('cliente_telefono'),
            'destino' => $this->request->getGet('destino'),
            'fecha' => $this->request->getGet('dia_entrega'),
            'hora' => $this->request->getGet('hora_inicio') . ' - ' . $this->request->getGet('hora_fin'),
            'precio' => $this->request->getGet('precio'),
            'envio' => $this->request->getGet('envio'),
            'total' => $this->request->getGet('total'),
        ];

        $html = view('packages/pdf/etiqueta', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true); // 🔥 para imágenes

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        // 🔥 TAMAÑO EXACTO 4x2 pulgadas
        $dompdf->setPaper([0, 0, 288, 144]);

        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }
    public function store() {}

    public function subirImagen() {}
    public function edit($id) {}

    public function update($id) {}

    public function setDestino() {}

    public function setReenvio() {}

    public function getPackageData($id) {}
    public function getDestinoInfo($id) {}
    public function devolver($id) {}
    public function entregar($id) {}

    public function showReturnPackages() {}
    public function quickLoad() {}
    public function quickStore() {}
    public function updateFlete() {}

    public function updatePagoParcial() {}
    public function updateFleteCompleto() {}
    public function marcarNoRetirado($id) {}
}

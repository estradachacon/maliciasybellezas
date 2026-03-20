<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\SellerModel;
use App\Models\SettledPointModel;
use App\Models\AccountModel;
use App\Models\TrackingDetailsModel;

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

    public function index()
    {
    }

    public function show($id = null)
    {
    }

    public function new()
    {
        return view('packages/new');
    }

    public function store()
    {
    }

    public function subirImagen()
    {
    }
    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function setDestino()
    {
    }

    public function setReenvio()
    {
    }

    public function getPackageData($id)
    {
    }
    public function getDestinoInfo($id)
    {
    }
    public function devolver($id)
    {
    }
    public function entregar($id)
    {
    }

    public function showReturnPackages()
    {
    }
    public function quickLoad()
    {
    }
    public function quickStore()
    {
    }
    public function updateFlete()
    {
    }

    public function updatePagoParcial()
    {
    }
    public function updateFleteCompleto()
    {
    }
    public function marcarNoRetirado($id)
    {
    }
}

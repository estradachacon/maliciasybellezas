<?php

namespace App\Models;

use CodeIgniter\Model;

class EncomendistasModel extends Model
{
    protected $table = 'encomendistas';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'encomendista_name',
        'created_at',
        'updated_at'
    ];


public function searchEncomendista($term)
{
    if (!$term || trim($term) === '') {
        return []; 
    }

    return $this->like('encomendista_name', $term)
                ->select('id, encomendista_name')
                ->findAll(20);
}

}

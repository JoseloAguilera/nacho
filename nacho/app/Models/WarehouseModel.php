<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table            = 'warehouses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'address', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'    => 'required|min_length[3]|max_length[100]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'El nombre del depósito es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    /**
     * Get only active warehouses for dropdowns
     */
    public function getActiveWarehouses()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get all warehouses (wrapper for clarity)
     */
    public function getAllWarehouses()
    {
        return $this->findAll();
    }
}

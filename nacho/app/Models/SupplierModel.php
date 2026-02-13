<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'document', 'phone', 'email', 'address'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'     => 'required|min_length[3]|max_length[200]',
        'document' => 'permit_empty|max_length[50]|is_unique[suppliers.document,id,{id}]',
        'email'    => 'permit_empty|valid_email'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'El nombre del proveedor es requerido'
        ],
        'document' => [
            'is_unique' => 'Este documento ya está registrado para otro proveedor'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get supplier account balance
     */
    public function getAccountBalance($supplierId)
    {
        $db = \Config\Database::connect();
        
        // Total de compras a crédito
        $totalPurchases = $db->table('purchases')
            ->select('SUM(total) as total')
            ->where('supplier_id', $supplierId)
            ->where('payment_type', 'credit')
            ->whereIn('status', ['pending', 'partial'])
            ->get()
            ->getRow()
            ->total ?? 0;
        
        // Total de pagos realizados
        $totalPayments = $db->table('supplier_payments')
            ->select('SUM(amount) as total')
            ->where('supplier_id', $supplierId)
            ->get()
            ->getRow()
            ->total ?? 0;
        
        return $totalPurchases - $totalPayments;
    }

    /**
     * Get supplier purchases history
     */
    public function getPurchasesHistory($supplierId)
    {
        return $this->db->table('purchases')
            ->where('supplier_id', $supplierId)
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();
    }
}

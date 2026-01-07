<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
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
        'document' => 'permit_empty|max_length[50]|is_unique[customers.document,id,{id}]',
        'email'    => 'permit_empty|valid_email'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'El nombre del cliente es requerido'
        ],
        'document' => [
            'is_unique' => 'Este documento ya está registrado para otro cliente'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get customer account balance
     */
    public function getAccountBalance($customerId)
    {
        $db = \Config\Database::connect();
        
        // Total de ventas a crédito
        $totalSales = $db->table('sales')
            ->select('SUM(total) as total')
            ->where('customer_id', $customerId)
            ->where('payment_type', 'credit')
            ->whereIn('status', ['pending', 'partial'])
            ->get()
            ->getRow()
            ->total ?? 0;
        
        // Total de pagos realizados
        $totalPayments = $db->table('customer_payments')
            ->select('SUM(amount) as total')
            ->where('customer_id', $customerId)
            ->get()
            ->getRow()
            ->total ?? 0;
        
        return $totalSales - $totalPayments;
    }

    /**
     * Get customer sales history
     */
    public function getSalesHistory($customerId)
    {
        return $this->db->table('sales')
            ->where('customer_id', $customerId)
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierPaymentModel extends Model
{
    protected $table            = 'supplier_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['purchase_id', 'supplier_id', 'amount', 'payment_date', 'payment_method', 'notes'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    // Validation
    protected $validationRules      = [
        'purchase_id'    => 'required|is_natural_no_zero',
        'supplier_id'    => 'required|is_natural_no_zero',
        'amount'         => 'required|decimal',
        'payment_date'   => 'required|valid_date',
        'payment_method' => 'required|in_list[cash,transfer,check,card]'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get payments for a supplier
     */
    public function getSupplierPayments($supplierId)
    {
        return $this->select('supplier_payments.*, purchases.purchase_number')
                    ->join('purchases', 'purchases.id = supplier_payments.purchase_id')
                    ->where('supplier_payments.supplier_id', $supplierId)
                    ->orderBy('supplier_payments.payment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get payments for a purchase
     */
    public function getPurchasePayments($purchaseId)
    {
        return $this->where('purchase_id', $purchaseId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }
}

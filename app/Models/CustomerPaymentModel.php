<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerPaymentModel extends Model
{
    protected $table            = 'customer_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sale_id', 'customer_id', 'amount', 'payment_date', 'payment_method', 'notes'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    // Validation
    protected $validationRules      = [
        'sale_id'        => 'required|is_natural_no_zero',
        'customer_id'    => 'required|is_natural_no_zero',
        'amount'         => 'required|decimal',
        'payment_date'   => 'required|valid_date',
        'payment_method' => 'required|in_list[cash,transfer,check,card]'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get payments for a customer
     */
    public function getCustomerPayments($customerId)
    {
        return $this->select('customer_payments.*, sales.sale_number')
                    ->join('sales', 'sales.id = customer_payments.sale_id')
                    ->where('customer_payments.customer_id', $customerId)
                    ->orderBy('customer_payments.payment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get payments for a sale
     */
    public function getSalePayments($saleId)
    {
        return $this->where('sale_id', $saleId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }
}

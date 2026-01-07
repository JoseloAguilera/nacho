<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['customer_id', 'user_id', 'sale_number', 'date', 'payment_type', 'subtotal', 'tax', 'total', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    // Validation
    protected $validationRules      = [
        'customer_id'  => 'required|is_natural_no_zero',
        'user_id'      => 'required|is_natural_no_zero',
        'sale_number'  => 'required|is_unique[sales.sale_number,id,{id}]',
        'date'         => 'required|valid_date',
        'payment_type' => 'required|in_list[cash,credit]',
        'total'        => 'required|decimal'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get sales with customer and user info
     */
    public function getSalesWithDetails()
    {
        return $this->select('sales.*, customers.name as customer_name, users.username as user_name')
                    ->join('customers', 'customers.id = sales.customer_id')
                    ->join('users', 'users.id = sales.user_id')
                    ->orderBy('sales.date', 'DESC')
                    ->findAll();
    }

    /**
     * Get sale with details
     */
    public function getSaleWithDetails($saleId)
    {
        $sale = $this->select('sales.*, customers.name as customer_name, customers.document as customer_document, users.username as user_name')
                     ->join('customers', 'customers.id = sales.customer_id')
                     ->join('users', 'users.id = sales.user_id')
                     ->find($saleId);

        if ($sale) {
            $db = \Config\Database::connect();
            $sale['details'] = $db->table('sale_details')
                ->select('sale_details.*, products.name as product_name, products.code as product_code')
                ->join('products', 'products.id = sale_details.product_id')
                ->where('sale_id', $saleId)
                ->get()
                ->getResultArray();
        }

        return $sale;
    }

    /**
     * Get pending balance for a sale
     */
    public function getPendingBalance($saleId)
    {
        $sale = $this->find($saleId);
        if (!$sale || $sale['payment_type'] === 'cash') {
            return 0;
        }

        $db = \Config\Database::connect();
        $totalPaid = $db->table('customer_payments')
            ->select('SUM(amount) as total')
            ->where('sale_id', $saleId)
            ->get()
            ->getRow()
            ->total ?? 0;

        return $sale['total'] - $totalPaid;
    }

    /**
     * Update sale status based on payments
     */
    public function updateStatus($saleId)
    {
        $sale = $this->find($saleId);
        if (!$sale) {
            return false;
        }

        $pendingBalance = $this->getPendingBalance($saleId);

        if ($pendingBalance <= 0) {
            $status = 'paid';
        } elseif ($pendingBalance < $sale['total']) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        return $this->update($saleId, ['status' => $status]);
    }

    /**
     * Generate next sale number
     */
    public function generateSaleNumber()
    {
        $lastSale = $this->orderBy('id', 'DESC')->first();
        $number = $lastSale ? intval(substr($lastSale['sale_number'], 2)) + 1 : 1;
        return 'V-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

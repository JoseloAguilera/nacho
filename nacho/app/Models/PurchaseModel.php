<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table            = 'purchases';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['supplier_id', 'user_id', 'warehouse_id', 'purchase_number', 'date', 'payment_type', 'subtotal', 'tax', 'total', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules      = [
        'supplier_id'     => 'required|is_natural_no_zero',
        'user_id'         => 'required|is_natural_no_zero',
        'purchase_number' => 'required|is_unique[purchases.purchase_number,id,{id}]',
        'date'            => 'required|valid_date',
        'payment_type'    => 'required|in_list[cash,credit]',
        'total'           => 'required|decimal'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get purchases with supplier and user info
     */
    public function getPurchasesWithDetails()
    {
        return $this->select('purchases.*, suppliers.name as supplier_name, users.username as user_name')
                    ->join('suppliers', 'suppliers.id = purchases.supplier_id')
                    ->join('users', 'users.id = purchases.user_id')
                    ->orderBy('purchases.date', 'DESC')
                    ->findAll();
    }

    /**
     * Get purchase with details
     */
    public function getPurchaseWithDetails($purchaseId)
    {
        $purchase = $this->select('purchases.*, suppliers.name as supplier_name, suppliers.document as supplier_document, users.username as user_name')
                         ->join('suppliers', 'suppliers.id = purchases.supplier_id')
                         ->join('users', 'users.id = purchases.user_id')
                         ->find($purchaseId);

        if ($purchase) {
            $db = \Config\Database::connect();
            $purchase['details'] = $db->table('purchase_details')
                ->select('purchase_details.*, products.name as product_name, products.code as product_code')
                ->join('products', 'products.id = purchase_details.product_id')
                ->where('purchase_id', $purchaseId)
                ->get()
                ->getResultArray();
        }

        return $purchase;
    }

    /**
     * Get pending balance for a purchase
     */
    public function getPendingBalance($purchaseId)
    {
        $purchase = $this->find($purchaseId);
        if (!$purchase || $purchase['payment_type'] === 'cash') {
            return 0;
        }

        $db = \Config\Database::connect();
        $totalPaid = $db->table('supplier_payments')
            ->select('SUM(amount) as total')
            ->where('purchase_id', $purchaseId)
            ->get()
            ->getRow()
            ->total ?? 0;

        return $purchase['total'] - $totalPaid;
    }

    /**
     * Update purchase status based on payments
     */
    public function updateStatus($purchaseId)
    {
        $purchase = $this->find($purchaseId);
        if (!$purchase) {
            return false;
        }

        $pendingBalance = $this->getPendingBalance($purchaseId);

        if ($pendingBalance <= 0) {
            $status = 'paid';
        } elseif ($pendingBalance < $purchase['total']) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        return $this->update($purchaseId, ['status' => $status]);
    }

    /**
     * Generate next purchase number
     */
    public function generatePurchaseNumber()
    {
        $lastPurchase = $this->orderBy('id', 'DESC')->first();
        $number = $lastPurchase ? intval(substr($lastPurchase['purchase_number'], 2)) + 1 : 1;
        return 'C-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

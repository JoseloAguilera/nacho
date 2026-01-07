<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryAdjustmentModel extends Model
{
    protected $table            = 'inventory_adjustments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'user_id', 'adjustment_type', 'quantity', 'previous_stock', 'new_stock', 'reason', 'notes'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'product_id' => 'required|is_natural_no_zero',
        'user_id' => 'required|is_natural_no_zero',
        'adjustment_type' => 'required|in_list[increase,decrease]',
        'quantity' => 'required|is_natural_no_zero',
        'previous_stock' => 'required|integer',
        'new_stock' => 'required|integer',
        'reason' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get adjustments with product and user details
     */
    public function getAdjustmentsWithDetails()
    {
        return $this->select('inventory_adjustments.*, products.code as product_code, products.name as product_name, users.username')
                    ->join('products', 'products.id = inventory_adjustments.product_id')
                    ->join('users', 'users.id = inventory_adjustments.user_id')
                    ->orderBy('inventory_adjustments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get adjustments for a specific product
     */
    public function getProductAdjustments($productId)
    {
        return $this->select('inventory_adjustments.*, users.username')
                    ->join('users', 'users.id = inventory_adjustments.user_id')
                    ->where('inventory_adjustments.product_id', $productId)
                    ->orderBy('inventory_adjustments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get adjustments by user
     */
    public function getUserAdjustments($userId)
    {
        return $this->select('inventory_adjustments.*, products.code as product_code, products.name as product_name')
                    ->join('products', 'products.id = inventory_adjustments.product_id')
                    ->where('inventory_adjustments.user_id', $userId)
                    ->orderBy('inventory_adjustments.created_at', 'DESC')
                    ->findAll();
    }
}

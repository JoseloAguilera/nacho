<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductStockModel extends Model
{
    protected $table            = 'product_stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'warehouse_id', 'quantity'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get stock for a specific product in a warehouse
     */
    public function getStock($productId, $warehouseId)
    {
        $result = $this->where('product_id', $productId)
                       ->where('warehouse_id', $warehouseId)
                       ->first();
        
        if ($result && isset($result['quantity'])) {
            return (int)$result['quantity'];
        }
        
        return 0;
    }

    /**
     * Get total stock for a product across all warehouses
     */
    public function getTotalStock($productId)
    {
        $result = $this->selectSum('quantity')
                       ->where('product_id', $productId)
                       ->first();
                       
        return $result['quantity'] ?? 0;
    }

    /**
     * Get all products with their stock in a specific warehouse
     */
    public function getProductsByWarehouse($warehouseId)
    {
        return $this->select('
                products.id,
                products.code,
                products.name,
                products.description,
                products.cost_price,
                products.price,
                products.min_sale_price,
                product_stock.quantity as stock,
                categories.name as category_name
            ')
            ->join('products', 'products.id = product_stock.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('product_stock.warehouse_id', $warehouseId)
            ->orderBy('products.name', 'ASC')
            ->findAll();
    }
}

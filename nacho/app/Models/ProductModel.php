<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['category_id', 'code', 'name', 'description', 'cost_price', 'price', 'min_sale_price', 'stock'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'category_id'    => 'required|is_natural_no_zero',
        'code'           => 'required|max_length[50]|is_unique[products.code,id,{id}]',
        'name'           => 'required|min_length[3]|max_length[200]',
        'cost_price'     => 'required|decimal',
        'price'          => 'required|decimal',
        'min_sale_price' => 'required|decimal',
        'stock'          => 'required|integer' // Validamos que se envíe, aunque se calcule
    ];
    protected $validationMessages   = [
        'category_id' => [
            'required' => 'La categoría es requerida'
        ],
        'code' => [
            'required'  => 'El código es requerido',
            'is_unique' => 'Este código ya está en uso'
        ],
        'name' => [
            'required' => 'El nombre del producto es requerido'
        ],
        'cost_price' => [
            'required' => 'El precio de costo es requerido'
        ],
        'price' => [
            'required' => 'El precio de venta es requerido'
        ],
        'min_sale_price' => [
            'required' => 'El precio mínimo de venta es requerido'
        ],
        'stock' => [
            'required' => 'El stock es requerido'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get products with category name
     */
    public function getProductsWithCategory()
    {
        // Se recalculan stocks globales antes de listar
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->findAll();
    }

    /**
     * Search products by name or code
     */
    public function searchProducts($term)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->like('products.name', $term)
                    ->orLike('products.code', $term)
                    ->findAll();
    }

    /**
     * Update stock in a specific warehouse
     */
    public function updateStock($productId, $quantity, $operation = 'add', $warehouseId = null)
    {
        $productStockModel = new ProductStockModel();
        
        // Si no se especifica depósito, usamos el Depósito Central por defecto (ID 1)
        // Esto es para compatibilidad, pero idealmente siempre debe venir el warehouseId
        if (!$warehouseId) {
            $warehouseModel = new WarehouseModel();
            $defaultWarehouse = $warehouseModel->where('name', 'Depósito Central')->first();
            $warehouseId = $defaultWarehouse['id'] ?? 1;
        }

        $currentStock = $productStockModel->getStock($productId, $warehouseId);

        $newStock = $operation === 'add' 
            ? $currentStock + $quantity 
            : $currentStock - $quantity;

        if ($newStock < 0) {
            return false; // No permitir stock negativo
        }

        // Actualizar o insertar en product_stock
        $existingStock = $productStockModel->where('product_id', $productId)
                                         ->where('warehouse_id', $warehouseId)
                                         ->first();

        if ($existingStock) {
            $productStockModel->update($existingStock['id'], ['quantity' => $newStock]);
        } else {
            $productStockModel->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $newStock
            ]);
        }

        // Actualizar stock total en la tabla de productos (caché)
        $totalStock = $productStockModel->getTotalStock($productId);
        $this->update($productId, ['stock' => $totalStock]);

        return true;
    }

    /**
     * Get products with low stock (global check)
     */
    public function getLowStockProducts($threshold = 10)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id')
                    ->where('products.stock <=', $threshold)
                    ->findAll();
    }
}

<?php

namespace App\Controllers;

use App\Models\InventoryAdjustmentModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;
use App\Models\ProductStockModel;

class InventoryAdjustments extends BaseController
{
    protected $adjustmentModel;
    protected $productModel;
    protected $warehouseModel;
    protected $productStockModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->adjustmentModel = new InventoryAdjustmentModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
        $this->productStockModel = new ProductStockModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Ajustes de Inventario',
            'adjustments' => $this->adjustmentModel->getAdjustmentsWithDetails()
        ];

        return view('inventory_adjustments/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nuevo Ajuste de Inventario',
            'products' => $this->productModel->getProductsWithCategory(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses()
        ];

        return view('inventory_adjustments/create', $data);
    }

    public function store()
    {
        $this->db->transStart();

        try {
            $productId = $this->request->getPost('product_id');
            $warehouseId = $this->request->getPost('warehouse_id');
            $adjustmentType = $this->request->getPost('adjustment_type');
            $quantity = $this->request->getPost('quantity');
            $reason = $this->request->getPost('reason');
            $notes = $this->request->getPost('notes');
            
            if (empty($warehouseId)) {
                return redirect()->back()->with('error', 'Debe seleccionar un depósito');
            }

            // Get current product stock in warehouse
            $currentStock = $this->productStockModel->getStock($productId, $warehouseId);
            
            // Calculate new stock
            if ($adjustmentType === 'increase') {
                $newStock = $currentStock + $quantity;
            } else {
                $newStock = $currentStock - $quantity;
                if ($newStock < 0) {
                    return redirect()->back()->with('error', "El stock de este producto en el depósito seleccionado ($currentStock) es insuficiente para realizar el ajuste.");
                }
            }

            // Create adjustment record
            $adjustmentData = [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'user_id' => $this->session->get('id'),
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'previous_stock' => $currentStock,
                'new_stock' => $newStock,
                'reason' => $reason,
                'notes' => $notes
            ];

            $this->adjustmentModel->insert($adjustmentData);

            // Update product stock using the correct model
            // We can use ProductModel wrapper or directly ProductStockModel.
            // Using ProductModel wrapper ensures consistency with other flows.
            $operation = $adjustmentType === 'increase' ? 'add' : 'subtract';
            $this->productModel->updateStock($productId, $quantity, $operation, $warehouseId);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                log_message('error', 'DB Error in InventoryAdjustments: ' . print_r($error, true));
                return redirect()->back()->with('error', 'Error al crear el ajuste: ' . ($error['message'] ?? 'Error desconocido'));
            }

            return redirect()->to('/inventory-adjustments')->with('success', 'Ajuste de inventario creado correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $adjustment = $this->adjustmentModel->select('inventory_adjustments.*, products.code as product_code, products.name as product_name, warehouses.name as warehouse_name, users.username')
                                           ->join('products', 'products.id = inventory_adjustments.product_id')
                                           ->join('warehouses', 'warehouses.id = inventory_adjustments.warehouse_id')
                                           ->join('users', 'users.id = inventory_adjustments.user_id')
                                           ->find($id);
        
        if (!$adjustment) {
            return redirect()->to('/inventory-adjustments')->with('error', 'Ajuste no encontrado');
        }

        $data = [
            'title' => 'Detalle de Ajuste #' . $id,
            'adjustment' => $adjustment
        ];

        return view('inventory_adjustments/view', $data);
    }

    public function history($productId)
    {
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Historial de Ajustes - ' . $product['name'],
            'product' => $product,
            'adjustments' => $this->adjustmentModel->getProductAdjustments($productId)
        ];

        return view('inventory_adjustments/history', $data);
    }

    public function getStock()
    {
        $productId = $this->request->getGet('product_id');
        $warehouseId = $this->request->getGet('warehouse_id');
        
        $stock = $this->productStockModel->getStock($productId, $warehouseId);
        
        return $this->response->setJSON(['stock' => (int)$stock]);
    }
}

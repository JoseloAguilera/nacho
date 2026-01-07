<?php

namespace App\Controllers;

use App\Models\InventoryAdjustmentModel;
use App\Models\ProductModel;

class InventoryAdjustments extends BaseController
{
    protected $adjustmentModel;
    protected $productModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->adjustmentModel = new InventoryAdjustmentModel();
        $this->productModel = new ProductModel();
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
            'products' => $this->productModel->getProductsWithCategory()
        ];

        return view('inventory_adjustments/create', $data);
    }

    public function store()
    {
        $this->db->transStart();

        try {
            $productId = $this->request->getPost('product_id');
            $adjustmentType = $this->request->getPost('adjustment_type');
            $quantity = $this->request->getPost('quantity');
            $reason = $this->request->getPost('reason');
            $notes = $this->request->getPost('notes');

            // Get current product stock
            $product = $this->productModel->find($productId);
            if (!$product) {
                return redirect()->back()->with('error', 'Producto no encontrado');
            }

            $previousStock = $product['stock'];
            
            // Calculate new stock
            if ($adjustmentType === 'increase') {
                $newStock = $previousStock + $quantity;
            } else {
                $newStock = $previousStock - $quantity;
                if ($newStock < 0) {
                    return redirect()->back()->with('error', 'El stock no puede ser negativo');
                }
            }

            // Create adjustment record
            $adjustmentData = [
                'product_id' => $productId,
                'user_id' => $this->session->get('id'),
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reason' => $reason,
                'notes' => $notes
            ];

            $this->adjustmentModel->insert($adjustmentData);

            // Update product stock
            $this->productModel->update($productId, ['stock' => $newStock]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error al crear el ajuste');
            }

            return redirect()->to('/inventory-adjustments')->with('success', 'Ajuste de inventario creado correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $adjustment = $this->adjustmentModel->select('inventory_adjustments.*, products.code as product_code, products.name as product_name, products.stock as current_stock, users.username')
                                           ->join('products', 'products.id = inventory_adjustments.product_id')
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
}

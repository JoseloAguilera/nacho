<?php

namespace App\Controllers;

use App\Models\PurchaseModel;
use App\Models\PurchaseDetailModel;
use App\Models\SupplierModel;
use App\Models\ProductModel;
use App\Models\WarehouseModel;

class Purchases extends BaseController
{
    protected $purchaseModel;
    protected $purchaseDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $warehouseModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->purchaseDetailModel = new PurchaseDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->productModel = new ProductModel();
        $this->warehouseModel = new WarehouseModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('purchases', 'view');

        $data = [
            'title' => 'Compras',
            'purchases' => $this->purchaseModel->getPurchasesWithDetails()
        ];

        return view('purchases/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('purchases', 'insert');

        $data = [
            'title' => 'Nueva Compra',
            'suppliers' => $this->supplierModel->findAll(),
            'products' => $this->productModel->getProductsWithCategory(),
            'warehouses' => $this->warehouseModel->getActiveWarehouses(), // Add warehouses
            'purchase_number' => $this->purchaseModel->generatePurchaseNumber()
        ];

        return view('purchases/create', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('purchases', 'insert');

        $this->db->transStart();

        try {
            $supplierId = $this->request->getPost('supplier_id');
            $warehouseId = $this->request->getPost('warehouse_id'); // Get warehouse_id
            $paymentType = $this->request->getPost('payment_type');
            $products = $this->request->getPost('products');

            if (empty($products)) {
                return redirect()->back()->with('error', 'Debe agregar al menos un producto');
            }
            
            if (empty($warehouseId)) {
                 return redirect()->back()->with('error', 'Debe seleccionar un depósito');
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($products as $product) {
                $subtotal += $product['quantity'] * $product['price'];
            }

            $tax = $subtotal * 0;
            $total = $subtotal + $tax;

            // Crear compra
            $purchaseData = [
                'supplier_id' => $supplierId,
                'user_id' => $this->session->get('id'),
                'warehouse_id' => $warehouseId, // Save warehouse_id
                'purchase_number' => $this->purchaseModel->generatePurchaseNumber(),
                'date' => date('Y-m-d'),
                'payment_type' => $paymentType,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $paymentType === 'cash' ? 'paid' : 'pending'
            ];

            $purchaseId = $this->purchaseModel->insert($purchaseData);


            if (!$purchaseId) {
                return redirect()->back()->with('error', 'Error de validación: ' . print_r($this->purchaseModel->errors(), true));
            }

            // Crear detalles y actualizar stock
            foreach ($products as $product) {
                $detailData = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price']
                ];

                $this->purchaseDetailModel->insert($detailData);

                // Aumentar stock en el depósito seleccionado
                $this->productModel->updateStock($product['product_id'], $product['quantity'], 'add', $warehouseId);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $dbError = $this->db->error();
                log_message('error', 'DB Error in Purchases: ' . print_r($dbError, true));
                return redirect()->back()->with('error', 'Error al crear la compra: ' . ($dbError['message'] ?? 'Error de base de datos'));
            }

            return redirect()->to('/purchases')->with('success', 'Compra creada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        // Check view permission
        require_permission('purchases', 'view');

        $purchase = $this->purchaseModel->getPurchaseWithDetails($id);
        
        if (!$purchase) {
            return redirect()->to('/purchases')->with('error', 'Compra no encontrada');
        }

        $data = [
            'title' => 'Detalle de Compra #' . $purchase['purchase_number'],
            'purchase' => $purchase,
            'pending_balance' => $this->purchaseModel->getPendingBalance($id)
        ];

        return view('purchases/view', $data);
    }

    public function delete($id)
    {
        // Check delete permission
        require_permission('purchases', 'delete');

        $this->db->transStart();

        try {
            $purchase = $this->purchaseModel->getPurchaseWithDetails($id);
            
            if (!$purchase) {
                return redirect()->to('/purchases')->with('error', 'Compra no encontrada');
            }

            // Reducir stock del depósito original
            // Nota: purchaseModel->getPurchaseWithDetails debe traer el warehouse_id
            $warehouseId = $purchase['warehouse_id'];

            foreach ($purchase['details'] as $detail) {
                $this->productModel->updateStock($detail['product_id'], $detail['quantity'], 'subtract', $warehouseId);
            }

            $this->purchaseModel->delete($id);

            $this->db->transComplete();

            return redirect()->to('/purchases')->with('success', 'Compra eliminada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/purchases')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}

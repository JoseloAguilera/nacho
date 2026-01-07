<?php

namespace App\Controllers;

use App\Models\PurchaseModel;
use App\Models\PurchaseDetailModel;
use App\Models\SupplierModel;
use App\Models\ProductModel;

class Purchases extends BaseController
{
    protected $purchaseModel;
    protected $purchaseDetailModel;
    protected $supplierModel;
    protected $productModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->purchaseDetailModel = new PurchaseDetailModel();
        $this->supplierModel = new SupplierModel();
        $this->productModel = new ProductModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Compras',
            'purchases' => $this->purchaseModel->getPurchasesWithDetails()
        ];

        return view('purchases/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nueva Compra',
            'suppliers' => $this->supplierModel->findAll(),
            'products' => $this->productModel->getProductsWithCategory(),
            'purchase_number' => $this->purchaseModel->generatePurchaseNumber()
        ];

        return view('purchases/create', $data);
    }

    public function store()
    {
        $this->db->transStart();

        try {
            $supplierId = $this->request->getPost('supplier_id');
            $paymentType = $this->request->getPost('payment_type');
            $products = $this->request->getPost('products');

            if (empty($products)) {
                return redirect()->back()->with('error', 'Debe agregar al menos un producto');
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
                'purchase_number' => $this->purchaseModel->generatePurchaseNumber(),
                'date' => date('Y-m-d'),
                'payment_type' => $paymentType,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $paymentType === 'cash' ? 'paid' : 'pending'
            ];

            $purchaseId = $this->purchaseModel->insert($purchaseData);

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

                // Aumentar stock
                $this->productModel->updateStock($product['product_id'], $product['quantity'], 'add');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error al crear la compra');
            }

            return redirect()->to('/purchases')->with('success', 'Compra creada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
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
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to('/purchases')->with('error', 'No tiene permisos');
        }

        $this->db->transStart();

        try {
            $purchase = $this->purchaseModel->getPurchaseWithDetails($id);
            
            if (!$purchase) {
                return redirect()->to('/purchases')->with('error', 'Compra no encontrada');
            }

            // Reducir stock
            foreach ($purchase['details'] as $detail) {
                $this->productModel->updateStock($detail['product_id'], $detail['quantity'], 'subtract');
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

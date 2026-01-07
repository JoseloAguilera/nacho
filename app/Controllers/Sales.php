<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;

class Sales extends BaseController
{
    protected $saleModel;
    protected $saleDetailModel;
    protected $customerModel;
    protected $productModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->session = session();
        $this->db = \Config\Database::connect();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Ventas',
            'sales' => $this->saleModel->getSalesWithDetails()
        ];

        return view('sales/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nueva Venta',
            'customers' => $this->customerModel->findAll(),
            'products' => $this->productModel->getProductsWithCategory(),
            'sale_number' => $this->saleModel->generateSaleNumber()
        ];

        return view('sales/create', $data);
    }

    public function store()
    {
        $this->db->transStart();

        try {
            // Validar datos básicos
            $customerId = $this->request->getPost('customer_id');
            $paymentType = $this->request->getPost('payment_type');
            $products = $this->request->getPost('products');
            $authPassword = $this->request->getPost('auth_password');

            if (empty($products)) {
                return redirect()->back()->with('error', 'Debe agregar al menos un producto');
            }

            // Validar precios mínimos
            $settingsModel = new \App\Models\SettingsModel();
            $minPricePassword = $settingsModel->getValue('min_price_password', '0000');
            $authRequired = false;

            foreach ($products as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    // Si el precio de venta es menor al mínimo
                    if ($item['price'] < $product['min_sale_price']) {
                        $authRequired = true;
                        break;
                    }
                }
            }

            if ($authRequired) {
                if (empty($authPassword)) {
                    return redirect()->back()->withInput()->with('error', 'Se requiere autorización para vender por debajo del precio mínimo');
                }

                if ($authPassword !== $minPricePassword) {
                    return redirect()->back()->withInput()->with('error', 'Contraseña de autorización incorrecta');
                }
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($products as $product) {
                $subtotal += $product['quantity'] * $product['price'];
            }

            $tax = $subtotal * 0; // Sin impuestos por ahora
            $total = $subtotal + $tax;

            // Crear venta
            $saleData = [
                'customer_id' => $customerId,
                'user_id' => $this->session->get('id'),
                'sale_number' => $this->saleModel->generateSaleNumber(),
                'date' => date('Y-m-d'),
                'payment_type' => $paymentType,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $paymentType === 'cash' ? 'paid' : 'pending'
            ];

            $saleId = $this->saleModel->insert($saleData);

            // Crear detalles y actualizar stock
            foreach ($products as $product) {
                $detailData = [
                    'sale_id' => $saleId,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price']
                ];

                $this->saleDetailModel->insert($detailData);

                // Reducir stock
                $this->productModel->updateStock($product['product_id'], $product['quantity'], 'subtract');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error al crear la venta');
            }

            return redirect()->to('/sales')->with('success', 'Venta creada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function view($id)
    {
        $sale = $this->saleModel->getSaleWithDetails($id);
        
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Venta no encontrada');
        }

        $data = [
            'title' => 'Detalle de Venta #' . $sale['sale_number'],
            'sale' => $sale,
            'pending_balance' => $this->saleModel->getPendingBalance($id)
        ];

        return view('sales/view', $data);
    }

    public function delete($id)
    {
        // Solo admin puede eliminar
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to('/sales')->with('error', 'No tiene permisos');
        }

        $this->db->transStart();

        try {
            $sale = $this->saleModel->getSaleWithDetails($id);
            
            if (!$sale) {
                return redirect()->to('/sales')->with('error', 'Venta no encontrada');
            }

            // Restaurar stock
            foreach ($sale['details'] as $detail) {
                $this->productModel->updateStock($detail['product_id'], $detail['quantity'], 'add');
            }

            // Eliminar venta (los detalles se eliminan por CASCADE)
            $this->saleModel->delete($id);

            $this->db->transComplete();

            return redirect()->to('/sales')->with('success', 'Venta eliminada correctamente');

        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/sales')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function validateAuth()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $password = $this->request->getPost('password');
        $settingsModel = new \App\Models\SettingsModel();
        $minPricePassword = $settingsModel->getValue('min_price_password', '0000');

        if ($password === $minPricePassword) {
            return $this->response->setJSON(['valid' => true]);
        } else {
            return $this->response->setJSON(['valid' => false]);
        }
    }
    public function searchProducts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $term = $this->request->getGet('term');
        
        $productModel = new \App\Models\ProductModel();
        
        $query = $productModel->select('products.*, categories.name as category_name')
                              ->join('categories', 'categories.id = products.category_id');

        if (!empty($term)) {
            $query->groupStart()
                  ->like('products.name', $term)
                  ->orLike('products.code', $term)
                  ->groupEnd();
        }

        // Limit to 50 results for performance
        $products = $query->limit(50)->find();

        return $this->response->setJSON($products);
    }
}

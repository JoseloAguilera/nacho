<?php

namespace App\Controllers;

use App\Models\ProductStockModel;
use App\Models\WarehouseModel;

class ProductStock extends BaseController
{
    protected $productStockModel;
    protected $warehouseModel;
    protected $session;

    public function __construct()
    {
        $this->productStockModel = new ProductStockModel();
        $this->warehouseModel = new WarehouseModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Stock de Productos',
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'products' => [],
            'selectedWarehouse' => null
        ];

        return view('product_stock/index', $data);
    }

    public function warehouse($warehouseId)
    {
        // Validate warehouse exists
        $warehouse = $this->warehouseModel->find($warehouseId);
        
        if (!$warehouse) {
            return redirect()->to('/product-stock')->with('error', 'Depósito no encontrado');
        }

        $data = [
            'title' => 'Stock de Productos',
            'warehouses' => $this->warehouseModel->getActiveWarehouses(),
            'products' => $this->productStockModel->getProductsByWarehouse($warehouseId),
            'selectedWarehouse' => $warehouseId
        ];

        return view('product_stock/index', $data);
    }
}

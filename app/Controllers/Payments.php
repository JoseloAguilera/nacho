<?php

namespace App\Controllers;

use App\Models\SupplierPaymentModel;
use App\Models\PurchaseModel;
use App\Models\SupplierModel;

class Payments extends BaseController
{
    protected $paymentModel;
    protected $purchaseModel;
    protected $supplierModel;
    protected $session;

    public function __construct()
    {
        $this->paymentModel = new SupplierPaymentModel();
        $this->purchaseModel = new PurchaseModel();
        $this->supplierModel = new SupplierModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Obtener compras a crédito pendientes
        $db = \Config\Database::connect();
        $purchases = $db->table('purchases')
            ->select('purchases.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchases.supplier_id')
            ->where('purchases.payment_type', 'credit')
            ->whereIn('purchases.status', ['pending', 'partial'])
            ->orderBy('purchases.date', 'DESC')
            ->get()
            ->getResultArray();

        // Calcular saldo pendiente
        foreach ($purchases as &$purchase) {
            $purchase['pending_balance'] = $this->purchaseModel->getPendingBalance($purchase['id']);
        }

        $data = [
            'title' => 'Cuentas por Pagar',
            'purchases' => $purchases
        ];

        return view('payments/index', $data);
    }

    public function create($purchaseId)
    {
        $purchase = $this->purchaseModel->getPurchaseWithDetails($purchaseId);
        
        if (!$purchase) {
            return redirect()->to('/payments')->with('error', 'Compra no encontrada');
        }

        $pendingBalance = $this->purchaseModel->getPendingBalance($purchaseId);

        $data = [
            'title' => 'Registrar Pago',
            'purchase' => $purchase,
            'pending_balance' => $pendingBalance,
            'payments' => $this->paymentModel->getPurchasePayments($purchaseId)
        ];

        return view('payments/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'purchase_id' => 'required|is_natural_no_zero',
            'amount' => 'required|decimal',
            'payment_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[cash,transfer,check,card]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $purchaseId = $this->request->getPost('purchase_id');
        $amount = $this->request->getPost('amount');

        // Verificar que el monto no exceda el saldo pendiente
        $pendingBalance = $this->purchaseModel->getPendingBalance($purchaseId);
        if ($amount > $pendingBalance) {
            return redirect()->back()->with('error', 'El monto excede el saldo pendiente');
        }

        $purchase = $this->purchaseModel->find($purchaseId);

        $data = [
            'purchase_id' => $purchaseId,
            'supplier_id' => $purchase['supplier_id'],
            'amount' => $amount,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_method' => $this->request->getPost('payment_method'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->paymentModel->insert($data)) {
            // Actualizar estado de la compra
            $this->purchaseModel->updateStatus($purchaseId);
            
            return redirect()->to('/payments')->with('success', 'Pago registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->paymentModel->errors());
        }
    }

    public function history($supplierId)
    {
        $supplier = $this->supplierModel->find($supplierId);
        
        if (!$supplier) {
            return redirect()->to('/payments')->with('error', 'Proveedor no encontrado');
        }

        $data = [
            'title' => 'Historial de Pagos - ' . $supplier['name'],
            'supplier' => $supplier,
            'payments' => $this->paymentModel->getSupplierPayments($supplierId),
            'balance' => $this->supplierModel->getAccountBalance($supplierId)
        ];

        return view('payments/history', $data);
    }
}

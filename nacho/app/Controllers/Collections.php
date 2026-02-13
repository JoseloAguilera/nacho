<?php

namespace App\Controllers;

use App\Models\CustomerPaymentModel;
use App\Models\SaleModel;
use App\Models\CustomerModel;

class Collections extends BaseController
{
    protected $paymentModel;
    protected $saleModel;
    protected $customerModel;
    protected $session;

    public function __construct()
    {
        $this->paymentModel = new CustomerPaymentModel();
        $this->saleModel = new SaleModel();
        $this->customerModel = new CustomerModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Obtener ventas a crédito pendientes
        $db = \Config\Database::connect();
        $sales = $db->table('sales')
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_type', 'credit')
            ->whereIn('sales.status', ['pending', 'partial'])
            ->orderBy('sales.date', 'DESC')
            ->get()
            ->getResultArray();

        // Calcular saldo pendiente para cada venta
        foreach ($sales as &$sale) {
            $sale['pending_balance'] = $this->saleModel->getPendingBalance($sale['id']);
        }

        $data = [
            'title' => 'Cuentas por Cobrar',
            'sales' => $sales
        ];

        return view('collections/index', $data);
    }

    public function create($saleId)
    {
        $sale = $this->saleModel->getSaleWithDetails($saleId);
        
        if (!$sale) {
            return redirect()->to('/collections')->with('error', 'Venta no encontrada');
        }

        $pendingBalance = $this->saleModel->getPendingBalance($saleId);

        $data = [
            'title' => 'Registrar Pago',
            'sale' => $sale,
            'pending_balance' => $pendingBalance,
            'payments' => $this->paymentModel->getSalePayments($saleId)
        ];

        return view('collections/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'sale_id' => 'required|is_natural_no_zero',
            'amount' => 'required|decimal',
            'payment_date' => 'required|valid_date',
            'payment_method' => 'required|in_list[cash,transfer,check,card]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $saleId = $this->request->getPost('sale_id');
        $amount = $this->request->getPost('amount');

        // Verificar que el monto no exceda el saldo pendiente
        $pendingBalance = $this->saleModel->getPendingBalance($saleId);
        if ($amount > $pendingBalance) {
            return redirect()->back()->with('error', 'El monto excede el saldo pendiente');
        }

        $sale = $this->saleModel->find($saleId);

        $data = [
            'sale_id' => $saleId,
            'customer_id' => $sale['customer_id'],
            'amount' => $amount,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_method' => $this->request->getPost('payment_method'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->paymentModel->insert($data)) {
            // Actualizar estado de la venta
            $this->saleModel->updateStatus($saleId);
            
            return redirect()->to('/collections')->with('success', 'Pago registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->paymentModel->errors());
        }
    }

    public function history($customerId)
    {
        $customer = $this->customerModel->find($customerId);
        
        if (!$customer) {
            return redirect()->to('/collections')->with('error', 'Cliente no encontrado');
        }

        $data = [
            'title' => 'Historial de Pagos - ' . $customer['name'],
            'customer' => $customer,
            'payments' => $this->paymentModel->getCustomerPayments($customerId),
            'balance' => $this->customerModel->getAccountBalance($customerId)
        ];

        return view('collections/history', $data);
    }
}

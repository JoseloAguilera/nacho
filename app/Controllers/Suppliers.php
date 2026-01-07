<?php

namespace App\Controllers;

use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    protected $supplierModel;
    protected $session;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Proveedores',
            'suppliers' => $this->supplierModel->findAll()
        ];

        return view('suppliers/index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Nuevo Proveedor'];
        return view('suppliers/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[200]',
            'document' => 'permit_empty|max_length[50]',
            'phone' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'document' => $this->request->getPost('document'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address')
        ];

        if ($this->supplierModel->insert($data)) {
            return redirect()->to('/suppliers')->with('success', 'Proveedor creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->supplierModel->errors());
        }
    }

    public function edit($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('/suppliers')->with('error', 'Proveedor no encontrado');
        }

        $data = [
            'title' => 'Editar Proveedor',
            'supplier' => $supplier
        ];

        return view('suppliers/edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[200]',
            'document' => 'permit_empty|max_length[50]',
            'phone' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'document' => $this->request->getPost('document'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address')
        ];

        if ($this->supplierModel->update($id, $data)) {
            return redirect()->to('/suppliers')->with('success', 'Proveedor actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->supplierModel->errors());
        }
    }

    public function delete($id)
    {
        if ($this->supplierModel->delete($id)) {
            return redirect()->to('/suppliers')->with('success', 'Proveedor eliminado correctamente');
        } else {
            return redirect()->to('/suppliers')->with('error', 'No se pudo eliminar el proveedor');
        }
    }

    public function account($id)
    {
        $supplier = $this->supplierModel->find($id);
        
        if (!$supplier) {
            return redirect()->to('/suppliers')->with('error', 'Proveedor no encontrado');
        }

        $data = [
            'title' => 'Cuenta Corriente - ' . $supplier['name'],
            'supplier' => $supplier,
            'balance' => $this->supplierModel->getAccountBalance($id),
            'purchases' => $this->supplierModel->getPurchasesHistory($id)
        ];

        return view('suppliers/account', $data);
    }
}

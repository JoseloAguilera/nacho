<?php

namespace App\Controllers;

use App\Models\CustomerModel;

class Customers extends BaseController
{
    protected $customerModel;
    protected $session;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Clientes',
            'customers' => $this->customerModel->findAll()
        ];

        return view('customers/index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Nuevo Cliente'];
        return view('customers/create', $data);
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

        if ($this->customerModel->insert($data)) {
            return redirect()->to('/customers')->with('success', 'Cliente creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->customerModel->errors());
        }
    }

    public function edit($id)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Cliente no encontrado');
        }

        $data = [
            'title' => 'Editar Cliente',
            'customer' => $customer
        ];

        return view('customers/edit', $data);
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

        if ($this->customerModel->update($id, $data)) {
            return redirect()->to('/customers')->with('success', 'Cliente actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->customerModel->errors());
        }
    }

    public function delete($id)
    {
        if ($this->customerModel->delete($id)) {
            return redirect()->to('/customers')->with('success', 'Cliente eliminado correctamente');
        } else {
            return redirect()->to('/customers')->with('error', 'No se pudo eliminar el cliente');
        }
    }

    public function account($id)
    {
        $customer = $this->customerModel->find($id);
        
        if (!$customer) {
            return redirect()->to('/customers')->with('error', 'Cliente no encontrado');
        }

        $data = [
            'title' => 'Cuenta Corriente - ' . $customer['name'],
            'customer' => $customer,
            'balance' => $this->customerModel->getAccountBalance($id),
            'sales' => $this->customerModel->getSalesHistory($id)
        ];

        return view('customers/account', $data);
    }
    public function ajaxStore()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[200]',
            'document' => 'permit_empty|max_length[50]',
            'phone' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email',
            'address' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'document' => $this->request->getPost('document'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address')
        ];

        if ($id = $this->customerModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'customer' => [
                    'id' => $id,
                    'name' => $data['name']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->customerModel->errors()
            ]);
        }
    }
}

<?php

namespace App\Controllers;

use App\Models\ExpenseModel;
use App\Models\ExpenseCategoryModel;

class Expenses extends BaseController
{
    protected $expenseModel;
    protected $categoryModel;
    protected $session;

    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
        $this->categoryModel = new ExpenseCategoryModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('expenses', 'view');

        $data = [
            'title' => 'Gastos',
            'expenses' => $this->expenseModel->getExpensesWithDetails(),
            'total' => $this->expenseModel->getTotalExpenses()
        ];

        return view('expenses/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('expenses', 'insert');

        $data = [
            'title' => 'Nuevo Gasto',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('expenses/create', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('expenses', 'insert');

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'category_id' => 'required|is_natural_no_zero',
            'date' => 'required|valid_date',
            'amount' => 'required|decimal|greater_than[0]',
            'description' => 'required|min_length[3]|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'user_id' => $this->session->get('id'),
            'date' => $this->request->getPost('date'),
            'amount' => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->expenseModel->insert($data)) {
            return redirect()->to('/expenses')->with('success', 'Gasto registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->expenseModel->errors());
        }
    }

    public function edit($id)
    {
        // Check update permission
        require_permission('expenses', 'update');

        $expense = $this->expenseModel->find($id);
        
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Gasto no encontrado');
        }

        $data = [
            'title' => 'Editar Gasto',
            'expense' => $expense,
            'categories' => $this->categoryModel->findAll()
        ];

        return view('expenses/edit', $data);
    }

    public function update($id)
    {
        // Check update permission
        require_permission('expenses', 'update');

        $expense = $this->expenseModel->find($id);
        
        if (!$expense) {
            return redirect()->to('/expenses')->with('error', 'Gasto no encontrado');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'category_id' => 'required|is_natural_no_zero',
            'date' => 'required|valid_date',
            'amount' => 'required|decimal|greater_than[0]',
            'description' => 'required|min_length[3]|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'date' => $this->request->getPost('date'),
            'amount' => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->expenseModel->update($id, $data)) {
            return redirect()->to('/expenses')->with('success', 'Gasto actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->expenseModel->errors());
        }
    }

    public function delete($id)
    {
        // Check delete permission
        require_permission('expenses', 'delete');
        
        if ($this->expenseModel->delete($id)) {
            return redirect()->to('/expenses')->with('success', 'Gasto eliminado correctamente');
        } else {
            return redirect()->to('/expenses')->with('error', 'Error al eliminar el gasto');
        }
    }

    public function report()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $data = [
            'title' => 'Reporte de Gastos',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'expenses' => $this->expenseModel->getExpensesByDateRange($startDate, $endDate),
            'total' => $this->expenseModel->getTotalExpenses($startDate, $endDate),
            'by_category' => $this->expenseModel->getTotalByCategory($startDate, $endDate)
        ];

        return view('expenses/report', $data);
    }
}

<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Categories extends BaseController
{
    protected $categoryModel;
    protected $session;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check view permission
        require_permission('categories', 'view');

        $data = [
            'title' => 'Categorías',
            'categories' => $this->categoryModel->getCategoriesWithCount()
        ];

        return view('categories/index', $data);
    }

    public function create()
    {
        // Check insert permission
        require_permission('categories', 'insert');

        $data = ['title' => 'Nueva Categoría'];
        return view('categories/create', $data);
    }

    public function store()
    {
        // Check insert permission
        require_permission('categories', 'insert');

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('/categories')->with('success', 'Categoría creada correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->categoryModel->errors());
        }
    }

    public function edit($id)
    {
        // Check update permission
        require_permission('categories', 'update');

        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            return redirect()->to('/categories')->with('error', 'Categoría no encontrada');
        }

        $data = [
            'title' => 'Editar Categoría',
            'category' => $category
        ];

        return view('categories/edit', $data);
    }

    public function update($id)
    {
        // Check update permission
        require_permission('categories', 'update');

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('/categories')->with('success', 'Categoría actualizada correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->categoryModel->errors());
        }
    }

    public function delete($id)
    {
        // Check delete permission
        require_permission('categories', 'delete');

        $productModel = new ProductModel();
        
        $productCount = $productModel->where('category_id', $id)->countAllResults();

        if ($productCount > 0) {
            return redirect()->to('/categories')->with('error', 'No se puede eliminar la categoría porque tiene ' . $productCount . ' productos asociados.');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('/categories')->with('success', 'Categoría eliminada correctamente');
        } else {
            return redirect()->to('/categories')->with('error', 'No se pudo eliminar la categoría');
        }
    }
}

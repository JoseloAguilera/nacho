<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseCategoryModel extends Model
{
    protected $table            = 'expense_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'El nombre de la categoría es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get categories with expense count
     */
    public function getCategoriesWithExpenseCount()
    {
        return $this->select('expense_categories.*, COUNT(expenses.id) as expense_count')
                    ->join('expenses', 'expenses.category_id = expense_categories.id', 'left')
                    ->groupBy('expense_categories.id')
                    ->findAll();
    }
}

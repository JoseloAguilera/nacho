<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table            = 'expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['category_id', 'user_id', 'date', 'amount', 'description', 'notes'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'user_id' => 'required|is_natural_no_zero',
        'date' => 'required|valid_date',
        'amount' => 'required|decimal|greater_than[0]',
        'description' => 'required|min_length[3]|max_length[500]'
    ];

    protected $validationMessages = [
        'category_id' => [
            'required' => 'La categoría es requerida'
        ],
        'date' => [
            'required' => 'La fecha es requerida',
            'valid_date' => 'Debe proporcionar una fecha válida'
        ],
        'amount' => [
            'required' => 'El monto es requerido',
            'decimal' => 'El monto debe ser un número válido',
            'greater_than' => 'El monto debe ser mayor a 0'
        ],
        'description' => [
            'required' => 'La descripción es requerida',
            'min_length' => 'La descripción debe tener al menos 3 caracteres'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get expenses with category and user details
     */
    public function getExpensesWithDetails()
    {
        return $this->select('expenses.*, expense_categories.name as category_name, users.username')
                    ->join('expense_categories', 'expense_categories.id = expenses.category_id')
                    ->join('users', 'users.id = expenses.user_id')
                    ->orderBy('expenses.date', 'DESC')
                    ->findAll();
    }

    /**
     * Get expenses by date range
     */
    public function getExpensesByDateRange($startDate, $endDate)
    {
        return $this->select('expenses.*, expense_categories.name as category_name, users.username')
                    ->join('expense_categories', 'expense_categories.id = expenses.category_id')
                    ->join('users', 'users.id = expenses.user_id')
                    ->where('expenses.date >=', $startDate)
                    ->where('expenses.date <=', $endDate)
                    ->orderBy('expenses.date', 'DESC')
                    ->findAll();
    }

    /**
     * Get total expenses by category
     */
    public function getTotalByCategory($startDate = null, $endDate = null)
    {
        $builder = $this->select('expense_categories.name as category_name, SUM(expenses.amount) as total')
                        ->join('expense_categories', 'expense_categories.id = expenses.category_id')
                        ->groupBy('expenses.category_id');

        if ($startDate && $endDate) {
            $builder->where('expenses.date >=', $startDate)
                   ->where('expenses.date <=', $endDate);
        }

        return $builder->findAll();
    }

    /**
     * Get total expenses for a period
     */
    public function getTotalExpenses($startDate = null, $endDate = null)
    {
        $builder = $this->selectSum('amount', 'total');

        if ($startDate && $endDate) {
            $builder->where('date >=', $startDate)
                   ->where('date <=', $endDate);
        }

        $result = $builder->first();
        return $result['total'] ?? 0;
    }
}

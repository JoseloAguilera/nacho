<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseDetailModel extends Model
{
    protected $table            = 'purchase_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['purchase_id', 'product_id', 'quantity', 'price', 'subtotal'];

    // Validation
    protected $validationRules      = [
        'purchase_id' => 'required|is_natural_no_zero',
        'product_id'  => 'required|is_natural_no_zero',
        'quantity'    => 'required|is_natural_no_zero',
        'price'       => 'required|decimal',
        'subtotal'    => 'required|decimal'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}

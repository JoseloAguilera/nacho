<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'is_system'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[roles.name,id,{id}]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required'  => 'El nombre del rol es requerido',
            'is_unique' => 'Este nombre de rol ya existe'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get role with all its permissions
     */
    public function getRoleWithPermissions($roleId)
    {
        $role = $this->find($roleId);
        
        if (!$role) {
            return null;
        }

        $permissionModel = new RolePermissionModel();
        $role['permissions'] = $permissionModel->getPermissionsByRole($roleId);

        return $role;
    }

    /**
     * Get all system roles (cannot be deleted)
     */
    public function getSystemRoles()
    {
        return $this->where('is_system', 1)->findAll();
    }

    /**
     * Get all custom roles (can be deleted)
     */
    public function getCustomRoles()
    {
        return $this->where('is_system', 0)->findAll();
    }

    /**
     * Check if role can be deleted
     */
    public function canDelete($roleId)
    {
        $role = $this->find($roleId);
        
        if (!$role) {
            return false;
        }

        // System roles cannot be deleted
        if ($role['is_system'] == 1) {
            return false;
        }

        // Check if role is assigned to any users
        $db = \Config\Database::connect();
        $userCount = $db->table('users')
            ->where('role_id', $roleId)
            ->countAllResults();

        return $userCount === 0;
    }
}

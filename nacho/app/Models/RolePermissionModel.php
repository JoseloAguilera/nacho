<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table            = 'role_permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['role_id', 'module', 'can_view', 'can_insert', 'can_update', 'can_delete'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'role_id' => 'required|is_natural_no_zero',
        'module'  => 'required|max_length[50]'
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all permissions for a specific role
     */
    public function getPermissionsByRole($roleId)
    {
        return $this->where('role_id', $roleId)
                    ->orderBy('module', 'ASC')
                    ->findAll();
    }

    /**
     * Get permissions for a specific role as associative array
     * Returns: ['module_name' => ['view' => 'S', 'insert' => 'N', ...]]
     */
    public function getPermissionsArray($roleId)
    {
        $permissions = $this->getPermissionsByRole($roleId);
        $result = [];

        foreach ($permissions as $perm) {
            $result[$perm['module']] = [
                'view'   => $perm['can_view'],
                'insert' => $perm['can_insert'],
                'update' => $perm['can_update'],
                'delete' => $perm['can_delete']
            ];
        }

        return $result;
    }

    /**
     * Get permission for a specific module
     */
    public function getModulePermission($roleId, $module)
    {
        return $this->where('role_id', $roleId)
                    ->where('module', $module)
                    ->first();
    }

    /**
     * Check if role has specific permission
     */
    public function checkPermission($roleId, $module, $action)
    {
        $permission = $this->getModulePermission($roleId, $module);
        
        if (!$permission) {
            return false;
        }

        $field = 'can_' . $action;
        return isset($permission[$field]) && $permission[$field] === 'S';
    }

    /**
     * Set permissions for a role (batch update)
     * $permissions format: ['module' => ['view' => 'S', 'insert' => 'N', ...]]
     */
    public function setPermissions($roleId, $permissions)
    {
        // Delete existing permissions for this role
        $this->where('role_id', $roleId)->delete();

        // Insert new permissions
        foreach ($permissions as $module => $actions) {
            $data = [
                'role_id'     => $roleId,
                'module'      => $module,
                'can_view'    => $actions['view'] ?? 'N',
                'can_insert'  => $actions['insert'] ?? 'N',
                'can_update'  => $actions['update'] ?? 'N',
                'can_delete'  => $actions['delete'] ?? 'N'
            ];

            $this->insert($data);
        }

        return true;
    }

    /**
     * Initialize default permissions for a new role
     * All permissions set to 'N' by default
     */
    public function initializePermissions($roleId)
    {
        $modules = \Config\Permissions::getModules();
        
        foreach ($modules as $moduleKey => $moduleName) {
            $this->insert([
                'role_id'     => $roleId,
                'module'      => $moduleKey,
                'can_view'    => 'N',
                'can_insert'  => 'N',
                'can_update'  => 'N',
                'can_delete'  => 'N'
            ]);
        }

        return true;
    }
}

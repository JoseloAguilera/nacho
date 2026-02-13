<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\RolePermissionModel;

class Roles extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $session;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new RolePermissionModel();
        $this->session = session();
        helper(['form', 'url', 'permission']);
    }

    public function index()
    {
        // Check permission
        if (!can_view('roles')) {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para acceder a esta sección');
        }

        $data = [
            'title' => 'Roles de Usuario',
            'roles' => $this->roleModel->findAll()
        ];

        return view('roles/index', $data);
    }

    public function create()
    {
        // Check permission
        if (!can_insert('roles')) {
            return redirect()->to('/roles')->with('error', 'No tiene permisos para crear roles');
        }

        $data = [
            'title' => 'Nuevo Rol',
            'modules' => \Config\Permissions::getModules()
        ];

        return view('roles/create', $data);
    }

    public function store()
    {
        // Check permission
        if (!can_insert('roles')) {
            return redirect()->to('/roles')->with('error', 'No tiene permisos para crear roles');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]|is_unique[roles.name]',
            'description' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Create role
        $roleData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_system' => 0
        ];

        if ($roleId = $this->roleModel->skipValidation(true)->insert($roleData)) {
            // Save permissions
            $this->savePermissions($roleId);

            return redirect()->to('/roles')->with('success', 'Rol creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->roleModel->errors());
        }
    }

    public function edit($id)
    {
        // Check permission
        if (!can_update('roles')) {
            return redirect()->to('/roles')->with('error', 'No tiene permisos para editar roles');
        }

        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado');
        }

        $data = [
            'title' => 'Editar Rol',
            'role' => $role,
            'modules' => \Config\Permissions::getModules(),
            'permissions' => $this->permissionModel->getPermissionsArray($id)
        ];

        return view('roles/edit', $data);
    }

    public function update($id)
    {
        // Check permission
        if (!can_update('roles')) {
            return redirect()->to('/roles')->with('error', 'No tiene permisos para editar roles');
        }

        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado');
        }

        // Prevent editing system roles
        if ($role['is_system'] == 1) {
            return redirect()->to('/roles')->with('error', 'No se pueden editar roles del sistema');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'name' => "required|min_length[3]|max_length[100]|is_unique[roles.name,id,{$id}]",
            'description' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Update role
        $roleData = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->roleModel->skipValidation(true)->update($id, $roleData)) {
            // Update permissions
            $this->savePermissions($id);

            return redirect()->to('/roles')->with('success', 'Rol actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->roleModel->errors());
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!can_delete('roles')) {
            return redirect()->to('/roles')->with('error', 'No tiene permisos para eliminar roles');
        }

        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado');
        }

        // Check if can delete
        if (!$this->roleModel->canDelete($id)) {
            $message = $role['is_system'] == 1 
                ? 'No se pueden eliminar roles del sistema' 
                : 'No se puede eliminar el rol porque tiene usuarios asignados';
            return redirect()->to('/roles')->with('error', $message);
        }

        if ($this->roleModel->delete($id)) {
            return redirect()->to('/roles')->with('success', 'Rol eliminado correctamente');
        } else {
            return redirect()->to('/roles')->with('error', 'No se pudo eliminar el rol');
        }
    }

    /**
     * Save permissions from form data
     */
    private function savePermissions($roleId)
    {
        $modules = \Config\Permissions::getModules();
        $permissions = [];

        foreach ($modules as $moduleKey => $moduleName) {
            $permissions[$moduleKey] = [
                'view'   => $this->request->getPost("perm_{$moduleKey}_view") === 'S' ? 'S' : 'N',
                'insert' => $this->request->getPost("perm_{$moduleKey}_insert") === 'S' ? 'S' : 'N',
                'update' => $this->request->getPost("perm_{$moduleKey}_update") === 'S' ? 'S' : 'N',
                'delete' => $this->request->getPost("perm_{$moduleKey}_delete") === 'S' ? 'S' : 'N'
            ];
        }

        $this->permissionModel->setPermissions($roleId, $permissions);
    }
}

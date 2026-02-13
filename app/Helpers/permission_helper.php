<?php

/**
 * Permission Helper Functions
 * Provides convenient functions to check user permissions
 */

if (!function_exists('has_permission')) {
    /**
     * Check if current user has specific permission
     *
     * @param string $module Module name (e.g., 'products', 'sales')
     * @param string $action Action name (view, insert, update, delete)
     * @return bool
     */
    function has_permission(string $module, string $action): bool
    {
        // Admin always has permission
        if (is_admin()) {
            return true;
        }

        $session = session();
        $permissions = $session->get('permissions');

        if (!$permissions || !is_array($permissions)) {
            return false;
        }

        if (!isset($permissions[$module])) {
            return false;
        }

        return isset($permissions[$module][$action]) && $permissions[$module][$action] === 'S';
    }
}

if (!function_exists('can_view')) {
    /**
     * Check if user can view/consult a module
     */
    function can_view(string $module): bool
    {
        return has_permission($module, 'view');
    }
}

if (!function_exists('can_insert')) {
    /**
     * Check if user can insert in a module
     */
    function can_insert(string $module): bool
    {
        return has_permission($module, 'insert');
    }
}

if (!function_exists('can_update')) {
    /**
     * Check if user can update in a module
     */
    function can_update(string $module): bool
    {
        return has_permission($module, 'update');
    }
}

if (!function_exists('can_delete')) {
    /**
     * Check if user can delete in a module
     */
    function can_delete(string $module): bool
    {
        return has_permission($module, 'delete');
    }
}

if (!function_exists('require_permission')) {
    /**
     * Require permission or redirect
     *
     * @param string $module
     * @param string $action
     * @param string $redirectUrl Where to redirect if no permission
     */
    function require_permission(string $module, string $action, string $redirectUrl = '/dashboard')
    {
        if (!has_permission($module, $action)) {
            session()->setFlashdata('error', 'No tiene permisos para acceder a esta sección');
            return redirect()->to($redirectUrl);
        }
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin (has full permissions)
     */
    function is_admin(): bool
    {
        $session = session();
        $roleId = $session->get('role_id');
        $roleName = $session->get('role_name');
        
        // Check by role_id (1 = Administrador) or role_name
        return $roleId == 1 || $roleName === 'Administrador';
    }
}

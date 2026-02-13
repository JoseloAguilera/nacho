<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required role
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Debe iniciar sesión');
        }

        // If arguments are provided, check if user has one of the required roles
        if ($arguments) {
            helper('permission');
            
            // Check if admin is required and user is admin
            if (in_array('admin', $arguments)) {
                if (is_admin()) {
                    return;
                }
            }

            // Check against role_name stored in session
            $userRole = strtolower($session->get('role_name') ?? '');
            
            // Also check old 'role' for backward compatibility
            $oldRole = strtolower($session->get('role') ?? '');

            if (!in_array($userRole, $arguments) && !in_array($oldRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'No tiene permisos para acceder a esta sección');
            }
        }
    }

    /**
     * Do nothing after
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

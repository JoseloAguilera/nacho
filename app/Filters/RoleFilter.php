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
            $userRole = $session->get('role');
            
            if (!in_array($userRole, $arguments)) {
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

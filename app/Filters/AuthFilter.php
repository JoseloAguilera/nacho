<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth/login')->with('error', 'Debe iniciar sesión para acceder');
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

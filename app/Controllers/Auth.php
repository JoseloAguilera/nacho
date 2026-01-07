<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Show login form
     */
    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Process login
     */
    public function attemptLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->verifyCredentials($username, $password);

        if ($user) {
            // Set session data
            $sessionData = [
                'id'         => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ];
            
            $this->session->set($sessionData);
            
            return redirect()->to('/dashboard')->with('success', 'Bienvenido, ' . $user['username']);
        } else {
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/auth/login')->with('success', 'Sesión cerrada correctamente');
    }

    /**
     * Show register form (only for admin)
     */
    public function register()
    {
        // Check if user is admin
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para registrar usuarios');
        }

        return view('auth/register');
    }

    /**
     * Process registration
     */
    public function attemptRegister()
    {
        // Check if user is admin
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'No tiene permisos para registrar usuarios');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'role'     => 'required|in_list[admin,ventas]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role')
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/dashboard')->with('success', 'Usuario registrado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }
}

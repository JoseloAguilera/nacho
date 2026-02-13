<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        $userId = $this->session->get('id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Mi Perfil',
            'user' => $user
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        $userId = $this->session->get('id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/auth/login');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$userId}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        // Only validate password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'required|min_length[6]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        if ($this->userModel->update($userId, $data)) {
            // Update session data if username/email changed
            $sessionData = [
                'username' => $data['username'],
                'email'    => $data['email']
            ];
            $this->session->set($sessionData);

            return redirect()->to('/profile')->with('success', 'Perfil actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }
}

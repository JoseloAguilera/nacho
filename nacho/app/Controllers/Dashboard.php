<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Dashboard home
     */
    public function index()
    {
        // Get user data
        $data = [
            'title' => 'Dashboard',
            'user'  => [
                'username' => $this->session->get('username'),
                'role'     => $this->session->get('role')
            ]
        ];

        return view('dashboard/index', $data);
    }
}

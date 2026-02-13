<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Redirect to login
        return redirect()->to('/auth/login');
    }
}

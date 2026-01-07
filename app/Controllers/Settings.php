<?php

namespace App\Controllers;

use App\Models\SettingsModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $session;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        // Solo admin puede acceder
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $data = [
            'title' => 'Configuración del Sistema',
            'settings' => $this->settingsModel->getAllSettings()
        ];

        return view('settings/index', $data);
    }

    public function update()
    {
        // Solo admin puede actualizar
        if ($this->session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Acceso denegado');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'company_name' => 'required|min_length[3]|max_length[100]',
            'company_ruc' => 'required|max_length[20]',
            'company_address' => 'required|max_length[200]',
            'company_email' => 'required|valid_email',
            'min_price_password' => 'required|min_length[4]'
        ];

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Actualizar configuraciones
        $settings = [
            'company_name',
            'company_ruc',
            'company_address',
            'company_email',
            'company_phone',
            'min_price_password'
        ];

        foreach ($settings as $key) {
            $value = $this->request->getPost($key);
            $this->settingsModel->setValue($key, $value);
        }

        return redirect()->to('/settings')->with('success', 'Configuración actualizada correctamente');
    }
}

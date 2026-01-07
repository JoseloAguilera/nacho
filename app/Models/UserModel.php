<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'email', 'password', 'role'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'role'     => 'required|in_list[admin,ventas]'
    ];
    protected $validationMessages   = [
        'username' => [
            'required'    => 'El nombre de usuario es requerido',
            'min_length'  => 'El nombre de usuario debe tener al menos 3 caracteres',
            'is_unique'   => 'Este nombre de usuario ya está en uso'
        ],
        'email' => [
            'required'     => 'El email es requerido',
            'valid_email'  => 'Debe proporcionar un email válido',
            'is_unique'    => 'Este email ya está registrado'
        ],
        'password' => [
            'required'    => 'La contraseña es requerida',
            'min_length'  => 'La contraseña debe tener al menos 6 caracteres'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials(string $username, string $password)
    {
        $user = $this->where('username', $username)
                     ->orWhere('email', $username)
                     ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Get user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get user by username
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Check if user has role
     */
    public function hasRole(int $userId, string $role): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === $role;
    }
}

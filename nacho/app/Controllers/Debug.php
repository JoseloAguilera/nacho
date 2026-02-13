<?php

namespace App\Controllers;

class Debug extends BaseController
{
    public function session()
    {
        $session = session();
        
        echo "<h2>CodeIgniter Session Debug</h2>";
        echo "<pre>";
        
        echo "Session ID: " . $session->session_id . "\n\n";
        
        echo "User ID: " . ($session->get('id') ?? 'Not set') . "\n";
        echo "Username: " . ($session->get('username') ?? 'Not set') . "\n";
        echo "Email: " . ($session->get('email') ?? 'Not set') . "\n";
        echo "Role ID: " . ($session->get('role_id') ?? 'Not set') . "\n";
        echo "Role Name: " . ($session->get('role_name') ?? 'Not set') . "\n";
        echo "Is Logged In: " . ($session->get('isLoggedIn') ? 'YES' : 'NO') . "\n\n";
        
        $permissions = $session->get('permissions');
        if ($permissions) {
            echo "Permissions loaded: YES\n";
            echo "Number of modules: " . count($permissions) . "\n\n";
            
            if (isset($permissions['products'])) {
                echo "Products module permissions:\n";
                print_r($permissions['products']);
            } else {
                echo "Products module NOT FOUND in permissions!\n";
            }
            
            echo "\n\nAll permissions:\n";
            print_r($permissions);
        } else {
            echo "Permissions loaded: NO\n";
            echo "ERROR: Permissions not in session!\n";
        }
        
        echo "\n\nFull Session Data:\n";
        print_r($session->get());
        
        echo "</pre>";
    }
}

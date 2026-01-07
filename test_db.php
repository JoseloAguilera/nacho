<?php

// Test database connection and user data
require __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

echo "=== Testing Database Connection ===\n\n";

// Get all users
$query = $db->query("SELECT id, username, email, password, role FROM users");
$users = $query->getResultArray();

echo "Users in database:\n";
foreach ($users as $user) {
    echo "\nID: {$user['id']}\n";
    echo "Username: {$user['username']}\n";
    echo "Email: {$user['email']}\n";
    echo "Role: {$user['role']}\n";
    echo "Password Hash: {$user['password']}\n";
    
    // Test password verification
    $testPassword = 'admin123';
    $verified = password_verify($testPassword, $user['password']);
    echo "Password 'admin123' verified: " . ($verified ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

// Generate new hash
echo "\n=== New Hash for 'admin123' ===\n";
$newHash = password_hash('admin123', PASSWORD_DEFAULT);
echo $newHash . "\n";

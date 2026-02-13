<?php
// Debug script to check session permissions
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['id'] ?? 'Not set') . "\n";
echo "Username: " . ($_SESSION['username'] ?? 'Not set') . "\n";
echo "Role ID: " . ($_SESSION['role_id'] ?? 'Not set') . "\n";
echo "Role Name: " . ($_SESSION['role_name'] ?? 'Not set') . "\n";
echo "Old Role: " . ($_SESSION['role'] ?? 'Not set') . "\n";
echo "\n";

if (isset($_SESSION['permissions'])) {
    echo "Permissions loaded: YES\n";
    echo "Number of modules: " . count($_SESSION['permissions']) . "\n\n";
    
    echo "Products module permissions:\n";
    if (isset($_SESSION['permissions']['products'])) {
        print_r($_SESSION['permissions']['products']);
    } else {
        echo "Products module NOT FOUND in permissions!\n";
    }
    
    echo "\n\nAll permissions:\n";
    print_r($_SESSION['permissions']);
} else {
    echo "Permissions loaded: NO\n";
    echo "ERROR: Permissions array not found in session!\n";
    echo "You need to logout and login again.\n";
}

echo "\n\nFull Session Data:\n";
print_r($_SESSION);
echo "</pre>";

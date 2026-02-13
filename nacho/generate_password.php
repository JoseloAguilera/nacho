<?php

// Script para generar hash de contraseña
// Ejecutar: php generate_password.php

$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Contraseña: {$password}\n";
echo "Hash: {$hash}\n";
echo "\nSQL para actualizar:\n";
echo "UPDATE users SET password = '{$hash}' WHERE username = 'admin';\n";
echo "UPDATE users SET password = '{$hash}' WHERE username = 'vendedor1';\n";

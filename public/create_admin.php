<?php

// Script simple para crear usuarios admin
// Ejecutar: http://localhost/nacho/public/create_admin.php

// Conexión directa a MySQL
$host = 'localhost';
$dbname = 'nacho';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Eliminar usuarios existentes
    $pdo->exec("DELETE FROM users");
    
    // Crear hash de contraseña
    $passwordPlain = 'admin123';
    $hash = password_hash($passwordPlain, PASSWORD_BCRYPT);
    
    // Insertar usuarios
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@nacho.com', $hash, 'admin']);
    $stmt->execute(['vendedor1', 'ventas@nacho.com', $hash, 'ventas']);
    
    echo "<h1>✅ Usuarios creados correctamente</h1>";
    echo "<p><strong>Usuario:</strong> admin<br><strong>Contraseña:</strong> admin123</p>";
    echo "<p><strong>Usuario:</strong> vendedor1<br><strong>Contraseña:</strong> admin123</p>";
    echo "<hr>";
    echo "<p><a href='auth/login' style='font-size: 18px; padding: 10px 20px; background: #6366f1; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a></p>";
    
    // Verificar
    $users = $pdo->query("SELECT id, username, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Usuarios en la base de datos:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='margin-top: 20px;'><small>Hash generado: <code>$hash</code></small></p>";
    
    // Test password verification
    echo "<h3>Verificación de contraseña:</h3>";
    $testUser = $pdo->query("SELECT password FROM users WHERE username = 'admin'")->fetch(PDO::FETCH_ASSOC);
    $verified = password_verify('admin123', $testUser['password']);
    echo "<p>Password 'admin123' verificada: <strong>" . ($verified ? '✅ SÍ' : '❌ NO') . "</strong></p>";
    
} catch (PDOException $e) {
    echo "<h1>❌ Error de conexión</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Verifica que MySQL esté corriendo y que la base de datos 'nacho' exista.</p>";
}

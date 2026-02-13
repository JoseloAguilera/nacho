-- Actualizar contraseñas de usuarios
-- Contraseña para ambos: admin123

UPDATE users SET password = '$2y$10$XolAj4M/YZnchkT0QZVrKOQ3pVJ3qX3qX3qX3qX3qX3qX3qX3qX3q' WHERE username = 'admin';
UPDATE users SET password = '$2y$10$XolAj4M/YZnchkT0QZVrKOQ3pVJ3qX3qX3qX3qX3qX3qX3qX3qX3q' WHERE username = 'vendedor1';
